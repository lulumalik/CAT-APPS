<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

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
        [
            'loc' => url('/blog'),
            'changefreq' => 'daily',
            'priority' => '0.8',
        ],
    ];

    $materials = \App\Models\Material::where('status', 'published')->get();
    foreach ($materials as $material) {
        $urls[] = [
            'loc' => url('/blog/' . $material->slug),
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ];
    }

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

Route::get('/blog/{slug}', function ($slug) {
    $material = \App\Models\Material::where('slug', $slug)->where('status', 'published')->first();
    return view('welcome', ['material' => $material]);
});

/*
| Serve files under /storage/* from disk (not the SPA). The catch-all below would
| otherwise return the Vue shell for URLs like /storage/registration/1/file.jpg.
| Registration objects live on registration.filesystem_disk; other public files use "public".
*/
Route::get('/storage/{path}', function (string $path) {
    $normalized = ltrim(str_replace('\\', '/', $path), '/');
    if ($normalized === '' || str_contains($normalized, '..')) {
        abort(404);
    }

    $diskName = str_starts_with($normalized, 'registration/')
        ? (string) config('registration.filesystem_disk')
        : 'public';

    $disk = Storage::disk($diskName);
    if (! $disk->exists($normalized)) {
        abort(404);
    }

    return $disk->response($normalized);
})->where('path', '.*');

Route::get('/email/verify/{id}/{hash}', function (Request $request, string $id, string $hash) {
    if (! $request->hasValidSignature()) {
        abort(403, 'Tautan verifikasi tidak valid atau sudah kedaluwarsa.');
    }

    $user = User::findOrFail($id);
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Hash verifikasi email tidak cocok.');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return redirect('/login?verified=1');
})->name('verification.verify');

Route::view('/{any}', 'welcome')->where('any', '^(?!storage(?:$|/)).*');
