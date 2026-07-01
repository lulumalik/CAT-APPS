<?php

namespace App\Support;

class RegistrationTemplatePath
{
    public static function resolve(): ?string
    {
        $configured = config('registration_forms.template_pdf_path');
        $candidates = array_filter([
            is_string($configured) ? $configured : null,
            resource_path('registration-templates/berkas-pendaftaran-akpol-2026.pdf'),
            storage_path('app/registration-templates/berkas-pendaftaran-akpol-2026.pdf'),
            base_path('berkas_pendaftaran_SELEKSI akpol_2026 - Copy.pdf'),
        ]);

        foreach ($candidates as $path) {
            if ($path !== '' && is_readable($path)) {
                return $path;
            }
        }

        return null;
    }
}
