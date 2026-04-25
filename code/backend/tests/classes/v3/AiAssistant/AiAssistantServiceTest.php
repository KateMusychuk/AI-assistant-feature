<?php

declare(strict_types=1);

namespace classes\v3\AiAssistant;

use DataBase;
use Exception;
use Helpers\DataSeeder\Accounts;
use Helpers\DataSeeder\Courses;
use Helpers\DataSeeder\Students;
use Helpers\DataSeeder\Tables;
use TestCases\DataBaseCases;
use UserHelper;

/**
 * Unit-ish tests for the AiAssistantService data-fetching methods and
 * buildSystemPrompt composition. These hit the test database via seeders +
 * raw inserts; they do NOT call any external AI provider.
 *
 * @group ai_assistant
 */
final class AiAssistantServiceTest extends DataBaseCases
{
    /**
     * fetchFaqContent must return an empty string when the `mui` table has
     * no matching P2Faq / faq_json row for the requested language.
     * The AI uses this empty-string signal to skip the FAQ section in the
     * system prompt, rather than leak raw SQL-null into the prompt.
     */
    public function testFetchFaqContentReturnsEmptyWhenRowMissing(): void
    {
        $this->assertSame('', AiAssistantService::fetchFaqContent('en'));
    }

    /**
     * fetchFaqContent (QA-NEW-17, 2026-04-24) reads from the `P2Faq` MUI
     * group — a `faq_json` structure row references Q/A + category-label
     * keys stored as separate rows in the same group. Formats each pair
     * as readable lines. We assert on the shape, not on the full literal
     * string — formatting helpers may add whitespace we don't want to
     * couple to.
     */
    public function testFetchFaqContentParsesMuiJson(): void
    {
        $rows = [
            // Category labels
            ['category_portal',    'Portal'],
            ['category_personal',  'Personal'],
            // Q/A content referenced from faq_json
            ['portal_question_1',  'How does the portal work?'],
            ['portal_answer_1',    'You navigate courses via the library.'],
            ['personal_question_1','How do I change my password?'],
            ['personal_answer_1',  'Account settings → Privacy and security.'],
            // Structure
            ['faq_json',           json_encode([
                [
                    'category'  => 'category_portal',
                    'questions' => [
                        ['q' => 'portal_question_1', 'a' => 'portal_answer_1'],
                    ],
                ],
                [
                    'category'  => 'category_personal',
                    'questions' => [
                        ['q' => 'personal_question_1', 'a' => 'personal_answer_1'],
                    ],
                ],
            ])],
        ];

        foreach ($rows as [$key, $value]) {
            DataBase::insertRows(
                Tables::MUI->value,
                [
                    'id'        => UserHelper::CreateGUID(),
                    'group'     => 'P2Faq',
                    'key'       => $key,
                    'lang'      => 'en',
                    'type'      => 'text',
                    'value'     => $value,
                    'portal_id' => null,
                ]
            );
        }

        $faq = AiAssistantService::fetchFaqContent('en');

        $this->assertIsString($faq);
        $this->assertNotSame('', $faq, 'FAQ text must be non-empty when matching P2Faq rows exist');
        $this->assertStringContainsString('[Portal]',   $faq, 'Category header "Portal" must appear');
        $this->assertStringContainsString('[Personal]', $faq, 'Category header "Personal" must appear');
        $this->assertStringContainsString('How does the portal work?',  $faq, 'First question text must appear');
        $this->assertStringContainsString('Account settings',           $faq, 'Password-answer fragment must appear');
    }

    /**
     * buildSystemPrompt composes the full student-facing prompt from five
     * data fetches plus several static sections. Even when dynamic data is
     * empty, the static scaffolding must always be present — the AI relies
     * on these section headers to classify questions correctly.
     *
     * This test asserts only on the static scaffolding, which makes it
     * robust against schema changes in any single fetcher.
     */
    public function testBuildSystemPromptAlwaysContainsStaticScaffolding(): void
    {
        $accountId = Accounts::instantiate()->populate()->getFirstId();
        $student   = Students::instantiate()->populate()->getFirst();
        $studentId = $student['id'];

        $prompt = AiAssistantService::buildSystemPrompt(
            studentId:   $studentId,
            accountId:   $accountId,
            pageContext: 'general',
            lang:        'en'
        );

        $this->assertIsString($prompt);
        $this->assertNotSame('', $prompt);

        // Static blocks that the prompt MUST always carry — these are the
        // anchors the model uses to classify the student's question.
        $requiredMarkers = [
            'PLATFORM KNOWLEDGE',        // points rules + navigation
            'CARD FORMAT',               // mandatory CARD rule for assignments/courses
            'OVERDUE ASSIGNMENTS',       // section header appears even when list is empty
            'UPCOMING ASSIGNMENTS',      // same
            'SKILL GAPS',                // same
        ];

        foreach ($requiredMarkers as $marker) {
            $this->assertStringContainsString(
                $marker,
                $prompt,
                "System prompt must contain the '{$marker}' block"
            );
        }
    }

    /**
     * When the student has NO overdue/upcoming/skill-gap data, the prompt must
     * still carry the section headers in their empty-state form — specifically
     * "OVERDUE ASSIGNMENTS: None" and "UPCOMING ASSIGNMENTS: None in the next
     * 14 days". This is the honest signal the AI relies on to answer "you have
     * no overdue items right now" instead of fabricating.
     *
     * Complements testBuildSystemPromptIncludesSeededOverdueAssignment —
     * that one proves populated state flows in, this one proves empty state
     * is rendered correctly and not silently skipped.
     */
    public function testBuildSystemPromptRendersEmptySectionsExplicitly(): void
    {
        $accountId = Accounts::instantiate()->populate()->getFirstId();
        $student   = Students::instantiate()->populate()->getFirst();
        $studentId = $student['id'];

        $prompt = AiAssistantService::buildSystemPrompt(
            studentId:   $studentId,
            accountId:   $accountId,
            pageContext: 'general',
            lang:        'en'
        );

        $this->assertStringContainsString(
            'OVERDUE ASSIGNMENTS: None',
            $prompt,
            'Empty overdue list must render as explicit "OVERDUE ASSIGNMENTS: None"'
        );
        $this->assertStringContainsString(
            'UPCOMING ASSIGNMENTS: None in the next 14 days',
            $prompt,
            'Empty upcoming list must render the full "None in the next 14 days" literal'
        );
    }

    /**
     * Seeded overdue course-assignment must flow through into the prompt.
     * We assert on a GUID-suffixed title (robust against baseline seeder
     * items) AND on the overdue-block header carrying a non-zero count.
     */
    public function testBuildSystemPromptIncludesSeededOverdueAssignment(): void
    {
        $seededTitle = 'Overdue Prompt Test Course ' . UserHelper::CreateGUID();

        $accountId = Accounts::instantiate()->populate()->getFirstId();
        $student   = Students::instantiate()->populate()->getFirst();
        $studentId = $student['id'];

        $course    = Courses::instantiate()
            ->setPreliminaryData([['name' => $seededTitle]])
            ->populate();
        $courseId  = $course->getFirstId();

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

        $prompt = AiAssistantService::buildSystemPrompt(
            studentId:   $studentId,
            accountId:   $accountId,
            pageContext: 'assignments',
            lang:        'en'
        );

        $this->assertStringContainsString(
            $seededTitle,
            $prompt,
            'Seeded overdue assignment title must appear in the composed system prompt'
        );
        // Populated state must NOT render the empty-state text.
        $this->assertStringNotContainsString(
            'OVERDUE ASSIGNMENTS: None',
            $prompt,
            'With seeded data present, the empty-state text must not appear'
        );
        // Header must carry a non-zero count. Kept permissive (\d+ not literal
        // "1") so default seeders adding unrelated assignments do not flap
        // this test.
        $this->assertMatchesRegularExpression(
            '/OVERDUE ASSIGNMENTS \(\d+\):/',
            $prompt,
            'OVERDUE ASSIGNMENTS header must include a non-zero count'
        );
    }

    /**
     * buildSystemPrompt emits a page-specific instruction hint based on the
     * `pageContext` argument. Verify that two different contexts produce
     * different hints — protects against silent regression where pageContext
     * stops being honoured and the assistant defaults to 'general' everywhere.
     *
     * Asserts on the full hint sentences (taken verbatim from the match-
     * expression in AiAssistantService), not just the individual keyword —
     * "assignments" / "progress" could bleed in from other parts of the prompt
     * (OVERDUE ASSIGNMENTS header, skill-progress text, etc.) and would
     * produce false positives.
     */
    public function testBuildSystemPromptReactsToPageContext(): void
    {
        $accountId = Accounts::instantiate()->populate()->getFirstId();
        $student   = Students::instantiate()->populate()->getFirst();
        $studentId = $student['id'];

        $assignmentsPrompt = AiAssistantService::buildSystemPrompt(
            studentId:   $studentId,
            accountId:   $accountId,
            pageContext: 'assignments',
            lang:        'en'
        );
        $myProgressPrompt  = AiAssistantService::buildSystemPrompt(
            studentId:   $studentId,
            accountId:   $accountId,
            pageContext: 'my-progress',
            lang:        'en'
        );

        // Exact hint strings from the pageContext match-expression in
        // AiAssistantService::buildSystemPrompt. If either sentence is missing,
        // the context routing is broken — not just the keyword bleeding in
        // from elsewhere.
        $this->assertStringContainsString(
            'The student is on the Assignments page.',
            $assignmentsPrompt,
            'Assignments page context must emit its verbatim hint sentence'
        );
        $this->assertStringContainsString(
            'The student is viewing My Progress.',
            $myProgressPrompt,
            'My-progress page context must emit its verbatim hint sentence'
        );

        // Cross-check: each hint must NOT appear in the other context's prompt.
        // Catches the case where both contexts somehow emit the same hint.
        $this->assertStringNotContainsString(
            'The student is viewing My Progress.',
            $assignmentsPrompt
        );
        $this->assertStringNotContainsString(
            'The student is on the Assignments page.',
            $myProgressPrompt
        );

        // Belt-and-suspenders: the two composed prompts must differ overall.
        $this->assertNotSame($assignmentsPrompt, $myProgressPrompt);
    }

    /**
     * loadLocalizedTemplate is the shared MUI lookup behind the greeting /
     * honest-fallback / refusal helpers. It must return the raw DB value
     * when a matching `ai_assistant` row exists for the given key + lang,
     * and null otherwise so the calling wrapper can pick its own EN default.
     */
    public function testLoadLocalizedTemplateResolvesExistingKey(): void
    {
        $key    = 'ai_assistant_test_template_' . bin2hex(random_bytes(4));
        $value  = 'Localized fixture value';

        DataBase::insertRows(
            Tables::MUI->value,
            [
                'id'        => UserHelper::CreateGUID(),
                'group'     => 'ai_assistant',
                'key'       => $key,
                'lang'      => 'en',
                'type'      => 'text',
                'value'     => $value,
                'portal_id' => null,
            ]
        );

        // Private helper — reach via reflection so the production contract
        // stays private.
        $refl = new \ReflectionClass(AiAssistantService::class);
        $m    = $refl->getMethod('loadLocalizedTemplate');
        $m->setAccessible(true);

        $resolved = $m->invoke(null, $key, 'en');
        $this->assertSame($value, $resolved, 'Existing MUI row must be returned verbatim');
    }

    /**
     * Missing lang row must yield null — callers layer their own EN fallback
     * on top. Separately covers: empty-string values behave the same as
     * missing rows (so callers never emit a stray empty literal).
     */
    public function testLoadLocalizedTemplateReturnsNullForMissingRow(): void
    {
        $refl = new \ReflectionClass(AiAssistantService::class);
        $m    = $refl->getMethod('loadLocalizedTemplate');
        $m->setAccessible(true);

        $resolved = $m->invoke(
            null,
            'ai_assistant_definitely_not_a_real_key_' . bin2hex(random_bytes(6)),
            'en'
        );
        $this->assertNull($resolved, 'Missing row must yield null (not empty string)');
    }

    /**
     * Lang scoping: a row seeded under `lang='de'` must not satisfy a lookup
     * for `lang='en'`. Guards against the classic \"first row wins\" SQL bug
     * when WHERE filters are too loose.
     */
    public function testLoadLocalizedTemplateIsLangScoped(): void
    {
        $key = 'ai_assistant_test_template_scope_' . bin2hex(random_bytes(4));

        DataBase::insertRows(
            Tables::MUI->value,
            [
                'id'        => UserHelper::CreateGUID(),
                'group'     => 'ai_assistant',
                'key'       => $key,
                'lang'      => 'de',
                'type'      => 'text',
                'value'     => 'DE-only value',
                'portal_id' => null,
            ]
        );

        $refl = new \ReflectionClass(AiAssistantService::class);
        $m    = $refl->getMethod('loadLocalizedTemplate');
        $m->setAccessible(true);

        $this->assertSame('DE-only value', $m->invoke(null, $key, 'de'));
        $this->assertNull($m->invoke(null, $key, 'en'), 'EN lookup must not pick up DE row');
    }
}
