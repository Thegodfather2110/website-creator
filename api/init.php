<?php
// api/init.php - API Initialization
require_once __DIR__ . '/../app/bootstrap.php';

function sendResponse(bool $success, string $message, array $data = []) {
    header('Content-Type: application/json');
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
