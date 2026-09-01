<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/users/create') {
    require_once __DIR__ . '/../controllers/UserController.php';
    $controller = new UserController();
    $controller->createUser();
    exit;
}
