<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../models/EmployeeRepository.php';
require_once __DIR__ . '/../models/DepartmentRepository.php';
require_once __DIR__ . '/../services/EmployeeService.php';

class EmployeeController extends BaseController
{
    private EmployeeService $service;

    public function __construct()
    {
        $conn = DBConfig::getConnection();

        $employeeRepository = new EmployeeRepository($conn);
        $departmentRepository = new DepartmentRepository($conn);

        $this->service = new EmployeeService(
            $employeeRepository,
            $departmentRepository
        );
    }

    public function getEmployees(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        $result = $this->service->getEmployees($_GET);

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? [],
            'pagination' => $result['pagination'] ?? null
        ]);
    }

    public function createEmployee(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        $data = $_POST;

        if (empty($data)) {
            $rawInput = file_get_contents('php://input');
            $decoded = json_decode($rawInput, true);

            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        $file = $_FILES['profile_photo'] ?? null;

        $result = $this->service->createEmployee(
            $data ?? [],
            $file
        );

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message'] ?? null
        ]);
    }

    public function getEmployeeById(int $employeeId): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        if ($employeeId <= 0) {
            $this->respond(400, [
                'success' => false,
                'message' => 'Invalid employee ID.'
            ]);
            return;
        }

        $result = $this->service->getEmployeeById($employeeId);

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null
        ]);
    }

    public function updateEmployee(int $employeeId): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        if ($employeeId <= 0) {
            $this->respond(400, [
                'success' => false,
                'message' => 'Invalid employee ID.'
            ]);
            return;
        }

        $data = $_POST;

        if (empty($data)) {
            $rawInput = file_get_contents('php://input');
            $decoded = json_decode($rawInput, true);

            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        $file = $_FILES['profile_photo'] ?? null;

        $result = $this->service->updateEmployee(
            $employeeId,
            $data ?? [],
            $file
        );

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message'] ?? null
        ]);
    }

    public function deactivateEmployee(int $employeeId): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        if ($employeeId <= 0) {
            $this->respond(400, [
                'success' => false,
                'message' => 'Invalid employee ID.'
            ]);
            return;
        }

        $result = $this->service->deactivateEmployee($employeeId);

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message'] ?? null
        ]);
    }

    public function getOwnProfile(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();

        $email = $_SESSION['email'];

        $result = $this->service->getOwnProfile($email);

        $this->respond(
            $result['statusCode'] ?? 500,
            [
                'success' => $result['success'],
                'message' => $result['message'] ?? null,
                'data' => $result['data'] ?? null
            ]
        );
    }

    public function getOwnDepartment(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();

        $email = $_SESSION['email'];

        $result = $this->service->getOwnDepartment($email);

        $this->respond(
            $result['statusCode'] ?? 500,
            [
                'success' => $result['success'],
                'message' => $result['message'] ?? null,
                'data' => $result['data'] ?? null
            ]
        );
    }

    public function updateOwnProfile(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();

        $email = $_SESSION['email'];

        $data = $_POST;

        $file = $_FILES['profile_photo'] ?? null;

        $result = $this->service->updateOwnProfile(
            $email,
            $data,
            $file
        );

        $this->respond(
            $result['statusCode'] ?? 500,
            [
                'success' => $result['success'],
                'message' => $result['message'] ?? null
            ]
        );
    }
}
