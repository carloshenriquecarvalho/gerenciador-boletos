### Exemplo: Script de Processamento de Registro (`src/actions/processa_registro.php`)

```php
<?php

// verify if the method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If not POST, access denied and redirected
    error_log("Tentativa de acesso GET a processa_registro.php");
    header("Location: ../../public/registro.php"); 
    exit();
}

require_once '../classes/Database.php';
$database = new Database();
$pdo = $database->getConnection();


$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha_texto_puro = $_POST['senha_texto_puro'] ?? '';
$re_senha = $_POST['re-senha'] ?? '';

if (empty($nome) || empty($email) || empty($senha_texto_puro) || empty($re_senha)) {
    error_log("Erro [Registro]: Existem dados faltando!");
    die("Erro: preencha todos os campos!");

} elseif ($senha_texto_puro !== $re_senha) {
    error_log("Erro [Registro]: As senhas não coincidem.");
    die("Erro: As senhas devem ser iguais!");
} else {
    // hashs validated pass
    $senha_hash = password_hash($senha_texto_puro, PASSWORD_DEFAULT);

    try {
        // checks the columns name
        $sql = "INSERT INTO usuario (nome_usuario, email, senha_hash) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([$nome, $email, $senha_hash]);

        // redirects to login page
        header("Location: ../../public/login.php");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() === '23000') { // constraint unique violation
            error_log("Erro [Registro]: Email duplicado - " . $email);
            die("Erro: este e-mail já está cadastrado.");
        } else {
            // any other database error
            error_log("Erro [Registro] DB: " . $e->getMessage());
            die("Erro ao processar o cadastro. Tente novamente mais tarde.");
        }
    }
} // Main else end

?>