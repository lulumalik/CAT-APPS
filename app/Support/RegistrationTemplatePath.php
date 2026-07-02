<?php

namespace App\Support;

class RegistrationTemplatePath
{
    public static function resolve(): ?string
    {
        $configured = config('registration_forms.template_pdf_path');
        $candidates = array_filter([
            is_string($configured) ? $configured : null,
            resource_path('registration-templates/surat_pernyataan_orang_tua.pdf'),
            storage_path('app/registration-templates/surat_pernyataan_orang_tua.pdf'),
        ]);

        foreach ($candidates as $path) {
            if ($path !== '' && is_readable($path)) {
                return $path;
            }
        }

        return null;
    }
}
