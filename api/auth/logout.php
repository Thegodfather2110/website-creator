<?php
require_once __DIR__ . '/../init.php';
use App\Core\ApiResponse;
use App\Core\Auth;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

Auth::logout();
ApiResponse::success(['message' => 'Logged out successfully']);
