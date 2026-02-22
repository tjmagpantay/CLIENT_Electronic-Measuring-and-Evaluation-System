<?php

/**
 * Load environment variables from .env file
 */
function loadEnv($path)
{
    if (!file_exists($path)) {
        die('.env file not found. Please copy .env.example to .env and configure it.');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse key-value pairs
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Set environment variable
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load environment variables
loadEnv(__DIR__ . '/../.env');

/**
 * Get environment variable value
 */
function env($key, $default = null)
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
