<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
            'data' => $result['data'] ?? []
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

        $service = new EmployeeService();
        $result = $service->createEmployee(
            $data ?? []
        );

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }

    public function getEmployeeById(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if ($id === null || $id <= 0) {
            $this->respond(400, [
                'success' => false,
                'message' => 'Invalid employee ID.'
            ]);
            return;
        }

        require_once __DIR__ . '/../config/dbConfig.php';
        require_once __DIR__ . '/../models/EmployeeRepository.php';

        $conn = DBConfig::getConnection();
        $repository = new EmployeeRepository($conn);
        $employee = $repository->getById($id);

        if (!$employee) {
            $this->respond(404, [
                'success' => false,
                'message' => 'Employee not found.'
            ]);
            return;
        }

        $this->respond(200, [
            'success' => true,
            'data' => $employee
        ]);
    }
}
