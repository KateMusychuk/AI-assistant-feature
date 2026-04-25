<?php

namespace resources\v2\ai_assistant\chat;

use classes\v3\AiAssistant\AiAssistantService;
use classes\v3\AiAssistant\ChatCompletionsStreamClient;

/**
 * POST /v2/ai_assistant/chat
 *
 * SSE event format:
 *   data: {"delta":"text chunk"}\n\n
 *   data: {"done":true}\n\n
 *   data: {"error":"message"}\n\n
 */
class post extends \ResourceV3
{
    protected bool $parseJsonBody = true;

    protected function defineAccessRules(): array
    {
        return ['student'];
    }

    public function execute(): void
    {
        // Anchor for the structured metrics log emitted at end-of-request —
        // latency is computed against this point so every retry / buffering
        // layer is included in what operators actually see.
        $requestStartedAt = microtime(true);

        $student   = \students::api()->getCurrentStudent();
        $studentId = $student['id'];
        $accountId = $student['account_id'];

        $allowedPageContexts = [
            'assignments', 'library', 'my-learning', 'my-progress',
            'search', 'saved', 'webinar', 'home', 'settings', 'faq',
            // Sub-contexts the portal frontend actually sends; must stay in
            // sync with PAGE_TYPE_MAP in kc-portals-new-era
            // src/lib/stores/ai-assistant/constants.ts.
            'settings-personal', 'settings-privacy', 'settings-license',
            'calendar',
        ];

        $message     = trim((string) $this->getParameter('message'));
        $pageContext = trim((string) ($this->getParameter('page_context') ?? 'general'));
        $lang        = trim((string) ($this->getParameter('lang') ?? 'en'));
        $history     = $this->getParameter('history');
        $history     = is_array($history) ? $history : [];

        if (!in_array($pageContext, $allowedPageContexts, true)) {
            $pageContext = 'general';
        }

        if ($message === '') {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['code' => 400, 'status' => 'Bad Request', 'response' => ['message' => 'message is required']]);
            exit();
        }

        // ---- Switch to SSE mode FIRST, before any heavy work ----
        // Clean output buffers without flushing (avoids sending stray PHP notices)
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        @ini_set('zlib.output_compression', false);
        ob_implicit_flush(true);

        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache, no-store');
        header('X-Accel-Buffering: no');
        header('Connection: keep-alive');

        // Heartbeat — confirms SSE connection is alive to the client.
        // Padding (4096 spaces) force-drains PHP-FPM `output_buffering=4096`
        // and the FastCGI packet buffer so the client sees bytes immediately —
        // without this, the first SSE chunk is held until the buffer naturally
        // fills, delaying TTFB to ~16s (see QA-NEW-12, 2026-04-24).
        echo str_repeat(' ', 4096) . "\n";
        echo ": connected\n\n";
        @ob_flush();
        flush();

        // ---- Build context (after SSE is open, errors can be streamed back) ----
        $promptSize = 0;
        try {
            $systemPrompt = AiAssistantService::buildSystemPrompt($studentId, $accountId, $pageContext, $lang);
            $promptSize   = mb_strlen($systemPrompt);
            // Track prompt size for monitoring — helps detect when prompt grows too large
            error_log(sprintf('[AI Assistant] prompt=%d chars, history=%d turns, page=%s', $promptSize, count($history), $pageContext));
        } catch (\Throwable $e) {
            error_log('[AI Assistant] buildSystemPrompt failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            echo 'data: ' . json_encode(['error' => 'Failed to load student context: ' . $e->getMessage()]) . "\n\n";
            echo 'data: ' . json_encode(['done' => true]) . "\n\n";
            flush();
            self::logRequestMetric([
                'status'        => 'error',
                'stage'         => 'build_system_prompt',
                'page_context'  => $pageContext,
                'lang'          => $lang,
                'prompt_size'   => 0,
                'history_turns' => count($history),
                'latency_ms'    => (int) round((microtime(true) - $requestStartedAt) * 1000),
                'error'         => $e->getMessage(),
            ]);
            exit();
        }

        // Build conversation: prior turns (last 20 max) + current message
        $messages = [];
        $history = array_slice($history, -20);
        foreach ($history as $turn) {
            $role    = ($turn['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
            $content = trim((string) ($turn['content'] ?? ''));
            if ($content !== '') {
                $messages[] = ['role' => $role, 'content' => $content];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        // ---- Stream from AI provider ----
        $streamError = null;
        try {
            ChatCompletionsStreamClient::stream(
                systemPrompt: $systemPrompt,
                messages: $messages,
                onDelta: function (string $delta): void {
                    echo 'data: ' . json_encode(['delta' => $delta], JSON_UNESCAPED_UNICODE) . "\n\n";
                    flush();
                },
                onError: function (string $error) use (&$streamError): void {
                    $streamError = $error;
                }
            );
        } catch (\Throwable $e) {
            $streamError = $e->getMessage();
            error_log('[AI Assistant] stream exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }

        if ($streamError !== null) {
            error_log('[AI Assistant] streamError surfaced to client: ' . $streamError);
            // Sanitize provider-side identifiers before surfacing the error
            // to the client — the original message still lands verbatim in
            // the server log above, but we don't leak provider org / request
            // IDs through the SSE stream.
            $clientError = self::sanitizeProviderError($streamError);
            echo 'data: ' . json_encode(['error' => $clientError]) . "\n\n";
            flush();
        }

        echo 'data: ' . json_encode(['done' => true]) . "\n\n";
        flush();

        // One structured log line per request — feeds the cost / latency /
        // error-rate dashboard on the ops side. Keeps the existing
        // human-readable prompt-size line above intact so current log
        // consumers aren't broken.
        self::logRequestMetric([
            'status'        => $streamError === null ? 'success' : 'error',
            'stage'         => $streamError === null ? 'stream' : 'stream_error',
            'page_context'  => $pageContext,
            'lang'          => $lang,
            'prompt_size'   => $promptSize,
            'history_turns' => count($messages) - 1, // subtract the user message just added
            'latency_ms'    => (int) round((microtime(true) - $requestStartedAt) * 1000),
            'error'         => $streamError !== null ? self::sanitizeProviderError($streamError) : null,
        ]);
        exit();
    }

    public function buildRequestSpec()
    {
        $this->request_spec['message'] = (new \StringType('message', true))
            ->setDpn('Student message');

        $this->request_spec['page_context'] = (new \StringType('page_context', false))
            ->setDpn('Current page context: assignments|library|my-learning|my-progress|search|saved|webinar|home|settings|faq. Unknown values fall back to general');

        $this->request_spec['history'] = (new \ArrayType('history', false))
            ->setDpn('Previous conversation turns [{role, content}, ...]');

        $this->request_spec['lang'] = (new \StringType('lang', false))
            ->setDpn('Language code for FAQ content');

        return $this;
    }

    public function buildResponseSpec(): \ResponseSpec
    {
        return new \ResponseContainer(new \StringType('stream', false));
    }

    /**
     * Emit one structured JSON-log line per chat request. Downstream log
     * shipping can grep / group by this tag to feed a cost + latency +
     * error-rate dashboard without any schema changes. Error messages
     * written here go through sanitizeProviderError so the log itself
     * doesn't carry provider-side identifiers.
     */
    private static function logRequestMetric(array $fields): void
    {
        $payload = array_merge(
            [
                'ts'    => date('c'),
                'event' => 'ai_assistant.chat.request',
            ],
            $fields
        );
        error_log('[AI Assistant metric] ' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Strip provider-side identifiers (Groq / OpenAI org_ and req_ prefixes,
     * billing URLs) from the error message before it reaches the client.
     * The frontend only needs the human-readable cause — internal IDs are
     * preserved in the server log above for diagnostics.
     */
    private static function sanitizeProviderError(string $message): string
    {
        // Drop org_XXXXXX / req_XXXXXX / key_XXXXXX identifiers.
        $clean = preg_replace('/\b(?:org|req|key)_[A-Za-z0-9]+\b/', '[redacted]', $message);
        // Drop upgrade-billing links the provider appends to rate-limit errors.
        $clean = preg_replace('#\shttps?://\S*billing\S*#i', '', $clean);
        // Collapse whitespace runs left behind by the substitutions.
        $clean = preg_replace('/\s+/', ' ', $clean);

        return trim($clean);
    }
}
