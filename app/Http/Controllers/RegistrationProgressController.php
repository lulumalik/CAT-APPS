<?php

namespace App\Http\Controllers;

use App\Models\RegistrationProgress;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RegistrationProgressController extends Controller
{
    public function mine(Request $request)
    {
        if (! Schema::hasTable('registration_progress')) {
            return response()->json([
                'user_id' => $request->user()->id,
                'current_step' => 'administration',
                'administration_status' => 'not_started',
                'psychology_status' => 'not_started',
                'health_status' => 'not_started',
                'physical_status' => 'not_started',
                'fully_completed' => false,
            ]);
        }

        $progress = RegistrationProgress::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'current_step' => 'administration',
                'administration_status' => 'not_started',
                'psychology_status' => 'not_started',
                'health_status' => 'not_started',
                'physical_status' => 'not_started',
                'fully_completed' => false,
            ]
        );

        return response()->json($progress);
    }

    public function updateMine(Request $request)
    {
        if (! Schema::hasTable('registration_progress')) {
            return response()->json([
                'message' => 'Struktur pendaftaran belum aktif. Jalankan migrasi database terbaru.',
            ], 503);
        }

        $data = $request->validate([
            'step' => 'required|in:administration,psychology,health,physical',
            'data' => 'nullable|array',
        ]);

        $progress = RegistrationProgress::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'current_step' => 'administration',
                'administration_status' => 'not_started',
                'psychology_status' => 'not_started',
                'health_status' => 'not_started',
                'physical_status' => 'not_started',
                'fully_completed' => false,
            ]
        );

        $step = $data['step'];
        $payload = $data['data'] ?? [];

        $statusCol = "{$step}_status";
        $dataCol = "{$step}_data";

        $stepStatus = $progress->{$statusCol};
        if ($progress->current_step !== $step && $stepStatus !== 'revision_requested') {
            return response()->json([
                'message' => 'Langkah ini tidak sedang aktif untuk akun Anda.',
            ], 422);
        }

        $progress->{$dataCol} = array_merge($progress->{$dataCol} ?? [], $payload);
        $progress->{$statusCol} = 'submitted';
        $progress->save();

        return response()->json($progress->fresh());
    }

    public function adminIndex(Request $request)
    {
        if (! Schema::hasTable('registration_progress')) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'total' => 0,
            ]);
        }

        $q = RegistrationProgress::with('user:id,name,email,program_category');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $q->whereHas('user', function ($uq) use ($s) {
                $uq->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('step')) {
            $q->where('current_step', $request->string('step')->toString());
        }

        return response()->json($q->orderByDesc('updated_at')->paginate(30));
    }

    public function adminShow(Request $request, User $user)
    {
        if (! Schema::hasTable('registration_progress')) {
            return response()->json([
                'message' => 'Struktur pendaftaran belum aktif. Jalankan migrasi database terbaru.',
            ], 503);
        }

        $progress = RegistrationProgress::with('user:id,name,email,program_category,in_quarantine')
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json($progress);
    }

    public function adminUpdate(Request $request, User $user)
    {
        if (! Schema::hasTable('registration_progress')) {
            return response()->json([
                'message' => 'Struktur pendaftaran belum aktif. Jalankan migrasi database terbaru.',
            ], 503);
        }

        $data = $request->validate([
            'step' => 'required|in:administration,psychology,health,physical',
            'status' => 'required|in:approved,revision_requested',
            'admin_note' => 'nullable|string|max:5000',
        ]);

        $progress = RegistrationProgress::where('user_id', $user->id)->firstOrFail();

        $step = $data['step'];
        $statusCol = "{$step}_status";
        $noteCol = "{$step}_admin_note";

        $progress->{$noteCol} = $data['admin_note'] ?? null;

        if ($data['status'] === 'revision_requested') {
            $progress->{$statusCol} = 'revision_requested';
            $progress->save();

            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'registration_revision',
                'title' => 'Perlu perbaikan tahap onboarding',
                'message' => sprintf(
                    'Tahap %s perlu diperbaiki. Silakan cek catatan admin.',
                    $step
                ),
                'payload' => [
                    'step' => $step,
                ],
            ]);

            return response()->json($progress->fresh()->load('user:id,name,email,program_category'));
        }

        $progress->{$statusCol} = 'approved';

        if ($step === 'administration') {
            $progress->current_step = 'psychology';
        } elseif ($step === 'psychology') {
            $progress->current_step = 'health';
        } elseif ($step === 'health') {
            $progress->current_step = 'physical';
        } else {
            $progress->current_step = 'completed';
            $progress->fully_completed = true;
        }

        $progress->save();

        UserNotification::create([
            'user_id' => $user->id,
            'type' => $progress->fully_completed ? 'registration_completed' : 'registration_approved',
            'title' => $progress->fully_completed
                ? 'Onboarding selesai'
                : 'Tahap onboarding disetujui',
            'message' => $progress->fully_completed
                ? 'Semua tahap onboarding sudah disetujui. Fitur kelas sudah terbuka.'
                : sprintf('Tahap %s disetujui admin. Lanjutkan ke tahap berikutnya.', $step),
            'payload' => [
                'step' => $step,
                'fully_completed' => (bool) $progress->fully_completed,
            ],
        ]);

        return response()->json($progress->fresh()->load('user:id,name,email,program_category'));
    }
}
