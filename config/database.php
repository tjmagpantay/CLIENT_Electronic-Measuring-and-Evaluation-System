<?php

/**
 * Database Configuration
 * This file loads database settings from environment variables
 */

require_once __DIR__ . '/env.php';

return [
    'host' => env('DB_HOST', 'localhost'),
    'database' => env('DB_NAME', 'e_mes_db'),
    'username' => env('DB_USER', 'root'),
    'password' => env('DB_PASS', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
