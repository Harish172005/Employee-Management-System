<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $uri === 'api/dashboard') {
    require_once __DIR__ . '/../controllers/DashboardController.php';

    (new DashboardController())->getDashboard();
    exit;
}
