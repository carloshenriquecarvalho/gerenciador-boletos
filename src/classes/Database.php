<?php
class Database
{
    private string $host = "localhost";
    private string $username = "carlos";
    private string $db_name = "gerenciador_boletos";
    private string $password = "0901";
    private string $charset = "utf8mb4";

    private ?PDO $conn = null;

    public function getConnection(): ?PDO
    {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";

            $options = [
                PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES      => false,
            ];

            try {
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e){
                error_log("Erro de conexao com o bd: " . $e->getMessage());
                die("Erro ao conectar com o banco de dados. Tente novamente mais tarde.");
            }
        }
        return $this->conn;

    }
}



?>