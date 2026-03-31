<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        if (config('nativephp-internal.running')) {
            $seedMarkerPath = storage_path('app/nativephp.seeded');

            if (! is_file($seedMarkerPath)) {
                Artisan::call('db:seed', ['--force' => true]);

                if (! is_dir(dirname($seedMarkerPath))) {
                    mkdir(dirname($seedMarkerPath), 0755, true);
                }

                file_put_contents($seedMarkerPath, date('c'));
            }
        }

        Window::open()
            ->width(1280)
            ->height(720)
            ->minWidth(1280)
            ->minHeight(720)
            ->rememberState();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
