<?php

namespace App;

use PDO;
use PDOException;
use Exception;

class Database
{
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private string $charset;
    private string $driver;
    private ?PDO $conn = null;

    private static ?Database $instance = null;

    /**
     * @throws Exception
     */
    private function __construct()
    {
        $this->loadConfig();
        $this->connect();
    }

    public static function getInstance(): Database
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function loadConfig(): void
    {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? '';
        $this->username = $_ENV['DB_USERNAME'] ?? '';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
        $this->driver = $_ENV['DB_DRIVER'] ?? 'mysql';
    }

    /**
     * @throws Exception
     */
    public function connect(): void
    {
        $dsn = "$this->driver:host=$this->host;dbname=$this->db_name;charset=$this->charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        $maxRetries = 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
                return;
            } catch (PDOException $e) {
                $this->logError("PDOException: " . $e->getMessage());
                $attempt++;
                if ($attempt < $maxRetries) {
                    sleep(2);
                } else {
                    throw new Exception("Connection failed: " . $e->getMessage());
                }
            }
        }
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }

    private function logError(string $message): void
    {
        $log_file = __DIR__ . "/../logs/db_errors.log";
        $timestamp = date('Y-m-d H:i:s');
        $log_message = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }

    private function __clone() {}

    /**
     * @throws Exception
     */
    private function __wakeup() {throw new Exception("Cannot unserialize a database object");}
}