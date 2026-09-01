<?php
class UserRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findByUsername(string $username)
    {
        $stmt = $this->conn->prepare(
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
        $stmt = $this->conn->prepare(
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
        $stmt = $this->conn->prepare(
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
    $stmt = $this->conn->prepare(
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
        $stmt = $this->conn->prepare(
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