<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/DepartmentService.php';

class DepartmentController extends BaseController
{
    public function createDepartment(): void
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

        $service = new DepartmentService();
        $result = $service->createDepartment($data ?? []);

        http_response_code($result['statusCode'] ?? 500);

        echo json_encode([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }
    public function getDepartments(): void
{
    header('Content-Type: application/json');

    AuthMiddleware::requireLogin();
    AuthMiddleware::requireAdmin();

    $service = new DepartmentService();
    $result = $service->getDepartments();

    $this->respond($result['statusCode'] ?? 500, [
        'success' => $result['success'],
        'message' => $result['message'] ?? null,
        'data' => $result['data'] ?? []
    ]);
}
}
