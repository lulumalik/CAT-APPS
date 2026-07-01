<?php

namespace App\Services;

use App\Models\RegistrationProgress;
use App\Models\User;
use App\Support\RegistrationFormField;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class RegistrationFormPdfService
{
    /**
     * @return array<string, mixed>
     */
    public function buildContext(User $user, ?RegistrationProgress $progress = null): array
    {
        $admin = $progress?->administration_data ?? [];
        $addressDomicile = $admin['address_domicile'] ?? $admin['address'] ?? '';
        $addressKk = $admin['address_kk'] ?? $admin['address'] ?? '';
        $domicileLines = RegistrationFormField::addressLines($addressDomicile, 3);
        $kkLines = RegistrationFormField::addressLines($addressKk, 3);
        $parentLines = RegistrationFormField::addressLines($admin['parent_address'] ?? '', 3);

        $gender = (string) ($admin['gender'] ?? '');
        $genderLabel = match ($gender) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '',
        };
        $genderShort = match ($gender) {
            'L' => 'Pria',
            'P' => 'Wanita',
            default => '',
        };

        $birthDateRaw = (string) ($admin['birth_date'] ?? '');
        $parentBirthRaw = (string) ($admin['parent_birth_date'] ?? '');
        $participantBirthRaw = trim((string) ($admin['birth_place'] ?? ''));
        $participantBirthDate = RegistrationFormField::formatDate($birthDateRaw);
        $birthPlaceDate = trim($participantBirthRaw.($participantBirthRaw && $participantBirthDate ? ', ' : '').$participantBirthDate);

        $now = now('Asia/Jakarta');

        return [
            'institution' => config('registration_forms.institution'),
            'course' => config('registration_forms.course'),
            'printed_at' => $now->locale('id')->translatedFormat('d F Y H:i'),
            'fields' => [
                'name' => (string) ($admin['full_name'] ?? $user->name ?? ''),
                'email' => (string) ($user->email ?? ''),
                'birth_place' => (string) ($admin['birth_place'] ?? ''),
                'birth_date' => $birthDateRaw,
                'birth_date_formatted' => $participantBirthDate,
                'birth_place_date' => $birthPlaceDate,
                'religion' => (string) ($admin['religion'] ?? ''),
                'ethnicity' => (string) ($admin['ethnicity'] ?? ''),
                'education' => (string) ($admin['education'] ?? ''),
                'occupation' => (string) ($admin['occupation'] ?? ''),
                'nik' => (string) ($admin['nik'] ?? ''),
                'postal_code' => (string) ($admin['postal_code'] ?? ''),
                'city' => (string) ($admin['city'] ?? config('registration_forms.institution.city')),
                'whatsapp' => RegistrationFormField::formatPhone($admin['whatsapp'] ?? ''),
                'phone' => RegistrationFormField::formatPhone($admin['phone'] ?? ''),
                'address_kk' => $addressKk,
                'address_domicile' => $addressDomicile,
                'address_kk_lines' => $kkLines,
                'address_domicile_lines' => $domicileLines,
                'gender' => $gender,
                'gender_label' => $genderLabel,
                'gender_short' => $genderShort,
                'height_cm' => $admin['height_cm'] ?? '',
                'weight_kg' => $admin['weight_kg'] ?? '',
                'marital_status' => (string) ($admin['marital_status'] ?? 'Belum kawin'),
                'participant_number' => (string) ($admin['participant_number'] ?? str_pad((string) $user->id, 6, '0', STR_PAD_LEFT)),
                'parent_name' => (string) ($admin['parent_name'] ?? ''),
                'parent_birth_place' => (string) ($admin['parent_birth_place'] ?? ''),
                'parent_birth_date' => $parentBirthRaw,
                'parent_birth_date_formatted' => RegistrationFormField::formatDate($parentBirthRaw),
                'parent_birth_place_date' => trim(
                    ($admin['parent_birth_place'] ?? '').(
                        ($admin['parent_birth_place'] ?? '') && RegistrationFormField::formatDate($parentBirthRaw) ? ', ' : ''
                    ).RegistrationFormField::formatDate($parentBirthRaw)
                ),
                'parent_occupation' => (string) ($admin['parent_occupation'] ?? ''),
                'parent_address' => (string) ($admin['parent_address'] ?? ''),
                'parent_address_lines' => $parentLines,
                'parent_relationship' => (string) ($admin['parent_relationship'] ?? ''),
                'father_name' => (string) ($admin['father_name'] ?? ''),
                'father_birth' => (string) ($admin['father_birth'] ?? ''),
                'father_occupation' => (string) ($admin['father_occupation'] ?? ''),
                'father_address' => (string) ($admin['father_address'] ?? ''),
                'mother_name' => (string) ($admin['mother_name'] ?? ''),
                'mother_birth' => (string) ($admin['mother_birth'] ?? ''),
                'mother_occupation' => (string) ($admin['mother_occupation'] ?? ''),
                'mother_address' => (string) ($admin['mother_address'] ?? ''),
                'education_sd' => (string) ($admin['education_sd'] ?? ''),
                'education_smp' => (string) ($admin['education_smp'] ?? ''),
                'education_sma' => (string) ($admin['education_sma'] ?? ''),
                'education_pt' => (string) ($admin['education_pt'] ?? ''),
                'hair' => (string) ($admin['hair'] ?? ''),
                'eyes' => (string) ($admin['eyes'] ?? ''),
                'other_traits' => (string) ($admin['other_traits'] ?? ''),
                'blood_type' => (string) ($admin['blood_type'] ?? ''),
                'document_date_city' => (string) ($admin['document_date_city'] ?? $admin['city'] ?? ''),
                'document_date_day' => (string) ($admin['document_date_day'] ?? (string) $now->day),
                'document_date_month' => (string) ($admin['document_date_month'] ?? $now->locale('id')->translatedFormat('F')),
            ],
            'line' => fn (?string $value, int $minDots = 36): string => RegistrationFormField::line($value, $minDots),
        ];
    }

    /**
     * @return list<array{number:int,slug:string,title:string}>
     */
    public function pageCatalog(): array
    {
        return array_map(
            fn (array $page) => [
                'number' => $page['number'],
                'slug' => $page['slug'],
                'title' => $page['title'],
            ],
            config('registration_forms.pages', [])
        );
    }

    public function findPageBySlug(string $slug): ?array
    {
        foreach (config('registration_forms.pages', []) as $page) {
            if ($page['slug'] === $slug) {
                return $page;
            }
        }

        return null;
    }

    public function makePdf(User $user, ?RegistrationProgress $progress = null, ?string $slug = null)
    {
        $context = $this->buildContext($user, $progress);

        if ($slug !== null) {
            $page = $this->findPageBySlug($slug);
            if ($page === null) {
                abort(404);
            }
            $context['pages'] = [$page];
        } else {
            $context['pages'] = config('registration_forms.pages', []);
        }

        return Pdf::loadView('pdf.registration-forms.combined', $context)
            ->setPaper('a4', 'portrait');
    }

    public function downloadFilename(User $user, ?string $slug = null): string
    {
        $name = Str::slug($user->name ?: 'peserta');
        if ($slug !== null) {
            return "berkas-{$slug}-{$name}.pdf";
        }

        return "berkas-pendaftaran-akpol-{$name}.pdf";
    }
}
