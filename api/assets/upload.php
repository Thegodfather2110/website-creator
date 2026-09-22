<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Core\Auth;
use App\Services\AssetService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

if (!Auth::isLoggedIn()) {
    ApiResponse::error('Unauthorized', 401);
}

if (!isset($_FILES['file']) || !isset($_POST['project_id'])) {
    ApiResponse::error('File and project ID required', 400);
}

$result = AssetService::uploadAsset((int)$_POST['project_id'], $_FILES['file']);
if ($result) {
    ApiResponse::success($result);
} else {
    ApiResponse::error('Upload failed');
}
