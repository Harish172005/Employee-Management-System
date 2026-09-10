<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/EmployeeRepositoryInterface.php';

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    public function hasEmployeesInDepartment(int $departmentId): bool
    {
        $stmt = $this->getConnection()->prepare(
            'SELECT EXISTS(
                SELECT 1
                FROM employees
                WHERE department_id = :department_id
            ) AS has_employees'
        );

        $stmt->execute([
            ':department_id' => $departmentId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function findByEmail(string $email): ?array
{
    $stmt = $this->conn->prepare(
        'SELECT id, email
         FROM employees
         WHERE email = :email
         LIMIT 1'
    );

    $stmt->execute([
        ':email' => $email
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ?: null;
}

    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $dateOfBirth,
        string $gender,
        string $dateOfJoining,
        int $departmentId,
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
                department_id,
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
                :department_id,
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
            ':department_id' => $departmentId,
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
            'SELECT
                e.*,
                d.department_name AS department
             FROM employees e
             JOIN departments d
                ON e.department_id = d.id
             ORDER BY e.id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->getConnection()->prepare(
            'SELECT
                e.*,
                d.department_name AS department
             FROM employees e
             JOIN departments d
                ON e.department_id = d.id
             WHERE e.id = :id'
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getByDepartmentId(int $departmentId): array
{
    $stmt = $this->conn->prepare(
        "SELECT
            id,
            first_name,
            last_name,
            email,
            designation,
            status
         FROM employees
         WHERE department_id = :department_id
         ORDER BY id ASC"
    );

    $stmt->execute([
        ':department_id' => $departmentId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getByEmail(string $email): ?array
    {
        $stmt = $this->getConnection()->prepare(
            'SELECT
                e.*,
                d.department_name AS department
             FROM employees e
             JOIN departments d ON e.department_id = d.id
             WHERE e.email = :email
             LIMIT 1'
        );

        $stmt->execute([
            ':email' => $email
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getFiltered(
    ?string $search = null,
    ?string $status = null,
    ?int $departmentId = null,
    int $limit = 10,
    int $offset = 0
): array {
    $sql = 'SELECT
                e.*,
                d.department_name AS department
            FROM employees e
            JOIN departments d
                ON e.department_id = d.id
            WHERE 1=1';

    $params = [];

    if ($search !== null && $search !== '') {

        if (ctype_digit($search)) {

            $sql .= ' AND e.id = :search_id';

            $params[':search_id'] = (int) $search;

        } else {

            $sql .= ' AND (
                e.first_name LIKE :search_first
                OR e.last_name LIKE :search_last
                OR e.email LIKE :search_email
                OR d.department_name LIKE :search_department
                OR e.designation LIKE :search_designation
            )';

            $searchValue = '%' . $search . '%';

            foreach ([
                ':search_first',
                ':search_last',
                ':search_email',
                ':search_department',
                ':search_designation'
            ] as $param) {
                $params[$param] = $searchValue;
            }
        }
    }

    if ($status !== null && $status !== '') {
        $sql .= ' AND e.status = :status';
        $params[':status'] = $status;
    }

    if ($departmentId !== null) {
        $sql .= ' AND e.department_id = :department_id';
        $params[':department_id'] = $departmentId;
    }

    $sql .= ' ORDER BY e.id ASC
              LIMIT :limit OFFSET :offset';

   $stmt = $this->getConnection()->prepare($sql);

foreach ($params as $param => $value) {
    $type = is_int($value)
        ? PDO::PARAM_INT
        : PDO::PARAM_STR;

    $stmt->bindValue(
        $param,
        $value,
        $type
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
  public function countFiltered(
    ?string $search = null,
    ?string $status = null,
    ?int $departmentId = null
): int {
    $sql = 'SELECT COUNT(*) AS total
            FROM employees e
            JOIN departments d
                ON e.department_id = d.id
            WHERE 1=1';

    $params = [];

    if ($search !== null && $search !== '') {

        if (ctype_digit($search)) {

            $sql .= ' AND e.id = :search_id';

            $params[':search_id'] = (int) $search;

        } else {

            $sql .= ' AND (
                e.first_name LIKE :search_first
                OR e.last_name LIKE :search_last
                OR e.email LIKE :search_email
                OR d.department_name LIKE :search_department
                OR e.designation LIKE :search_designation
            )';

            $searchValue = '%' . $search . '%';

            foreach ([
                ':search_first',
                ':search_last',
                ':search_email',
                ':search_department',
                ':search_designation'
            ] as $param) {
                $params[$param] = $searchValue;
            }
        }
    }

    if ($status !== null && $status !== '') {
        $sql .= ' AND e.status = :status';
        $params[':status'] = $status;
    }

    if ($departmentId !== null) {
        $sql .= ' AND e.department_id = :department_id';
        $params[':department_id'] = $departmentId;
    }

    $stmt = $this->getConnection()->prepare($sql);
    $stmt->execute($params);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int) ($result['total'] ?? 0);
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
            'department_id',
            'designation',
            'salary',
            'address',
            'profile_photo',
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

        $sql = 'UPDATE employees SET '
            . implode(', ', $updates)
            . ' WHERE id = :id';

        $stmt = $this->getConnection()->prepare($sql);

        return $stmt->execute($params);
    }

    public function deactivate(int $id): bool
    {
        $stmt = $this->getConnection()->prepare(
            'UPDATE employees
             SET status = :status
             WHERE id = :id'
        );

        return $stmt->execute([
            ':status' => 'inactive',
            ':id' => $id
        ]);
    }
}
