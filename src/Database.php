<?php
class Database
{
    private string $host;
    private string $driver;
    private string $username;
    private string $db_name;
    private string $password;
    private string $charset;

    private ?PDO $conn = null;

    public function __construct(){

        $config_path = __DIR__ . '/../config/settings.ini';

        if (!$settings = parse_ini_file($config_path, TRUE)) {
            throw new Exception('Unable to open file: ' . $config_path . '.');
        }

        $db = $settings['database'];

        $this->driver = $db['driver'];
        $this->host = $db['host'];
        $this->username = $db['username'];
        $this->db_name = $db['schema'];
        $this->password = $db['password'];
        $this->charset = $db['charset'];
    }

    public function getConnection(): ?PDO
    {
        if ($this->conn === null) {
            $dsn = "{$this->driver}:host={$this->host};dbname={$this->db_name};charset={$this->charset}";

            $options = [
                PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,  
                PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES      => false,
            ];

            try {
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
                // echo "It worked!";
            } catch (PDOException $e){
                error_log("Erro de conexao com o bd: " . $e->getMessage());
                die("Erro ao conectar com o banco de dados. Tente novamente mais tarde.");
            }
        }
        return $this->conn;
    }
}
?>