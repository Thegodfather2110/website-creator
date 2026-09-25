<?php
// app/bootstrap.php - Master application bootstrap

// 1. Load Configuration
$config = require __DIR__ . '/../config/config.php';

// 2. Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 3. URL/Base Path Helper (Section 47)
function url(string $path = ''): string {
    global $config;
    // Using base_url from config, fallback to '/'
    $baseUrl = $config['app']['base_url'] ?? '/';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function apiUrl(string $path = ''): string {
    return url('/api/' . ltrim($path, '/'));
}

// 4. Session Initialization (Centralized - Section 23/Section 55)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
