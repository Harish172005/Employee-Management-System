<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/UserRepositoryInterface.php';

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function findByUsername(string $username)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT id, name, username, email, password, role, status
             FROM users
             WHERE username = :username
             LIMIT 1"
        );

        $stmt->execute([
            ':username' => $username
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail(string $email)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT id, name, username, email, password, role, status
             FROM users
             WHERE email = :email
             LIMIT 1"
        );

        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(
        string $name,
        string $email,
        string $username,
        string $password,
        string $role,
        string $status
    ): bool {
        $stmt = $this->getConnection()->prepare(
            "INSERT INTO users (name, email, username, password, role, status)
             VALUES (:name, :email, :username, :password, :role, :status)"
        );

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':username' => $username,
            ':password' => $password,
            ':role' => $role,
            ':status' => $status
        ]);
    }

    public function getUserById($userId)
    {
    $stmt = $this->getConnection()->prepare(
        "SELECT id, password
         FROM users
         WHERE id = :id
         LIMIT 1"
    );

    $stmt->execute([
        ':id' => $userId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword(int $userId, string $password): bool
    {
        $stmt = $this->getConnection()->prepare(
            "UPDATE users
             SET password = :password
             WHERE id = :id"
        );

        return $stmt->execute([
            ':password' => $password,
            ':id' => $userId
        ]);
    }
}