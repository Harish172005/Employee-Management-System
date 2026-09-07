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

    $search = $_GET['search'] ?? null;
    $status = $_GET['status'] ?? null;

        $service = new DepartmentService();

        $result = $service->getDepartments(
            $search,
            $status
        );

    $this->respond($result['statusCode'] ?? 500, [
        'success' => $result['success'],
        'message' => $result['message'] ?? null,
        'data' => $result['data'] ?? []
    ]);
  }
 
  public function updateDepartment(int $id): void
{
    header('Content-Type: application/json');

    AuthMiddleware::requireLogin();
    AuthMiddleware::requireAdmin();

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $service = new DepartmentService();

    $result = $service->updateDepartment($id, $data);

    $this->respond(
        $result['statusCode'] ?? 500,
        [
            'success' => $result['success'],
            'message' => $result['message'] ?? null
        ]
    );
}

public function getDepartmentById($id) : void {
    header('Content-Type: application/json');

    AuthMiddleware::requireLogin();
    AuthMiddleware::requireAdmin();

     $service = new DepartmentService();

    $result = $service->getDepartmentById($id);
    $this->respond(
        $result['statusCode'] ?? 500,
        [
            'success' => $result['success'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null
        ]
    );

}

public function deactivateDepartment(int $departmentId): void
{
    header('Content-Type: application/json');

    AuthMiddleware::requireLogin();
    AuthMiddleware::requireAdmin();

    if ($departmentId <= 0) {
        $this->respond(400, [
            'success' => false,
            'message' => 'Invalid department ID.'
        ]);
        return;
    }

    $service = new DepartmentService();
    $result = $service->deactivateDepartment($departmentId);

    $this->respond($result['statusCode'] ?? 500, [
        'success' => $result['success'],
        'message' => $result['message'] ?? null
    ]);
}
}
