<?php

interface UserRepositoryInterface
{
    public function findByUsername(string $username);
    public function findByEmail(string $email);
    public function create(
        string $name,
        string $email,
        string $username,
        string $password,
        string $role,
        string $status
    ): bool;
    public function getUserById($userId);
    public function updatePassword(int $userId, string $password): bool;
}
