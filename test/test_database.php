<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header('Content-Type: text/plain');

try {

    $pdo = Database::getInstance()->getConnection();

    // 6. This is our "Feedback"
    echo "✅ SUCCESS! (.env file worked!)\n\n";

    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "You have successfully connected to the '$dbName' database.";

} catch (Exception $e) {
    http_response_code(500);
    echo "❌ FAILED TO CONNECT:\n\n";
    echo $e->getMessage();
}