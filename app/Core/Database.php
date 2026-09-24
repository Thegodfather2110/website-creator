<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    /**
     * Get the database connection (Singleton pattern).
     *
     * @return PDO
     * @throws \Exception
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            // Load configuration
            $config = require __DIR__ . '/../../config/config.php';
            $dbConfig = $config['db'];

            // Construct DSN ensuring values are treated as strings
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                (string)$dbConfig['host'],
                (string)$dbConfig['port'],
                (string)$dbConfig['dbname'],
                (string)$dbConfig['charset']
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);
            } catch (PDOException $e) {
                throw new \Exception(
                    "Database connection failed for {$dbConfig['user']}@{$dbConfig['host']}:{$dbConfig['port']}/{$dbConfig['dbname']}. " .
                    "Start MySQL/MariaDB and import schema.sql. Details: " . $e->getMessage()
                );
            }
        }
        return self::$instance;
    }
}
