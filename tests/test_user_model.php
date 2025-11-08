<?php
require "../vendor/autoload.php";

use App\model\User;

$user = new User(1, 'carlos', 'carlos@gmail.com');

echo "<p>";
echo "<strong>ID:</strong> " . $user->getId() . "<br>";
echo "<strong>Nome:</strong> " . $user->getName() . "<br>";
echo "<strong>Email:</strong> " . $user->getEmail() . "<br>";
echo "</p>";

echo "<pre>";
var_dump($user);
echo "</pre>";
