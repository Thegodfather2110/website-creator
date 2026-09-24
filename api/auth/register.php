<?php
require_once __DIR__ . '/../init.php';
use App\Core\Auth;
use App\Core\ApiResponse;
use App\Services\ProjectService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    ApiResponse::error('Username, email, and password are required', 400);
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
        ApiResponse::error('Registration failed');
    }
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
