<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppNotExpired
{
    /**
     * Block expired students from app features except profile and activity history.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'user' || ! $user->isAppExpired()) {
            return $next($request);
        }

        if ($this->isAllowedForExpired($request)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Masa aktif aplikasi Anda telah berakhir. Anda hanya dapat mengakses profil dan riwayat aktivitas.',
            'code' => 'app_expired',
        ], 403);
    }

    private function isAllowedForExpired(Request $request): bool
    {
        if ($request->is('api/user') || $request->is('api/logout')) {
            return true;
        }

        if ($request->is('api/email/verification-notification')) {
            return true;
        }

        if ($request->isMethod('GET') && $request->is('api/my-registration')) {
            return true;
        }

        if ($request->isMethod('GET') && $request->is('api/my-activity-history')) {
            return true;
        }

        return false;
    }
}
