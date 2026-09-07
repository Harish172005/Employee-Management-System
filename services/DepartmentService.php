<?php

require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../models/DepartmentRepository.php';
require_once __DIR__ . '/../traits/FieldValidationTrait.php';

class DepartmentService
{
    use FieldValidationTrait;
    public function createDepartment(array $data): array
    {
        $requiredFields = ['department_name', 'status'];

        $requiredError = $this->validateRequiredFields($data, $requiredFields);
        if ($requiredError !== null) {
            return $requiredError;
        }

        $departmentName = trim((string)$data['department_name']);
        $status = trim((string)$data['status']);

        if (!in_array($status, ['active', 'inactive'], true)) {
            return [
                'success' => false,
                'message' => 'Status must be active or inactive.',
                'statusCode' => 400
            ];
        }

        $description = isset($data['description']) ? trim((string)$data['description']) : null;
        if ($description === '') {
            $description = null;
        }

        $conn = DBConfig::getConnection();
        $departmentRepository = new DepartmentRepository($conn);

        if ($departmentRepository->findByName($departmentName)) {
            return [
                'success' => false,
                'message' => 'Department already exists.',
                'statusCode' => 409
            ];
        }

        $created = $departmentRepository->create(
            $departmentName,
            $description,
            $status
        );

        if (!$created) {
            return [
                'success' => false,
                'message' => 'Failed to create department.',
                'statusCode' => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Department created successfully.',
            'statusCode' => 201
        ];
    }

    public function getDepartments(
    ?string $search = null,
    ?string $status = null
): array {
    $search = $search !== null
        ? trim($search)
        : null;

    $status = $status !== null
        ? trim($status)
        : null;

    if (
        $status !== null &&
        $status !== '' &&
        !in_array(
            $status,
            ['active', 'inactive'],
            true
        )
    ) {
        return [
            'success' => false,
            'message' => 'Invalid status filter.',
            'statusCode' => 400
        ];
    }

    $conn = DBConfig::getConnection();
    $repository = new DepartmentRepository($conn);

    $departments = $repository->getFiltered(
        $search,
        $status
    );

    return [
        'success' => true,
        'message' => 'Departments retrieved successfully.',
        'data' => $departments,
        'statusCode' => 200
    ];
}

public function updateDepartment(int $id, array $data): array
{
    if ($id <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid department ID.',
            'statusCode' => 400
        ];
    }

    $requiredFields = [
        'department_name',
        'status'
    ];

    $requiredError = $this->validateRequiredFields(
        $data,
        $requiredFields
    );

    if ($requiredError !== null) {
        return $requiredError;
    }

    $departmentName = trim(
        (string) $data['department_name']
    );

    $status = trim(
        (string) $data['status']
    );

    $description = isset($data['description'])
        ? trim((string) $data['description'])
        : null;

    if ($description === '') {
        $description = null;
    }

    if (!in_array(
        $status,
        ['active', 'inactive'],
        true
    )) {
        return [
            'success' => false,
            'message' => 'Status must be active or inactive.',
            'statusCode' => 400
        ];
    }

    $conn = DBConfig::getConnection();

    $repository = new DepartmentRepository($conn);

    $department = $repository->getById($id);

    if (!$department) {
        return [
            'success' => false,
            'message' => 'Department not found.',
            'statusCode' => 404
        ];
    }

    if (
        $department['department_name'] !== $departmentName &&
        $repository->findByName($departmentName)
    ) {
        return [
            'success' => false,
            'message' => 'Department name already exists.',
            'statusCode' => 409
        ];
    }

    $updated = $repository->update(
        $id,
        [
            'department_name' => $departmentName,
            'description' => $description,
            'status' => $status
        ]
    );

    if (!$updated) {
        return [
            'success' => false,
            'message' => 'Failed to update department.',
            'statusCode' => 500
        ];
    }

    return [
        'success' => true,
        'message' => 'Department updated successfully.',
        'statusCode' => 200
    ];
  }

  public function getDepartmentById(int $id): array
{
    if ($id <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid department ID.',
            'statusCode' => 400
        ];
    }

    $conn = DBConfig::getConnection();

    $repository = new DepartmentRepository($conn);

    $department = $repository->getById($id);

    if (!$department) {
        return [
            'success' => false,
            'message' => 'Department not found.',
            'statusCode' => 404
        ];
    }

    return [
        'success' => true,
        'message' => 'Department retrieved successfully.',
        'data' => $department,
        'statusCode' => 200
    ];
}

public function deactivateDepartment(
        int $departmentId
    ): array {

        try {

            if ($departmentId <= 0) {
                return $this->error(
                    'Invalid department ID.',
                    400
                );
            }

            $conn =
                DBConfig::getConnection();

            $departmentRepository =
                new DepartmentRepository($conn);

            $department =
                $departmentRepository->getById(
                    $departmentId
                );

            if (!$department) {
                return $this->error(
                    'Department not found.',
                    404
                );
            }

            if (
                $department['status'] ===
                'inactive'
            ) {
                return $this->error(
                    'Department is already inactive.',
                    400
                );
            }

            $deactivated =
                $departmentRepository->deactivate(
                    $departmentId
                );

            if (!$deactivated) {
                return $this->error(
                    'Failed to deactivate department.',
                    500
                );
            }

            return [
                'success' => true,
                'message' =>
                    'Department deactivated successfully.',
                'statusCode' => 200
            ];

        } catch (Throwable $e) {

            $this->logException($e);

            return $this->error(
                'Failed to deactivate department.',
                500
            );
        }
    }

     private function error(
        string $message,
        int $statusCode
    ): array {

        return [
            'success' => false,
            'message' => $message,
            'statusCode' => $statusCode
        ];
    }

    private function logException(
        Throwable $e
    ): void {

        error_log(
            $e->getMessage()
        );

        error_log(
            $e->getTraceAsString()
        );
    }
}


