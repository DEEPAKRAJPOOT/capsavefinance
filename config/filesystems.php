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

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'temp' => [
            'driver' => 'local',
            'root' => storage_path('temp'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'root' => env('AWS_ROOT'),
            'directory_separator' => '/',
        ],
        'fact_ftp' => [
            'driver' => 'ftp',
            'host' => env('FACT_FTP_HOST'),
            'username' => env('FACT_FTP_USERNAME'),
            'password' => env('FACT_FTP_PASSWORD'),
            'port' => (int) env('FACT_FTP_PORT') ?? 21,
            'root' => env('FACT_FTP_ROOT') ?? '',
            'passive' => env('FACT_FTP_PASSIVE') ?? true,
            'ssl' => env('FACT_FTP_SSL') ?? true,
            'timeout' => env('FACT_FTP_TIMEOUT') ?? 30,
            'ignorePassiveAddress' => env('FACT_FTP_ING_PASSIVE_ADDR') ?? false,
            'utf8' => env('FACT_UTF8') ?? false,
            'transferMode' => env('FACT_TRANSFER_MODE') ?? FTP_BINARY,
            'systemType' => env('FACT_SYSTEM_TYPE') ?? null,
            'timestampsOnUnixListingsEnabled' => env('FACT_TIMESTAMPS_ON_UNIX_LISTINGS_ENABLED') ?? false,
            'recurseManually' => env('FACT_RECURSE_MANUALLY') ?? true
        ],
    ],

];
