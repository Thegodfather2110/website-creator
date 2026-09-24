<?php
require_once __DIR__ . '/../init.php';
use App\Core\Auth;
use App\Core\ApiResponse;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['email']) ? trim($input['email']) : '';
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    ApiResponse::error('Email/Username and password are required', 400);
    exit;
}

if (Auth::login($email, $password)) {
    ApiResponse::success(['message' => 'Login successful']);
    exit;
} else {
    ApiResponse::error('Invalid credentials', 401);
    exit;
}
