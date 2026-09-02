<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

$method = $_SERVER['REQUEST_METHOD'];

// API routes
if (str_starts_with($uri, 'api/')) {

    require __DIR__ . '/../routes/AuthRoutes.php';

    if ($uri === 'api/users' || str_starts_with($uri, 'api/users/')) {
        require __DIR__ . '/../routes/UserRoutes.php';
    }

    if ($uri === 'api/employees' || str_starts_with($uri, 'api/employees/')) {
        require __DIR__ . '/../routes/EmployeeRoutes.php';
    }

    if ($uri === 'api/departments' || str_starts_with($uri, 'api/departments/')) {
        require __DIR__ . '/../routes/DepartmentRoutes.php';
    }

    exit;
}


// Page routes

if ($method === 'GET' && $uri === 'login') {

    require __DIR__ . '/../views/pages/login.html';

    exit;
}


if ($method === 'GET' && $uri === 'admin') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requireRole(['admin']);

    require __DIR__ . '/../views/pages/admin-dashboard.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/add-user') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requireRole(['admin']);

    require __DIR__ . '/../views/pages/add-user.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/add-employee') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requireRole(['admin']);

    require __DIR__ . '/../views/pages/add-employee.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/add-department') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requireRole(['admin']);

    require __DIR__ . '/../views/pages/add-department.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/employees') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requireRole(['admin']);

    require __DIR__ . '/../views/pages/admin-employees.html';

    exit;
}

if ($method === 'GET' && $uri === 'change-password') {

    require __DIR__ . '/../views/pages/change-password.html';
    exit;
}


if ($method === 'GET' && $uri === 'employee') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requireRole(['employee']);

    require __DIR__ . '/../views/pages/employee-dashboard.html';

    exit;
}


// Route not found
http_response_code(404);

echo "404 - Page not found";

require __DIR__ . '/../routes/AuthRoutes.php';