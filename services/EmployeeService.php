<?php

require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../models/EmployeeRepository.php';
require_once __DIR__ . '/../models/DepartmentRepository.php';
require_once __DIR__ . '/../traits/FieldValidationTrait.php';
require_once __DIR__ . '/../utilities/EmployeeValidator.php';
require_once __DIR__ . '/../utilities/FileValidator.php';

class EmployeeService
{
    use FieldValidationTrait;

    private const PER_PAGE = 5;

    private const UPLOAD_DIR =
        __DIR__ . '/../public/uploads/profile-photos/';

    public function getEmployees(array $filters = []): array
    {
        try {
            $conn = DBConfig::getConnection();

            $employeeRepository =
                new EmployeeRepository($conn);

            $departmentRepository =
                new DepartmentRepository($conn);

            $search = $this->cleanFilter(
                $filters['search'] ?? null
            );

            $status = $this->cleanFilter(
                $filters['status'] ?? null
            );

            $departmentId = null;

            if (
                isset($filters['department_id']) &&
                $filters['department_id'] !== ''
            ) {
                $departmentId =
                    filter_var(
                        $filters['department_id'],
                        FILTER_VALIDATE_INT
                    );

                if (
                    $departmentId === false ||
                    $departmentId <= 0
                ) {
                    return $this->error(
                        'Invalid department filter.',
                        400
                    );
                }

                $departmentId = (int) $departmentId;
            }

            $page = isset($filters['page'])
                ? max(1, (int) $filters['page'])
                : 1;

            $offset =
                ($page - 1) * self::PER_PAGE;

            $filterError =
                EmployeeValidator::validateFilters(
                    $status,
                    $departmentId
                );

            if ($filterError !== null) {
                return $filterError;
            }

            if ($departmentId !== null) {

                $department =
                    $departmentRepository->getById(
                        $departmentId
                    );

                if (!$department) {
                    return $this->error(
                        'Department not found.',
                        400
                    );
                }

                if (
                    $department['status'] !==
                    'active'
                ) {
                    return $this->error(
                        'Department is inactive.',
                        400
                    );
                }
            }

            $totalCount =
                $employeeRepository->countFiltered(
                    $search,
                    $status,
                    $departmentId
                );

            $totalPages =
                (int) ceil(
                    $totalCount / self::PER_PAGE
                );

            $employees =
                $employeeRepository->getFiltered(
                    $search,
                    $status,
                    $departmentId,
                    self::PER_PAGE,
                    $offset
                );

            return [
                'success' => true,
                'message' =>
                    'Employees retrieved successfully.',
                'data' => $employees,
                'pagination' => [
                    'currentPage' => $page,
                    'perPage' => self::PER_PAGE,
                    'totalCount' => $totalCount,
                    'totalPages' => $totalPages
                ],
                'statusCode' => 200
            ];

        } catch (Throwable $e) {

            $this->logException($e);

            return $this->error(
                'Failed to fetch employees.',
                500
            );
        }
    }

    public function getEmployeeById(
        int $employeeId
    ): array {

        if ($employeeId <= 0) {
            return $this->error(
                'Invalid employee ID.',
                400
            );
        }

        try {

            $conn =
                DBConfig::getConnection();

            $employeeRepository =
                new EmployeeRepository($conn);

            $employee =
                $employeeRepository->getById(
                    $employeeId
                );

            if (!$employee) {
                return $this->error(
                    'Employee not found.',
                    404
                );
            }

            return [
                'success' => true,
                'message' =>
                    'Employee retrieved successfully.',
                'data' => $employee,
                'statusCode' => 200
            ];

        } catch (Throwable $e) {

            $this->logException($e);

            return $this->error(
                'Failed to retrieve employee.',
                500
            );
        }
    }

    public function createEmployee(
        array $data
    ): array {

        $uploadedPhotoPath = null;

        try {

            $requiredFields = [
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
                'status'
            ];

            $requiredError =
                $this->validateRequiredFields(
                    $data,
                    $requiredFields
                );

            if ($requiredError !== null) {
                return $requiredError;
            }

            $validationError =
                EmployeeValidator::validateCreate(
                    $data
                );

            if ($validationError !== null) {
                return $validationError;
            }

            $conn =
                DBConfig::getConnection();

            $departmentRepository =
                new DepartmentRepository($conn);

            $employeeRepository =
                new EmployeeRepository($conn);

            $departmentId =
                (int) $data['department_id'];

            $department =
                $departmentRepository->getById(
                    $departmentId
                );

            if (!$department) {
                return $this->error(
                    'Department not found.',
                    400
                );
            }

            if (
                $department['status'] !==
                'active'
            ) {
                return $this->error(
                    'Department is inactive.',
                    400
                );
            }

                if ($employeeRepository->findByEmail($email)) {
                    return $this->error(
                        'Email already exists.',
                        409
                    );
                }

            $uploadResult =
                $this->uploadProfilePhoto(
                    $_FILES['profile_photo'] ?? null
                );

            if (
                is_array($uploadResult) &&
                $uploadResult['success'] === false
            ) {
                return $uploadResult;
            }

            $uploadedPhotoPath =
                $uploadResult;

            $saved =
                $employeeRepository->create(
                    trim(
                        (string) $data['first_name']
                    ),
                    trim(
                        (string) $data['last_name']
                    ),
                    trim(
                        (string) $data['email']
                    ),
                    trim(
                        (string) $data['phone']
                    ),
                    trim(
                        (string) $data['date_of_birth']
                    ),
                    trim(
                        (string) $data['gender']
                    ),
                    trim(
                        (string) $data['date_of_joining']
                    ),
                    $departmentId,
                    trim(
                        (string) $data['designation']
                    ),
                    (float) $data['salary'],
                    trim(
                        (string) $data['address']
                    ),
                    $uploadedPhotoPath,
                    trim(
                        (string) $data['status']
                    )
                );

            if (!$saved) {

                $this->deletePhoto(
                    $uploadedPhotoPath
                );

                return $this->error(
                    'Failed to create employee.',
                    500
                );
            }

            return [
                'success' => true,
                'message' =>
                    'Employee created successfully.',
                'statusCode' => 201
            ];

        } catch (Throwable $e) {

            if ($uploadedPhotoPath !== null) {
                $this->deletePhoto(
                    $uploadedPhotoPath
                );
            }

            $this->logException($e);

            return $this->error(
                'Failed to create employee.',
                500
            );
        }
    }

    public function updateEmployee(
        int $employeeId,
        array $data,
        ?array $file = null
    ): array {

        $uploadedPhotoPath = null;

        try {

            if ($employeeId <= 0) {
                return $this->error(
                    'Invalid employee ID.',
                    400
                );
            }

            $conn =
                DBConfig::getConnection();

            $employeeRepository =
                new EmployeeRepository($conn);

            $employee =
                $employeeRepository->getById(
                    $employeeId
                );

            if (!$employee) {
                return $this->error(
                    'Employee not found.',
                    404
                );
            }

            $validationError =
                EmployeeValidator::validateUpdate(
                    $data
                );

            if ($validationError !== null) {
                return $validationError;
            }

            $updateData = [];

            if (
                array_key_exists(
                    'first_name',
                    $data
                )
            ) {

                $firstName =
                    trim(
                        (string) $data['first_name']
                    );

                if ($firstName === '') {
                    return $this->error(
                        'First name is required.',
                        400
                    );
                }

                $updateData['first_name'] =
                    $firstName;
            }

            if (
                array_key_exists(
                    'last_name',
                    $data
                )
            ) {

                $lastName =
                    trim(
                        (string) $data['last_name']
                    );

                if ($lastName === '') {
                    return $this->error(
                        'Last name is required.',
                        400
                    );
                }

                $updateData['last_name'] =
                    $lastName;
            }

            if (
                array_key_exists(
                    'email',
                    $data
                )
            ) {

                $updateData['email'] =
                    trim(
                        (string) $data['email']
                    );
            }

            if (
                array_key_exists(
                    'phone',
                    $data
                )
            ) {

                $phone =
                    trim(
                        (string) $data['phone']
                    );

                if ($phone !== '') {
                    $updateData['phone'] =
                        $phone;
                }
            }

            if (
                array_key_exists(
                    'date_of_birth',
                    $data
                )
            ) {

                $updateData['date_of_birth'] =
                    trim(
                        (string) $data['date_of_birth']
                    );
            }

            if (
                array_key_exists(
                    'gender',
                    $data
                )
            ) {

                $updateData['gender'] =
                    trim(
                        (string) $data['gender']
                    );
            }

            if (
                array_key_exists(
                    'date_of_joining',
                    $data
                )
            ) {

                $updateData['date_of_joining'] =
                    trim(
                        (string) $data['date_of_joining']
                    );
            }

            if (
                array_key_exists(
                    'department_id',
                    $data
                )
            ) {

                $departmentId =
                    (int) $data['department_id'];

                $departmentRepository =
                    new DepartmentRepository(
                        $conn
                    );

                $department =
                    $departmentRepository->getById(
                        $departmentId
                    );

                if (!$department) {
                    return $this->error(
                        'Department not found.',
                        400
                    );
                }

                if (
                    $department['status'] !==
                    'active'
                ) {
                    return $this->error(
                        'Department is inactive.',
                        400
                    );
                }

                $updateData['department_id'] =
                    $departmentId;
            }

            if (
                array_key_exists(
                    'designation',
                    $data
                )
            ) {

                $designation =
                    trim(
                        (string) $data['designation']
                    );

                if ($designation !== '') {
                    $updateData['designation'] =
                        $designation;
                }
            }

            if (
                array_key_exists(
                    'salary',
                    $data
                )
            ) {

                $updateData['salary'] =
                    (float) $data['salary'];
            }

            if (
                array_key_exists(
                    'address',
                    $data
                )
            ) {

                $address =
                    trim(
                        (string) $data['address']
                    );

                if ($address !== '') {
                    $updateData['address'] =
                        $address;
                }
            }

            if (
                array_key_exists(
                    'status',
                    $data
                )
            ) {

                $updateData['status'] =
                    trim(
                        (string) $data['status']
                    );
            }

            $oldPhoto =
                $employee['profile_photo'] ??
                null;

            if (
                $file !== null &&
                $file['error'] !==
                UPLOAD_ERR_NO_FILE
            ) {

                $uploadResult =
                    $this->uploadProfilePhoto(
                        $file
                    );

                if (
                    is_array($uploadResult) &&
                    $uploadResult['success'] === false
                ) {
                    return $uploadResult;
                }

                $uploadedPhotoPath =
                    $uploadResult;

                $updateData['profile_photo'] =
                    $uploadedPhotoPath;
            }

            if (empty($updateData)) {
                return $this->error(
                    'No valid fields provided for update.',
                    400
                );
            }

            $updated =
                $employeeRepository->update(
                    $employeeId,
                    $updateData
                );

            if (!$updated) {

                $this->deletePhoto(
                    $uploadedPhotoPath
                );

                return $this->error(
                    'Failed to update employee.',
                    500
                );
            }

            if ($uploadedPhotoPath !== null) {
                $this->deletePhoto(
                    $oldPhoto
                );
            }

            return [
                'success' => true,
                'message' =>
                    'Employee updated successfully.',
                'statusCode' => 200
            ];

        } catch (Throwable $e) {

            if ($uploadedPhotoPath !== null) {
                $this->deletePhoto(
                    $uploadedPhotoPath
                );
            }

            $this->logException($e);

            return $this->error(
                'Failed to update employee.',
                500
            );
        }
    }

    public function deactivateEmployee(
        int $employeeId
    ): array {

        try {

            if ($employeeId <= 0) {
                return $this->error(
                    'Invalid employee ID.',
                    400
                );
            }

            $conn =
                DBConfig::getConnection();

            $employeeRepository =
                new EmployeeRepository($conn);

            $employee =
                $employeeRepository->getById(
                    $employeeId
                );

            if (!$employee) {
                return $this->error(
                    'Employee not found.',
                    404
                );
            }

            if (
                $employee['status'] ===
                'inactive'
            ) {
                return $this->error(
                    'Employee is already inactive.',
                    400
                );
            }

            $deactivated =
                $employeeRepository->deactivate(
                    $employeeId
                );

            if (!$deactivated) {
                return $this->error(
                    'Failed to deactivate employee.',
                    500
                );
            }

            return [
                'success' => true,
                'message' =>
                    'Employee deactivated successfully.',
                'statusCode' => 200
            ];

        } catch (Throwable $e) {

            $this->logException($e);

            return $this->error(
                'Failed to deactivate employee.',
                500
            );
        }
    }

    private function uploadProfilePhoto(
        ?array $file
    ): string|array|null {

        if (
            $file === null ||
            $file['error'] ===
            UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        $fileErrors =
            FileValidator::validate(
                $file
            );

        if (!empty($fileErrors)) {
            return $this->error(
                $fileErrors[0],
                400
            );
        }

        $uploadedPath =
            FileValidator::moveUploadedFile(
                $file,
                self::UPLOAD_DIR
            );

        if ($uploadedPath === null) {
            return $this->error(
                'Failed to upload the profile photo.',
                500
            );
        }

        return $uploadedPath;
    }

    private function deletePhoto(
        ?string $photoPath
    ): void {

        if (empty($photoPath)) {
            return;
        }

        $fullPath =
            __DIR__ . '/../public/' .
            $photoPath;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    private function cleanFilter(
        mixed $value
    ): ?string {

        if ($value === null) {
            return null;
        }

        $value =
            trim(
                (string) $value
            );

        return $value === ''
            ? null
            : $value;
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