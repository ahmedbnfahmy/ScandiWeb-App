<?php declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;
    private static ?Config $config = null;

    public static function initialize(array $env): void {
        self::$config = new Config($env);
    }

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            if (self::$config === null) {
                throw new PDOException("Database not initialized. Call Database::initialize() first.");
            }

            try {
                $dbConfig = self::$config->db;
                $dsn = "{$dbConfig["driver"]}:host={$dbConfig["host"]};dbname={$dbConfig["database"]};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];

                self::$connection = new PDO($dsn, $dbConfig["user"], $dbConfig["pass"], $options);
                echo "Database connection successful!";
            } catch (PDOException $e) {
                throw new PDOException("Connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    public static function getConfig(): ?Config {
        return self::$config;
    }
}
