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

    if ($uri === 'api/employees' || str_starts_with($uri, 'api/employees/') ||  str_starts_with($uri, 'api/employee/')) {
        require __DIR__ . '/../routes/EmployeeRoutes.php';
    }

    if ($uri === 'api/departments' || str_starts_with($uri, 'api/departments/')) {
        require __DIR__ . '/../routes/DepartmentRoutes.php';
    }

    if ($uri === 'api/dashboard') {
        require __DIR__ . '/../routes/DashboardRoutes.php';
    }

    exit;
}


// Page routes

if ($method === 'GET' && $uri === 'login') {

    require __DIR__ . '/../views/login.html';

    exit;
}


if ($method === 'GET' && $uri === 'admin') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['admin']);

    require __DIR__ . '/../views/pages/admin/admin-dashboard.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/add-user') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['admin']);

    require __DIR__ . '/../views/pages/admin/add-user.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/add-employee') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['admin']);

    require __DIR__ . '/../views/pages/admin/add-employee.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/add-department') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['admin']);

    require __DIR__ . '/../views/pages/admin/add-department.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/employees') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['admin']);

    require __DIR__ . '/../views/pages/admin/admin-employees.html';

    exit;
}

if ($method === 'GET' && $uri === 'change-password') {

    require __DIR__ . '/../views/change-password.html';
    exit;
}


if ($method === 'GET' && $uri === 'employee') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['employee']);

    require __DIR__ . '/../views/pages/employee/employee-dashboard.html';

    exit;
}

if ($method === 'GET' && $uri === 'employee/profile') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['employee']);

    require __DIR__ . '/../views/pages/employee/employee-profile.html';

    exit;
}

if ($method === 'GET' && $uri === 'employee/department') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['employee']);

    require __DIR__ . '/../views/pages/employee/employee-department.html';

    exit;
}

if ($method === 'GET' && $uri === 'admin/departments') {

    require __DIR__ . '/../middlewares/AuthMiddleware.php';

    AuthMiddleware::requirePageRole(['admin']);

    require __DIR__ . '/../views/pages/admin/department-management.html';

    exit;
}


// Route not found
http_response_code(404);

echo "404 - Page not found";

require __DIR__ . '/../routes/AuthRoutes.php';


