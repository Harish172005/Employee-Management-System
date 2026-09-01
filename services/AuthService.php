<?php

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
        require_once __DIR__ . '/../utilities/PasswordValidator.php';

        $passwordErrors = PasswordValidator::validate($password);

        if (!empty($passwordErrors)) {
            return [
                'success' => false,
                'message' => $passwordErrors[0]
            ];
        }


        require_once __DIR__ . '/../config/dbConfig.php';
        require_once __DIR__ . '/../utilities/PasswordHasher.php';

        $conn = DBConfig::getConnection();

        $stmt = $conn->prepare(
            "SELECT id, name, username, email, password, role, status
             FROM users
             WHERE username = :username
             LIMIT 1"
        );

        $stmt->execute([
            ':username' => $username
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
}