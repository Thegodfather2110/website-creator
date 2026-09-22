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
if (empty($input)) {
    $input = $_POST;
}

$pageId = $input['id'] ?? $input['page_id'] ?? null;
$documentJson = $input['document_json'] ?? $input['document'] ?? null;

if (!$pageId || $documentJson === null) {
    ApiResponse::error('Page ID and document JSON are required', 400);
}

try {
    $db = Database::getConnection();
    $encoded = is_string($documentJson) ? $documentJson : json_encode($documentJson);
    $stmt = $db->prepare("UPDATE pages SET document_json = ?, updated_at = NOW() WHERE id = ?");

    if ($stmt->execute([$encoded, (int)$pageId])) {
        ApiResponse::success(['message' => 'Page saved successfully', 'id' => (int)$pageId]);
    } else {
        ApiResponse::error('Failed to save page');
    }
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
