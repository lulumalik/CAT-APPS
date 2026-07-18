<?php

namespace App\Services;

use App\Models\ExamDefinition;
use App\Models\ExamSubmission;
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

    public function notify(StudentReport $report, string $title): void
    {
        if (! Schema::hasTable('user_notifications')) {
            return;
        }

        $recipients = [$report->student_user_id];

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

    private function resolveSubjectLabel(?string $category): string
    {
        if (! $category) {
            return 'Akademik';
        }

        return $category;
    }

    private function reportExistsForSubmission(int $studentId, int $submissionId): bool
    {
        return StudentReport::query()
            ->where('student_user_id', $studentId)
            ->where('type', StudentReport::TYPE_DAILY)
            ->get()
            ->contains(fn (StudentReport $report) => (int) ($report->metrics['submission_id'] ?? 0) === $submissionId);
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
