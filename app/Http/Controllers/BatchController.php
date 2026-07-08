<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\User;
use App\Services\BatchClassSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    public function __construct(private BatchClassSyncService $sync)
    {
    }

    public function index(Request $request)
    {
        if (! Schema::hasTable('batches')) {
            return response()->json([]);
        }

        $q = Batch::query()
            ->withCount(['students', 'bimbleClasses'])
            ->orderByDesc('is_active')
            ->orderByDesc('starts_on')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $q->where(function ($inner) use ($s) {
                $inner->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%");
            });
        }

        if ($request->boolean('active_only')) {
            $q->where('is_active', true);
        }

        return response()->json($q->get());
    }

    public function show(Batch $batch)
    {
        $batch->loadCount(['students', 'bimbleClasses']);

        return response()->json($batch);
    }

    public function students(Request $request, Batch $batch)
    {
        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);

        $q = $batch->students()
            ->select('users.id', 'users.name', 'users.username', 'users.email', 'users.program_category', 'users.role')
            ->orderBy('users.name');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $q->where(function ($inner) use ($s) {
                $inner->where('users.name', 'like', "%{$s}%")
                    ->orWhere('users.username', 'like', "%{$s}%")
                    ->orWhere('users.email', 'like', "%{$s}%");
            });
        }

        return response()->json($q->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:64|unique:batches,code',
            'starts_on' => 'nullable|date',
            'ends_on' => 'nullable|date|after_or_equal:starts_on',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['is_active'] = array_key_exists('is_active', $data)
            ? (bool) $data['is_active']
            : true;
        $data['created_by'] = $request->user()->id;

        $batch = Batch::create($data);

        return response()->json($batch->loadCount(['students', 'bimbleClasses']), 201);
    }

    public function update(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => ['nullable', 'string', 'max:64', Rule::unique('batches', 'code')->ignore($batch->id)],
            'starts_on' => 'nullable|date',
            'ends_on' => 'nullable|date|after_or_equal:starts_on',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
        ]);

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        $batch->update($data);

        return response()->json($batch->fresh()->loadCount(['students', 'bimbleClasses']));
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();

        return response()->json(['message' => 'Batch dihapus.']);
    }

    public function attachStudent(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $student = User::query()->findOrFail($data['user_id']);
        if ($student->role !== 'user') {
            return response()->json(['message' => 'Hanya peserta (siswa) yang bisa dimasukkan ke batch.'], 422);
        }
        if (User::isExamOnlyProgram($student->program_category)) {
            return response()->json(['message' => 'Peserta Kelas Ujian tidak dimasukkan ke batch kursus.'], 422);
        }

        $progress = $student->registrationProgress;
        if ($progress && ! $progress->payment_confirmed) {
            return response()->json(['message' => 'Peserta belum lunas, belum bisa dimasukkan ke batch.'], 422);
        }

        $batch->students()->syncWithoutDetaching([$student->id]);
        $sync = $this->sync->syncStudentToBatchClasses($batch, $student);

        return response()->json([
            'message' => 'Peserta ditambahkan ke batch.',
            'auto_assigned' => $sync,
            'students_count' => $batch->students()->count(),
        ]);
    }

    public function detachStudent(Batch $batch, User $user)
    {
        $batch->students()->detach($user->id);

        return response()->json([
            'message' => 'Peserta dilepas dari batch.',
            'students_count' => $batch->students()->count(),
        ]);
    }
}
