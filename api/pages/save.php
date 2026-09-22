<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Core\Auth;
use App\Core\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

if (!Auth::isLoggedIn()) {
    ApiResponse::error('Unauthorized', 401);
}

$input = json_decode(file_get_contents('php://input'), true);
$pageId = $input['id'] ?? null;
$documentJson = $input['document_json'] ?? null;

if (!$pageId || !$documentJson) {
    ApiResponse::error('Page ID and document JSON are required', 400);
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare("UPDATE pages SET document_json = ? WHERE id = ?");
    if ($stmt->execute([json_encode($documentJson), $pageId])) {
        ApiResponse::success(['message' => 'Page saved successfully']);
    } else {
        ApiResponse::error('Failed to save page');
    }
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
