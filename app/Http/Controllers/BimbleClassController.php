<?php

namespace App\Http\Controllers;

use App\Models\BimbleClass;
use App\Models\Material;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BimbleClassController extends Controller
{
    public function mine(Request $request)
    {
        if (! Schema::hasTable('bimble_classes') || ! Schema::hasTable('bimble_class_user')) {
            return response()->json([]);
        }

        $classes = $request->user()->bimbleClasses()->orderBy('bimble_classes.name')->get();

        return response()->json($classes);
    }

    public function index(Request $request)
    {
        if (! Schema::hasTable('bimble_classes')) {
            return response()->json([]);
        }

        $user = $request->user();
        $q = BimbleClass::withCount('students')->with('instructor:id,name,role');

        if ($user->role === 'mentor') {
            $q->where(function ($inner) use ($user) {
                $inner->where('created_by', $user->id)
                    ->orWhere('instructor_id', $user->id);
            });
        }

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $q->where(function ($x) use ($s) {
                $x->where('name', 'like', "%{$s}%")->orWhere('class_code', 'like', "%{$s}%");
            });
        }

        return response()->json($q->orderByDesc('updated_at')->get());
    }

    public function show(Request $request, BimbleClass $bimbleClass)
    {
        $this->authorizeManage($request, $bimbleClass);

        $bimbleClass->load(['students:id,name,email,program_category', 'materials', 'testDefinitions', 'instructor:id,name,role']);

        return response()->json($bimbleClass);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'class_code' => 'nullable|string|max:32',
            'instructor_id' => ['nullable', Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', ['admin', 'mentor']))],
            'academic_period_start' => 'nullable|date',
            'academic_period_end' => 'nullable|date|after_or_equal:academic_period_start',
            'participant_count' => 'nullable|integer|min:0',
            'program_type' => 'required|string|in:vip_offline,vip_online,regular_offline,regular_online,bimbingan_online,try_out',
        ]);

        if (empty($data['class_code'])) {
            $data['class_code'] = strtoupper(Str::random(8));
        }

        if ($request->user()->role === 'mentor') {
            $data['instructor_id'] = $request->user()->id;
        }

        $instructor = null;
        if (! empty($data['instructor_id'])) {
            $instructor = User::query()
                ->whereIn('role', ['admin', 'mentor'])
                ->find($data['instructor_id']);
        }
        if (! empty($data['instructor_id']) && ! $instructor) {
            return response()->json(['message' => 'Pengajar harus user dengan role admin atau mentor.'], 422);
        }
        $data['instructor_name'] = $instructor?->name;
        $data['academic_period'] = $this->composeAcademicPeriod($data['academic_period_start'] ?? null, $data['academic_period_end'] ?? null);
        $data['created_by'] = $request->user()->id;

        $class = BimbleClass::create($data);

        return response()->json($class->load('instructor:id,name,role'), 201);
    }

    public function update(Request $request, BimbleClass $bimbleClass)
    {
        $this->authorizeManage($request, $bimbleClass);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'class_code' => 'sometimes|required|string|max:32',
            'instructor_id' => ['nullable', Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', ['admin', 'mentor']))],
            'academic_period_start' => 'nullable|date',
            'academic_period_end' => 'nullable|date|after_or_equal:academic_period_start',
            'participant_count' => 'nullable|integer|min:0',
            'program_type' => 'sometimes|required|string|in:vip_offline,vip_online,regular_offline,regular_online,bimbingan_online,try_out',
        ]);

        if ($request->user()->role === 'mentor') {
            $data['instructor_id'] = $request->user()->id;
        }

        if (array_key_exists('instructor_id', $data)) {
            $instructor = null;
            if (! empty($data['instructor_id'])) {
                $instructor = User::query()
                    ->whereIn('role', ['admin', 'mentor'])
                    ->find($data['instructor_id']);
            }
            if (! empty($data['instructor_id']) && ! $instructor) {
                return response()->json(['message' => 'Pengajar harus user dengan role admin atau mentor.'], 422);
            }
            $data['instructor_name'] = $instructor?->name;
        }

        if (array_key_exists('academic_period_start', $data) || array_key_exists('academic_period_end', $data)) {
            $start = $data['academic_period_start'] ?? $bimbleClass->academic_period_start;
            $end = $data['academic_period_end'] ?? $bimbleClass->academic_period_end;
            $data['academic_period'] = $this->composeAcademicPeriod($start, $end);
        }

        $bimbleClass->update($data);

        return response()->json($bimbleClass->fresh()->load('instructor:id,name,role'));
    }

    public function destroy(Request $request, BimbleClass $bimbleClass)
    {
        $this->authorizeManage($request, $bimbleClass);
        $bimbleClass->delete();

        return response()->noContent();
    }

    public function workspace(Request $request, BimbleClass $bimbleClass)
    {
        $user = $request->user();
        $bimbleClass->load('instructor:id,name,role');
        $ok = $user->role === 'admin'
            || ($user->role === 'mentor' && ((int) $bimbleClass->created_by === (int) $user->id || (int) $bimbleClass->instructor_id === (int) $user->id))
            || $bimbleClass->students()->where('users.id', $user->id)->exists();

        if (! $ok) {
            return response()->json(['message' => 'Anda belum terdaftar di kelas ini.'], 403);
        }

        $materialsBySession = collect();
        if (! $bimbleClass->isTryOut()) {
            $materialsBySession = $bimbleClass->materials()
                ->where('materials.status', 'published')
                ->get()
                ->groupBy(fn ($m) => $m->pivot->session_number);
        }

        $tests = $bimbleClass->testDefinitions()->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'name' => $t->name,
                'description' => $t->description,
                'category' => $t->category,
                'kind' => $t->pivot->kind,
                'duration' => $t->duration,
                'start_time' => $t->start_time,
                'end_time' => $t->end_time,
                'status' => $t->status,
                'is_locked' => $t->is_locked,
                'can_submit' => $t->can_submit,
            ];
        });

        $activities = collect();
        if (Schema::hasTable('class_activities')) {
            $activities = $bimbleClass->activities()
                ->with('creator:id,name')
                ->limit(20)
                ->get();
        }

        return response()->json([
            'class' => $bimbleClass,
            'materials_by_session' => $materialsBySession,
            'tests' => $tests,
            'activities' => $activities,
            'flags' => [
                'hide_materials' => $bimbleClass->isTryOut(),
                'vip_quarantine_note' => $bimbleClass->program_type === 'vip_online',
            ],
        ]);
    }

    public function attachStudent(Request $request, BimbleClass $bimbleClass)
    {
        $this->authorizeManage($request, $bimbleClass);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $bimbleClass->students()->syncWithoutDetaching([
            $data['user_id'] => ['role' => 'student'],
        ]);

        UserNotification::create([
            'user_id' => $data['user_id'],
            'type' => 'class_assigned',
            'title' => 'Kelas baru ditambahkan',
            'message' => sprintf('Anda ditambahkan ke kelas %s.', $bimbleClass->name),
            'payload' => [
                'class_id' => $bimbleClass->id,
                'class_name' => $bimbleClass->name,
            ],
        ]);

        return response()->json(['message' => 'Peserta ditambahkan.']);
    }

    public function detachStudent(Request $request, BimbleClass $bimbleClass, User $user)
    {
        $this->authorizeManage($request, $bimbleClass);
        $bimbleClass->students()->detach($user->id);

        return response()->json(['message' => 'Peserta dihapus dari kelas.']);
    }

    public function attachMaterial(Request $request, BimbleClass $bimbleClass)
    {
        $this->authorizeManage($request, $bimbleClass);

        $data = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'session_number' => 'nullable|integer|min:1|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $bimbleClass->materials()->syncWithoutDetaching([
            $data['material_id'] => [
                'session_number' => $data['session_number'] ?? 1,
                'sort_order' => $data['sort_order'] ?? 0,
            ],
        ]);

        Material::where('id', $data['material_id'])->update(['visibility' => 'class_only']);

        return response()->json(['message' => 'Materi ditautkan ke kelas.']);
    }

    public function detachMaterial(Request $request, BimbleClass $bimbleClass, Material $material)
    {
        $this->authorizeManage($request, $bimbleClass);
        $bimbleClass->materials()->detach($material->id);

        return response()->json(['message' => 'Materi dilepas dari kelas.']);
    }

    public function attachTest(Request $request, BimbleClass $bimbleClass)
    {
        $this->authorizeManage($request, $bimbleClass);

        $data = $request->validate([
            'test_definition_id' => 'required|exists:test_definitions,id',
            'kind' => 'required|in:cbt,quiz',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $bimbleClass->testDefinitions()->syncWithoutDetaching([
            $data['test_definition_id'] => [
                'kind' => $data['kind'],
                'sort_order' => $data['sort_order'] ?? 0,
            ],
        ]);

        return response()->json(['message' => 'Tes ditautkan ke kelas.']);
    }

    public function detachTest(Request $request, BimbleClass $bimbleClass, \App\Models\TestDefinition $testDefinition)
    {
        $this->authorizeManage($request, $bimbleClass);
        $bimbleClass->testDefinitions()->detach($testDefinition->id);

        return response()->json(['message' => 'Tes dilepas dari kelas.']);
    }

    private function authorizeManage(Request $request, BimbleClass $bimbleClass): void
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            return;
        }
        if ($user->role === 'mentor' && ((int) $bimbleClass->created_by === (int) $user->id || (int) $bimbleClass->instructor_id === (int) $user->id)) {
            return;
        }
        abort(403, 'Unauthorized');
    }

    public function instructors(Request $request)
    {
        $user = $request->user();
        $query = User::query()
            ->whereIn('role', ['admin', 'mentor'])
            ->orderBy('name');

        if ($user->role === 'mentor') {
            $query->where('id', $user->id);
        }

        return response()->json($query->get(['id', 'name', 'role']));
    }

    private function composeAcademicPeriod(?string $start, ?string $end): ?string
    {
        if (! $start || ! $end) {
            return null;
        }

        return sprintf('%s - %s', $start, $end);
    }
}
