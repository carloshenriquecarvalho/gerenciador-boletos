<?php
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/repository/UsuarioRepository.php';
$database = new Database();
$pdo = $database->getConnection();
$repo = new UsuarioRepository($pdo);

$repo->updatePassword(5, "minha_senha_segura123", "09010237");
?>