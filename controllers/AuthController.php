<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ .'/../models/UserRepository.php';

class AuthController
{
    private AuthService $service;
        public function __construct()
       {
        $conn = DBConfig::getConnection();

        $userRepository = new UserRepository($conn);

        $this->service = new AuthService(
            $userRepository
        );
    }

    public function login()
    {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        $result = $this->service->login($username, $password);
        
        if ($result['success']) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(401);
            echo json_encode(['error' => $result['message']]);
        }
    }
    
    public function logout()
    {
        $result = $this->service->logout();
        echo json_encode($result);
    }

    public function changePassword()
    {
    header('Content-Type: application/json');

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $currentPassword = $data['currentPassword'] ?? '';
    $newPassword = $data['newPassword'] ?? '';

    $result = $this->service->changePassword(
        $currentPassword,
        $newPassword
    );

    if ($result['success']) {

        http_response_code(200);

    } else {

        http_response_code(400);

    }

    echo json_encode($result);
  }
}