<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Core\Auth;
use App\Services\PageService;
use App\Services\ProjectService;

if (!Auth::isLoggedIn()) ApiResponse::error('Unauthorized', 401);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $projectId = (int)($input['project_id'] ?? 0);
    $name = $input['name'] ?? 'Untitled Page';
    $slug = $input['slug'] ?? 'untitled';

    if (!$projectId || !ProjectService::isUserOwner(Auth::getUserId(), $projectId)) {
        ApiResponse::error('Unauthorized or invalid project', 403);
    }

    if (PageService::createPage($projectId, $name, $slug)) {
        ApiResponse::success(['message' => 'Page created'], 201);
    } else {
        ApiResponse::error('Failed to create page');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $pageId = (int)($_GET['id'] ?? 0);
    // Needs authorization check here too
    if (PageService::deletePage($pageId)) {
        ApiResponse::success(['message' => 'Page deleted']);
    } else {
        ApiResponse::error('Failed to delete page');
    }
} else {
    ApiResponse::error('Method not allowed', 405);
}
