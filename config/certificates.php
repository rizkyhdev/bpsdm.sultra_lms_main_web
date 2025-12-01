<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Certificate Background Image
    |--------------------------------------------------------------------------
    |
    | Path to the background image for certificates. This should be a path
    | relative to the public directory or a full URL. Set via CERT_BG env var.
    |
    */
    'background_image' => env('CERT_BG', null),

    /*
    |--------------------------------------------------------------------------
    | Certificate Issuer Name
    |--------------------------------------------------------------------------
    |
    | The name of the organization issuing the certificate. Defaults to app name.
    | Set via CERT_ISSUER_NAME env var.
    |
    */
    'issuer_name' => env('CERT_ISSUER_NAME', config('app.name')),

    /*
    |--------------------------------------------------------------------------
    | Download URL TTL
    |--------------------------------------------------------------------------
    |
    | The number of minutes that a signed download URL is valid for.
    | Default is 30 minutes.
    |
    */
    'download_ttl_minutes' => env('CERT_DOWNLOAD_TTL', 30),

    /*
    |--------------------------------------------------------------------------
    | Certificate Storage Disk
    |--------------------------------------------------------------------------
    |
    | The storage disk to use for storing certificate PDFs.
    |
    */
    'storage_disk' => env('CERT_STORAGE_DISK', 'local'),
];

