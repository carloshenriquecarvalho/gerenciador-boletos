<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Tentativa de acesso via GET ao processa_registro.php");
    header("Location: ../../public/cadastro.php");
}

require_once '../classes/Database.php';
$database = new Database();
$pdo = $database->getConnection();

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha_texto_puro = $_POST['senha_texto_puro'] ?? '';
$re_senha = $_POST['re-senha'] ?? '';

if (empty($nome) || empty($email) || empty($senha_texto_puro) || empty($re_senha)) {
    error_log("Erro: existem dados faltando!");
    die("preencha todos os campos e tente novamente mais tarde!");

} elseif ($senha_texto_puro !== $re_senha) {
    error_log("Erro: as senhas devem ser iguais");

    die("As senhas devem ser iguais");
} else {
    $senha_hash = password_hash($senha_texto_puro, PASSWORD_DEFAULT);

    try {
        $sql = "insert into usuario (nome_usuario, email, senha_hash) values (?, ?, ?);";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([$nome, $email, $senha_hash]);

        header("Location: ../../public/login.php");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            error_log("Erro [Registro]: Email ja cadastrado. " . $email);
            die("Erro: este email ja esta cadastrado");
        } else {
            error_log("Erro [Registro] BD: " . $e->getMessage());
            die("Erro ao processar cadastro. Tente novamente mais tarde");
        }
    }
}

?>