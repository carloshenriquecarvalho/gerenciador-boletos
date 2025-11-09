<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

session_start();

use App\Database;
use App\repository\UserRepository;
use App\controller\UserController;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

$database = Database::getInstance();
$conn = $database->getConnection();

$repo = new UserRepository($conn);
$controller = new UserController($repo);
$controller->handleUpdatePasswordRequest();
