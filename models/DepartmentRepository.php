<?php

class DepartmentRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findByName(string $departmentName): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT id, department_name, description, status
             FROM departments
             WHERE department_name = :department_name
             LIMIT 1'
        );

        $stmt->execute([
            ':department_name' => $departmentName
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->conn->query(
            'SELECT id, department_name, description, status
             FROM departments
             ORDER BY id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT id, department_name, description, status
             FROM departments
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function create(
        string $departmentName,
        ?string $description,
        string $status
    ): bool {
        $stmt = $this->conn->prepare(
            'INSERT INTO departments (
                department_name,
                description,
                status
            ) VALUES (
                :department_name,
                :description,
                :status
            )'
        );

        return $stmt->execute([
            ':department_name' => $departmentName,
            ':description' => $description,
            ':status' => $status
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $allowedFields = [
            'department_name',
            'description',
            'status'
        ];

        $updates = [];
        $params = [
            ':id' => $id
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = 'UPDATE departments SET '
            . implode(', ', $updates)
            . ' WHERE id = :id';

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($params);
    }

    public function deactivate(int $id): bool
    {
        $stmt = $this->conn->prepare(
            'UPDATE departments
             SET status = :status
             WHERE id = :id'
        );

        return $stmt->execute([
            ':status' => 'inactive',
            ':id' => $id
        ]);
    }
}