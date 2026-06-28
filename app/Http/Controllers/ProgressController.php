<?php

namespace App\Http\Controllers;

use App\Models\BimbleClass;
use App\Models\ManualRankingEntry;
use App\Models\RegistrationProgress;
use App\Models\StudentGuardian;
use App\Models\StudentReport;
use App\Models\TestDefinition;
use App\Models\TestSubmission;
use App\Models\User;
use App\Services\WeeklyStudentReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProgressController extends Controller
{
    /**
     * Children linked to the authenticated parent.
     */
    public function children(Request $request)
    {
        $parent = $request->user();

        if (! Schema::hasTable('student_guardians')) {
            return response()->json(['items' => []]);
        }

        $links = StudentGuardian::query()
            ->where('guardian_user_id', $parent->id)
            ->where('invite_status', StudentGuardian::STATUS_ACCEPTED)
            ->with('student:id,name,username,email,program_category')
            ->get();

        $items = $links->filter(fn ($l) => $l->student)->map(function ($link) {
            $student = $link->student;

            return [
                'link_id' => $link->id,
                'relationship' => $link->relationshipLabel(),
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'username' => $student->username,
                    'program_category' => $student->program_category,
                ],
            ];
        })->values();

        return response()->json(['items' => $items]);
    }

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
            'physical' => $this->physicalBars($student),
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
     * Shared authorization: the student themself, a linked accepted parent, or staff.
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

        if ($user->role === 'parent' && Schema::hasTable('student_guardians')) {
            $linked = StudentGuardian::where('guardian_user_id', $user->id)
                ->where('student_user_id', $student->id)
                ->where('invite_status', StudentGuardian::STATUS_ACCEPTED)
                ->exists();
            if ($linked) {
                return;
            }
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
            'academic_timeline' => $this->academicTimeline($student),
            'academic_subjects' => $this->academicSubjects($student),
            'academic_subject_timeline' => $this->academicSubjectTimeline($student),
            'physical' => $this->physicalBars($student),
            'physical_timeline' => $this->physicalTimeline($student),
            'materials' => $this->materialsPerClass($student),
        ];
    }

    /**
     * Per-subject academic scores over time (percentage), one series per subject.
     *
     * @return list<array<string, mixed>>
     */
    private function academicSubjectTimeline(User $student): array
    {
        if (! Schema::hasTable('test_submissions')) {
            return [];
        }

        $akademik = collect(config('rankings.groups', []))->firstWhere('id', 'akademik');
        $subcategories = $akademik['subcategories'] ?? [];
        if ($subcategories === []) {
            return [];
        }

        $submissions = TestSubmission::query()
            ->where('user_id', $student->id)
            ->whereNotNull('score')
            ->with('testDefinition:id,category,question_ids')
            ->orderBy('submitted_at')
            ->orderBy('id')
            ->get();

        $series = [];
        foreach ($subcategories as $sub) {
            $categories = $sub['test_categories'] ?? [];
            $points = [];
            foreach ($submissions as $submission) {
                $cat = $submission->testDefinition?->category;
                if (! $cat || ! in_array($cat, $categories, true)) {
                    continue;
                }
                $total = count($submission->testDefinition?->question_ids ?? []);
                if ($total < 1) {
                    continue;
                }
                $points[] = [
                    'date' => optional($submission->submitted_at ?? $submission->created_at)->toDateString(),
                    'percent' => round(((float) $submission->score / $total) * 100, 1),
                ];
            }

            $series[] = [
                'id' => $sub['id'],
                'label' => $sub['label'],
                'points' => $this->sortTimelinePoints($points),
            ];
        }

        return $series;
    }

    /**
     * Per-subcategory jasmani scores over time, one series per component.
     *
     * @return list<array<string, mixed>>
     */
    private function physicalTimeline(User $student): array
    {
        if (! Schema::hasTable('manual_ranking_entries')) {
            return [];
        }

        $jasmani = collect(config('rankings.groups', []))->firstWhere('id', 'jasmani');
        $subcategories = $jasmani['subcategories'] ?? [];
        if ($subcategories === []) {
            return [];
        }

        $entries = ManualRankingEntry::query()
            ->where('group_id', 'jasmani')
            ->where('user_id', $student->id)
            ->orderBy('score_date')
            ->orderBy('id')
            ->get();

        $series = [];
        foreach ($subcategories as $sub) {
            $points = [];
            foreach ($entries as $entry) {
                if ($entry->subcategory_id !== $sub['id']) {
                    continue;
                }
                $points[] = [
                    'date' => optional($entry->score_date ?? $entry->created_at)->toDateString(),
                    'value' => (float) $entry->score,
                ];
            }

            $series[] = [
                'id' => $sub['id'],
                'label' => $sub['label'],
                'unit' => $sub['unit'] ?? null,
                'sort' => $sub['sort'] ?? 'desc',
                'points' => $this->sortTimelinePoints($points),
            ];
        }

        return $series;
    }

    /**
     * Test scores over time (percentage).
     *
     * @return list<array<string, mixed>>
     */
    private function academicTimeline(User $student): array
    {
        if (! Schema::hasTable('test_submissions')) {
            return [];
        }

        $submissions = TestSubmission::query()
            ->where('user_id', $student->id)
            ->whereNotNull('score')
            ->with('testDefinition:id,name,category,question_ids')
            ->orderBy('submitted_at')
            ->orderBy('id')
            ->limit(60)
            ->get();

        $rows = [];
        foreach ($submissions as $sub) {
            $total = count($sub->testDefinition?->question_ids ?? []);
            if ($total < 1) {
                continue;
            }
            $pct = round(((float) $sub->score / $total) * 100, 1);
            $rows[] = [
                'label' => $sub->testDefinition?->name ?? 'Tes',
                'category' => $sub->testDefinition?->category,
                'percent' => $pct,
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
     * Best percentage per academic subject (from rankings config).
     *
     * @return list<array<string, mixed>>
     */
    private function academicSubjects(User $student): array
    {
        if (! Schema::hasTable('test_submissions')) {
            return [];
        }

        $akademik = collect(config('rankings.groups', []))->firstWhere('id', 'akademik');
        $subcategories = $akademik['subcategories'] ?? [];
        if ($subcategories === []) {
            return [];
        }

        $submissions = TestSubmission::query()
            ->where('user_id', $student->id)
            ->whereNotNull('score')
            ->with('testDefinition:id,category,question_ids')
            ->get();

        $rows = [];
        foreach ($subcategories as $sub) {
            $categories = $sub['test_categories'] ?? [];
            $best = null;
            foreach ($submissions as $submission) {
                $cat = $submission->testDefinition?->category;
                if (! $cat || ! in_array($cat, $categories, true)) {
                    continue;
                }
                $total = count($submission->testDefinition?->question_ids ?? []);
                if ($total < 1) {
                    continue;
                }
                $pct = round(((float) $submission->score / $total) * 100, 1);
                $best = $best === null ? $pct : max($best, $pct);
            }

            $rows[] = [
                'id' => $sub['id'],
                'label' => $sub['label'],
                'percent' => $best,
            ];
        }

        return $rows;
    }

    /**
     * Physical (jasmani) results as labelled bars.
     *
     * @return list<array<string, mixed>>
     */
    private function physicalBars(User $student): array
    {
        if (! Schema::hasTable('registration_progress')) {
            return [];
        }

        $progress = RegistrationProgress::where('user_id', $student->id)->first();
        $data = $progress?->physical_data ?? [];

        $jasmani = collect(config('rankings.groups', []))->firstWhere('id', 'jasmani');
        $subcategories = $jasmani['subcategories'] ?? [];

        $rows = [];
        foreach ($subcategories as $sub) {
            $value = $this->extractNumericScore($data[$sub['id']] ?? null);
            $rows[] = [
                'id' => $sub['id'],
                'label' => $sub['label'],
                'unit' => $sub['unit'] ?? null,
                'value' => $value,
                'sort' => $sub['sort'] ?? 'desc',
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

    private function extractNumericScore(mixed $raw): ?float
    {
        if (is_numeric($raw)) {
            return (float) $raw;
        }

        if (is_array($raw)) {
            foreach (['value', 'score', 'result', 'nilai'] as $key) {
                if (isset($raw[$key]) && is_numeric($raw[$key])) {
                    return (float) $raw[$key];
                }
            }
        }

        return null;
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
