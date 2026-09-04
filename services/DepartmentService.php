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

    public function getDepartments(): array
   {
    $conn = DBConfig::getConnection();
    $repository = new DepartmentRepository($conn);

    $departments = $repository->getAll();

    return [
        'success' => true,
        'message' => 'Departments retrieved successfully.',
        'data' => $departments,
        'statusCode' => 200
    ];
  }
}
