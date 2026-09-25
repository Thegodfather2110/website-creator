<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Core\Auth;
use App\Core\Database;
use PDO;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

if (!Auth::isLoggedIn()) {
    ApiResponse::error('Unauthorized', 401);
}

$pageId = $_GET['id'] ?? null;
if (!$pageId) {
    ApiResponse::error('Page ID is required', 400);
}

try {
    $db = Database::getConnection();
    // Validate ownership: page must belong to a project owned by the current user
    $stmt = $db->prepare("
        SELECT p.*
        FROM pages p
        JOIN projects pr ON p.project_id = pr.id
        WHERE p.id = ? AND pr.user_id = ?
    ");
    $stmt->execute([(int)$pageId, (int)Auth::getUserId()]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$page) {
        ApiResponse::error('Page not found or unauthorized', 404);
    }

    if (!empty($page['document_json'])) {
        $decoded = json_decode($page['document_json'], true);
        $page['document_json'] = $decoded !== null ? $decoded : $page['document_json'];
    }

    ApiResponse::success($page);
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
