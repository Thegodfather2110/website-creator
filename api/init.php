<?php
// Common API initialization for JSON responses
header('Content-Type: application/json');

// Autoloader/Setup (assuming simple approach for now)
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Auth.php';
require_once __DIR__ . '/../app/Core/ApiResponse.php';

function sendResponse(bool $success, string $message, array $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function getJsonInput(): array {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}
