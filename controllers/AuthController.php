<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../services/AuthService.php';

class AuthController
{
    public function login()
    {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        $service = new AuthService();
        $result = $service->login($username, $password);
        
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
        $service = new AuthService();
        $result = $service->logout();
        echo json_encode($result);
    }
}