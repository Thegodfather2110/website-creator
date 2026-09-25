<?php
require_once __DIR__ . '/../init.php';
use App\Core\Auth;
use App\Core\ApiResponse;

Auth::logout();
ApiResponse::success(['message' => 'Logged out successfully']);
