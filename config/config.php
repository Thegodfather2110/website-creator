<?php
// Configuration settings for the Website Creator Engine

return [
    'db' => [
        'host' => getenv('WEBSITE_DB_HOST') ?: '127.0.0.1',
        'port' => getenv('WEBSITE_DB_PORT') ?: '3306',
        'dbname' => getenv('WEBSITE_DB_NAME') ?: 'website_creator',
        'user' => getenv('WEBSITE_DB_USER') ?: 'root',
        'pass' => getenv('WEBSITE_DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    // Add future configurations here (e.g., mailer, API keys)
];
