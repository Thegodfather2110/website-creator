<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Core\Auth;
use App\Services\CommentService;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $pageId = $_GET['page_id'] ?? null;
    $comments = CommentService::getComments((int)$pageId);
    ApiResponse::success($comments);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::isLoggedIn()) ApiResponse::error('Unauthorized', 401);

    $input = json_decode(file_get_contents('php://input'), true);
    $comment = CommentService::addComment((int)$input['page_id'], $_SESSION['user_id'], $input['node_id'], $input['content']);
    ApiResponse::success(['message' => 'Comment added']);
}
