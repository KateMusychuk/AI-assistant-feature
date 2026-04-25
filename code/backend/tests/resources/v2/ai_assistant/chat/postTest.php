<?php

declare(strict_types=1);

namespace resources\v2\ai_assistant\chat;

use Helpers\Authenticable\StudentAuthenticable;
use Helpers\Containers\Response;
use Helpers\DataSeeder\Accounts;
use Helpers\DataSeeder\Students;
use TestCases\ResourceCases;
use UserStories\ResourceUserStory;

/**
 * Tests for POST /v2/ai_assistant/chat.
 *
 * The happy-path response is an SSE stream that proxies content from an
 * external provider (Groq). Live-calling the provider in unit tests would
 * be flaky and expensive, so this file only covers paths that do NOT
 * require the stream:
 *   - access control (no auth -> 401)
 *   - input validation (empty message -> 400 JSON, returned BEFORE the
 *     endpoint switches into SSE mode)
 *
 * Future work: happy-path coverage once `ChatCompletionsStreamClient` is
 * refactored to accept an injectable / mockable transport.
 *
 * @group ai_assistant
 */
class postTest extends ResourceCases
{
    /**
     * The endpoint is gated by the `student` access rule — an unauthenticated
     * request must never hit the SSE path.
     */
    public function testUnauthorizedAccess(): void
    {
        $this->checkResource(
            $this->newResourceUserStory()
                ->parameterPost('message', 'hello')
                ->expectResponse(null, 401)
        );
    }

    /**
     * An empty `message` trips the explicit `if ($message === '')` guard
     * before any SSE headers are flushed. The response must be a clean
     * JSON 400 with `{code: 400, status: 'Bad Request', response.message}`
     * — NOT an SSE stream with an error event inside.
     *
     * Indirect JSON-vs-SSE check: the response body is parsed as JSON by
     * the test harness. If the endpoint had incorrectly switched to SSE
     * before emitting the 400 (the exact bug this test guards against),
     * the body wouldn't parse into a structured array and the code / message
     * assertions below would fail.
     *
     * This also protects us against the opposite regression: if a future
     * refactor moves the empty-check AFTER the SSE switch, the student
     * would see a broken stream instead of a clean 400.
     */
    public function testEmptyMessageRejectedBeforeStreaming(): void
    {
        Accounts::instantiate()->populate();
        Students::instantiate()->populate();

        $this->checkResource(
            $this->newResourceUserStory()
                ->applyAuthorization(new StudentAuthenticable())
                ->parameterPost('message', '')
                ->responseValidator(function (ResourceUserStory $userStory, Response $container) {
                    $body = $container->getResponse();

                    // Must be parseable as JSON (SSE would yield null/empty).
                    $this->assertIsArray($body, 'Body must be valid JSON, not an SSE stream');
                    $this->assertSame(400, (int)($body['code'] ?? 0), 'JSON body must carry code=400');

                    // The error message is returned under response.message — makes the
                    // failure actionable for the client without opening DevTools.
                    $message = $container->getJsonContentParam('message');
                    $this->assertIsString($message);
                    $this->assertStringContainsString('message', strtolower((string) $message));
                })
                ->expectResponse(null, 400)
        );
    }
}
