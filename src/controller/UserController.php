<?php

namespace App\controller;
use App\Database;
use App\repository\UserRepository;
class UserController
{
    private UserRepository $repo;
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function handleRegisterRequest(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'invalid_input']);
            return;
        }
        $name = trim($data['name']);
        $email = trim($data['email']);
        $password = $data['password'];

        try {
            $success = $this->repo->register($name, $email, $password);

            if ($success) {
                http_response_code(201);
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(409); // conflict (likely duplicate)
                echo json_encode(['status' => 'duplicate_or_error']);
            }
        } catch (\Throwable $e) {
            error_log("Register error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'fail']);
        }
        exit;
    }
}
