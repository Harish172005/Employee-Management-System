<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/EmployeeService.php';

class EmployeeController extends BaseController
{
    public function getEmployees(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        $service = new EmployeeService();
        $result = $service->getEmployees($_GET);

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

        $service = new EmployeeService();

        $result = $service->createEmployee(
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

        $service = new EmployeeService();

        $result = $service->getEmployeeById($employeeId);

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

        $service = new EmployeeService();

        $result = $service->updateEmployee(
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

        $service = new EmployeeService();

        $result = $service->deactivateEmployee($employeeId);

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message'] ?? null
        ]);
    }
}