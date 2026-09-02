<?php
require_once __DIR__ . '/../middlewares/CsrfMiddleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $uri === 'api/employees') {
    require_once __DIR__ . '/../controllers/EmployeeController.php';
    $controller = new EmployeeController();
    $controller->getEmployees();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/^api\/employees\/\d+$/', $uri)) {
    require_once __DIR__ . '/../controllers/EmployeeController.php';
    $controller = new EmployeeController();
    $controller->getEmployeeById();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/employees/create') {
    CsrfMiddleware::requireToken();

    require_once __DIR__ . '/../controllers/EmployeeController.php';
    $controller = new EmployeeController();
    $controller->createEmployee();
    exit;
}
