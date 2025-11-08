<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\repository\UserRepository;
use App\Database;

$pdo = Database::getInstance()->getConnection();
$repo = new UserRepository($pdo);

try {
    $repo->updateName("Joaquin", 6);
    echo "It Worked";
} catch (PDOException $e) {
    echo "Failed to update name with email carlos@gmail: " . $e->getMessage();
}