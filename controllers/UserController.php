<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ .'/../models/UserRepository.php';

class UserController extends BaseController
{

        private UserService $service;
        public function __construct()
       {
        $conn = DBConfig::getConnection();

        $userRepository = new UserRepository($conn);

        $this->service = new UserService(
            $userRepository
        );
    }
    public function createUser(): void
    {
        header('Content-Type: application/json');

        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->service->createUser(
            $data ?? []
        );

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }
}
