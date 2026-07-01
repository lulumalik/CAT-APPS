<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class RegistrationFormField
{
    public static function line(?string $value, int $minDots = 36): string
    {
        $trimmed = trim((string) $value);
        if ($trimmed !== '') {
            return $trimmed;
        }

        return str_repeat('.', $minDots);
    }

    public static function formatDate(?string $value): string
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return '';
        }

        try {
            return Carbon::parse($trimmed, 'Asia/Jakarta')->locale('id')->translatedFormat('d F Y');
        } catch (\Throwable) {
            return $trimmed;
        }
    }

    public static function formatPhone(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';
        if ($digits === '') {
            return '';
        }
        if (str_starts_with($digits, '62')) {
            return '0'.substr($digits, 2);
        }

        return $digits;
    }

    /**
     * @return list<string>
     */
    public static function addressLines(?string $value, int $lines = 3): array
    {
        $chunks = preg_split("/\r\n|\n|\r/", trim((string) $value)) ?: [];
        $chunks = array_values(array_filter(array_map('trim', $chunks), fn ($l) => $l !== ''));

        $result = [];
        for ($i = 0; $i < $lines; $i++) {
            $result[] = $chunks[$i] ?? '';
        }

        return $result;
    }
}
