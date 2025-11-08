<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\repository\UserRepository;
use App\Database;

$pdo = Database::getInstance()->getConnection();
$repo = new UserRepository($pdo);

try {
    $repo->updateEmail("joaquin@gmail.com", "carlos0901", "carlos@gmail.com");
    echo "It worked!";
} catch (PDOException $e) {
    echo "Failed to update email!" . $e->getMessage();
}