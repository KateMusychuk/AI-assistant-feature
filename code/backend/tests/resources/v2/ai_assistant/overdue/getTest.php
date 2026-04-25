<?php

declare(strict_types=1);

namespace resources\v2\ai_assistant\overdue;

use DataBase;
use Helpers\Authenticable\StudentAuthenticable;
use Helpers\Containers\Response;
use Helpers\DataSeeder\Accounts;
use Helpers\DataSeeder\Courses;
use Helpers\DataSeeder\Students;
use Helpers\DataSeeder\Tables;
use TestCases\ResourceCases;
use UserHelper;
use UserStories\ResourceUserStory;

/**
 * Tests for GET /v2/ai_assistant/overdue.
 *
 * Covers:
 *   - baseline (fresh student, no assignments) -> count=0, items=[]
 *   - main behaviour (one seeded overdue assignment) -> count>=1 and our
 *     item is present with the expected shape and semantics
 *   - access control (no auth -> 401)
 *
 * @group ai_assistant
 */
class getTest extends ResourceCases
{
    /**
     * A freshly seeded student (no assignments yet) gets count=0 and items=[].
     *
     * Depends on the current baseline: if future Students/Accounts seeders
     * ever start auto-creating assignments, this test would need to seed an
     * explicit "no overdue" context instead of relying on defaults.
     */
    public function testReturnsZeroCountForFreshStudent(): void
    {
        Accounts::instantiate()->populate();
        Students::instantiate()->populate();

        $this->checkResource(
            $this->newResourceUserStory()
                ->applyAuthorization(new StudentAuthenticable())
                ->responseValidator(function (ResourceUserStory $userStory, Response $container) {
                    $response = $container->getResponse();
                    $body     = $response['response'] ?? [];

                    $this->assertArrayHasKey('count', $body);
                    $this->assertArrayHasKey('items', $body);
                    $this->assertSame(0, $body['count']);
                    $this->assertSame([], $body['items']);
                    $this->assertSame($body['count'], count($body['items']), 'count must match items length');
                })
                ->expectResponse(null, 200)
        );
    }

    /**
     * Main behaviour: when the student has an overdue course assignment,
     * the endpoint returns count>=1, items contains OUR seeded assignment,
     * pass_due_date is actually in the past, and the item carries the
     * expected shape (id, title, type, is_mandatory, pass_due_date, progress).
     *
     * The test finds its own item by a GUID-suffixed title so it remains
     * stable even if baseline seeders ever add unrelated assignments.
     */
    public function testReturnsOverdueItems(): void
    {
        $seededTitle = 'Overdue Test Course ' . UserHelper::CreateGUID();

        $accountId = Accounts::instantiate()->populate()->getFirstId();

        $student   = Students::instantiate()->populate()->getFirst();
        $studentId = $student['id'];

        $course    = Courses::instantiate()
            ->setPreliminaryData([['name' => $seededTitle]])
            ->populate();
        $courseId  = $course->getFirstId();

        // One overdue course assignment — due date 10 days in the past.
        DataBase::insertRows(
            Tables::COURSE_ASSIGNMENTS->value,
            [
                'id'            => UserHelper::CreateGUID(),
                'account_id'    => $accountId,
                'student_id'    => $studentId,
                'course_id'     => $courseId,
                'pass_due_date' => date('Y-m-d H:i:s', strtotime('-10 days')),
            ]
        );

        $this->checkResource(
            $this->newResourceUserStory()
                ->applyAuthorization(new StudentAuthenticable())
                ->responseValidator(function (ResourceUserStory $userStory, Response $container) use ($seededTitle) {
                    $response = $container->getResponse();
                    $body     = $response['response'] ?? [];

                    $this->assertArrayHasKey('count', $body);
                    $this->assertArrayHasKey('items', $body);

                    // Count / items length must stay consistent — catches
                    // off-by-one bugs in resource normalisation.
                    $this->assertSame($body['count'], count($body['items']), 'count must match items length');
                    $this->assertGreaterThanOrEqual(1, $body['count']);

                    // Find the assignment we seeded, by title — robust against
                    // other assignments that default seeders might add.
                    $ours = null;
                    foreach ($body['items'] as $item) {
                        if (($item['title'] ?? null) === $seededTitle) {
                            $ours = $item;
                            break;
                        }
                    }
                    $this->assertNotNull($ours, "Seeded assignment '{$seededTitle}' not found in response");

                    // Shape
                    foreach (['id', 'title', 'type', 'is_mandatory', 'pass_due_date', 'progress'] as $key) {
                        $this->assertArrayHasKey($key, $ours, "Seeded item must have '{$key}'");
                    }
                    $this->assertIsString($ours['id']);
                    $this->assertIsString($ours['title']);
                    $this->assertIsBool($ours['is_mandatory']);
                    $this->assertIsInt($ours['progress']);

                    // Semantics — not just shape
                    $this->assertSame('course', $ours['type'], 'We seeded a course assignment, type must be "course"');
                    $this->assertNotNull($ours['pass_due_date'], 'pass_due_date is required for an overdue item');
                    $this->assertLessThan(
                        time(),
                        strtotime($ours['pass_due_date']),
                        'pass_due_date must be in the past for an overdue item'
                    );
                })
                ->expectResponse(null, 200)
        );
    }

    /**
     * The endpoint is gated by the `student` access rule — an unauthenticated
     * request must never leak overdue data.
     */
    public function testUnauthorizedAccess(): void
    {
        $this->checkResource(
            $this->newResourceUserStory()
                ->expectResponse(null, 401)
        );
    }
}
