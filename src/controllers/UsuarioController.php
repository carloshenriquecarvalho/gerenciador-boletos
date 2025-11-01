<?php

require_once __DIR__ . '/../repository/UsuarioRepository.php';

class UsuarioController
{
    private UsuarioRepository $repo;

    public function __construct(UsuarioRepository $repo)
    {
        $this->repo = $repo;
    }

    public function handleRegistroRequest(): void
    {
        $dados = json_decode(file_get_contents('php://input'), true);
        $nome = $dados['nome'];
        $email = $dados['email'];
        $senha = $dados['senha'];

        try {
            $this->repo->registrar($nome, $email, $senha);
            header('Content-Type: application/json');
            http_response_code(201);
            echo json_encode(['status' => 'sucesso', 'mensagem' => 'Usuario registrado com sucesso']);
        } catch (PDOException $e) {
            header('Content-Type: application/json');

            if ($e->getCode() === '23000') {
                http_response_code(409); 
                echo json_encode(['status' => 'erro', 'mensagem' => 'Este e-mail já está em uso.']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao registrar usuário.']);
            }
        }
        exit;
        
    }

    public function handleLoginRequest(): void 
    {
        $dados = json_decode(file_get_contents('php://input'), true);
        $email = $dados['email'];
        $senha = $dados['senha'];

        try {
            $usuario = $this->repo->login($email, $senha);

            if ($usuario !== null) {
                session_start();
                $_SESSION['user_id'] = $usuario->getIdUsuario();
                $_SESSION['user_nome'] = $usuario->getNome();

                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode([
                    'status' => 'sucesso', 
                    'mensagem' => 'Login bem-sucedido!',
                    'usuario' => [
                        'nome' => $usuario->getNome(),
                        'email' => $usuario->getEmail()
                    ]
                ]);

            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'erro', 'mensagem' => 'Email ou senha inválidos.']);
            }

        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno no servidor ao tentar fazer login.']);
        }
        exit;
    }
}

?>
