<?php

session_start();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    error_log("Metodo de request nao e post");
    header("Location: ../../public/login.php");
    exit;
}

require_once '../classes/Database.php';
$database = new Database();
$pdo = $database->getConnection();

$email = $_POST['email'] ?? '';
$senha_texto_puro = $_POST['senha'] ?? '';

if (empty($email) || empty($senha_texto_puro)){
    header("Location: ../../public/login.php?error=campos_vazios");
    exit;
}

try {
    $sql = 'select id_usuario, email, senha_hash, nome_usuario from usuario where email = ?;';
    $stmt = $pdo->prepare($sql);

    $stmt->execute([$email]);
    $usuario = $stmt->fetch();


    if ($usuario && password_verify($senha_texto_puro, $usuario['senha_hash'])) {
        

        session_regenerate_id(true);
        $_SESSION['user_id'] = $usuario['id_usuario'];
        $_SESSION['user_email'] = $usuario['email'];
        $_SESSION['user_name'] = $usuario['nome_usuario'];
        $_SESSION['logged_in'] = true;

        header("Location: ../../public/dashboard.php");
        exit;
    } else {
        error_log("Falha na tentativa de login para o e-mail: $email");
        header("Location: ../../public/login.php?erro=credenciais_invalidas");
        exit;
    }

} catch (PDOException $e) {
    error_log("Erro na consulta de login: " . $e->getMessage());
    header('Location: ../../public/login.php?erro=db_query_error');
    exit;
}
?>