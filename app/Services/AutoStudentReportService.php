<?php

namespace App\Services;

use App\Models\ExamDefinition;
use App\Models\ExamSubmission;
use App\Models\RegistrationProgress;
use App\Models\StudentGuardian;
use App\Models\StudentReport;
use App\Models\TestDefinition;
use App\Models\TestSubmission;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Schema;

class AutoStudentReportService
{
    public function fromTestSubmission(
        User $student,
        TestDefinition $test,
        TestSubmission $submission,
        ?int $createdBy = null,
    ): ?StudentReport {
        if (! Schema::hasTable('student_reports') || $this->reportExistsForSubmission($student->id, $submission->id)) {
            return null;
        }

        $questionIds = $test->question_ids ?? [];
        $total = is_array($questionIds) ? count($questionIds) : 0;
        if ($total < 1) {
            return null;
        }

        $scaled = (int) round(((float) $submission->score / $total) * 100);
        $subjectLabel = $this->resolveSubjectLabel($test->category);
        $submittedAt = $submission->submitted_at ?? $submission->created_at ?? now();

        $report = StudentReport::create([
            'student_user_id' => $student->id,
            'bimble_class_id' => null,
            'created_by' => $createdBy,
            'type' => StudentReport::TYPE_DAILY,
            'report_date' => $submittedAt->toDateString(),
            'title' => sprintf('Hasil tes: %s', $test->name),
            'summary' => sprintf(
                'Ananda menyelesaikan tes %s (%s) dengan nilai %d.',
                $test->name,
                $subjectLabel,
                $scaled,
            ),
            'categories' => [
                'akademik' => sprintf('%s — %s: %d', $subjectLabel, $test->name, $scaled),
            ],
            'metrics' => [
                'auto_source' => 'test_submission',
                'submission_id' => $submission->id,
                'test_id' => $test->id,
                'test_name' => $test->name,
                'category' => $test->category,
                'subject_label' => $subjectLabel,
                'scaled_score' => $scaled,
                'score' => (int) $submission->score,
                'total' => $total,
            ],
        ]);

        $this->notify($report, 'Update nilai tes');

        app(WeeklyStudentReportService::class)->syncForDate(
            $student,
            $report->report_date,
            $createdBy,
            true,
        );

        return $report;
    }

    public function fromExamSubmission(
        User $student,
        ExamDefinition $exam,
        ExamSubmission $submission,
        ?int $createdBy = null,
    ): ?StudentReport {
        if (! Schema::hasTable('student_reports') || $this->reportExistsForExamSubmission($student->id, $submission->id)) {
            return null;
        }

        $questionIds = $exam->question_ids ?? [];
        $total = is_array($questionIds) ? count($questionIds) : 0;
        if ($total < 1) {
            return null;
        }

        $scaled = (int) round(((float) $submission->score / $total) * 100);
        $subjectLabel = $this->resolveSubjectLabel($exam->category);
        $submittedAt = $submission->submitted_at ?? $submission->created_at ?? now();

        $report = StudentReport::create([
            'student_user_id' => $student->id,
            'bimble_class_id' => null,
            'created_by' => $createdBy,
            'type' => StudentReport::TYPE_DAILY,
            'report_date' => $submittedAt->toDateString(),
            'title' => sprintf('Hasil ujian: %s', $exam->name),
            'summary' => sprintf(
                'Ananda menyelesaikan ujian %s (%s) dengan nilai %d.',
                $exam->name,
                $subjectLabel,
                $scaled,
            ),
            'categories' => [
                'akademik' => sprintf('%s — %s: %d', $subjectLabel, $exam->name, $scaled),
            ],
            'metrics' => [
                'auto_source' => 'exam_submission',
                'submission_id' => $submission->id,
                'exam_id' => $exam->id,
                'exam_name' => $exam->name,
                'category' => $exam->category,
                'subject_label' => $subjectLabel,
                'scaled_score' => $scaled,
                'score' => (int) $submission->score,
                'total' => $total,
            ],
        ]);

        $this->notify($report, 'Update nilai ujian');

        app(WeeklyStudentReportService::class)->syncForDate(
            $student,
            $report->report_date,
            $createdBy,
            true,
        );

        return $report;
    }

    public function fromJasmaniScore(
        User $student,
        array $subcategory,
        float $score,
        ?int $createdBy = null,
        ?string $notes = null,
        ?int $manualEntryId = null,
        ?int $bimbleClassId = null,
        ?string $scoreDate = null,
    ): ?StudentReport {
        if (! Schema::hasTable('student_reports')) {
            return null;
        }

        $subId = (string) ($subcategory['id'] ?? '');
        $label = (string) ($subcategory['label'] ?? $subId);
        $unit = $subcategory['unit'] ?? null;

        if ($subId === '') {
            return null;
        }

        $this->syncPhysicalData($student, $subId, $score);

        if ($manualEntryId !== null && $this->reportExistsForManualEntry($student->id, $manualEntryId)) {
            return null;
        }

        $display = $this->formatScore($score, $unit);
        $summaryParts = [sprintf('%s: %s.', $label, $display)];
        if ($notes !== null && trim($notes) !== '') {
            $summaryParts[] = 'Catatan: '.trim($notes);
        }

        $report = StudentReport::create([
            'student_user_id' => $student->id,
            'bimble_class_id' => $bimbleClassId,
            'created_by' => $createdBy,
            'type' => StudentReport::TYPE_DAILY,
            'report_date' => $scoreDate ?: now()->toDateString(),
            'title' => sprintf('Update jasmani: %s', $label),
            'summary' => implode(' ', $summaryParts),
            'categories' => [
                'jasmani' => sprintf('%s — %s', $label, $display),
            ],
            'metrics' => [
                'auto_source' => 'jasmani_manual',
                'manual_entry_id' => $manualEntryId,
                'subcategory_id' => $subId,
                'subcategory_label' => $label,
                'score' => $score,
                'unit' => $unit,
            ],
        ]);

        $this->notify($report, 'Update nilai jasmani');

        app(WeeklyStudentReportService::class)->syncForDate(
            $student,
            $report->report_date,
            $createdBy,
            true,
        );

        return $report;
    }

    public function fromManualAcademicScore(
        User $student,
        array $subcategory,
        string $assessmentName,
        float $score,
        ?int $createdBy = null,
        ?string $notes = null,
        ?int $manualEntryId = null,
        ?int $bimbleClassId = null,
        ?string $scoreDate = null,
    ): ?StudentReport {
        if (! Schema::hasTable('student_reports')) {
            return null;
        }

        $label = (string) ($subcategory['label'] ?? 'Akademik');
        $scaled = (int) round($score);

        if ($manualEntryId !== null && $this->reportExistsForManualEntry($student->id, $manualEntryId)) {
            return null;
        }

        $summaryParts = [sprintf('Ananda mengerjakan %s (%s) dengan nilai %d.', $assessmentName, $label, $scaled)];
        if ($notes !== null && trim($notes) !== '') {
            $summaryParts[] = 'Catatan: '.trim($notes);
        }

        $report = StudentReport::create([
            'student_user_id' => $student->id,
            'bimble_class_id' => $bimbleClassId,
            'created_by' => $createdBy,
            'type' => StudentReport::TYPE_DAILY,
            'report_date' => $scoreDate ?: now()->toDateString(),
            'title' => sprintf('Hasil quiz/kelas: %s', $assessmentName),
            'summary' => implode(' ', $summaryParts),
            'categories' => [
                'akademik' => sprintf('%s — %s: %d', $label, $assessmentName, $scaled),
            ],
            'metrics' => [
                'auto_source' => 'akademik_manual',
                'manual_entry_id' => $manualEntryId,
                'subcategory_id' => $subcategory['id'] ?? null,
                'subcategory_label' => $label,
                'assessment_name' => $assessmentName,
                'scaled_score' => $scaled,
                'score' => $scaled,
                'notes' => $notes,
            ],
        ]);

        $this->notify($report, 'Update nilai quiz/kelas');

        app(WeeklyStudentReportService::class)->syncForDate(
            $student,
            $report->report_date,
            $createdBy,
            true,
        );

        return $report;
    }

    public function notify(StudentReport $report, string $title): void
    {
        if (! Schema::hasTable('user_notifications')) {
            return;
        }

        $recipients = [$report->student_user_id];

        if (Schema::hasTable('student_guardians')) {
            $guardianIds = StudentGuardian::query()
                ->where('student_user_id', $report->student_user_id)
                ->where('invite_status', StudentGuardian::STATUS_ACCEPTED)
                ->whereNotNull('guardian_user_id')
                ->pluck('guardian_user_id')
                ->all();
            $recipients = array_merge($recipients, $guardianIds);
        }

        foreach (array_unique($recipients) as $userId) {
            UserNotification::create([
                'user_id' => $userId,
                'type' => 'student_report',
                'title' => $title,
                'message' => $report->title,
                'payload' => [
                    'report_id' => $report->id,
                    'student_user_id' => $report->student_user_id,
                    'type' => $report->type,
                    'auto' => true,
                ],
            ]);
        }
    }

    private function syncPhysicalData(User $student, string $subId, float $score): void
    {
        if (! Schema::hasTable('registration_progress')) {
            return;
        }

        $progress = RegistrationProgress::firstOrCreate(
            ['user_id' => $student->id],
            [
                'current_step' => 'administration',
                'administration_status' => 'not_started',
                'psychology_status' => 'not_started',
                'health_status' => 'not_started',
                'physical_status' => 'not_started',
                'fully_completed' => false,
            ]
        );

        $data = $progress->physical_data ?? [];
        $data[$subId] = $score;
        $progress->physical_data = $data;
        $progress->save();
    }

    private function resolveSubjectLabel(?string $category): string
    {
        if (! $category) {
            return 'Akademik';
        }

        foreach (config('rankings.groups', []) as $group) {
            if (($group['id'] ?? '') !== 'akademik') {
                continue;
            }

            foreach ($group['subcategories'] ?? [] as $sub) {
                if (in_array($category, $sub['test_categories'] ?? [], true)) {
                    return (string) $sub['label'];
                }
            }
        }

        return $category;
    }

    private function formatScore(float $score, ?string $unit): string
    {
        $formatted = rtrim(rtrim(number_format($score, 2, '.', ''), '0'), '.');

        return $unit ? "{$formatted} {$unit}" : $formatted;
    }

    private function reportExistsForSubmission(int $studentId, int $submissionId): bool
    {
        return StudentReport::query()
            ->where('student_user_id', $studentId)
            ->where('type', StudentReport::TYPE_DAILY)
            ->get()
            ->contains(fn (StudentReport $report) => (int) ($report->metrics['submission_id'] ?? 0) === $submissionId);
    }

    private function reportExistsForManualEntry(int $studentId, int $manualEntryId): bool
    {
        return StudentReport::query()
            ->where('student_user_id', $studentId)
            ->where('type', StudentReport::TYPE_DAILY)
            ->get()
            ->contains(fn (StudentReport $report) => (int) ($report->metrics['manual_entry_id'] ?? 0) === $manualEntryId);
    }

    private function reportExistsForExamSubmission(int $studentId, int $submissionId): bool
    {
        return StudentReport::query()
            ->where('student_user_id', $studentId)
            ->where('type', StudentReport::TYPE_DAILY)
            ->get()
            ->contains(function (StudentReport $report) use ($submissionId) {
                if (($report->metrics['auto_source'] ?? '') !== 'exam_submission') {
                    return false;
                }

                return (int) ($report->metrics['submission_id'] ?? 0) === $submissionId;
            });
    }
}
