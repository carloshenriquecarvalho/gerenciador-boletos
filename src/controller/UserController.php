<?php

namespace App\controller;
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
            exit;
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
                http_response_code(409);
                echo json_encode(['status' => 'duplicate_or_error']);
            }
        } catch (\Throwable $e) {
            error_log("Register error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'fail']);
        }
        exit;
    }

    public function handleLoginRequest(): void
    {
        header('Content-Type: application/json');

        // Decode input safely
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'invalid_input']);
            exit;
        }

        $email = trim($data['email']);
        $password = $data['password'];

        try {
            $user = $this->repo->login($email, $password);
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_nome'] = $user->getName();
                $_SESSION['user_email'] = $user->getEmail();
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'user' => [
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                        'email' => $user->getEmail()
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['status' => 'invalid_credentials']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            error_log("Login failed: " . $e->getMessage());
            echo json_encode(['status' => 'fail']);
        }
    }

    public function handleUpdateNameRequest(): void
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized']);
            exit;
        }
        $id = $_SESSION['user_id'];
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $newName = $data['name'] ?? null;

        if (empty($newName)) {
            http_response_code(400);
            echo json_encode(['status' => 'invalid_input']);
            exit;
        }
        try{
            $success = $this->repo->updateName($newName, $id);

            if ($success) {
                $_SESSION['user_nome'] = $newName;
                http_response_code(200);
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'fail']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'fail']);
        }
        exit;
    }

    public function handleUpdateEmailRequest(): void
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized']);
        }
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'] ?? null;
        $oldEmail = $_SESSION['email'] ?? null;
        $password = $data['password'] ?? null;

        try {
            $success = $this->repo->updateEmail($email, $password, $oldEmail);
            if ($success) {
                http_response_code(200);
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'fail']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'fail']);
        }
        exit;
    }

    public function handleUpdatePasswordRequest(): void
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized']);
        }
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $_SESSION['user_id'];
        $oldPassword = $data['oldPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;
        if (empty($oldPassword) || empty($newPassword)) {
            http_response_code(400);
            echo json_encode(['status' => 'invalid_input']);
            exit;
        }
        try {
            $success = $this->repo->updatePassword($id, $newPassword,$oldPassword);
            if ($success) {
                echo json_encode(['status' => 'success']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'fail']);
        }
        exit;
    }

    public function handleDeleteRequest(): void
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized']);
        }
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $_SESSION['user_id'];
        try {
            $success = $this->repo->delete($id);
            if ($success) {
                http_response_code(200);
                echo json_encode(['status' => 'success']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'fail']);
        }
        exit;
    }
}
