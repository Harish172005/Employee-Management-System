<?php
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

if ($uri === 'api/auth/login' && $method === 'POST') {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/auth/login') {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
    exit;
}

require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/auth/logout') {
    $controller = new AuthController();
    $controller->logout();
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $uri === 'api/auth/check') {
    header('Content-Type: application/json');

    if (isset($_SESSION['user_id'])) {
        $user = AuthMiddleware::getCurrentUser();
        http_response_code(200);
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    }

    exit;
}