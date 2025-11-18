<?php
namespace App\repository;

use App\model\User;
use PDO;
use PDOException;

class UserRepository
{
    private ?PDO $conn;
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    private const string SQL_INSERT_USER_INTO_TABLE = 'INSERT INTO gerenciador_boletos.usuario (nome_usuario, email, senha_hash) VALUES (:name, :email, :password_hash)';
    private const string SQL_SELECT_USER_FROM_TABLE = 'SELECT * FROM gerenciador_boletos.usuario WHERE email = :email';
    private const string SQL_SELECT_USER_FROM_TABLE_ID = 'SELECT * FROM gerenciador_boletos.usuario WHERE id_usuario = :id_usuario';
    private const string SQL_UPDATE_NAME = 'UPDATE gerenciador_boletos.usuario SET nome_usuario = :nome_usuario WHERE id_usuario = :id_usuario';
    private const string SQL_UPDATE_EMAIL = 'UPDATE gerenciador_boletos.usuario SET email = :email WHERE id_usuario = :id_usuario';
    private const string SQL_UPDATE_PASSWORD = 'UPDATE gerenciador_boletos.usuario SET senha_hash = :password_hash WHERE id_usuario = :id_usuario';
    private const string SQL_DELETE_USER_FROM_TABLE = 'DELETE FROM gerenciador_boletos.usuario WHERE id_usuario = :id_usuario';

    //register
    public function register(string $name, string $email, string $password): bool
    {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt=$this->conn->prepare(self::SQL_INSERT_USER_INTO_TABLE);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password_hash' => $password_hash
            ]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                error_log("Failed to register: email already registered. " . $e->getMessage());
            } else {
                error_log("Failed to register: " . $e->getMessage());
            }
            return false;
        }
    }

    //login
    public function login(string $email, string $password): ?User
    {
        if (empty($email) || empty($password)) {
            error_log("Empty username or password");
            return null;
        }
        $stmt = $this->conn->prepare(self::SQL_SELECT_USER_FROM_TABLE);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data || !password_verify($password, $data['senha_hash'])) {
            error_log("Wrong password");
            return null;
        }
        return new User(id: $data['id_usuario'], name: $data['nome_usuario'], email: $data['email']);
    }

    //update name
    public function updateName(string $name, int $id): bool
    {
        $stmt = $this->conn->prepare(self::SQL_SELECT_USER_FROM_TABLE_ID);
        try {
            $stmt->execute([':id_usuario' => $id]);
            $data = $stmt->fetch();

            if ($data) {
                $stmt_update = $this->conn->prepare(self::SQL_UPDATE_NAME);
                $stmt_update->execute([':nome_usuario' => $name, ':id_usuario' => $id]);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Failed to update: " . $e->getMessage());
            return false;
        }
    }

    //update email
    public function updateEmail(string $newEmail, string $password, int $id): bool
    {
        $stmt = $this->conn->prepare(self::SQL_SELECT_USER_FROM_TABLE_ID);
        try {
            $stmt->execute([':id_usuario' => $id]);
            $data = $stmt->fetch();

            if ($data && password_verify($password, $data['senha_hash'])) {
                $stmt_update = $this->conn->prepare(self::SQL_UPDATE_EMAIL);
                $stmt_update->execute([':email' => $newEmail, ':id_usuario' => $id]);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Failed to update email: " . $e->getMessage());
            return false;
        }
    }

    //update password
    public function updatePassword
    (int $id, string $password, string $oldPassword): bool
    {
        $stmt = $this->conn->prepare(self::SQL_SELECT_USER_FROM_TABLE_ID);

        try {
            $stmt->execute([':id_usuario' => $id]);
            $data = $stmt->fetch();
            if ($data) {
                if (password_verify($oldPassword, $data['senha_hash'])) {
                    $new_password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_update = $this->conn->prepare(self::SQL_UPDATE_PASSWORD);
                    $stmt_update->execute([':password_hash' => $new_password_hash, ':id_usuario' => $id]);
                    return true;
                }
                return false;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Failed to update: " . $e->getMessage());
            return false;
        }
    }

    //delete account
    public function delete(int $id_usuario): bool
    {
        $stmt = $this->conn->prepare(self::SQL_DELETE_USER_FROM_TABLE);
        try {
            $stmt->execute([':id_usuario' => $id_usuario]);
            return true;
        } catch (PDOException $e) {
            error_log("Failed to delete: " . $e->getMessage());
            return false;
        }
    }
}