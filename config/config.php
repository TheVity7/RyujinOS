<?php

declare(strict_types=1);

/**
 * Builds the application configuration array from environment variables
 * loaded out of the project root ".env" file.
 */

return [
    'app' => [
        'name'     => env('APP_NAME', 'RyujinOS'),
        'env'      => env('APP_ENV', 'production'),
        'debug'    => env_bool('APP_DEBUG', false),
        'url'      => rtrim((string) env('APP_URL', 'http://localhost:8000'), '/'),
        'key'      => env('APP_KEY', ''),
        'locale'   => env('APP_LOCALE', 'tr'),
        'currency' => env('APP_CURRENCY', 'TL'),
        'timezone' => env('APP_TIMEZONE', 'Europe/Istanbul'),
    ],

    'db' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'host'       => env('DB_HOST', '127.0.0.1'),
        'port'       => (int) env('DB_PORT', '3306'),
        'database'   => env('DB_DATABASE', 'ryujinos'),
        'username'   => env('DB_USERNAME', 'root'),
        'password'   => env('DB_PASSWORD', ''),
        'sqlite'     => env('DB_SQLITE_PATH', 'storage/ryujinos.sqlite'),
    ],

    'auth' => [
        'integration' => env('AUTH_INTEGRATION', 'native'),
        'table'       => env('AUTHME_TABLE', 'authme'),
        'columns'     => [
            'name'      => env('AUTHME_COL_NAME', 'username'),
            'password'  => env('AUTHME_COL_PASSWORD', 'password'),
            'email'     => env('AUTHME_COL_EMAIL', 'email'),
            'ip'        => env('AUTHME_COL_IP', 'ip'),
            'lastlogin' => env('AUTHME_COL_LASTLOGIN', 'lastlogin'),
            'regdate'   => env('AUTHME_COL_REGDATE', 'regdate'),
        ],
        'hash' => strtoupper((string) env('AUTHME_HASH', 'SHA256')),
    ],

    'minecraft' => [
        'name'     => env('MC_SERVER_NAME', 'play.ryujinos.net'),
        'host'     => env('MC_SERVER_HOST', '127.0.0.1'),
        'port'     => (int) env('MC_SERVER_PORT', '25565'),
        'rcon_host'     => env('RCON_HOST', '127.0.0.1'),
        'rcon_port'     => (int) env('RCON_PORT', '25575'),
        'rcon_password' => env('RCON_PASSWORD', ''),
    ],

    'shopier' => [
        'api_user'   => env('SHOPIER_API_USER', ''),
        'api_key'    => env('SHOPIER_API_KEY', ''),
        'api_secret' => env('SHOPIER_API_SECRET', ''),
        'test_mode'  => env_bool('SHOPIER_TEST_MODE', true),
        'credit_rate' => (float) env('CREDIT_RATE', '1'),
    ],
];
