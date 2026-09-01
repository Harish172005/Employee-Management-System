<?php

require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../utilities/PasswordHasher.php';
require_once __DIR__ . '/../utilities/PasswordValidator.php';
  require_once __DIR__ . '/../models/User.php';

class AuthService
{
    public function login(string $username, string $password): array
    {
        if ($username === '' || $password === '') {
            return [
                'success' => false,
                'message' => 'Username and password are required.'
            ];
        }

        $passwordErrors = PasswordValidator::validate($password);

        if (!empty($passwordErrors)) {
            return [
                'success' => false,
                'message' => $passwordErrors[0]
            ];
        }


        $user = new UserRepository(DBConfig::getConnection());
        $user = $user->findByUsername($username);

        $genericError = 'Invalid username or password.';

        if (!$user) {
            return [
                'success' => false,
                'message' => $genericError
            ];
        }

        if ($user['status'] !== 'active') {
            return [
                'success' => false,
                'message' => 'This account has been deactivated.'
            ];
        }

        if (!PasswordHasher::verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => $genericError
            ];
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['status'] = $user['status'];

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'status' => $user['status']
            ]
        ];
    }
    public function logout(): array
    {
        header('Content-Type: application/json');

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();

    http_response_code(200);

   return [
    'success' => true,
    'message' => 'Logged out successfully.'
];
    }

    public function changePassword(
    string $currentPassword,
    string $newPassword
): array {

    if ($currentPassword === '' || $newPassword === '') {
        return [
            'success' => false,
            'message' => 'All fields are required.'
        ];
    }

    $conn = DBConfig::getConnection();

    $userModel = new UserRepository($conn);

    $userId = $_SESSION['user_id'];

    $user = $userModel->getUserByPassword($userId);

    if (!$user) {
        return [
            'success' => false,
            'message' => 'User not found.'
        ];
    }

    if (!PasswordHasher::verify(
        $currentPassword,
        $user['password']
    )) {
        return [
            'success' => false,
            'message' => 'Current password is incorrect.'
        ];
    }

    $errors = PasswordValidator::validate($newPassword);

    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => $errors[0]
        ];
    }

    if (PasswordHasher::verify(
        $newPassword,
        $user['password']
    )) {
        return [
            'success' => false,
            'message' => 'New password must be different from the current password.'
        ];
    }

    $hashedPassword = PasswordHasher::hash($newPassword);

    $updated = $userModel->updatePassword(
        $userId,
        $hashedPassword
    );

    if (!$updated) {
        return [
            'success' => false,
            'message' => 'Failed to change password.'
        ];
    }

    return [
        'success' => true,
        'message' => 'Password changed successfully.'
    ];
}
}