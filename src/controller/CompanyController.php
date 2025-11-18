<?php

namespace App\controller;
use App\repository\CompanyRepository;
class CompanyController
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function handleRegisterRequest(): void
    {
        header("Content-type: application/json");

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['companyName'])) {
            http_response_code(400);
            echo json_encode(['status' => 'failed', 'message' => 'Invalid request.']);
            exit;
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $companyName = trim($data['companyName']);

        try {
            $success = $this->companyRepository->createCompany($companyName, $user_id);
            if ($success) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'message' => 'Company successfully registered.']);
            } else {
                http_response_code(409);
                echo json_encode(['status' => 'error', 'message' => 'An error occured while registering the company.']);
            }
        } catch (\Exception $exception) {
            http_response_code(400);
            echo json_encode(['status' => 'failed', 'message' => $exception->getMessage()]);
        }
    }

    public function handle
}