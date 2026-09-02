<?php

require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../models/EmployeeRepository.php';
require_once __DIR__ . '/../utilities/EmailValidator.php';
require_once __DIR__ . '/../utilities/FileValidator.php';

class EmployeeService
{
    public function createEmployee(array $data): array
    {
        $requiredFields = [
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
            'status'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                return [
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.',
                    'statusCode' => 400
                ];
            }
        }

        $uploadedPhotoPath = null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['profile_photo'];
            $fileErrors = FileValidator::validate($file);

            if (!empty($fileErrors)) {
                return [
                    'success' => false,
                    'message' => $fileErrors[0],
                    'statusCode' => 400
                ];
            }

            $uploadDir = __DIR__ . '/../public/uploads/profile-photos/';
            $uploadedPhotoPath = FileValidator::moveUploadedFile($file, $uploadDir);

            if ($uploadedPhotoPath === null) {
                return [
                    'success' => false,
                    'message' => 'Failed to upload the profile photo.',
                    'statusCode' => 500
                ];
            }
        }

        $emailErrors = EmailValidator::validate(trim((string)$data['email']));
        if (!empty($emailErrors)) {
            return [
                'success' => false,
                'message' => $emailErrors[0],
                'statusCode' => 400
            ];
        }

        $gender = trim((string)$data['gender']);
        if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
            return [
                'success' => false,
                'message' => 'Gender must be Male, Female, or Other.',
                'statusCode' => 400
            ];
        }

        $status = trim((string)$data['status']);
        if (!in_array($status, ['active', 'inactive'], true)) {
            return [
                'success' => false,
                'message' => 'Status must be active or inactive.',
                'statusCode' => 400
            ];
        }

        $department = trim((string)$data['department']);
        $allowedDepartments = [
            'IT',
            'Finance',
            'Marketing',
            'Sales',
            'Operations',
            'Administration',
            'Customer Support',
            'Research & Development',
            'Quality Assurance'
        ];

        if (!in_array($department, $allowedDepartments, true)) {
            return [
                'success' => false,
                'message' => 'Department is invalid.',
                'statusCode' => 400
            ];
        }

        if (!is_numeric($data['salary'])) {
            return [
                'success' => false,
                'message' => 'Salary must be a valid number.',
                'statusCode' => 400
            ];
        }

        $conn = DBConfig::getConnection();
        $employeeRepository = new EmployeeRepository($conn);

        $saved = $employeeRepository->create(
            trim((string)$data['first_name']),
            trim((string)$data['last_name']),
            trim((string)$data['email']),
            trim((string)$data['phone']),
            trim((string)$data['date_of_birth']),
            $gender,
            trim((string)$data['date_of_joining']),
            $department,
            trim((string)$data['designation']),
            (float)$data['salary'],
            trim((string)$data['address']),
            $uploadedPhotoPath,
            $status
        );

        if (!$saved) {
            return [
                'success' => false,
                'message' => 'Failed to create employee.',
                'statusCode' => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Employee created successfully.',
            'statusCode' => 201
        ];
    }
}
