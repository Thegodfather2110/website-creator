<?php
require_once __DIR__ . '/../init.php';
use App\Core\Auth;
use App\Core\ApiResponse;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    ApiResponse::error('Username, email, and password are required');
}

try {
    if (Auth::register($username, $email, $password)) {
        ApiResponse::success(['message' => 'User registered successfully'], 201);
    } else {
        ApiResponse::error('Registration failed');
    }
} catch (\Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
