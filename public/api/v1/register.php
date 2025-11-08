<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Database;
use App\repository\UserRepository;
use App\controller\UserController;

$database = Database::getInstance();
$conn = $database->getConnection();

$repo = new UserRepository($conn);
$controller = new UserController($repo);
$controller->handleRegisterRequest();