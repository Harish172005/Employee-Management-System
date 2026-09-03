<?php

require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../models/EmployeeRepository.php';
require_once __DIR__ . '/../traits/FieldValidationTrait.php';
require_once __DIR__ . '/../utilities/EmailValidator.php';
require_once __DIR__ . '/../utilities/FileValidator.php';

class EmployeeService
{
    use FieldValidationTrait;
    public function getEmployees(array $filters = []): array
    {
        try {
            $conn = DBConfig::getConnection();
            $employeeRepository = new EmployeeRepository($conn);

            $search = isset($filters['search']) ? trim((string)$filters['search']) : null;
            $status = isset($filters['status']) ? trim((string)$filters['status']) : null;
            $department = isset($filters['department']) ? trim((string)$filters['department']) : null;
            $page = isset($filters['page']) ? intval($filters['page']) : 1;
            $perPage = 5;

            if ($page < 1) {
                $page = 1;
            }

            $offset = ($page - 1) * $perPage;

            if ($status !== null && $status !== '' && !in_array($status, ['active', 'inactive'], true)) {
                return [
                    'success' => false,
                    'message' => 'Invalid status filter.',
                    'statusCode' => 400
                ];
            }

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

            if ($department !== null && $department !== '' && !in_array($department, $allowedDepartments, true)) {
                return [
                    'success' => false,
                    'message' => 'Invalid department filter.',
                    'statusCode' => 400
                ];
            }

            $totalCount = $employeeRepository->countFiltered($search !== '' ? $search : null, $status !== '' ? $status : null, $department !== '' ? $department : null);
            $totalPages = ceil($totalCount / $perPage);

            $employees = $employeeRepository->getFiltered($search !== '' ? $search : null, $status !== '' ? $status : null, $department !== '' ? $department : null, $perPage, $offset);

            return [
                'success' => true,
                'data' => $employees,
                'pagination' => [
                    'currentPage' => $page,
                    'perPage' => $perPage,
                    'totalCount' => $totalCount,
                    'totalPages' => $totalPages
                ],
                'statusCode' => 200
            ];
        } catch (Throwable $e) {
             error_log($e->getMessage());
             error_log($e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Failed to fetch employees.',
                'statusCode' => 500
            ];
        }
    }

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

        $requiredError = $this->validateRequiredFields($data, $requiredFields);
        if ($requiredError !== null) {
            return $requiredError;
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

   public function updateEmployee(int $employeeId, array $data, ?array $file = null): array
   {
    try {
        $conn = DBConfig::getConnection();
        $employeeRepository = new EmployeeRepository($conn);

        // Check if employee exists
        $employee = $employeeRepository->getById($employeeId);
        if (!$employee) {
            return [
                'success' => false,
                'message' => 'Employee not found.',
                'statusCode' => 404
            ];
        }

        $updateData = [];

        // Validate and prepare fields to update
        if (isset($data['first_name'])) {
            $firstName = trim((string)$data['first_name']);
            if ($firstName === '') {
                return [
                    'success' => false,
                    'message' => 'First name is required.',
                    'statusCode' => 400
                ];
            }
            $updateData['first_name'] = $firstName;
        }

        if (isset($data['last_name'])) {
            $lastName = trim((string)$data['last_name']);
            if ($lastName === '') {
                return [
                    'success' => false,
                    'message' => 'Last name is required.',
                    'statusCode' => 400
                ];
            }
            $updateData['last_name'] = $lastName;
        }

        if (isset($data['email'])) {
            $email = trim((string)$data['email']);
            $emailErrors = EmailValidator::validate($email);
            if (!empty($emailErrors)) {
                return [
                    'success' => false,
                    'message' => $emailErrors[0],
                    'statusCode' => 400
                ];
            }
            $updateData['email'] = $email;
        }

        if (isset($data['phone'])) {
            $phone = trim((string)$data['phone']);
            if ($phone !== '') {
                $updateData['phone'] = $phone;
            }
        }

        if (isset($data['gender'])) {
            $gender = trim((string)$data['gender']);
            if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
                return [
                    'success' => false,
                    'message' => 'Gender must be Male, Female, or Other.',
                    'statusCode' => 400
                ];
            }
            $updateData['gender'] = $gender;
        }

        if (isset($data['department'])) {
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
            $updateData['department'] = $department;
        }

        if (isset($data['designation'])) {
            $designation = trim((string)$data['designation']);
            if ($designation !== '') {
                $updateData['designation'] = $designation;
            }
        }

        if (isset($data['salary'])) {
            if (!is_numeric($data['salary'])) {
                return [
                    'success' => false,
                    'message' => 'Salary must be a valid number.',
                    'statusCode' => 400
                ];
            }
            $updateData['salary'] = (float)$data['salary'];
        }

        if (isset($data['address'])) {
            $address = trim((string)$data['address']);
            if ($address !== '') {
                $updateData['address'] = $address;
            }
        }

        if (isset($data['status'])) {
            $status = trim((string)$data['status']);
            if (!in_array($status, ['active', 'inactive'], true)) {
                return [
                    'success' => false,
                    'message' => 'Status must be active or inactive.',
                    'statusCode' => 400
                ];
            }
            $updateData['status'] = $status;
        }

        $uploadedPhotoPath = null;
        $oldPhoto = $employee['profile_photo'] ?? null;

        if (
            isset($file) &&
            $file['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $fileErrors = FileValidator::validate($file);

            if (!empty($fileErrors)) {
                return [
                    'success' => false,
                    'message' => $fileErrors[0],
                    'statusCode' => 400
                ];
            }

            $uploadDir = __DIR__ . '/../public/uploads/profile-photos/';

            $uploadedPhotoPath = FileValidator::moveUploadedFile(
                $file,
                $uploadDir
            );

            if ($uploadedPhotoPath === null) {
                return [
                    'success' => false,
                    'message' => 'Failed to upload the profile photo.',
                    'statusCode' => 500
                ];
            }
            $updateData['profile_photo'] = $uploadedPhotoPath;
        }

        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'No valid fields provided for update.',
                'statusCode' => 400
            ];
        }

        $updated = $employeeRepository->update(
            $employeeId,
            $updateData
        );

        if (!$updated) {
            if ($uploadedPhotoPath !== null) {
                $newPhotoPath = __DIR__ . '/../public/' . $uploadedPhotoPath;

                if (file_exists($newPhotoPath)) {
                    unlink($newPhotoPath);
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to update employee.',
                'statusCode' => 500
            ];
        }

        if (
            $uploadedPhotoPath !== null &&
            !empty($oldPhoto)
        ) {
            $oldPhotoPath = __DIR__ . '/../public/' . $oldPhoto;

            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
        }

        return [
            'success' => true,
            'message' => 'Employee updated successfully.',
            'statusCode' => 200
        ];
    } catch (Throwable $e) {
        return [
            'success' => false,
            'message' => 'Failed to update employee.',
            'statusCode' => 500
        ];
    }
}
    public function deactivateEmployee(int $employeeId): array
    {
        try {
            $conn = DBConfig::getConnection();
            $employeeRepository = new EmployeeRepository($conn);

            // Check if employee exists
            $employee = $employeeRepository->getById($employeeId);
            if (!$employee) {
                return [
                    'success' => false,
                    'message' => 'Employee not found.',
                    'statusCode' => 404
                ];
            }

            // Check if already inactive
            if ($employee['status'] === 'inactive') {
                return [
                    'success' => false,
                    'message' => 'Employee is already inactive.',
                    'statusCode' => 400
                ];
            }

            $deactivated = $employeeRepository->deactivate($employeeId);

            if (!$deactivated) {
                return [
                    'success' => false,
                    'message' => 'Failed to deactivate employee.',
                    'statusCode' => 500
                ];
            }

            return [
                'success' => true,
                'message' => 'Employee deactivated successfully.',
                'statusCode' => 200
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to deactivate employee.',
                'statusCode' => 500
            ];
        }
    }
}
