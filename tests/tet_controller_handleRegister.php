<?php
use App\repository\UserRepository;
use App\Database;
use App\controller;

$pdo = Database::getInstance()->getConnection();
$repo = new UserRepository($pdo);
$controller = new controller\UserController($repo);

$controller->handleRegisterRequest();