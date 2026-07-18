<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($material) ? $material->title : 'CATLab - Simulasi Computer Assisted Test (CAT)' }}</title>
        <meta name="google-site-verification" content="oHVVXwQv4qB7m3tjr5G0EWAbiVw22JVmC7MLqe0hcFQ" />
        <meta name="description" content="{{ isset($material) ? Str::limit(strip_tags($material->content), 160) : 'Coba kemampuanmu lewat free tryout CAT untuk JLPT, TOEFL, SNMPTN, SBMPTN, JFT, dan ujian lain. Kerjakan simulasi, lihat hasil, kirim feedback.' }}">
        <meta name="keywords" content="CAT, Computer Assisted Test, ujian online, tryout, JLPT, TOEFL, SNMPTN, SBMPTN, JFT, bank soal{{ isset($material) && $material->category ? ', ' . $material->category : '' }}">
        <meta name="robots" content="index,follow">
        <meta name="theme-color" content="#2563eb">
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="icon" type="image/x-icon" href="{{ Vite::asset('resources/assets/favicon_io/favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ Vite::asset('resources/assets/favicon_io/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ Vite::asset('resources/assets/favicon_io/favicon-16x16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/assets/favicon_io/apple-touch-icon.png') }}">
        <meta property="og:type" content="{{ isset($material) ? 'article' : 'website' }}">
        <meta property="og:site_name" content="CATLab">
        <meta property="og:title" content="{{ isset($material) ? $material->title : 'CATLab - Simulasi Computer Assisted Test (CAT)' }}">
        <meta property="og:description" content="{{ isset($material) ? Str::limit(strip_tags($material->content), 160) : 'Coba kemampuanmu lewat free tryout CAT untuk JLPT, TOEFL, SNMPTN, SBMPTN, JFT, dan ujian lain. Kerjakan simulasi, lihat hasil, kirim feedback.' }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ Vite::asset('resources/assets/favicon_io/android-chrome-512x512.png') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ isset($material) ? $material->title : 'CATLab - Simulasi Computer Assisted Test (CAT)' }}">
        <meta name="twitter:description" content="{{ isset($material) ? Str::limit(strip_tags($material->content), 160) : 'Coba kemampuanmu lewat free tryout CAT untuk JLPT, TOEFL, SNMPTN, SBMPTN, JFT, dan ujian lain. Kerjakan simulasi, lihat hasil, kirim feedback.' }}">
        <meta name="google-site-verification" content="fSlJFrpkUUiF_T03wYZONezqDP06ii5bmVkNgF4z5tc" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; background: #f8fafc; color: #0f172a; }
            </style>
        @endif

        <style>
            html, body {
                font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            }
        </style>
    </head>
    <body class="text-text bg-background min-h-screen">
        <div id="app"></div>
        <noscript>
            <main>
                <h1>CATLab</h1>
                <p>Simulasi Computer Assisted Test (CAT) untuk latihan kemampuan ujian seperti JLPT, TOEFL, SNMPTN, SBMPTN, dan JFT.</p>
                <ul>
                    <li>Free tryout berbasis waktu</li>
                    <li>Hasil langsung setelah selesai</li>
                    <li>Kirim feedback untuk pengembangan platform</li>
                </ul>
                <p>
                    <a href="{{ url('/free-tryout') }}">Free Tryout</a>
                    ·
                    <a href="{{ url('/login') }}">Masuk</a>
                </p>
            </main>
        </noscript>
    </body>
</html>
