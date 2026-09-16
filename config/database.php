<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | الاتصال الافتراضي بقاعدة البيانات
    |--------------------------------------------------------------------------
    |
    | قاعدة إنتاج هذا النظام هي Oracle (اتصال "oracle" أدناه عبر حزمة
    | yajra/laravel-oci8). تم توفير اتصال "sqlite" بديل للتجربة السريعة على
    | أي بيئة لا تتوفر بها عميل Oracle (OCI8 / Instant Client) فقط.
    |
    */

    'default' => env('DB_CONNECTION', 'oracle'),

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ],

        'oracle' => [
            'driver' => 'oracle',
            'tns' => env('DB_TNS', ''),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1521'),
            'database' => env('DB_DATABASE', ''),
            'service_name' => env('DB_SERVICE_NAME', env('DB_DATABASE', '')),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'AL32UTF8'),
            'prefix' => env('DB_PREFIX', ''),
            'prefix_schema' => env('DB_SCHEMA_PREFIX', ''),
            'edition' => env('DB_EDITION', 'ora$base'),
            'server_version' => env('DB_SERVER_VERSION', '11g'),
            'load_balance' => env('DB_LOAD_BALANCE', 'yes'),
            'dynamic' => [],
        ],


    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
