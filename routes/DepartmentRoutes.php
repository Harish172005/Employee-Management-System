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
