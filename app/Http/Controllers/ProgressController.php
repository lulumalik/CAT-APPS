<?php

namespace App\Http\Controllers;

use App\Models\BimbleClass;
use App\Models\ExamSubmission;
use App\Models\StudentReport;
use App\Models\TestSubmission;
use App\Models\User;
use App\Services\WeeklyStudentReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProgressController extends Controller
{
    public function studentProgress(Request $request, User $student)
    {
        $this->authorizeAccess($request, $student);

        return response()->json($this->buildProgress($student));
    }

    /**
     * @return array<string, mixed>
     */
    public function progressDataForStudent(User $student): array
    {
        return $this->buildProgress($student);
    }

    public function studentResults(Request $request, User $student)
    {
        $this->authorizeAccess($request, $student);

        return response()->json([
            'student' => ['id' => $student->id, 'name' => $student->name],
            'academic' => $this->academicSubjects($student),
        ]);
    }

    public function studentReports(Request $request, User $student)
    {
        $this->authorizeAccess($request, $student);

        $emptyDaily = [
            'data' => [],
            'current_page' => 1,
            'last_page' => 1,
            'total' => 0,
            'per_page' => 10,
            'date' => $request->input('date', now()->toDateString()),
        ];

        if (! Schema::hasTable('student_reports')) {
            return response()->json([
                'student' => ['id' => $student->id, 'name' => $student->name],
                'daily' => $emptyDaily,
                'weekly' => [],
            ]);
        }

        $date = $request->input('date', now()->toDateString());
        $perPage = min(50, max(5, (int) $request->input('per_page', 10)));

        $weeklyService = app(WeeklyStudentReportService::class);
        $weeklyService->syncMissingWeeks($student);
        $weeklyService->syncForDate($student, $date, null, false);

        $daily = StudentReport::query()
            ->where('student_user_id', $student->id)
            ->where('type', StudentReport::TYPE_DAILY)
            ->whereDate('report_date', $date)
            ->with(['creator:id,name', 'bimbleClass:id,name'])
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        $weekly = StudentReport::query()
            ->where('student_user_id', $student->id)
            ->where('type', StudentReport::TYPE_WEEKLY)
            ->with(['creator:id,name', 'bimbleClass:id,name'])
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(fn ($r) => $this->serializeReport($r));

        return response()->json([
            'student' => ['id' => $student->id, 'name' => $student->name],
            'daily' => [
                'data' => collect($daily->items())->map(fn ($r) => $this->serializeReport($r))->values(),
                'current_page' => $daily->currentPage(),
                'last_page' => $daily->lastPage(),
                'total' => $daily->total(),
                'per_page' => $daily->perPage(),
                'date' => $date,
            ],
            'weekly' => $weekly,
        ]);
    }

    /**
     * Shared authorization: the student themself or staff.
     */
    private function authorizeAccess(Request $request, User $student): void
    {
        $user = $request->user();

        if ($user->id === $student->id) {
            return;
        }

        if (in_array($user->role, ['admin', 'mentor'], true)) {
            return;
        }

        abort(403, 'Tidak punya akses ke data peserta ini.');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildProgress(User $student): array
    {
        return [
            'student' => ['id' => $student->id, 'name' => $student->name],
            'exam_timeline' => $this->examTimeline($student),
            'academic_subjects' => $this->academicSubjects($student),
            'academic_subject_timeline' => $this->academicSubjectTimeline($student),
            'quiz_subject_results' => $this->quizSubjectResults($student),
            'materials' => $this->materialsPerClass($student),
        ];
    }

    /**
     * Per-category academic scores over time (percentage), one series per category.
     *
     * @return list<array<string, mixed>>
     */
    private function academicSubjectTimeline(User $student): array
    {
        $query = $this->quizSubmissionsQuery($student);
        if ($query === null) {
            return [];
        }

        $submissions = (clone $query)
            ->orderBy('submitted_at')
            ->orderBy('id')
            ->get();

        $byCategory = [];
        foreach ($submissions as $submission) {
            $test = $submission->testDefinition;
            $cat = $test?->category;
            if (! $cat) {
                continue;
            }
            $total = count($test?->question_ids ?? []);
            if ($total < 1) {
                continue;
            }
            $scaled = $this->scaleScoreToHundred((float) $submission->score, $total);
            if ($scaled === null) {
                continue;
            }
            $byCategory[$cat][] = [
                'date' => optional($submission->submitted_at ?? $submission->created_at)->toDateString(),
                'value' => $scaled,
                'quiz_name' => $test->name,
                'label' => $test->name,
            ];
        }

        $series = [];
        foreach ($byCategory as $category => $points) {
            $series[] = [
                'id' => $category,
                'label' => $this->resolveAcademicSubjectLabel($category),
                'points' => $this->sortTimelinePoints($points),
            ];
        }

        return $series;
    }

    /**
     * Flat list of quiz results per subject for detail tables.
     *
     * @return list<array<string, mixed>>
     */
    private function quizSubjectResults(User $student): array
    {
        $query = $this->quizSubmissionsQuery($student);
        if ($query === null) {
            return [];
        }

        $results = [];
        foreach ((clone $query)->orderByDesc('submitted_at')->orderByDesc('id')->get() as $submission) {
            $test = $submission->testDefinition;
            if (! $test) {
                continue;
            }
            $total = count($test->question_ids ?? []);
            if ($total < 1) {
                continue;
            }
            $scaled = $this->scaleScoreToHundred((float) $submission->score, $total);
            if ($scaled === null) {
                continue;
            }

            $results[] = [
                'subject' => $test->category,
                'subject_label' => $this->resolveAcademicSubjectLabel($test->category),
                'quiz_name' => $test->name,
                'score' => $scaled,
                'date' => optional($submission->submitted_at ?? $submission->created_at)->toDateString(),
            ];
        }

        return $results;
    }

    /**
     * Exam scores over time (skala 0–100 dari total soal ujian).
     *
     * @return list<array<string, mixed>>
     */
    private function examTimeline(User $student): array
    {
        if (! Schema::hasTable('exam_submissions')) {
            return [];
        }

        $submissions = ExamSubmission::query()
            ->where('user_id', $student->id)
            ->whereNotNull('score')
            ->with('examDefinition:id,name,question_ids')
            ->orderBy('submitted_at')
            ->orderBy('id')
            ->limit(60)
            ->get();

        $rows = [];
        foreach ($submissions as $sub) {
            $total = count($sub->examDefinition?->question_ids ?? []);
            if ($total < 1) {
                continue;
            }
            $scaled = $this->scaleScoreToHundred((float) $sub->score, $total);
            if ($scaled === null) {
                continue;
            }
            $rows[] = [
                'label' => $sub->examDefinition?->name ?? 'Ujian',
                'value' => $scaled,
                'date' => optional($sub->submitted_at ?? $sub->created_at)->toDateString(),
            ];
        }

        return $this->sortTimelinePoints($rows);
    }

    /**
     * @param  list<array{date?: string|null}>  $points
     * @return list<array{date?: string|null}>
     */
    private function sortTimelinePoints(array $points): array
    {
        usort($points, function (array $a, array $b): int {
            return strcmp((string) ($a['date'] ?? ''), (string) ($b['date'] ?? ''));
        });

        return $points;
    }

    /**
     * Best percentage per academic category (from testDefinition.category).
     *
     * @return list<array<string, mixed>>
     */
    private function academicSubjects(User $student): array
    {
        $query = $this->quizSubmissionsQuery($student);
        if ($query === null) {
            return [];
        }

        $bestByCategory = [];
        foreach ($query->get() as $submission) {
            $cat = $submission->testDefinition?->category;
            if (! $cat) {
                continue;
            }
            $total = count($submission->testDefinition?->question_ids ?? []);
            if ($total < 1) {
                continue;
            }
            $scaled = $this->scaleScoreToHundred((float) $submission->score, $total);
            if ($scaled === null) {
                continue;
            }
            $bestByCategory[$cat] = isset($bestByCategory[$cat])
                ? max($bestByCategory[$cat], $scaled)
                : $scaled;
        }

        $rows = [];
        foreach ($bestByCategory as $category => $best) {
            $rows[] = [
                'id' => $category,
                'label' => $this->resolveAcademicSubjectLabel($category),
                'value' => $best,
            ];
        }

        return $rows;
    }

    /**
     * Material & activity progress per class the student belongs to.
     *
     * @return list<array<string, mixed>>
     */
    private function materialsPerClass(User $student): array
    {
        if (! Schema::hasTable('bimble_classes') || ! Schema::hasTable('bimble_class_user')) {
            return [];
        }

        $classes = $student->bimbleClasses()
            ->orderBy('bimble_classes.name')
            ->get(['bimble_classes.id', 'name', 'class_code', 'program_type']);

        $hasMaterials = Schema::hasTable('bimble_class_material');
        $hasActivities = Schema::hasTable('class_activities');

        return $classes->map(function (BimbleClass $class) use ($hasMaterials, $hasActivities) {
            $materialsCount = $hasMaterials ? $class->materials()->count() : 0;
            $sessions = $hasMaterials
                ? (int) $class->materials()->max('bimble_class_material.session_number')
                : 0;
            $activitiesCount = $hasActivities ? $class->activities()->count() : 0;

            return [
                'id' => $class->id,
                'name' => $class->name,
                'class_code' => $class->class_code,
                'materials_count' => $materialsCount,
                'sessions_count' => $sessions,
                'activities_count' => $activitiesCount,
            ];
        })->values()->all();
    }

    private function scaleScoreToHundred(float $score, int $totalQuestions): ?int
    {
        if ($totalQuestions < 1) {
            return null;
        }

        return (int) round(($score / $totalQuestions) * 100);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\TestSubmission>|null
     */
    private function quizSubmissionsQuery(User $student)
    {
        if (! Schema::hasTable('test_submissions')) {
            return null;
        }

        return TestSubmission::query()
            ->where('user_id', $student->id)
            ->whereNotNull('score')
            ->whereHas('testDefinition', function ($q) {
                $q->where('is_free_tryout', false);
                if (Schema::hasTable('bimble_class_test')) {
                    $q->whereHas('bimbleClasses', function ($cq) {
                        $cq->where('bimble_class_test.kind', 'quiz');
                    });
                }
            })
            ->with('testDefinition:id,name,category,question_ids');
    }

    private function resolveAcademicSubjectLabel(?string $category): string
    {
        if (! $category) {
            return 'Akademik';
        }

        return $category;
    }

    private function serializeReport(StudentReport $report): array
    {
        return [
            'id' => $report->id,
            'type' => $report->type,
            'title' => $report->title,
            'summary' => $report->summary,
            'categories' => $report->categories ?? [],
            'metrics' => $report->metrics ?? [],
            'report_date' => $report->report_date?->toDateString(),
            'period_start' => $report->period_start?->toDateString(),
            'period_end' => $report->period_end?->toDateString(),
            'created_at' => $report->created_at?->toIso8601String(),
            'class' => $report->bimbleClass ? ['id' => $report->bimbleClass->id, 'name' => $report->bimbleClass->name] : null,
            'created_by' => $report->creator?->name,
        ];
    }
}
