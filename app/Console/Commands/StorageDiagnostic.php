<?php

namespace App\Console\Commands;

use App\Services\RegistrationFileStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\RegistrationProgress;
use Illuminate\Support\Facades\Schema;

class StorageDiagnostic extends Command
{
    protected $signature = 'app:storage-diagnostic {--user= : Cek berkas pendaftaran user ID tertentu}';

    protected $description = 'Cek mount storage, disk registrasi, dan berkas pendaftaran (jalankan di Coolify Terminal)';

    public function handle(RegistrationFileStorage $files): int
    {
        $this->info('=== Laravel storage diagnostic ===');
        $this->line('storage_path(): '.storage_path());
        $this->line('REGISTRATION_FILESYSTEM_DISK: '.config('registration.filesystem_disk', 'local'));
        $this->line('local disk root: '.config('filesystems.disks.local.root'));
        $this->line('public disk root: '.config('filesystems.disks.public.root'));

        $marker = storage_path('.volume-check');
        $markerExisted = is_file($marker);
        @file_put_contents($marker, 'ok '.now()->toIso8601String());
        $this->newLine();
        $this->info($markerExisted
            ? 'Volume check: .volume-check SUDAH ADA sebelumnya (kemungkinan volume persisten aktif).'
            : 'Volume check: .volume-check BARU dibuat — setelah redeploy, jalankan lagi. Kalau hilang, volume Coolify belum benar.');

        $regPrivate = storage_path('app/private/registration');
        $regPublic = storage_path('app/public/registration');
        $this->newLine();
        $this->line("private registration dir: {$regPrivate}");
        $this->line('  exists: '.(is_dir($regPrivate) ? 'yes' : 'NO'));
        $this->line('  files: '.$this->countFilesRecursive($regPrivate));
        $this->line("public registration dir: {$regPublic}");
        $this->line('  exists: '.(is_dir($regPublic) ? 'yes' : 'NO'));
        $this->line('  files: '.$this->countFilesRecursive($regPublic));

        if (is_readable('/proc/mounts')) {
            $this->newLine();
            $this->info('Mount pada /var/www/html/storage:');
            $mounts = array_filter(
                file('/proc/mounts', FILE_IGNORE_NEW_LINES) ?: [],
                fn (string $line) => str_contains($line, '/var/www/html/storage')
            );
        if ($mounts === []) {
            $this->warn('  TIDAK ADA mount khusus — storage ikut layer container (HILANG saat redeploy).');
            $this->warn('  Coolify → Persistent Storage → Destination: /var/www/html/storage');
        } else {
            foreach ($mounts as $line) {
                $this->line('  '.$line);
            }
            $this->info('  Mount OK — volume persisten aktif.');
        }
        }

        $userId = $this->option('user');
        if ($userId !== null && Schema::hasTable('registration_progress')) {
            $this->newLine();
            $this->info("Berkas registrasi user_id={$userId}:");
            $progress = RegistrationProgress::where('user_id', $userId)->first();
            if ($progress === null) {
                $this->warn('  registration_progress tidak ditemukan.');

                return self::SUCCESS;
            }
            $data = $progress->administration_data ?? [];
            foreach (RegistrationFileStorage::ADMINISTRATION_PATH_KEYS as $key) {
                $path = $data[$key] ?? null;
                if (! is_string($path) || $path === '') {
                    $this->line("  {$key}: (kosong di DB)");

                    continue;
                }
                $disk = $files->findDisk($path);
                $this->line('  '.$key.': '.$path.' → '.($disk ? 'ADA di disk' : 'TIDAK ADA di semua disk'));
            }

            $orphans = 0;
            foreach (RegistrationFileStorage::ADMINISTRATION_PATH_KEYS as $key) {
                $path = $data[$key] ?? null;
                if (is_string($path) && $path !== '' && $files->findDisk($path) === null) {
                    $orphans++;
                }
            }
            if ($orphans > 0) {
                $this->newLine();
                $this->warn("  {$orphans} path di database tanpa file fisik (upload sebelum volume / redeploy lama).");
                $this->warn('  Minta peserta upload ulang berkas, atau hapus path dari DB.');
            }
        }

        $this->newLine();
        $this->comment('Catatan: file TIDAK ada di folder git di host. Cek dari dalam container (Coolify Terminal), bukan di repo clone.');

        return self::SUCCESS;
    }

    private function countFilesRecursive(string $dir): int
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
