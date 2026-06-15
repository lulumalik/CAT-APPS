<?php

$registrationDiskDefault = env('REGISTRATION_FILESYSTEM_DISK', 'local');

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem disk for onboarding document uploads
    |--------------------------------------------------------------------------
    |
    | Defaults to "local" (storage/app/private — not web-accessible).
    | Files are streamed only via GET /api/registration-files/{user}/{field}
    | (session auth: owner or admin).
    |
    | Use "public" only for legacy setups; prefer "local" or private "s3"/R2.
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
