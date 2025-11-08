<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    echo "worked";
} catch (PDOException $e) {
    echo $e->getMessage();
}

