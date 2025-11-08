<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\repository\UserRepository;
use App\Database;

$pdo = Database::getInstance()->getConnection();
$repo = new UserRepository($pdo);

try {
    $repo->delete(6);
    echo "User deleted.\n";
} catch (PDOException $e) {
    error_log("Failed to delete: " . $e->getMessage());
}