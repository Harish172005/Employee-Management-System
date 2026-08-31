<?php

class AuthMiddleware
{
   
    public static function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized: Please login first'
            ]);
            exit;
        }
    }

    public static function requireRole($requiredRole)
    {
        self::requireLogin();

        $userRole = $_SESSION['role'] ?? null;
        $allowedRoles = is_array($requiredRole) ? $requiredRole : [$requiredRole];

        if (!in_array($userRole, $allowedRoles, true)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Forbidden: You do not have permission to access this resource'
            ]);
            exit;
        }
    }

   
    public static function requireAdmin()
    {
        self::requireRole('admin');
    }

    public static function requireEmployee()
    {
        self::requireRole('employee');
    }

    
    public static function getCurrentUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'status' => $_SESSION['status']
        ];
    }

    
    public static function requireActive()
    {
        self::requireLogin();

        $status = $_SESSION['status'] ?? null;
        if ($status !== 'active') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Your account has been deactivated'
            ]);
            exit;
        }
    }
}
