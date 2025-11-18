<?php
namespace App\controller;
use App\repository\UserRepository;
use PDOException;

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
            echo json_encode(['status' => 'failed', 'message' => 'invalid_input.']);
            exit;
        }

        $name = trim($data['name']);
        $email = trim($data['email']);
        $password = $data['password'];

        try {
            $success = $this->repo->register($name, $email, $password);

            if ($success) {
                http_response_code(201); 
                echo json_encode(['status' => 'success', 'message' => 'User successfully registered.']);
            } else {
                http_response_code(409); 
                echo json_encode(['status' => 'duplicate_or_error', 'message' => 'Email already exists.']);
            }
        } catch (PDOException $e) {
            error_log("Register error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'fail', 'message' => 'Internal Server Error.']);
        }
        exit;
    }

    public function handleLoginRequest(): void
    {
        header('Content-Type: application/json');

        // 1. Read Request
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'invalid_input', 'message' => 'Email And Password are required.']);
            exit;
        }

        $email = trim($data['email']);
        $password = $data['password'];

        try {
            $user = $this->repo->login($email, $password);
            if ($user) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_nome'] = $user->name;
                $_SESSION['user_email'] = $user->getEmail();
                // ---------------------------

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
                echo json_encode(['status' => 'invalid_credentials', 'message' => 'Email or Password invalid.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Login failed: " . $e->getMessage());
            echo json_encode(['status' => 'fail', 'message' => 'Internal Server Error.']);
        }
        exit;
    }

    public function handleUpdateNameRequest(): void
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized', 'message' => 'Unauthorized access.']);
            exit;
        }

        $id = $_SESSION['user_id'];

        $data = json_decode(file_get_contents("php://input"), true);
        $newName = $data['name'] ?? null;

        if (empty($newName)) {
            http_response_code(400);
            echo json_encode(['status' => 'invalid_input', 'message' => 'New Name cannot be empty.']);
            exit;
        }
        try{
            $success = $this->repo->updateName($newName, $id);

            if ($success) {
                $_SESSION['user_nome'] = $newName; // 6. Update the "locker"
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Name updated successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'fail', 'message' => 'Failed to update name.']);
            }
        } catch (\Throwable) {
            http_response_code(500);
            echo json_encode(['status' => 'fail', 'message' => 'Internal Server Error.']);
        }
        exit;
    }

    public function handleUpdateEmailRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized', 'message' => 'Unauthorized access.']);
            exit;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $_SESSION['user_id'];
        $newEmail = $data['email'] ?? null;
        $password = $data['password'] ?? null; // Password to confirm action

        if (empty($newEmail) || empty($password)) {
            http_response_code(400);
            echo json_encode(['status' => 'invalid_input', 'message' => 'Email And Password are required.']);
            exit;
        }

        try {
            $success = $this->repo->updateEmail($newEmail, $password, $id);
            if ($success) {
                $_SESSION['user_email'] = $newEmail; // Update the session
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Email updated.']);
            } else {
                http_response_code(401); // Wrong password
                echo json_encode(['status' => 'fail_password', 'message' => 'Wrong Password.']);
            }
        } catch (\Throwable) {
            http_response_code(500);
            echo json_encode(['status' => 'fail', 'message' => 'Internal Server Error.']);
        }
        exit;
    }

    public function handleUpdatePasswordRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized', 'message' => 'Unauthorized access.']);
            exit;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $_SESSION['user_id'];
        $oldPassword = $data['oldPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;

        if (empty($oldPassword) || empty($newPassword)) {
            http_response_code(400);
            echo json_encode(['status' => 'invalid_input', 'message' => 'Both passwords are required.']);
            exit;
        }
        try {
            $success = $this->repo->updatePassword($id, $newPassword, $oldPassword);
            if ($success) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Senha atualizada.']);
            } else {
                http_response_code(401);
                echo json_encode(['status' => 'fail_password', 'message' => 'Senha antiga incorreta.']);
            }
        } catch (\Throwable) {
            http_response_code(500);
            echo json_encode(['status' => 'fail', 'message' => 'Internal server error.']);
        }
        exit;
    }

    public function handleDeleteRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized', 'message' => 'Unauthorized access.']);
            exit;
        }

        header('Content-Type: application/json');
        $id = $_SESSION['user_id'];

        try {
            $success = $this->repo->delete($id);
            if ($success) {
                session_unset();
                session_destroy();
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Account deleted successfully.']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'fail', 'message' => 'User Not Found.']);
            }
        } catch (\Throwable) {
            http_response_code(500);
            echo json_encode(['status' => 'fail', 'message' => 'Internal server error.']);
        }
        exit;
    }
}