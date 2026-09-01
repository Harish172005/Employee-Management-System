<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/EmployeeService.php';

class EmployeeController
{
    public function createEmployee(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);

        $service = new EmployeeService();
        $result = $service->createEmployee(
            $data ?? []
        );

        http_response_code($result['statusCode'] ?? 500);

        echo json_encode([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }
}
