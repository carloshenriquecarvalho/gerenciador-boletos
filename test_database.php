<?php
require_once __DIR__ . '/src/Database.php';
$database = new Database();
$database->getConnection();
?>