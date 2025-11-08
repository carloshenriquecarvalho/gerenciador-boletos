<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\repository\UserRepository;
use App\Database;

$pdo = Database::getInstance()->getConnection();
$repo = new UserRepository($pdo);

try {
    $repo->updatePassword("joaquin@gmail.com", "joaquin0901", "carlos0901");
    echo "It worked!";
} catch (PDOException $e) {
    error_log("Failed to update: " . $e->getMessage());
}