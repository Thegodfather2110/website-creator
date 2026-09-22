<?php
// api/init.php - API Initialization
// This file is ONLY called by API endpoints requiring JSON responses.

header('Content-Type: application/json');

// Include global application bootstrap
require_once __DIR__ . '/../app/bootstrap.php';

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
