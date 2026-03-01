<?php

/**
 * Application Configuration
 */

require_once __DIR__ . '/env.php';

return [
    'name' => env('APP_NAME', 'lgmes'),
    'env' => env('APP_ENV', 'development'),
    'url' => env('APP_URL', 'http://localhost/lgmes'),
    'key' => env('APP_KEY', ''),

    // Path configurations
    'paths' => [
        'root' => dirname(__DIR__),
        'app' => dirname(__DIR__) . '/app',
        'public' => dirname(__DIR__) . '/public',
        'views' => dirname(__DIR__) . '/app/views',
    ],
];
