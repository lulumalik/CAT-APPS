<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sitemap.xml', function () {
    $urls = [
        [
            'loc' => url('/'),
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ],
        [
            'loc' => url('/rankings'),
            'changefreq' => 'weekly',
            'priority' => '0.6',
        ],
    ];

    $lastmod = now()->toDateString();

    $escaped = static fn (string $value): string => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

    $items = '';
    foreach ($urls as $url) {
        $items .= '<url>';
        $items .= '<loc>' . $escaped($url['loc']) . '</loc>';
        $items .= '<lastmod>' . $escaped($lastmod) . '</lastmod>';
        $items .= '<changefreq>' . $escaped($url['changefreq']) . '</changefreq>';
        $items .= '<priority>' . $escaped($url['priority']) . '</priority>';
        $items .= '</url>';
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
        . $items
        . '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
});

Route::middleware('role:admin')->group(function () {
    Route::get('/admin/question-banks', function () {
        return response()->json(['ok' => true]);
    });
    Route::get('/admin/tests', function () {
        return response()->json(['ok' => true]);
    });
});

Route::view('/{any}', 'welcome')->where('any', '.*');
