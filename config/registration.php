<?php

$registrationDiskDefault = env('REGISTRATION_FILESYSTEM_DISK')
    ?: env('UPLOAD_FILESYSTEM_DISK')
    ?: ((
        filled(env('AWS_ACCESS_KEY_ID'))
        && filled(env('AWS_SECRET_ACCESS_KEY'))
        && filled(env('AWS_BUCKET'))
        && filled(env('AWS_ENDPOINT'))
    ) ? 's3' : 'public');

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem disk for onboarding document uploads
    |--------------------------------------------------------------------------
    |
    | REGISTRATION_FILESYSTEM_DISK overrides everything (e.g. "public" for local only).
    | Otherwise UPLOAD_FILESYSTEM_DISK is used if set.
    | Otherwise: S3/R2 when AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_BUCKET, and
    | AWS_ENDPOINT are set; else "public" (storage:link).
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
