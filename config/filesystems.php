<?php

/*
| Cloudflare R2 and some S3-compatible APIs accept AWS_DEFAULT_REGION=auto in .env,
| but the AWS SDK expects a concrete SigV4 region string. We map "auto" / empty to us-east-1
| when a custom endpoint is set (R2 ignores this for routing).
*/
$s3Region = (string) env('AWS_DEFAULT_REGION', 'us-east-1');
if (filled(env('AWS_ENDPOINT')) && in_array(strtolower($s3Region), ['auto', ''], true)) {
    $s3Region = 'us-east-1';
}

$uploadDiskDefault = (filled(env('AWS_ACCESS_KEY_ID'))
    && filled(env('AWS_SECRET_ACCESS_KEY'))
    && filled(env('AWS_BUCKET'))
    && filled(env('AWS_ENDPOINT')))
    ? 's3'
    : 'public';

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Disk for app uploads (questions, etc.)
    |--------------------------------------------------------------------------
    |
    | Defaults to the S3-compatible disk when AWS_* is fully configured, else
    | "public" (local storage/app/public + storage:link). Override with UPLOAD_FILESYSTEM_DISK.
    |
    */

    'upload_disk' => env('UPLOAD_FILESYSTEM_DISK', $uploadDiskDefault),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => $s3Region,
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
