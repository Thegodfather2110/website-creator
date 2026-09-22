<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Core\Auth;
use App\Services\PageService;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

if (!Auth::isLoggedIn()) {
    ApiResponse::error('Unauthorized', 401);
}

$projectId = $_GET['project_id'] ?? null;

if (!$projectId) {
    ApiResponse::error('Project ID is required', 400);
}

try {
    $pages = PageService::getPagesForProject((int)$projectId);
    ApiResponse::success($pages);
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
