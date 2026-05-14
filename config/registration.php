<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem disk for onboarding document uploads
    |--------------------------------------------------------------------------
    |
    | Use "public" for local development (php artisan storage:link).
    | On Laravel Cloud, attach Object Storage and set this to "s3" (or the
    | disk name you configured). Laravel Cloud injects AWS_* env vars.
    |
    */

    'filesystem_disk' => env('REGISTRATION_FILESYSTEM_DISK', 'public'),

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
