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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/users/create') {
    require_once __DIR__ . '/../controllers/UserController.php';
    $controller = new UserController();
    $controller->createUser();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/employees/create') {
    require_once __DIR__ . '/../controllers/EmployeeController.php';
    $controller = new EmployeeController();
    $controller->createEmployee();
    exit;
}

require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/auth/logout') {
    $controller = new AuthController();
    $controller->logout();
    exit;
} 

elseif (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $uri === 'api/auth/change-password'
) {

    AuthMiddleware::requireLogin();

    $controller = new AuthController();

    $controller->changePassword();

    exit;
}