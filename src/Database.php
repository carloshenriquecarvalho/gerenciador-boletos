<?php
namespace App;

use Dotenv\Dotenv;
use PDO;
use PDOException;
use Exception;
class Database
{
    private string $db_name;
    private string $db_user;
    private string $db_password;
    private string $db_host;
    private string $db_charset;
    private string $db_driver;
    private ?PDO $conn = null;
    private static ?Database $instance = null;

    /**
     * @throws Exception
     */
    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        $this->loadConfig();
        $this->connect();
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function loadConfig(): void
    {
        $this->db_name = $_ENV['DB_NAME'] ?? '';
        $this->db_user = $_ENV['DB_USER'] ?? 'carlos';
        $this->db_password = $_ENV['DB_PASSWORD'] ?? '';
        $this->db_host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
        $this->db_driver = $_ENV['DB_DRIVER'] ?? 'mysql';
    }

    /**
     * @throws Exception
     */
    private function connect(): void
    {
        $dsn = "{$this->db_driver}:host={$this->db_host};dbname={$this->db_name};charset={$this->db_charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        $maxRetries = 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try{
                $this->conn = new PDO($dsn, $this->db_user, $this->db_password, $options);
                return;
            } catch (PDOException $e) {
                $attempt++;
                $this->logError($e->getMessage());
                if ($attempt < $maxRetries) {
                    sleep(2);
                } else {
                    throw new Exception("PDOException:" . $e->getMessage());
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
        $time_stamp = date("Y-m-d H:i:s");
        $message = $time_stamp . " " . $message . PHP_EOL;
        file_put_contents($log_file, $message, FILE_APPEND);
    }

    private function __clone() {}

    /**
     * @throws Exception
     */
    private function __wakeup() {throw new Exception("Cannot unserialize a singleton.");}
}