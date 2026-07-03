<?php

namespace App\Http\Controllers;

use App\Models\BimbleClass;
use App\Models\ClassActivity;
use App\Models\ExamSubmission;
use App\Models\Question;
use App\Models\RegistrationProgress;
use App\Models\TestDefinition;
use App\Models\TestSubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function overview(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return response()->json($this->adminOverview());
        }

        if ($user->role === 'mentor') {
            return response()->json($this->mentorOverview($user->id));
        }

        if ($user->role === 'parent') {
            return response()->json($this->parentOverview($user->id));
        }

        return response()->json($this->studentOverview($user->id));
    }

    public function studentOverviewForStaff(Request $request, User $student)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        if ($student->role !== 'user') {
            return response()->json(['message' => 'Akun ini bukan peserta.'], 422);
        }

        return response()->json(array_merge(
            $this->studentOverview($student->id),
            [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'username' => $student->username,
                    'email' => $student->email,
                    'program_category' => $student->program_category,
                ],
            ],
        ));
    }

    public function myActivityHistory(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'user') {
            abort(403);
        }

        $overview = $this->studentOverview($user->id);

        return response()->json([
            'classes' => $overview['classes'] ?? [],
            'class_activities' => $overview['class_activities'] ?? [],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function studentOverviewData(int $userId): array
    {
        return $this->studentOverview($userId);
    }

    private function parentOverview(int $parentId): array
    {
        if (! Schema::hasTable('student_guardians')) {
            return ['role' => 'parent', 'children' => [], 'recent_reports' => []];
        }

        $links = \App\Models\StudentGuardian::query()
            ->where('guardian_user_id', $parentId)
            ->where('invite_status', \App\Models\StudentGuardian::STATUS_ACCEPTED)
            ->with('student:id,name,username,program_category')
            ->get()
            ->filter(fn ($l) => $l->student);

        $studentIds = $links->pluck('student_user_id')->all();

        $reportsByStudent = collect();
        $recentReports = collect();
        if (Schema::hasTable('student_reports') && ! empty($studentIds)) {
            $reports = \App\Models\StudentReport::query()
                ->whereIn('student_user_id', $studentIds)
                ->with(['student:id,name', 'creator:id,name'])
                ->orderByDesc('report_date')
                ->orderByDesc('id')
                ->limit(60)
                ->get();

            $reportsByStudent = $reports->groupBy('student_user_id');
            $recentReports = $reports->take(15)->map(fn ($r) => [
                'id' => $r->id,
                'type' => $r->type,
                'title' => $r->title,
                'student' => $r->student?->name,
                'report_date' => $r->report_date?->toDateString(),
                'created_by' => $r->creator?->name,
            ])->values();
        }

        $children = $links->map(function ($link) use ($reportsByStudent) {
            $student = $link->student;
            $latest = $reportsByStudent->get($student->id)?->first();

            return [
                'link_id' => $link->id,
                'relationship' => $link->relationshipLabel(),
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'username' => $student->username,
                    'program_category' => $student->program_category,
                ],
                'latest_report' => $latest ? [
                    'title' => $latest->title,
                    'type' => $latest->type,
                    'report_date' => $latest->report_date?->toDateString(),
                ] : null,
            ];
        })->values();

        return [
            'role' => 'parent',
            'children' => $children,
            'recent_reports' => $recentReports,
        ];
    }

    private function adminOverview(): array
    {
        $registered = User::where('role', 'user')->count();
        $accepted = Schema::hasTable('registration_progress')
            ? RegistrationProgress::where('fully_completed', true)->count()
            : 0;

        $classes = Schema::hasTable('bimble_classes')
            ? BimbleClass::withCount('students')
                ->orderByDesc('updated_at')
                ->limit(12)
                ->get(['id', 'name', 'class_code', 'program_type', 'created_by', 'updated_at'])
            : collect();

        $activities = Schema::hasTable('class_activities')
            ? ClassActivity::with(['bimbleClass:id,name,class_code', 'creator:id,name'])
                ->orderByDesc('happened_at')
                ->orderByDesc('id')
                ->limit(15)
                ->get()
            : collect();

        return [
            'role' => 'admin',
            'stats' => [
                'questions' => Question::count(),
                'registered_users' => $registered,
                'accepted_users' => $accepted,
                'classes_count' => Schema::hasTable('bimble_classes') ? BimbleClass::count() : 0,
            ],
            'classes' => $classes,
            'recent_activities' => $activities,
        ];
    }

    private function mentorOverview(int $mentorId): array
    {
        if (! Schema::hasTable('bimble_classes')) {
            return [
                'role' => 'mentor',
                'classes' => [],
                'upcoming_tests' => [],
                'recent_activities' => [],
            ];
        }

        $classes = BimbleClass::withCount('students')
            ->where('created_by', $mentorId)
            ->orderBy('name')
            ->get(['id', 'name', 'class_code', 'program_type', 'updated_at']);

        $classIds = $classes->pluck('id')->all();

        $latestByClass = collect();
        $recentActivities = collect();
        if (Schema::hasTable('class_activities') && ! empty($classIds)) {
            $recentActivities = ClassActivity::with(['bimbleClass:id,name,class_code', 'creator:id,name'])
                ->whereIn('bimble_class_id', $classIds)
                ->orderByDesc('happened_at')
                ->orderByDesc('id')
                ->limit(15)
                ->get();

            $latestByClass = ClassActivity::whereIn('bimble_class_id', $classIds)
                ->orderByDesc('happened_at')
                ->orderByDesc('id')
                ->get()
                ->groupBy('bimble_class_id')
                ->map(fn ($group) => $group->first());
        }

        $classItems = $classes->map(function ($c) use ($latestByClass) {
            $latest = $latestByClass->get($c->id);
            return [
                'id' => $c->id,
                'name' => $c->name,
                'class_code' => $c->class_code,
                'program_type' => $c->program_type,
                'students_count' => $c->students_count,
                'latest_activity' => $latest ? [
                    'title' => $latest->title,
                    'happened_at' => $latest->happened_at,
                ] : null,
            ];
        });

        $upcomingTests = collect();
        if (Schema::hasTable('bimble_class_test') && ! empty($classIds)) {
            $upcomingTests = TestDefinition::with(['bimbleClasses' => function ($q) use ($classIds) {
                $q->whereIn('bimble_classes.id', $classIds)->select('bimble_classes.id', 'name', 'class_code');
            }])
                ->where('is_active', true)
                ->where('start_time', '>=', now())
                ->whereHas('bimbleClasses', fn ($q) => $q->whereIn('bimble_classes.id', $classIds))
                ->orderBy('start_time')
                ->limit(15)
                ->get(['id', 'name', 'start_time', 'end_time', 'category'])
                ->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'name' => $t->name,
                        'category' => $t->category,
                        'start_time' => $t->start_time,
                        'end_time' => $t->end_time,
                        'classes' => $t->bimbleClasses->map(fn ($c) => [
                            'id' => $c->id,
                            'name' => $c->name,
                            'class_code' => $c->class_code,
                        ])->values(),
                    ];
                });
        }

        return [
            'role' => 'mentor',
            'classes' => $classItems,
            'upcoming_tests' => $upcomingTests,
            'recent_activities' => $recentActivities,
        ];
    }

    private function studentOverview(int $userId): array
    {
        if (! Schema::hasTable('bimble_classes') || ! Schema::hasTable('bimble_class_user')) {
            return [
                'role' => 'user',
                'registration' => $this->studentRegistrationStatus($userId),
                'classes' => [],
                'class_activities' => [],
            ];
        }

        $classes = User::find($userId)
            ?->bimbleClasses()
            ->withCount('students')
            ->orderBy('bimble_classes.name')
            ->get(['bimble_classes.id', 'name', 'class_code', 'program_type']) ?? collect();

        $classIds = $classes->pluck('id')->all();

        $activities = collect();
        $latestByClass = collect();
        if (Schema::hasTable('class_activities') && ! empty($classIds)) {
            $activities = ClassActivity::with(['bimbleClass:id,name,class_code', 'creator:id,name'])
                ->whereIn('bimble_class_id', $classIds)
                ->orderByDesc('happened_at')
                ->orderByDesc('id')
                ->limit(20)
                ->get();

            $latestByClass = $activities->groupBy('bimble_class_id')->map(fn ($group) => $group->first());
        }

        $classItems = $classes->map(function ($c) use ($latestByClass) {
            $latest = $latestByClass->get($c->id);
            return [
                'id' => $c->id,
                'name' => $c->name,
                'class_code' => $c->class_code,
                'program_type' => $c->program_type,
                'students_count' => $c->students_count,
                'latest_activity' => $latest ? [
                    'title' => $latest->title,
                    'happened_at' => $latest->happened_at,
                ] : null,
            ];
        });

        return [
            'role' => 'user',
            'registration' => $this->studentRegistrationStatus($userId),
            'classes' => $classItems,
            'class_activities' => $this->mergeStudentActivities($userId, $activities),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function studentAssessmentActivities(int $userId): Collection
    {
        $items = collect();

        if (Schema::hasTable('exam_submissions')) {
            ExamSubmission::query()
                ->where('user_id', $userId)
                ->with('examDefinition:id,name,category,question_ids')
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->limit(40)
                ->get()
                ->each(function (ExamSubmission $submission) use ($items) {
                    $exam = $submission->examDefinition;
                    if (! $exam) {
                        return;
                    }

                    $total = is_array($exam->question_ids) ? count($exam->question_ids) : 0;
                    $scaled = $total > 0 ? (int) round(((float) $submission->score / $total) * 100) : 0;

                    $items->push([
                        'id' => 'exam-submission-'.$submission->id,
                        'activity_type' => 'exam',
                        'title' => 'Menyelesaikan ujian: '.$exam->name,
                        'description' => sprintf('Nilai: %d', $scaled),
                        'happened_at' => $submission->submitted_at ?? $submission->created_at,
                        'created_at' => $submission->created_at,
                        'bimble_class' => null,
                        'creator' => null,
                    ]);
                });
        }

        if (Schema::hasTable('test_submissions')) {
            TestSubmission::query()
                ->where('user_id', $userId)
                ->with('testDefinition:id,name,category,question_ids')
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->limit(40)
                ->get()
                ->each(function (TestSubmission $submission) use ($items) {
                    $test = $submission->testDefinition;
                    if (! $test) {
                        return;
                    }

                    $total = is_array($test->question_ids) ? count($test->question_ids) : 0;
                    $scaled = $total > 0 ? (int) round(((float) $submission->score / $total) * 100) : 0;

                    $items->push([
                        'id' => 'test-submission-'.$submission->id,
                        'activity_type' => 'quiz',
                        'title' => 'Menyelesaikan quiz: '.$test->name,
                        'description' => sprintf('Nilai: %d', $scaled),
                        'happened_at' => $submission->submitted_at ?? $submission->created_at,
                        'created_at' => $submission->created_at,
                        'bimble_class' => null,
                        'creator' => null,
                    ]);
                });
        }

        return $items;
    }

    /**
     * @param  Collection<int, ClassActivity>  $classActivities
     * @return array<int, array<string, mixed>>
     */
    private function mergeStudentActivities(int $userId, Collection $classActivities): array
    {
        $serialized = $classActivities->map(function (ClassActivity $activity) {
            return [
                'id' => 'class-activity-'.$activity->id,
                'activity_type' => 'class',
                'title' => $activity->title,
                'description' => $activity->description,
                'happened_at' => $activity->happened_at ?? $activity->created_at,
                'created_at' => $activity->created_at,
                'bimble_class' => $activity->bimbleClass ? [
                    'id' => $activity->bimbleClass->id,
                    'name' => $activity->bimbleClass->name,
                    'class_code' => $activity->bimbleClass->class_code,
                ] : null,
                'creator' => $activity->creator ? [
                    'id' => $activity->creator->id,
                    'name' => $activity->creator->name,
                ] : null,
            ];
        });

        return $serialized
            ->concat($this->studentAssessmentActivities($userId))
            ->sortByDesc(fn (array $item) => $this->activityTimestamp($item))
            ->take(25)
            ->values()
            ->map(fn (array $item) => $this->normalizeActivityTimestamps($item))
            ->all();
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function activityTimestamp(array $item): int
    {
        $value = $item['happened_at'] ?? $item['created_at'] ?? null;
        if ($value instanceof Carbon) {
            return $value->timestamp;
        }

        return $value ? (int) strtotime((string) $value) : 0;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function normalizeActivityTimestamps(array $item): array
    {
        foreach (['happened_at', 'created_at'] as $field) {
            if (($item[$field] ?? null) instanceof Carbon) {
                $item[$field] = $item[$field]->toIso8601String();
            }
        }

        return $item;
    }

    private function studentRegistrationStatus(int $userId): array
    {
        if (! Schema::hasTable('registration_progress')) {
            return [
                'fully_completed' => false,
                'current_step' => 'administration',
            ];
        }

        $progress = RegistrationProgress::where('user_id', $userId)->first();
        if (! $progress) {
            return [
                'fully_completed' => false,
                'current_step' => 'administration',
            ];
        }

        return [
            'fully_completed' => (bool) $progress->fully_completed,
            'current_step' => $progress->current_step,
        ];
    }
}

