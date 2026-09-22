<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Services\TemplateService;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    $templates = TemplateService::listTemplates();
    ApiResponse::success($templates);
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
