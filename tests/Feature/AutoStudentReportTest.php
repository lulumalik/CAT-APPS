<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\StudentGuardian;
use App\Models\StudentReport;
use App\Models\TestDefinition;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoStudentReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_weekly_jasmani_narrative_summarizes_range_and_trend(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $monday = now()->startOfWeek();

        $reports = [
            [$monday->copy()->addDay(), 20, 101],
            [$monday->copy()->addDays(3), 20, 102],
            [$monday->copy()->addDays(5), 10, 103],
        ];

        foreach ($reports as [$date, $score, $entryId]) {
            StudentReport::create([
                'student_user_id' => $student->id,
                'type' => StudentReport::TYPE_DAILY,
                'report_date' => $date->toDateString(),
                'title' => 'Update jasmani: Sprint',
                'categories' => [
                    'jasmani' => sprintf('Sprint — %d detik', $score),
                ],
                'metrics' => [
                    'auto_source' => 'jasmani_manual',
                    'manual_entry_id' => $entryId,
                    'subcategory_id' => 'sprint',
                    'subcategory_label' => 'Sprint',
                    'score' => $score,
                    'unit' => 'detik',
                ],
            ]);
        }

        $weekly = app(\App\Services\WeeklyStudentReportService::class)->syncForDate($student, $monday);

        $this->assertNotNull($weekly);
        $this->assertArrayHasKey('jasmani', $weekly->categories);
        $this->assertStringContainsString('Sprint: waktu terbaik 10 detik', $weekly->categories['jasmani']);
        $this->assertStringContainsString('waktu terlama 20 detik', $weekly->categories['jasmani']);
        $this->assertStringContainsString('progress positif', $weekly->categories['jasmani']);
        $this->assertStringNotContainsString('•', $weekly->categories['jasmani']);
    }

    public function test_weekly_summary_is_created_when_daily_report_exists(): void
    {
        $student = User::factory()->create(['role' => 'user']);

        StudentReport::create([
            'student_user_id' => $student->id,
            'type' => StudentReport::TYPE_DAILY,
            'report_date' => now()->toDateString(),
            'title' => 'Laporan harian uji',
            'summary' => 'Contoh',
        ]);

        $weekly = app(\App\Services\WeeklyStudentReportService::class)->syncForDate($student, now());

        $this->assertNotNull($weekly);
        $this->assertDatabaseHas('student_reports', [
            'student_user_id' => $student->id,
            'type' => StudentReport::TYPE_WEEKLY,
        ]);
    }

    public function test_test_submission_creates_auto_daily_report_and_notifies_parent(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $parent = User::factory()->create(['role' => 'parent']);

        StudentGuardian::create([
            'student_user_id' => $student->id,
            'guardian_user_id' => $parent->id,
            'guardian_name' => 'Bapak Test',
            'relationship' => 'ayah',
            'invite_status' => StudentGuardian::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        $question = Question::create([
            'question' => '2 + 2 = ?',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A' => '3', 'B' => '4', 'C' => '5', 'D' => '6'],
            'correct' => 'B',
            'created_by' => $student->id,
        ]);

        $start = now()->subHour();
        $end = now()->addHour();

        $test = TestDefinition::create([
            'name' => 'Tryout Matematika',
            'description' => 'Tes otomatis',
            'category' => 'Math',
            'duration' => 30,
            'schedule_at' => $start,
            'start_time' => $start,
            'end_time' => $end,
            'is_active' => true,
            'question_ids' => [$question->id],
            'created_by' => $student->id,
        ]);

        $response = $this->actingAs($student)->postJson("/api/tests/{$test->id}/submit", [
            'answers' => [(string) $question->id => 'B'],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('student_reports', [
            'student_user_id' => $student->id,
            'type' => StudentReport::TYPE_DAILY,
            'title' => 'Hasil tes: Tryout Matematika',
        ]);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $parent->id,
            'type' => 'student_report',
            'title' => 'Update nilai tes',
        ]);

        $this->assertDatabaseHas('student_reports', [
            'student_user_id' => $student->id,
            'type' => StudentReport::TYPE_WEEKLY,
        ]);
    }
}
