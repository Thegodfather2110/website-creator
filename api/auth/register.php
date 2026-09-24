<?php
require_once __DIR__ . '/../init.php';
use App\Core\Auth;
use App\Core\ApiResponse;
use App\Services\ProjectService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Sanitization and basic validation
$username = isset($input['username']) ? trim($input['username']) : '';
$email = isset($input['email']) ? filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL) : false;
$password = $input['password'] ?? '';

if (empty($username) || !$email || empty($password)) {
    ApiResponse::error('A valid username, email, and password are required', 400);
    exit;
}

try {
    $userId = Auth::register($username, $email, $password);

    if ($userId !== false) {
        // Successful registration, create workspace
        $projectId = ProjectService::createProject($userId, 'My First Website');
        ApiResponse::success([
            'message' => 'User registered and project created',
            'redirect' => '/public/editor.php?id=' . $projectId
        ], 201);
    } else {
        ApiResponse::error('Registration failed', 400);
        exit;
    }
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
    exit;
}
