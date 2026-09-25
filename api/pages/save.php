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

    // Enforce ownership: page must belong to a project owned by the current user
    $stmt = $db->prepare("
        UPDATE pages p
        JOIN projects pr ON pr.id = p.project_id
        SET p.document_json = ?, p.updated_at = NOW()
        WHERE p.id = ? AND pr.user_id = ?
    ");

    if ($stmt->execute([$encoded, (int)$pageId, Auth::getUserId()])) {
        if ($stmt->rowCount() > 0) {
            ApiResponse::success(['message' => 'Page saved successfully', 'id' => (int)$pageId]);
        } else {
            ApiResponse::error('Page not found or unauthorized', 404);
        }
    } else {
        ApiResponse::error('Failed to save page');
    }
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
