<?php

namespace Database\Seeders;

use App\Models\ExamCategory;
use App\Models\ExamTrack;
use Illuminate\Database\Seeder;

class ExamCategorySeeder extends Seeder
{
    /**
     * Kategori bisnis CATLab: bahasa & seleksi pendidikan.
     * Idempotent (updateOrCreate berdasarkan slug) sehingga aman dijalankan berulang.
     */
    public function run(): void
    {
        $catalog = [
            [
                'name' => 'Bahasa Jepang',
                'slug' => 'bahasa-jepang',
                'description' => 'Persiapan ujian kemampuan bahasa Jepang untuk studi dan bekerja di Jepang.',
                'icon' => 'languages',
                'sort_order' => 1,
                'tracks' => [
                    [
                        'name' => 'JLPT',
                        'slug' => 'jlpt',
                        'description' => 'Japanese Language Proficiency Test (N5–N1) untuk sertifikasi kemampuan bahasa Jepang.',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'JFT-Basic',
                        'slug' => 'jft',
                        'description' => 'Japan Foundation Test for Basic Japanese, syarat visa Specified Skilled Worker (SSW).',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Bahasa Inggris',
                'slug' => 'bahasa-inggris',
                'description' => 'Persiapan tes bahasa Inggris internasional untuk kuliah dan karier global.',
                'icon' => 'book-open',
                'sort_order' => 2,
                'tracks' => [
                    [
                        'name' => 'IELTS',
                        'slug' => 'ielts',
                        'description' => 'International English Language Testing System untuk studi dan migrasi.',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'TOEFL',
                        'slug' => 'toefl',
                        'description' => 'Test of English as a Foreign Language untuk kebutuhan akademik.',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Seleksi Masuk PTN',
                'slug' => 'seleksi-ptn',
                'description' => 'Persiapan seleksi masuk perguruan tinggi negeri di Indonesia.',
                'icon' => 'graduation-cap',
                'sort_order' => 3,
                'tracks' => [
                    [
                        'name' => 'SNMPTN',
                        'slug' => 'snmptn',
                        'description' => 'Simulasi tes potensi skolastik untuk jalur seleksi nasional berbasis prestasi.',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'SBMPTN',
                        'slug' => 'sbmptn',
                        'description' => 'Simulasi ujian tulis berbasis komputer untuk seleksi bersama masuk PTN.',
                        'sort_order' => 2,
                    ],
                ],
            ],
        ];

        foreach ($catalog as $categoryData) {
            $tracks = $categoryData['tracks'];
            unset($categoryData['tracks']);

            $category = ExamCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData + ['is_active' => true]
            );

            foreach ($tracks as $trackData) {
                ExamTrack::updateOrCreate(
                    ['slug' => $trackData['slug']],
                    $trackData + [
                        'exam_category_id' => $category->id,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
