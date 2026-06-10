<?php

namespace App\Http\Controllers;

use App\Models\BimbleClass;
use App\Models\RegistrationProgress;
use App\Models\StudentGuardian;
use App\Models\StudentReport;
use App\Models\TestDefinition;
use App\Models\TestSubmission;
use App\Models\User;
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

        if (! Schema::hasTable('student_reports')) {
            return response()->json(['daily' => [], 'weekly' => []]);
        }

        $reports = StudentReport::query()
            ->where('student_user_id', $student->id)
            ->with(['creator:id,name', 'bimbleClass:id,name'])
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->limit(120)
            ->get();

        return response()->json([
            'student' => ['id' => $student->id, 'name' => $student->name],
            'daily' => $reports->where('type', StudentReport::TYPE_DAILY)->values()->map(fn ($r) => $this->serializeReport($r)),
            'weekly' => $reports->where('type', StudentReport::TYPE_WEEKLY)->values()->map(fn ($r) => $this->serializeReport($r)),
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
            'physical' => $this->physicalBars($student),
            'materials' => $this->materialsPerClass($student),
        ];
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

        return $rows;
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
            'class' => $report->bimbleClass ? ['id' => $report->bimbleClass->id, 'name' => $report->bimbleClass->name] : null,
            'created_by' => $report->creator?->name,
        ];
    }
}
