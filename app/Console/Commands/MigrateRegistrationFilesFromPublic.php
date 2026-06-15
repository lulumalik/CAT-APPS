<?php

namespace App\Console\Commands;

use App\Services\RegistrationFileStorage;
use Illuminate\Console\Command;

class MigrateRegistrationFilesFromPublic extends Command
{
    protected $signature = 'registration:migrate-public-files {--dry-run : Count files without copying}';

    protected $description = 'Move onboarding documents from legacy public disk to the private registration disk';

    public function handle(RegistrationFileStorage $storage): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $target = $storage->registrationDiskName();

        if (in_array($target, ['public'], true)) {
            $this->warn('REGISTRATION_FILESYSTEM_DISK=public — nothing to migrate. Set REGISTRATION_FILESYSTEM_DISK=local first.');

            return self::FAILURE;
        }

        $this->info($dryRun
            ? "Dry run: counting legacy files on public disk (target: {$target})…"
            : "Migrating legacy registration files to disk \"{$target}\"…");

        $stats = $storage->migrateAllLegacyPublicFiles($dryRun);

        $this->table(
            ['Result', 'Count'],
            [
                [$dryRun ? 'Would migrate' : 'Migrated', (string) $stats['migrated']],
                ['Already on target disk', (string) $stats['skipped']],
                ['Missing on all disks', (string) $stats['missing']],
                ['Errors', (string) $stats['errors']],
            ]
        );

        if ($stats['errors'] > 0) {
            return self::FAILURE;
        }

        if (! $dryRun && $stats['migrated'] > 0) {
            $this->info('Done. Legacy copies under storage/app/public/registration/ were removed after copy.');
        }

        return self::SUCCESS;
    }
}
