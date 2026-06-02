<?php

$registrationDiskDefault = env('REGISTRATION_FILESYSTEM_DISK')
    ?: env('UPLOAD_FILESYSTEM_DISK')
    ?: 'public';

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem disk for onboarding document uploads
    |--------------------------------------------------------------------------
    |
    | REGISTRATION_FILESYSTEM_DISK overrides everything (e.g. "public" for local only).
    | Otherwise UPLOAD_FILESYSTEM_DISK is used if set.
    | Otherwise defaults to "public" (storage:link).
    |
    */

    'filesystem_disk' => $registrationDiskDefault,

    /*
    |--------------------------------------------------------------------------
    | Signed URLs for private object storage
    |--------------------------------------------------------------------------
    |
    | If your bucket is private, set REGISTRATION_USE_SIGNED_URLS=true so
    | browsers receive temporary URLs instead of permanent public URLs.
    |
    */

    'use_signed_urls' => env('REGISTRATION_USE_SIGNED_URLS', false),

    'signed_url_ttl_hours' => (int) env('REGISTRATION_SIGNED_URL_TTL_HOURS', 24),

];
