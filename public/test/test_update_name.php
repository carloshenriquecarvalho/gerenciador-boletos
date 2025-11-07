<?php
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/repository/UsuarioRepository.php';
$database = new Database();
$pdo = $database->getConnection();
$repo = new UsuarioRepository($pdo);

$repo->updateName("melissa", 3);
if ($repo){
    echo "nice!";
}
?>