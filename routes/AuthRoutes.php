<?php
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../middlewares/CsrfMiddleware.php';

if ($uri === 'api/auth/csrf-token' && $method === 'GET') {
    echo json_encode([
        'success' => true,
        'token' => CsrfMiddleware::generateToken()
    ]);
    exit;
}

if ($uri === 'api/auth/login' && $method === 'POST') {
    CsrfMiddleware::requireToken();

    require_once __DIR__ . '/../controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
    exit;
}

require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/auth/logout') {
    CsrfMiddleware::requireToken();

    $controller = new AuthController();
    $controller->logout();
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $uri === 'api/auth/change-password'
) {
    CsrfMiddleware::requireToken();
    AuthMiddleware::requireLogin();

    $controller = new AuthController();
    $controller->changePassword();
    exit;
}