<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/employees/create') {
    require_once __DIR__ . '/../controllers/EmployeeController.php';
    $controller = new EmployeeController();
    $controller->createEmployee();
    exit;
}
