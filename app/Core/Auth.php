<?php
namespace App\Core;

use App\Core\Session;

class Auth {
    public static function register(string $username, string $email, string $password): bool {
        $db = Database::getConnection();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hash]);
    }

    public static function login(string $email, string $password): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            Session::regenerate();
            Session::set('user_id', $user['id']);
            Session::set('user_email', $user['email']);
            return true;
        }
        return false;
    }

    public static function isLoggedIn(): bool {
        return Session::get('user_id') !== null;
    }

    public static function logout(): void {
        Session::destroy();
    }
}
