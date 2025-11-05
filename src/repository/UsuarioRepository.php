<?php
require_once __DIR__ . '/../model/Usuario.php';

class UsuarioRepository
{
    private const SQL_REGISTER = 'insert into gerenciador_boletos.usuario(nome_usuario, email, senha_hash) values(?, ?, ?)';
    private const SQL_SELECT_BY_EMAIL = 'select * from gerenciador_boletos.usuario where email = ?';
    private const SQL_SELECT_BY_ID = 'select * from gerenciador_boletos.usuario where id_usuario = ?';
    private const SQL_DELETE_USER_BY_ID = 'delete from gerenciador_boletos.usuario where id_usuario = ?';
    private const SQL_UPDATE_USER_NAME = 'update gerenciador_boletos.usuario set nome_usuario = ? where id_usuario = ?';
    private const SQL_UPDATE_USER_EMAIL = 'update gerenciador_boletos.usuario set email = ? where id_usuario = ?';
    private const SQL_UPDATE_USER_PASSWORD = 'update gerenciador_boletos.usuario set senha_hash = ? where id_usuario = ?';

    private ?PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function registrar(string $nome_usuario, string $email, string $senha) 
    {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        try{
            $stmt = $this->pdo->prepare(self::SQL_REGISTER);

            $stmt->execute([
                $nome_usuario,
                $email,
                $senha_hash
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar usuario: " . $e->getMessage());
            throw $e;
        }
    }

    public function login(string $email, string $senha)
    {
        try 
        {
            $stmt = $this->pdo->prepare(self::SQL_SELECT_BY_EMAIL);
            $stmt->execute([$email]);
            $dados_usuario = $stmt->fetch();

            if ($dados_usuario and password_verify($senha, $dados_usuario['senha_hash'])) {
                return new Usuario(
                    $dados_usuario['email'],
                    $dados_usuario['nome_usuario'],
                    $dados_usuario['id_usuario']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log("erro: " . $e->getMessage());
            return null;
        }
    }

    public function deletar(int $id)
    {
        try {
            $stmt_delete = $this->pdo->prepare(self::SQL_DELETE_USER_BY_ID);
            $stmt_delete->execute([$id]);

            return true;
        } catch (PDOException $e) {
            error_log("Nao foi possivel deletar o usuario com o ID: {$id}. Tente novamente mais tarde.");
            return false;
        }
    }

    public function updateName(string $newName, int $id)
    {
        try {
            $stmt = $this->pdo->prepare(self::SQL_UPDATE_USER_NAME);
            $stmt->execute([$newName, $id]);
            return true;
        } catch (PDOException $e){
            error_log("It was not possible to update user's name: " . $e->getMessage());
            return false;
        }
    }

    public function updateEmail(int $id, string $newEmail)
    {
        try {
            $stmt = $this->pdo->prepare(self::SQL_UPDATE_USER_EMAIL);
            $stmt->execute([$newEmail, $id]);

            return true;
        } catch (PDOException $e) {
            error_log("It was not possible to change user's email. Try again later: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword(int $id, string $oldPassword, string $newPassword)
    {
        try {
            $stmtSelect = $this->pdo->prepare(self::SQL_SELECT_BY_ID);
            $stmtSelect->execute([$id]);
            $userData = $stmtSelect->fetch();

            if ($userData and password_verify($oldPassword, $userData['senha_hash'])) {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare(self::SQL_UPDATE_USER_PASSWORD);
                $stmt->execute([
                    $newPasswordHash,
                    $id
                ]);
                return true;
            } else {
                return false;
            } 
        } catch (PDOException $e){
                error_log("It was not possible to update user's password: " . $e->getMessage());
                return false;
        }
    }
}
?>