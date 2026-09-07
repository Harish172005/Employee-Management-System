<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class DBConfig
{
    public static function getConnection(): PDO
    {
        Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $database = $_ENV['DB_NAME'] ?? 'employee-management';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        try {
            return new PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
}
