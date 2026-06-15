<?php

namespace App\Services;

use App\Models\RegistrationProgress;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RegistrationFileStorage
{
  /** @var list<string> */
    public const ADMINISTRATION_PATH_KEYS = [
        'id_document_path',
        'kk_path',
        'report_card_path',
        'passport_photo_path',
        'full_body_photo_path',
    ];

    /** @var list<string> */
    private const LEGACY_PUBLIC_DISKS = ['public'];

    public function registrationDiskName(): string
    {
        return (string) config('registration.filesystem_disk', 'local');
    }

    /**
     * @return list<string>
     */
    public function diskCandidates(): array
    {
        $candidates = [
            $this->registrationDiskName(),
            'local',
            'public',
            (string) config('filesystems.upload_disk', 'public'),
            's3',
        ];

        return array_values(array_unique(array_filter($candidates)));
    }

    public function findDisk(string $path): ?Filesystem
    {
        foreach ($this->diskCandidates() as $diskName) {
            try {
                $disk = Storage::disk($diskName);
                if ($disk->exists($path)) {
                    return $disk;
                }
            } catch (Throwable $e) {
                Log::debug('Registration file disk probe failed.', [
                    'disk' => $diskName,
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * If the file still lives on a legacy public disk, copy it to the configured private disk.
     */
    public function migrateLegacyPublicFileToPrivate(string $path): bool
    {
        $targetName = $this->registrationDiskName();
        if (in_array($targetName, self::LEGACY_PUBLIC_DISKS, true)) {
            return false;
        }

        $target = Storage::disk($targetName);
        if ($target->exists($path)) {
            return false;
        }

        foreach ($this->legacyPublicDiskNames() as $legacyName) {
            if ($legacyName === $targetName) {
                continue;
            }

            try {
                $legacy = Storage::disk($legacyName);
                if (! $legacy->exists($path)) {
                    continue;
                }

                $target->put($path, $legacy->get($path));
                $legacy->delete($path);

                Log::info('Migrated registration file from public to private disk.', [
                    'path' => $path,
                    'from' => $legacyName,
                    'to' => $targetName,
                ]);

                return true;
            } catch (Throwable $e) {
                Log::warning('Failed migrating registration file to private disk.', [
                    'path' => $path,
                    'from' => $legacyName,
                    'to' => $targetName,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return false;
    }

    public function resolveDiskForServing(string $path): ?Filesystem
    {
        $this->migrateLegacyPublicFileToPrivate($path);

        return $this->findDisk($path);
    }

    public function storeRegistrationFile(\Illuminate\Http\UploadedFile $file, string $directory): string
    {
        $diskName = $this->registrationDiskName();
        $disk = Storage::disk($diskName);

        $storedPath = $file->store($directory, $diskName);
        if (! is_string($storedPath) || $storedPath === '') {
            throw new \RuntimeException('Gagal menyimpan berkas ke disk '.$diskName.'.');
        }

        if (! $disk->exists($storedPath)) {
            $absolute = $disk->path($storedPath);
            Log::error('Registration file missing immediately after store.', [
                'disk' => $diskName,
                'relative' => $storedPath,
                'absolute' => $absolute,
            ]);
            throw new \RuntimeException('Berkas tidak ditemukan setelah unggah di: '.$absolute);
        }

        Log::info('Registration file stored.', [
            'disk' => $diskName,
            'relative' => $storedPath,
            'absolute' => $disk->path($storedPath),
            'bytes' => $disk->size($storedPath),
        ]);

        return $storedPath;
    }

    /**
     * @return array{ok: bool, disk: string, relative: string, absolute: string, message: string}
     */
    public function writeTestFile(): array
    {
        $diskName = $this->registrationDiskName();
        $disk = Storage::disk($diskName);
        $relative = 'registration/_write-test/'.now()->format('YmdHis').'.txt';
        $payload = 'write-test '.now()->toIso8601String();

        $disk->put($relative, $payload);
        $absolute = $disk->path($relative);
        $ok = $disk->exists($relative) && is_file($absolute);

        if ($ok) {
            $disk->delete($relative);
        }

        return [
            'ok' => $ok,
            'disk' => $diskName,
            'relative' => $relative,
            'absolute' => $absolute,
            'message' => $ok
                ? 'Tulis & baca disk OK.'
                : 'GAGAL menulis ke disk — cek permission volume / REGISTRATION_FILESYSTEM_DISK.',
        ];
    }

    /**
     * @return array{migrated: int, skipped: int, missing: int, errors: int}
     */
    public function migrateAllLegacyPublicFiles(bool $dryRun = false): array
    {
        $stats = [
            'migrated' => 0,
            'skipped' => 0,
            'missing' => 0,
            'errors' => 0,
        ];

        if (! Schema::hasTable('registration_progress')) {
            return $stats;
        }

        $targetName = $this->registrationDiskName();
        if (in_array($targetName, self::LEGACY_PUBLIC_DISKS, true)) {
            return $stats;
        }

        RegistrationProgress::query()
            ->whereNotNull('administration_data')
            ->cursor()
            ->each(function (RegistrationProgress $progress) use ($dryRun, $targetName, &$stats) {
                $data = $progress->administration_data ?? [];

                foreach (self::ADMINISTRATION_PATH_KEYS as $pathKey) {
                    $path = $data[$pathKey] ?? null;
                    if (! is_string($path) || $path === '') {
                        continue;
                    }

                    $onTarget = false;
                    try {
                        $onTarget = Storage::disk($targetName)->exists($path);
                    } catch (Throwable) {
                        $stats['errors']++;
                        continue;
                    }

                    if ($onTarget) {
                        $stats['skipped']++;

                        continue;
                    }

                    $legacyDisk = null;
                    foreach ($this->legacyPublicDiskNames() as $legacyName) {
                        try {
                            if (Storage::disk($legacyName)->exists($path)) {
                                $legacyDisk = $legacyName;
                                break;
                            }
                        } catch (Throwable) {
                            $stats['errors']++;
                        }
                    }

                    if ($legacyDisk === null) {
                        $stats['missing']++;

                        continue;
                    }

                    if ($dryRun) {
                        $stats['migrated']++;

                        continue;
                    }

                    if ($this->migrateLegacyPublicFileToPrivate($path)) {
                        $stats['migrated']++;
                    } else {
                        $stats['errors']++;
                    }
                }
            });

        return $stats;
    }

    /**
     * @return list<string>
     */
    private function legacyPublicDiskNames(): array
    {
        $names = self::LEGACY_PUBLIC_DISKS;

        $uploadDisk = (string) config('filesystems.upload_disk', 'public');
        if ($uploadDisk !== '' && ! in_array($uploadDisk, $names, true)) {
            $names[] = $uploadDisk;
        }

        return $names;
    }
}
