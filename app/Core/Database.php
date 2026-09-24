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

            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
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
