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

if (!is_array($input)) {
    ApiResponse::error('Invalid JSON request body', 400);
}

// Sanitization and basic validation
$username = trim((string) ($input['username'] ?? ''));
$emailInput = trim((string) ($input['email'] ?? ''));
$email = filter_var($emailInput, FILTER_VALIDATE_EMAIL);
$password = (string) ($input['password'] ?? '');

if (empty($username) || !$email || empty($password)) {
    ApiResponse::error('A valid username, email, and password are required', 400);
}

if (strlen($username) > 50) {
    ApiResponse::error('Username must be 50 characters or fewer', 400);
}

if (strlen($password) < 8) {
    ApiResponse::error('Password must be at least 8 characters', 400);
}

try {
    $userId = Auth::register($username, (string) $email, $password);

    if ($userId !== false) {
        $workspace = ProjectService::createDefaultProject($userId, 'My First Website');
        ApiResponse::success([
            'message' => 'User registered and project created',
            'user_id' => $userId,
            'project_id' => $workspace['project_id'],
            'page_id' => $workspace['page_id'],
            'redirect' => '/public/editor.php?id=' . $workspace['page_id']
        ], 201);
    } else {
        ApiResponse::error('Registration failed', 400);
    }
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
