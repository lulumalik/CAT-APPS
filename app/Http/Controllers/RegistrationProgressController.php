<?php

namespace App\Http\Controllers;

use App\Models\RegistrationProgress;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegistrationProgressController extends Controller
{
    /** @var array<string, string> input name => administration_data path key */
    private const ADMIN_FILE_FIELDS = [
        'id_document' => 'id_document_path',
        'kk' => 'kk_path',
        'report_card' => 'report_card_path',
        'passport_photo' => 'passport_photo_path',
        'full_body_photo' => 'full_body_photo_path',
    ];

    private function registrationDisk(): string
    {
        return config('registration.filesystem_disk', 'public');
    }

    private function registrationFileUrl(string $path): string
    {
        $diskName = $this->registrationDisk();
        $disk = Storage::disk($diskName);

        if (config('registration.use_signed_urls', false)) {
            return $disk->temporaryUrl(
                $path,
                now()->addHours(max(1, (int) config('registration.signed_url_ttl_hours', 24)))
            );
        }

        return $disk->url($path);
    }

    /**
     * @return array<string, string>
     */
    private function buildAdministrationFileUrls(?array $administrationData): array
    {
        if ($administrationData === null) {
            return [];
        }

        $urls = [];
        foreach (self::ADMIN_FILE_FIELDS as $input => $pathKey) {
            if (! empty($administrationData[$pathKey])) {
                $urls[$pathKey] = $this->registrationFileUrl($administrationData[$pathKey]);
            }
        }

        return $urls;
    }

    private function serializeRegistrationProgress(RegistrationProgress $progress): array
    {
        $data = $progress->toArray();
        $data['administration_file_urls'] = $this->buildAdministrationFileUrls($progress->administration_data);

        return $data;
    }

    private function scrubLegacyAdministrationUrls(array $data): array
    {
        foreach ([
            'id_document_url',
            'kk_url',
            'report_card_url',
            'passport_photo_url',
            'full_body_photo_url',
            'profile_photo_url',
        ] as $k) {
            unset($data[$k]);
        }

        return $data;
    }

    private function mergeAdministrationFiles(Request $request, array $merged): array
    {
        $disk = $this->registrationDisk();
        $dir = 'registration/'.$request->user()->id;

        foreach (self::ADMIN_FILE_FIELDS as $input => $pathKey) {
            if (! $request->hasFile($input)) {
                continue;
            }

            $isPhoto = in_array($input, ['passport_photo', 'full_body_photo'], true);
            $rules = [
                $input => [
                    'required',
                    'file',
                    'max:12288',
                    $isPhoto ? 'mimes:jpeg,jpg,png,webp' : 'mimes:jpeg,jpg,png,webp,pdf',
                ],
            ];
            Validator::make($request->all(), $rules)->validate();

            if (! empty($merged[$pathKey])) {
                Storage::disk($disk)->delete($merged[$pathKey]);
            }

            $merged[$pathKey] = $request->file($input)->store($dir, $disk);
        }

        return $merged;
    }

    private function assertAdministrationFilesComplete(array $merged): void
    {
        $missing = [];
        foreach (self::ADMIN_FILE_FIELDS as $input => $pathKey) {
            if (empty($merged[$pathKey])) {
                $missing[$input] = ['Unggah berkas wajib untuk dokumen ini.'];
            }
        }
        if ($missing !== []) {
            throw ValidationException::withMessages($missing);
        }
    }

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

        return response()->json($this->serializeRegistrationProgress($progress));
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

        if (in_array($step, ['psychology', 'health', 'physical'], true)) {
            return response()->json([
                'message' => 'Tahap psikologi, kesehatan, dan fisik diuji secara offline. Anda tidak perlu mengunggah data di sini; staf akan memperbarui status verifikasi.',
            ], 422);
        }

        $statusCol = "{$step}_status";

        $stepStatus = $progress->{$statusCol};
        if ($progress->current_step !== $step && $stepStatus !== 'revision_requested') {
            return response()->json([
                'message' => 'Langkah ini tidak sedang aktif untuk akun Anda.',
            ], 422);
        }

        if ($step === 'administration') {
            $phoneRules = ['required', 'string', 'max:64', 'regex:/^628[0-9]{7,12}$/'];
            $text = $request->validate([
                'full_name' => 'required|string|max:255',
                'whatsapp' => $phoneRules,
                'phone' => $phoneRules,
                'address_kk' => 'required|string|max:4000',
                'address_domicile' => 'required|string|max:4000',
                'gender' => 'required|in:L,P',
                'height_cm' => 'required|numeric|min:50|max:280',
                'weight_kg' => 'required|numeric|min:15|max:250',
            ], [
                'whatsapp.regex' => 'Format nomor WhatsApp harus diawali 628 dan hanya angka (contoh: 6281234567890).',
                'phone.regex' => 'Format nomor telepon harus diawali 628 dan hanya angka (contoh: 6281234567890).',
            ]);

            $merged = $this->scrubLegacyAdministrationUrls(array_merge(
                $progress->administration_data ?? [],
                $text
            ));
            $merged = $this->mergeAdministrationFiles($request, $merged);
            $this->assertAdministrationFilesComplete($merged);
            $progress->administration_data = $merged;
        }

        $progress->{$statusCol} = 'submitted';
        $progress->save();

        return response()->json($this->serializeRegistrationProgress($progress->fresh()));
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

        $paginator = $q->orderByDesc('updated_at')->paginate(30);
        $paginator->getCollection()->transform(
            fn (RegistrationProgress $p) => $this->serializeRegistrationProgress($p)
        );

        return response()->json($paginator);
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

        return response()->json($this->serializeRegistrationProgress($progress));
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

            return response()->json($this->serializeRegistrationProgress(
                $progress->fresh()->load('user:id,name,email,program_category')
            ));
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
                ? 'Pendaftaran selesai'
                : 'Tahap pendaftaran disetujui',
            'message' => $progress->fully_completed
                ? 'Semua tahap pendaftaran sudah disetujui. Fitur kelas sudah terbuka.'
                : sprintf('Tahap %s disetujui admin. Buka halaman pendaftaran untuk melihat status verifikasi tahap berikutnya.', $step),
            'payload' => [
                'step' => $step,
                'fully_completed' => (bool) $progress->fully_completed,
            ],
        ]);

        return response()->json($this->serializeRegistrationProgress(
            $progress->fresh()->load('user:id,name,email,program_category')
        ));
    }
}
