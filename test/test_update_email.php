<?php
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/repository/UsuarioRepository.php';
$database = new Database();
$pdo = $database->getConnection();
$repo = new UsuarioRepository($pdo);

$repo->updateEmail(3, "melissatorres0901@gmail.com");
?>