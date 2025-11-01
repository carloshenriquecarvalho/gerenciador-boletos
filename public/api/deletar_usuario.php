<?php
session_start();
if (!isset($_SESSION['user_id'])){
    header('Content-Type: application/json');
    http_response_code(401);

    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Acesso nao autorizado. Faca login para continuar'
    ]);
    exit;
}

require_once __DIR__ . '/src/controllers/UsuarioController.php';
require_once __DIR__ . '/src/repository/UsuarioRepository.php';
require_once __DIR__ . '/src/Database.php';

$database = new Database();
$pdo = $database->getConnection();
$repo = new UsuarioRepository($pdo);

$id_usuario = $_SESSION['user_id'];


header('Content-Type: application/json');

if ($sucesso) {
    // Se deu certo, destruímos a sessão (logout)
    session_unset();
    session_destroy();
    
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Sua conta foi deletada com sucesso.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Não foi possível deletar sua conta. Tente novamente mais tarde.'
    ]);
}
exit;
?>