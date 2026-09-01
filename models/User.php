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