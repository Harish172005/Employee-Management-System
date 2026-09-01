<?php

class CsrfMiddleware
{
    public static function generateToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token): bool
    {
        if (
            empty($_SESSION['csrf_token']) ||
            !is_string($token) ||
            $token === ''
        ) {
            return false;
        }

        return hash_equals(
            $_SESSION['csrf_token'],
            $token
        );
    }

    public static function requireToken(): void
    {
        if (
            $_SERVER['REQUEST_METHOD'] === 'GET' ||
            $_SERVER['REQUEST_METHOD'] === 'HEAD' ||
            $_SERVER['REQUEST_METHOD'] === 'OPTIONS'
        ) {
            return;
        }

        $token = $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? $_SERVER['HTTP_X_CSRF-TOKEN']
            ?? $_POST['csrf_token']
            ?? null;

        if (!self::validateToken($token)) {
            http_response_code(419);
            echo json_encode([
                'success' => false,
                'message' => 'CSRF token missing or invalid.'
            ]);
            exit;
        }
    }
}