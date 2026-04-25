<?php

namespace classes\v3\AiAssistant;

use classes\v3\Config\Config;

/**
 * Streams a response from an OpenAI-compatible Chat Completions API using SSE.
 * Works with Groq, OpenAI, OpenRouter, and any compatible provider.
 *
 * Usage:
 *   ChatCompletionsStreamClient::stream($systemPrompt, $messages, function(string $delta) {
 *       echo "data: " . json_encode(['delta' => $delta]) . "\n\n";
 *       flush();
 *   });
 */
final class ChatCompletionsStreamClient
{
    /**
     * Call a Chat Completions API with streaming=true.
     * Invokes $onDelta(string $textDelta) for every content chunk.
     *
     * @param string   $systemPrompt  The system prompt to use
     * @param array    $messages      Array of ['role' => 'user'|'assistant', 'content' => string]
     * @param callable $onDelta       Callback receiving each text chunk
     * @param callable|null $onError  Optional callback receiving error string
     * @return void
     * @throws \RuntimeException on CURL or API error
     */
    public static function stream(
        string   $systemPrompt,
        array    $messages,
        callable $onDelta,
        ?callable $onError = null
    ): void {
        $apiKey    = Config::get('ai_assistant.api_key', '');
        $model     = Config::get('ai_assistant.model', 'llama-3.3-70b-versatile');
        $maxTokens = (int) Config::get('ai_assistant.max_tokens', 1024);
        $apiUrl    = Config::get('ai_assistant.api_url', 'https://api.groq.com/openai/v1/chat/completions');
        $maxAttempts = max(1, (int) Config::get('ai_assistant.max_retries', 3));
        // ms — doubled on each retry up to a reasonable cap so we don't stall the SSE socket.
        $initialBackoffMs = 500;

        if (empty($apiKey)) {
            throw new \RuntimeException('AI API key is not configured');
        }

        // Prepend system message
        $allMessages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $messages
        );

        $payload = json_encode([
            'model'      => $model,
            'max_tokens' => $maxTokens,
            'messages'   => $allMessages,
            'stream'     => true,
        ]);

        // Wrap the user's onDelta so we can detect whether ANY content was
        // already streamed to the client in this attempt. Retries are only
        // safe before the first delta is emitted — otherwise we'd
        // double-render content.
        $deltaEmitted = false;
        $wrappedOnDelta = function (string $delta) use ($onDelta, &$deltaEmitted): void {
            $deltaEmitted = true;
            ($onDelta)($delta);
        };

        $attempt = 0;
        $lastError = null;
        while ($attempt < $maxAttempts) {
            $attempt++;
            // Buffer for incomplete SSE lines that span CURL chunks — fresh per attempt.
            $lineBuffer = '';
            // Collect raw response body for error diagnostics — fresh per attempt.
            $errorBody = '';

            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey,
                    'Accept: text/event-stream',
                ],
                CURLOPT_TIMEOUT        => 120,
                // Stream each chunk as it arrives
                CURLOPT_WRITEFUNCTION  => function ($ch, $data) use ($wrappedOnDelta, &$lineBuffer, &$errorBody): int {
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    // On error responses, collect the body instead of parsing SSE
                    if ($httpCode >= 400) {
                        $errorBody .= $data;
                        return strlen($data);
                    }

                    $lineBuffer .= $data;

                    // Process all complete lines in the buffer
                    while (($pos = strpos($lineBuffer, "\n")) !== false) {
                        $line       = substr($lineBuffer, 0, $pos);
                        $lineBuffer = substr($lineBuffer, $pos + 1);
                        $line       = rtrim($line, "\r");

                        if (!str_starts_with($line, 'data: ')) {
                            continue;
                        }

                        $json = substr($line, 6); // strip "data: "
                        if ($json === '[DONE]') {
                            break;
                        }

                        $event = json_decode($json, true);
                        if (!is_array($event)) {
                            continue;
                        }

                        // OpenAI-compatible format: choices[0].delta.content
                        $delta = $event['choices'][0]['delta']['content'] ?? '';
                        if ($delta !== '') {
                            ($wrappedOnDelta)($delta);
                        }
                    }

                    return strlen($data); // MUST return the number of bytes consumed
                },
            ]);

            curl_exec($ch);
            $errno    = curl_errno($ch);
            $error    = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($errno !== CURLE_OK) {
                $lastError = "AI API CURL error [{$errno}]: {$error}";
                // Retry on network-ish errors (timeouts, connection reset) only
                // while no delta has been streamed yet. Otherwise the client
                // already has partial content and a retry would duplicate it.
                if (!$deltaEmitted && $attempt < $maxAttempts && self::isTransientCurl($errno)) {
                    usleep(self::backoffMicroseconds($initialBackoffMs, $attempt));
                    continue;
                }
                break;
            }

            if ($httpCode >= 400) {
                $detail = '';
                if ($errorBody !== '') {
                    $decoded = json_decode($errorBody, true);
                    $detail  = $decoded['error']['message'] ?? trim(substr($errorBody, 0, 300));
                }
                $lastError = "AI API returned HTTP {$httpCode}" . ($detail !== '' ? ": {$detail}" : '');
                // Retry only on 429 rate-limits and 5xx server errors, and
                // only before any delta reached the client.
                $isRetryable = $httpCode === 429 || ($httpCode >= 500 && $httpCode < 600);
                if (!$deltaEmitted && $isRetryable && $attempt < $maxAttempts) {
                    usleep(self::backoffMicroseconds($initialBackoffMs, $attempt));
                    continue;
                }
                break;
            }

            // Success — no error, exit the loop.
            return;
        }

        if ($lastError !== null) {
            if ($onError !== null) {
                ($onError)($lastError);
            }
            throw new \RuntimeException($lastError);
        }
    }

    /**
     * Is this CURL error code worth a retry? True for timeouts and
     * connection-level blips, false for config errors like CURLE_URL_MALFORMAT.
     */
    private static function isTransientCurl(int $errno): bool
    {
        return in_array(
            $errno,
            [
                CURLE_OPERATION_TIMEOUTED,
                CURLE_COULDNT_CONNECT,
                CURLE_COULDNT_RESOLVE_HOST,
                CURLE_GOT_NOTHING,
                CURLE_RECV_ERROR,
                CURLE_SEND_ERROR,
            ],
            true
        );
    }

    /**
     * Exponential backoff in microseconds — 500 ms, 1000 ms, 2000 ms, …
     * Capped at 4 s so an SSE client isn't left waiting silently.
     */
    private static function backoffMicroseconds(int $initialMs, int $attempt): int
    {
        $ms  = $initialMs * (2 ** ($attempt - 1));
        $cap = 4000;
        return min($ms, $cap) * 1000;
    }
}
