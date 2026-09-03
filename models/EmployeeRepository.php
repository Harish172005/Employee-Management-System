<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/EmployeeRepositoryInterface.php';

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $dateOfBirth,
        string $gender,
        string $dateOfJoining,
        string $department,
        string $designation,
        float $salary,
        string $address,
        ?string $profilePhoto,
        string $status
    ): bool {
        $stmt = $this->getConnection()->prepare(
            'INSERT INTO employees (
                first_name,
                last_name,
                email,
                phone,
                date_of_birth,
                gender,
                date_of_joining,
                department,
                designation,
                salary,
                address,
                profile_photo,
                status
            ) VALUES (
                :first_name,
                :last_name,
                :email,
                :phone,
                :date_of_birth,
                :gender,
                :date_of_joining,
                :department,
                :designation,
                :salary,
                :address,
                :profile_photo,
                :status
            )'
        );

        return $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone' => $phone,
            ':date_of_birth' => $dateOfBirth,
            ':gender' => $gender,
            ':date_of_joining' => $dateOfJoining,
            ':department' => $department,
            ':designation' => $designation,
            ':salary' => $salary,
            ':address' => $address,
            ':profile_photo' => $profilePhoto,
            ':status' => $status
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->getConnection()->query(
            'SELECT *
             FROM employees
             ORDER BY id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->getConnection()->prepare(
            'SELECT * FROM employees WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

   public function getFiltered(
    ?string $search = null,
    ?string $status = null,
    ?string $department = null,
    int $limit = 10,
    int $offset = 0
): array {
    $sql = 'SELECT * FROM employees WHERE 1=1';

    $params = [];

    if ($search !== null && $search !== '') {
        $sql .= ' AND (
            first_name LIKE :search_first
            OR last_name LIKE :search_last
            OR email LIKE :search_email
            OR phone LIKE :search_phone
            OR department LIKE :search_department
            OR designation LIKE :search_designation
        )';

        $searchValue = '%' . $search . '%';

        $params[':search_first'] = $searchValue;
        $params[':search_last'] = $searchValue;
        $params[':search_email'] = $searchValue;
        $params[':search_phone'] = $searchValue;
        $params[':search_department'] = $searchValue;
        $params[':search_designation'] = $searchValue;
    }

    if ($status !== null && $status !== '') {
        $sql .= ' AND status = :status';
        $params[':status'] = $status;
    }

    if ($department !== null && $department !== '') {
        $sql .= ' AND department = :department';
        $params[':department'] = $department;
    }

    $sql .= ' ORDER BY id ASC
              LIMIT :limit OFFSET :offset';

    $stmt = $this->getConnection()->prepare($sql);

    foreach ([
        ':search_first',
        ':search_last',
        ':search_email',
        ':search_phone',
        ':search_department',
        ':search_designation'
    ] as $param) {
        if (isset($params[$param])) {
            $stmt->bindValue($param, $params[$param], PDO::PARAM_STR);
        }
    }

    if (isset($params[':status'])) {
        $stmt->bindValue(
            ':status',
            $params[':status'],
            PDO::PARAM_STR
        );
    }

    if (isset($params[':department'])) {
        $stmt->bindValue(
            ':department',
            $params[':department'],
            PDO::PARAM_STR
        );
    }

    $stmt->bindValue(
        ':limit',
        $limit,
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        ':offset',
        $offset,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function countFiltered(?string $search = null, ?string $status = null, ?string $department = null): int
    {
        $sql = 'SELECT COUNT(*) as total FROM employees WHERE 1=1';
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= ' AND (
                first_name LIKE :search OR
                last_name LIKE :search OR
                email LIKE :search OR
                phone LIKE :search OR
                department LIKE :search OR
                designation LIKE :search
            )';
            $params[':search'] = '%' . $search . '%';
        }

        if ($status !== null && $status !== '') {
            $sql .= ' AND status = :status';
            $params[':status'] = $status;
        }

        if ($department !== null && $department !== '') {
            $sql .= ' AND department = :department';
            $params[':department'] = $department;
        }

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total'] ?? 0);
    }

    public function update(int $id, array $data): bool
    {
        $allowedFields = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'date_of_birth',
            'gender',
            'date_of_joining',
            'department',
            'designation',
            'salary',
            'address',
            'profile_photo',
            'status'
        ];

        $updates = [];
        $params = [':id' => $id];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = 'UPDATE employees SET ' . implode(', ', $updates) . ' WHERE id = :id';

        $stmt = $this->getConnection()->prepare($sql);
        return $stmt->execute($params);
    }

    public function deactivate(int $id): bool
    {
        $stmt = $this->getConnection()->prepare(
            'UPDATE employees SET status = :status WHERE id = :id'
        );

        return $stmt->execute([':status' => 'inactive', ':id' => $id]);
    }
}
