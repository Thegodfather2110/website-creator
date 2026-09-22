<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Core\Database;
use PDO;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

$pageId = $_GET['id'] ?? null;
if (!$pageId) {
    ApiResponse::error('Page ID is required', 400);
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([(int)$pageId]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$page) {
        ApiResponse::error('Page not found', 404);
    }

    if (!empty($page['document_json'])) {
        $decoded = json_decode($page['document_json'], true);
        $page['document_json'] = $decoded !== null ? $decoded : $page['document_json'];
    }

    ApiResponse::success($page);
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
