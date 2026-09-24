<?php
namespace App\Core;

use App\Core\Session;

class Auth {
    public static function register(string $username, string $email, string $password): int|false {
        $db = Database::getConnection();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        if (!$stmt->execute([$username, $email, $hash])) {
            return false;
        }

        $userId = (int) $db->lastInsertId();
        Session::regenerate();
        Session::set('user_id', $userId);
        Session::set('user_username', $username);
        Session::set('user_email', $email);
        return $userId;
    }

    // Support login via either email or username
    public static function login(string $identifier, string $password): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if (!$user) {
            error_log("Login attempt failed: No user found for identifier: " . $identifier);
            return false;
        }

        if (password_verify($password, $user['password_hash'])) {
            Session::regenerate();
            Session::set('user_id', $user['id']);
            Session::set('user_username', $user['username']);
            Session::set('user_email', $user['email']);
            return true;
        }

        error_log("Login attempt failed: Password mismatch for user: " . $identifier);
        return false;
    }

    public static function isLoggedIn(): bool {
        return Session::get('user_id') !== null;
    }

    public static function logout(): void {
        Session::destroy();
    }
}
