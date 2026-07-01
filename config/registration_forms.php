<?php

return [

    'institution' => [
        'name' => 'Lembaga Kursus Pratistha Cendekia Prestasi',
        'city' => 'Bandung',
        'director_title' => 'DIREKTUR LEMBAGA KURSUS PRATISTHA CENDEKIA PRESTASI',
    ],

    'course' => [
        'selection_year' => 2027,
        'statement_year' => 2026,
        'announcement_number' => 'Peng/- /VI/2026',
        'announcement_month' => 'Juni',
    ],

    /*
    |--------------------------------------------------------------------------
    | 17 halaman berkas (sesuai PDF sumber)
    |--------------------------------------------------------------------------
    */
    'pages' => [
        ['number' => 1, 'slug' => 'petunjuk', 'title' => 'Petunjuk Berkas Pendaftaran', 'view' => 'pdf.registration-forms.pages.01-petunjuk'],
        ['number' => 2, 'slug' => 'permohonan', 'title' => 'Permohonan Mengikuti Kursus', 'view' => 'pdf.registration-forms.pages.02-permohonan'],
        ['number' => 3, 'slug' => 'persetujuan-orang-tua', 'title' => 'Surat Persetujuan Orang Tua / Wali', 'view' => 'pdf.registration-forms.pages.03-persetujuan-orang-tua'],
        ['number' => 4, 'slug' => 'pernyataan-belum-menikah', 'title' => 'Surat Pernyataan Belum Pernah Menikah', 'view' => 'pdf.registration-forms.pages.04-pernyataan-belum-menikah'],
        ['number' => 5, 'slug' => 'riwayat-hidup-1', 'title' => 'Daftar Riwayat Hidup (1)', 'view' => 'pdf.registration-forms.pages.05-riwayat-hidup-1'],
        ['number' => 6, 'slug' => 'riwayat-hidup-2', 'title' => 'Daftar Riwayat Hidup (2)', 'view' => 'pdf.registration-forms.pages.06-riwayat-hidup-2'],
        ['number' => 7, 'slug' => 'riwayat-hidup-3', 'title' => 'Daftar Riwayat Hidup (3)', 'view' => 'pdf.registration-forms.pages.07-riwayat-hidup-3'],
        ['number' => 8, 'slug' => 'halaman-kosong', 'title' => 'Halaman Kosong', 'view' => 'pdf.registration-forms.pages.08-kosong'],
        ['number' => 9, 'slug' => 'pernyataan-tidak-terikat', 'title' => 'Surat Pernyataan Tidak Terikat Perjanjian', 'view' => 'pdf.registration-forms.pages.09-pernyataan-tidak-terikat'],
        ['number' => 10, 'slug' => 'pernyataan-kkn', 'title' => 'Surat Pernyataan Kejujuran / KKN', 'view' => 'pdf.registration-forms.pages.10-pernyataan-kkn'],
        ['number' => 11, 'slug' => 'pernyataan-norma', 'title' => 'Surat Pernyataan Norma Agama & Kesusilaan', 'view' => 'pdf.registration-forms.pages.11-pernyataan-norma'],
        ['number' => 12, 'slug' => 'pernyataan-pancasila', 'title' => 'Surat Pernyataan Pancasila & NKRI', 'view' => 'pdf.registration-forms.pages.12-pernyataan-pancasila'],
        ['number' => 13, 'slug' => 'pernyataan-tidak-mundur', 'title' => 'Surat Pernyataan Tidak Mengundurkan Diri', 'view' => 'pdf.registration-forms.pages.13-pernyataan-tidak-mundur'],
        ['number' => 14, 'slug' => 'pemeriksaan-asli-1', 'title' => 'Pemeriksaan Berkas Asli (1)', 'view' => 'pdf.registration-forms.pages.14-pemeriksaan-asli-1'],
        ['number' => 15, 'slug' => 'pemeriksaan-asli-2', 'title' => 'Pemeriksaan Berkas Asli (2)', 'view' => 'pdf.registration-forms.pages.15-pemeriksaan-asli-2'],
        ['number' => 16, 'slug' => 'pemeriksaan-fotocopy-1', 'title' => 'Pemeriksaan Fotocopy/Legalisir (1)', 'view' => 'pdf.registration-forms.pages.16-pemeriksaan-fotocopy-1'],
        ['number' => 17, 'slug' => 'pemeriksaan-fotocopy-2', 'title' => 'Pemeriksaan Fotocopy/Legalisir (2)', 'view' => 'pdf.registration-forms.pages.17-pemeriksaan-fotocopy-2'],
    ],

];
