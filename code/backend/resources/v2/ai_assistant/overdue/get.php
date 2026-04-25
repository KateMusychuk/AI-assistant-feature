<?php

namespace resources\v2\ai_assistant\overdue;

use classes\v3\AiAssistant\AiAssistantService;

/**
 * GET /v2/ai_assistant/overdue
 *
 * Returns the student's overdue assignments.
 * Used by the FAB badge (count) and the overdue banner inside the panel.
 *
 * Response:
 * {
 *   "code": 200,
 *   "status": "OK",
 *   "response": {
 *     "count": 3,
 *     "items": [
 *       {
 *         "id": "...",
 *         "title": "...",
 *         "type": "course|learning_path|skill_path",
 *         "is_mandatory": true,
 *         "pass_due_date": "2025-01-15 00:00:00",
 *         "progress": 40
 *       }
 *     ]
 *   }
 * }
 */
class get extends \ResourceV3
{
    protected function defineAccessRules(): array
    {
        return ['student'];
    }

    /**
     * @throws \Exception
     */
    public function execute(): void
    {
        $student   = \students::api()->getCurrentStudent();
        $studentId = $student['id'];
        $accountId = $student['account_id'];
        // Language is optional: the portal's FAB badge uses `.length` only, so
        // omitting the param keeps the legacy default-lang behaviour. Passing
        // it surfaces localized titles to any future consumer without
        // changing the response shape.
        $lang      = trim((string) ($this->getParameter('lang') ?? ''));

        $items = AiAssistantService::fetchOverdue($studentId, $accountId, $lang);

        // Normalise to only the fields the frontend needs
        $normalized = array_map(static function (array $item): array {
            return [
                'id'            => $item['id'] ?? '',
                'title'         => $item['title'] ?? '',
                'type'          => $item['type'] ?? 'course',
                'is_mandatory'  => !empty($item['is_mandatory']),
                'pass_due_date' => $item['pass_due_date'] ?? null,
                'progress'      => (int)($item['progress'] ?? 0),
            ];
        }, $items);

        $this->setResponse([
            'count' => count($normalized),
            'items' => $normalized,
        ]);
    }

    public function buildRequestSpec()
    {
        $this->request_spec['lang'] = (new \StringType('lang', false))
            ->setDpn('Language code for localized item titles (optional)');

        return $this;
    }

    public function buildResponseSpec(): \ResponseSpec
    {
        // Response is a JSON object {count, items[]} — JsonType accepts any array/string
        return new \ResponseContainer(new \JsonType('overdue', false, true));
    }
}
