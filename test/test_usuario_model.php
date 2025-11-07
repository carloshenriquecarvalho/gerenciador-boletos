<?php
require_once __DIR__ . '/src/model/Usuario.php';

$p = new Usuario("carlos@gmail.com", "carlos", null);

echo "<p><strong>Nome:</strong> {$p->getNome()} <br>";
echo "<p><strong>Email:</strong> {$p->getEmail()} </p>";
?>