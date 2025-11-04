<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/repository/UsuarioRepository.php';
require_once __DIR__ . '/../../src/controller/UsuarioController.php';

$db = new Database();
$pdo = $db->getConnection();
$repo = new UsuarioRepository($pdo);
$controller = new UsuarioController($repo);

$controller->handleDeleteRequest();
?>