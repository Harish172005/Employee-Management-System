<?php
require_once __DIR__ . '/../middlewares/CsrfMiddleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/departments/create') {
    CsrfMiddleware::requireToken();

    require_once __DIR__ . '/../controllers/DepartmentController.php';
    $controller = new DepartmentController();
    $controller->createDepartment();
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'GET' && $uri === 'api/departments') {
    require_once __DIR__ . '/../controllers/DepartmentController.php';
    $controller = new DepartmentController();
    $controller->getDepartments();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && preg_match('/^api\/departments\/(\d+)$/',$uri, $matches)) {
    require_once __DIR__ . '/../controllers/DepartmentController.php';
    $controller = new DepartmentController();
    $departmentId = intval($matches[1]);
    $controller->updateDepartment($departmentId);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/^api\/departments\/(\d+)$/',$uri, $matches)) {
    require_once __DIR__ . '/../controllers/DepartmentController.php';
    $controller = new DepartmentController();
    $departmentId = intval($matches[1]);
    $controller->getDepartmentById($departmentId);
}
