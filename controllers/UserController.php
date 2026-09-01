<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/UserService.php';

class UserController
{
    public function createUser(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();

        $data = json_decode(file_get_contents('php://input'), true);

        $service = new UserService();
        $result = $service->createUser(
            $data ?? [],
            $_SESSION['role'] ?? ''
        );

        http_response_code($result['statusCode'] ?? 500);

        echo json_encode([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }
}
