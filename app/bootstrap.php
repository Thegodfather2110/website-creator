<?php
// app/bootstrap.php - Master application bootstrap
// Autoloader and core service initialization

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

// 3. URL Helper
function url(string $path = ''): string {
    // Assuming project root is absolute, can be updated via ENV variable later
    return '/' . ltrim($path, '/');
}
