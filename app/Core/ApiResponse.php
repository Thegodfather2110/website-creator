<?php
namespace App\Core;

class ApiResponse {
    public static function send(mixed $data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    public static function error(string $message, int $statusCode = 400): void {
        self::send(['error' => $message], $statusCode);
    }

    public static function success(mixed $data = null, int $statusCode = 200): void {
        self::send(['success' => true, 'data' => $data], $statusCode);
    }
}
