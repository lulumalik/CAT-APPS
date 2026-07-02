<?php

return [
    /*
    | Hierarki kategori peringkat: grup (mis. Jasmani) → subkategori (Sprint, Push Up, …).
    |
    | scoring_mode (alur di kelas):
    |   manual        — nilai diinput manual / tes fisik lapangan (bukan bank soal CAT)
    |   test          — dari tes online (bank soal + Manajemen Tes)
    |   material_only — hanya materi di ruang kelas, tanpa peringkat otomatis
    |
    | source (sumber data otomatis peringkat):
    |   physical = registration_progress.physical_data
    |   academic = test_submissions
    */
    'class_subject_guide' => [
        'jasmani' => 'Gunakan materi kelas untuk panduan latihan. Nilai peringkat diinput manual di halaman ini atau lewat progres pendaftaran — bukan bank soal.',
        // 'psikologi' => 'Materi + tes khusus bila ada; bisa juga input manual untuk tes offline.',
    ],
    'groups' => [
        [
            'id' => 'jasmani',
            'label' => 'Jasmani',
            'scoring_mode' => 'manual',
            'source' => 'physical',
            'subcategories' => [
                ['id' => 'sprint', 'label' => 'Sprint', 'unit' => 'detik', 'sort' => 'asc'],
                ['id' => 'push_up', 'label' => 'Push Up', 'unit' => 'reps', 'sort' => 'desc'],
                ['id' => 'pull_up', 'label' => 'Pull Up', 'unit' => 'reps', 'sort' => 'desc'],
                ['id' => 'sit_up', 'label' => 'Sit Up', 'unit' => 'reps', 'sort' => 'desc'],
                ['id' => 'shuttle_run', 'label' => 'Shuttle Run', 'unit' => 'detik', 'sort' => 'asc'],
                ['id' => 'renang', 'label' => 'Renang', 'unit' => 'detik', 'sort' => 'asc'],
            ],
        ],
        /*
        |--------------------------------------------------------------------------
        | Psikologi — dinonaktifkan sementara
        |--------------------------------------------------------------------------
        [
            'id' => 'psikologi',
            'label' => 'Psikologi',
            'scoring_mode' => 'manual',
            'source' => 'academic',
            'subcategories' => [
                ['id' => 'psikologi_umum', 'label' => 'Psikologi Umum', 'test_categories' => ['Psychology', 'Psikologi']],
            ],
        ],
        */
    ],
];
