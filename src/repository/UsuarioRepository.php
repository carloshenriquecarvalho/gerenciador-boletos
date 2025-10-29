<?php
require_once __DIR__ . '/../model/Usuario.php';

class UsuarioRepository
{
    private const SQL_REGISTER = 'insert into gerenciador_boletos.usuario(nome_usuario, email, senha_hash) values(?, ?, ?)';
    private const SQL_SELECT_BY_EMAIL = 'select * from gerenciador_boletos.usuario where email = ?';

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
}

?>