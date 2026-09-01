<?php

require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utilities/PasswordHasher.php';
require_once __DIR__ . '/../utilities/PasswordValidator.php';
require_once __DIR__ . '/../utilities/EmailValidator.php';

class UserService
{
    public function createUser(array $data, string $currentRole): array
    {
        if ($currentRole !== 'admin') {
            return [
                'success' => false,
                'message' => 'Only admins can create users.',
                'statusCode' => 403
            ];
        }

        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $username = trim((string)($data['username'] ?? ''));
        $password = (string)($data['password'] ?? '');
        $role = trim((string)($data['role'] ?? ''));
        $status = trim((string)($data['status'] ?? ''));

        if ($name === '' || $email === '' || $username === '' || $password === '' || $role === '' || $status === '') {
            return [
                'success' => false,
                'message' => 'All fields are required.',
                'statusCode' => 400
            ];
        }

        $emailErrors = EmailValidator::validate($email);
        if (!empty($emailErrors)) {
            return [
                'success' => false,
                'message' => $emailErrors[0],
                'statusCode' => 400
            ];
        }

        if (!in_array($role, ['employee', 'admin'], true)) {
            return [
                'success' => false,
                'message' => 'Role must be employee or admin.',
                'statusCode' => 400
            ];
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            return [
                'success' => false,
                'message' => 'Status must be active or inactive.',
                'statusCode' => 400
            ];
        }

        $passwordErrors = PasswordValidator::validate($password);
        if (!empty($passwordErrors)) {
            return [
                'success' => false,
                'message' => $passwordErrors[0],
                'statusCode' => 400
            ];
        }

        $conn = DBConfig::getConnection();
        $userRepository = new UserRepository($conn);

        if ($userRepository->findByUsername($username)) {
            return [
                'success' => false,
                'message' => 'Username already exists.',
                'statusCode' => 409
            ];
        }

        if ($userRepository->findByEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email already exists.',
                'statusCode' => 409
            ];
        }

        $hashedPassword = PasswordHasher::hash($password);

        $created = $userRepository->create(
            $name,
            $email,
            $username,
            $hashedPassword,
            $role,
            $status
        );

        if (!$created) {
            return [
                'success' => false,
                'message' => 'Failed to create user.',
                'statusCode' => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'User created successfully.',
            'statusCode' => 201
        ];
    }
}
