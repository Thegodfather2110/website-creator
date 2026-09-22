<?php
require_once __DIR__ . '/../../api/init.php';
use App\Core\ApiResponse;
use App\Services\AiService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

// Logic: AI expects a prompt, returns a structured NODE or PAGE
$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

if (empty($prompt)) {
    ApiResponse::error('Prompt required', 400);
}

try {
    // 1. Generate (AI Logic)
    $structure = AiService::generateStructure($prompt);

    // 2. Validate (Section 23 - Validation Boundary)
    // Here we would implement schema validation against our doc model

    ApiResponse::success($structure);
} catch (\Exception $e) {
    ApiResponse::error('AI Generation Failed: ' . $e->getMessage(), 500);
}
