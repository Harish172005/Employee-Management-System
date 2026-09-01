<?php

require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../models/UserRepository.php';
require_once __DIR__ . '/../utilities/EmailValidator.php';
require_once __DIR__ . '/../utilities/FileValidator.php';

class EmployeeService
{
    public function createEmployee(array $data): array
    {
        $requiredFields = [
            'employee_id',
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

        if (!is_numeric($data['salary'])) {
            return [
                'success' => false,
                'message' => 'Salary must be a valid number.',
                'statusCode' => 400
            ];
        }

        $conn = DBConfig::getConnection();

        $stmt = $conn->prepare(
            'INSERT INTO employees (
                employee_id,
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
                :employee_id,
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

        $saved = $stmt->execute([
            ':employee_id' => trim((string)$data['employee_id']),
            ':first_name' => trim((string)$data['first_name']),
            ':last_name' => trim((string)$data['last_name']),
            ':email' => trim((string)$data['email']),
            ':phone' => trim((string)$data['phone']),
            ':date_of_birth' => trim((string)$data['date_of_birth']),
            ':gender' => $gender,
            ':date_of_joining' => trim((string)$data['date_of_joining']),
            ':department' => trim((string)$data['department']),
            ':designation' => trim((string)$data['designation']),
            ':salary' => (float)$data['salary'],
            ':address' => trim((string)$data['address']),
            ':profile_photo' => $uploadedPhotoPath,
            ':status' => $status
        ]);

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
