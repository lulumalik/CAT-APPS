<?php

namespace App\Http\Controllers;

use App\Models\RegistrationProgress;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\RegistrationProgressStatusNotification;
use App\Services\RegistrationFileStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RegistrationProgressController extends Controller
{
    public function __construct(
        private readonly RegistrationFileStorage $registrationFiles
    ) {}

    private function sendRegistrationEmailStatus(
        User $user,
        string $event,
        string $step,
        ?string $adminNote = null,
        ?string $nextStep = null
    ): void {
        try {
            $user->notify(new RegistrationProgressStatusNotification(
                event: $event,
                step: $step,
                adminNote: $adminNote,
                nextStep: $nextStep,
            ));
        } catch (Throwable $e) {
            Log::warning('Failed sending registration progress email.', [
                'user_id' => $user->id,
                'event' => $event,
                'step' => $step,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function nextStepAfterApproval(string $step): ?string
    {
        return match ($step) {
            'administration' => 'psychology',
            'psychology' => 'health',
            'health' => 'physical',
            'physical' => 'completed',
            default => null,
        };
    }

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
        return $this->registrationFiles->registrationDiskName();
    }

    private function canViewRegistrationFile(User $actor, User $owner): bool
    {
        return $actor->id === $owner->id || $actor->role === 'admin';
    }

    private function registrationFileResponse(string $path)
    {
        $disk = $this->registrationFiles->resolveDiskForServing($path);
        if ($disk === null) {
            Log::warning('Registration file missing on all disks.', [
                'path' => $path,
                'tried_disks' => $this->registrationFiles->diskCandidates(),
            ]);
            abort(404);
        }

        $response = $disk->response($path);
        $response->headers->set('Cache-Control', 'private, max-age=3600');

        return $response;
    }

    /**
     * @return array<string, string>
     */
    private function buildAdministrationFileUrls(RegistrationProgress $progress): array
    {
        $urls = [];
        $administrationData = $progress->administration_data ?? [];

        foreach (self::ADMIN_FILE_FIELDS as $input => $pathKey) {
            $storedPath = $administrationData[$pathKey] ?? null;
            if (! is_string($storedPath) || $storedPath === '') {
                continue;
            }
            if ($this->registrationFiles->findDisk($storedPath) === null) {
                continue;
            }
            $urls[$pathKey] = '/api/registration-files/'.$progress->user_id.'/'.$input;
        }

        return $urls;
    }

    /**
     * @return array<string, bool>
     */
    private function buildAdministrationFilePresence(RegistrationProgress $progress): array
    {
        $presence = [];
        $administrationData = $progress->administration_data ?? [];

        foreach (self::ADMIN_FILE_FIELDS as $input => $pathKey) {
            $storedPath = $administrationData[$pathKey] ?? null;
            if (! is_string($storedPath) || $storedPath === '') {
                $presence[$pathKey] = false;

                continue;
            }
            $presence[$pathKey] = $this->registrationFiles->findDisk($storedPath) !== null;
        }

        return $presence;
    }

    private function serializeRegistrationProgress(RegistrationProgress $progress): array
    {
        $data = $progress->toArray();
        $data['administration_file_urls'] = $this->buildAdministrationFileUrls($progress);
        $data['administration_files_present'] = $this->buildAdministrationFilePresence($progress);

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
                $oldDisk = $this->registrationFiles->findDisk($merged[$pathKey]);
                if ($oldDisk !== null) {
                    $oldDisk->delete($merged[$pathKey]);
                }
            }

            $merged[$pathKey] = $this->registrationFiles->storeRegistrationFile(
                $request->file($input),
                $dir
            );
        }

        return $merged;
    }

    private function canEditAdministration(RegistrationProgress $progress): bool
    {
        if ($progress->fully_completed) {
            return false;
        }

        return $progress->current_step === 'administration'
            || $progress->administration_status === 'revision_requested';
    }

    public function uploadAdministrationFile(Request $request)
    {
        if (! Schema::hasTable('registration_progress')) {
            return response()->json([
                'message' => 'Struktur pendaftaran belum aktif. Jalankan migrasi database terbaru.',
            ], 503);
        }

        $data = $request->validate([
            'field' => 'required|in:'.implode(',', array_keys(self::ADMIN_FILE_FIELDS)),
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

        if (! $this->canEditAdministration($progress)) {
            return response()->json([
                'message' => 'Tahap administrasi tidak sedang aktif untuk akun Anda.',
            ], 422);
        }

        $field = $data['field'];
        $pathKey = self::ADMIN_FILE_FIELDS[$field];
        $merged = $this->scrubLegacyAdministrationUrls($progress->administration_data ?? []);

        $isPhoto = in_array($field, ['passport_photo', 'full_body_photo'], true);
        Validator::make($request->all(), [
            $field => [
                'required',
                'file',
                'max:12288',
                $isPhoto ? 'mimes:jpeg,jpg,png,webp' : 'mimes:jpeg,jpg,png,webp,pdf',
            ],
        ])->validate();

        if (! empty($merged[$pathKey])) {
            $oldDisk = $this->registrationFiles->findDisk($merged[$pathKey]);
            if ($oldDisk !== null) {
                $oldDisk->delete($merged[$pathKey]);
            }
        }

        $dir = 'registration/'.$request->user()->id;
        try {
            $merged[$pathKey] = $this->registrationFiles->storeRegistrationFile(
                $request->file($field),
                $dir
            );
        } catch (\RuntimeException $e) {
            Log::error('Registration file upload failed.', [
                'user_id' => $request->user()->id,
                'field' => $field,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
        $progress->administration_data = $merged;
        $progress->save();

        return response()->json($this->serializeRegistrationProgress($progress->fresh()));
    }

    public function servePublicFile(string $path)
    {
        $normalized = ltrim(str_replace('\\', '/', $path), '/');
        if ($normalized === '' || str_contains($normalized, '..')) {
            abort(404);
        }

        // Onboarding documents (KTP, KK, …) must not be served without authentication.
        if (str_starts_with($normalized, 'registration/')) {
            abort(404);
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($normalized)) {
            abort(404);
        }

        $response = $disk->response($normalized);
        $response->headers->set('Cache-Control', 'private, max-age=3600');

        return $response;
    }

    public function streamAdministrationFile(Request $request, User $user, string $field)
    {
        if (! array_key_exists($field, self::ADMIN_FILE_FIELDS)) {
            abort(404);
        }

        $actor = $request->user();
        if ($actor === null) {
            abort(401);
        }
        if (! $this->canViewRegistrationFile($actor, $user)) {
            abort(403);
        }

        if (! Schema::hasTable('registration_progress')) {
            abort(404);
        }

        $progress = RegistrationProgress::where('user_id', $user->id)->first();
        if ($progress === null) {
            abort(404);
        }

        $pathKey = self::ADMIN_FILE_FIELDS[$field];
        $storedPath = $progress->administration_data[$pathKey] ?? null;
        if (! is_string($storedPath) || $storedPath === '') {
            abort(404);
        }

        return $this->registrationFileResponse($storedPath);
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
                'phone.regex' => 'Format nomor telepon orang tua harus diawali 628 dan hanya angka (contoh: 6281234567890).',
            ]);

            $merged = $this->scrubLegacyAdministrationUrls(array_merge(
                $progress->administration_data ?? [],
                $text
            ));
            $merged = $this->mergeAdministrationFiles($request, $merged);
            $progress->administration_data = $merged;
        }

        $progress->{$statusCol} = 'submitted';
        $progress->save();
        $this->sendRegistrationEmailStatus(
            $request->user(),
            'submitted',
            $step
        );

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
            $this->sendRegistrationEmailStatus(
                $user,
                'revision_requested',
                $step,
                $data['admin_note'] ?? null
            );

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
        $this->sendRegistrationEmailStatus(
            $user,
            $progress->fully_completed ? 'completed' : 'approved',
            $step,
            $data['admin_note'] ?? null,
            $progress->fully_completed ? null : $this->nextStepAfterApproval($step)
        );

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

    public function adminStorageDiagnostic(Request $request, RegistrationFileStorage $files)
    {
        if (! config('app.debug')) {
            abort(404);
        }

        $userId = $request->query('user');
        $writeTest = $files->writeTestFile();

        $payload = [
            'cwd' => getcwd(),
            'storage_path' => storage_path(),
            'registration_disk' => config('registration.filesystem_disk', 'local'),
            'local_disk_root' => config('filesystems.disks.local.root'),
            'write_test' => $writeTest,
            'volume_mounts' => [],
            'registration_private_files' => $this->countFilesUnder(storage_path('app/private/registration')),
            'registration_public_files' => $this->countFilesUnder(storage_path('app/public/registration')),
        ];

        if (is_readable('/proc/mounts')) {
            $payload['volume_mounts'] = array_values(array_filter(
                file('/proc/mounts', FILE_IGNORE_NEW_LINES) ?: [],
                fn (string $line) => str_contains($line, '/var/www/html/storage')
            ));
        }

        if ($userId !== null && Schema::hasTable('registration_progress')) {
            $progress = RegistrationProgress::where('user_id', $userId)->first();
            if ($progress !== null) {
                $payload['user'] = [
                    'user_id' => (int) $userId,
                    'files' => $this->buildAdministrationFilePresence($progress),
                    'paths' => array_intersect_key(
                        $progress->administration_data ?? [],
                        array_flip(RegistrationFileStorage::ADMINISTRATION_PATH_KEYS)
                    ),
                ];
            }
        }

        return response()->json($payload);
    }

    private function countFilesUnder(string $dir): int
    {
        if (! is_dir($dir)) {
            return 0;
        }
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }

        return $count;
    }
}
