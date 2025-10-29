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
            // ETAPA 3 (Erro): Se o repo falhou, o PHP "pula" para cá.
            header('Content-Type: application/json');

            // Bônus: checar se é erro de e-mail duplicado
            if ($e->getCode() === '23000') {
                http_response_code(409); // 409 = "Conflict" (Dado já existe)
                echo json_encode(['status' => 'erro', 'mensagem' => 'Este e-mail já está em uso.']);
            } else {
                // Outro erro qualquer de banco
                http_response_code(500); // 500 = "Internal Server Error"
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
            // 2. Chamar o login E GUARDAR O RESULTADO
            $usuario = $this->repo->login($email, $senha);

            // 3. CHECAR O RESULTADO!
            if ($usuario !== null) {
                // SUCESSO! Login válido. $usuario é um objeto.
                
                // É AQUI que você inicia a sessão!
                session_start();
                $_SESSION['user_id'] = $usuario->getIdUsuario();
                $_SESSION['user_nome'] = $usuario->getNome();

                // 4. Responder o JSON de SUCESSO
                header('Content-Type: application/json');
                http_response_code(200); // 200 = OK (padrão para login)
                echo json_encode([
                    'status' => 'sucesso', 
                    'mensagem' => 'Login bem-sucedido!',
                    'usuario' => [ // É uma boa prática retornar quem logou
                        'nome' => $usuario->getNome(),
                        'email' => $usuario->getEmail()
                    ]
                ]);

            } else {
                // FALHA! $usuario é null (email ou senha errados)
                
                // 4. Responder o JSON de FALHA
                header('Content-Type: application/json');
                http_response_code(401); // 401 = Unauthorized (Não autorizado)
                echo json_encode(['status' => 'erro', 'mensagem' => 'Email ou senha inválidos.']);
            }

        } catch (PDOException $e) {
            // ERRO DE BANCO (ex: conexão caiu)
            header('Content-Type: application/json');
            http_response_code(500); // 500 = Erro Interno
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno no servidor ao tentar fazer login.']);
        }
        exit;
    }
}

?>
