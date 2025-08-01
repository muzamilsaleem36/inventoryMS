<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/uploads'),
            'url' => env('APP_URL').'/storage/uploads',
            'visibility' => 'public',
            'throw' => false,
        ],

        'products' => [
            'driver' => 'local',
            'root' => storage_path('app/public/products'),
            'url' => env('APP_URL').'/storage/products',
            'visibility' => 'public',
            'throw' => false,
        ],

        'logos' => [
            'driver' => 'local',
            'root' => storage_path('app/public/logos'),
            'url' => env('APP_URL').'/storage/logos',
            'visibility' => 'public',
            'throw' => false,
        ],

        'receipts' => [
            'driver' => 'local',
            'root' => storage_path('app/receipts'),
            'visibility' => 'private',
            'throw' => false,
        ],

        'reports' => [
            'driver' => 'local',
            'root' => storage_path('app/reports'),
            'visibility' => 'private',
            'throw' => false,
        ],

        'backups' => [
            'driver' => 'local',
            'root' => storage_path('app/backups'),
            'visibility' => 'private',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        'ftp' => [
            'driver' => 'ftp',
            'host' => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            'port' => env('FTP_PORT', 21),
            'root' => env('FTP_ROOT'),
            'passive' => true,
            'ssl' => env('FTP_SSL', false),
            'timeout' => 30,
            'throw' => false,
        ],

        'sftp' => [
            'driver' => 'sftp',
            'host' => env('SFTP_HOST'),
            'username' => env('SFTP_USERNAME'),
            'password' => env('SFTP_PASSWORD'),
            'privateKey' => env('SFTP_PRIVATE_KEY'),
            'passphrase' => env('SFTP_PASSPHRASE'),
            'port' => env('SFTP_PORT', 22),
            'root' => env('SFTP_ROOT'),
            'timeout' => 30,
            'throw' => false,
        ],

        'google' => [
            'driver' => 'google',
            'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
            'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'folderId' => env('GOOGLE_DRIVE_FOLDER_ID'),
            'throw' => false,
        ],

        'dropbox' => [
            'driver' => 'dropbox',
            'authorization_token' => env('DROPBOX_AUTHORIZATION_TOKEN'),
            'throw' => false,
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
        public_path('uploads') => storage_path('app/uploads'),
        public_path('products') => storage_path('app/public/products'),
        public_path('logos') => storage_path('app/public/logos'),
    ],

    /*
    |--------------------------------------------------------------------------
    | POS System File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for file uploads specific to the POS system
    |
    */

    'pos_settings' => [
        'max_file_size' => env('POS_MAX_FILE_SIZE', 5242880), // 5MB in bytes
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'allowed_document_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'image_quality' => env('POS_IMAGE_QUALITY', 85),
        'thumbnail_size' => env('POS_THUMBNAIL_SIZE', 150),
        'product_image_size' => env('POS_PRODUCT_IMAGE_SIZE', 800),
        'logo_max_size' => env('POS_LOGO_MAX_SIZE', 1048576), // 1MB in bytes
    ],
]; 