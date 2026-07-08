<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BimbleClass;
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
        $batch->load([
            'students:id,name,username,email,program_category,app_expires_at,role',
            'bimbleClasses:id,name,class_code,program_type',
        ]);
        $batch->loadCount(['students', 'bimbleClasses']);

        return response()->json($batch);
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
            'app_expires_at' => 'nullable|date',
        ]);

        $student = User::query()->findOrFail($data['user_id']);
        if ($student->role !== 'user') {
            return response()->json(['message' => 'Hanya peserta (siswa) yang bisa dimasukkan ke batch.'], 422);
        }
        if (User::isExamOnlyProgram($student->program_category)) {
            return response()->json(['message' => 'Peserta Kelas Ujian tidak dimasukkan ke batch kursus.'], 422);
        }

        if (array_key_exists('app_expires_at', $data) && Schema::hasColumn('users', 'app_expires_at')) {
            $student->app_expires_at = $data['app_expires_at'];
            $student->save();
        }

        $batch->students()->syncWithoutDetaching([$student->id]);
        $sync = $this->sync->syncStudentToBatchClasses($batch, $student);

        return response()->json([
            'message' => 'Peserta ditambahkan ke batch.',
            'auto_assigned' => $sync,
            'batch' => $batch->fresh()->load([
                'students:id,name,username,email,program_category,app_expires_at,role',
                'bimbleClasses:id,name,class_code,program_type',
            ])->loadCount(['students', 'bimbleClasses']),
        ]);
    }

    public function detachStudent(Batch $batch, User $user)
    {
        $batch->students()->detach($user->id);

        return response()->json([
            'message' => 'Peserta dilepas dari batch.',
            'batch' => $batch->fresh()->load([
                'students:id,name,username,email,program_category,app_expires_at,role',
                'bimbleClasses:id,name,class_code,program_type',
            ])->loadCount(['students', 'bimbleClasses']),
        ]);
    }

    public function updateStudentExpires(Request $request, Batch $batch, User $user)
    {
        if (! $batch->students()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Peserta tidak ada di batch ini.'], 404);
        }

        $data = $request->validate([
            'app_expires_at' => 'nullable|date',
        ]);

        if (! Schema::hasColumn('users', 'app_expires_at')) {
            return response()->json(['message' => 'Kolom masa aktif belum tersedia.'], 422);
        }

        $user->app_expires_at = $data['app_expires_at'] ?? null;
        $user->save();

        return response()->json([
            'message' => 'Masa aktif diperbarui.',
            'user' => $user->fresh(['batches:id,name']),
        ]);
    }

    public function attachClass(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'bimble_class_id' => 'required|exists:bimble_classes,id',
        ]);

        $class = BimbleClass::query()->findOrFail($data['bimble_class_id']);
        $batch->bimbleClasses()->syncWithoutDetaching([$class->id]);
        $sync = $this->sync->syncBatchesToClass($class, [$batch->id]);

        return response()->json([
            'message' => 'Kelas ditautkan ke batch. Peserta batch otomatis di-assign.',
            'auto_assigned' => $sync,
            'batch' => $batch->fresh()->load([
                'students:id,name,username,email,program_category,app_expires_at,role',
                'bimbleClasses:id,name,class_code,program_type',
            ])->loadCount(['students', 'bimbleClasses']),
        ]);
    }

    public function detachClass(Batch $batch, BimbleClass $bimbleClass)
    {
        $batch->bimbleClasses()->detach($bimbleClass->id);

        return response()->json([
            'message' => 'Kelas dilepas dari batch.',
            'batch' => $batch->fresh()->load([
                'students:id,name,username,email,program_category,app_expires_at,role',
                'bimbleClasses:id,name,class_code,program_type',
            ])->loadCount(['students', 'bimbleClasses']),
        ]);
    }

    public function syncClass(Batch $batch, BimbleClass $bimbleClass)
    {
        if (! $batch->bimbleClasses()->where('bimble_classes.id', $bimbleClass->id)->exists()) {
            return response()->json(['message' => 'Kelas belum ditautkan ke batch ini.'], 422);
        }

        $sync = $this->sync->syncBatchesToClass($bimbleClass, [$batch->id]);

        return response()->json([
            'message' => 'Sinkronisasi selesai.',
            'auto_assigned' => $sync,
        ]);
    }
}
