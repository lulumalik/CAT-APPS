<?php

namespace App\Http\Controllers;

use App\Models\BimbleClass;
use App\Models\ClassActivity;
use App\Models\Question;
use App\Models\RegistrationProgress;
use App\Models\TestDefinition;
use App\Models\User;
use Illuminate\Http\Request;
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

        return response()->json($this->studentOverview($user->id));
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
            'class_activities' => $activities,
        ];
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

