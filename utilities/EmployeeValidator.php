<?php

require_once __DIR__ . '/EmailValidator.php';

class EmployeeValidator
{
    private const ALLOWED_GENDERS = [
        'Male',
        'Female',
        'Other'
    ];

    private const ALLOWED_STATUSES = [
        'active',
        'inactive'
    ];

    private const ALLOWED_DEPARTMENTS = [
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


    public static function validateCreate(array $data): ?array
    {
        $emailError = self::validateEmail($data['email']);

        if ($emailError !== null) {
            return $emailError;
        }

        $genderError = self::validateGender($data['gender']);

        if ($genderError !== null) {
            return $genderError;
        }

        $statusError = self::validateStatus($data['status']);

        if ($statusError !== null) {
            return $statusError;
        }

        $departmentError = self::validateDepartment(
            $data['department']
        );

        if ($departmentError !== null) {
            return $departmentError;
        }

        $salaryError = self::validateSalary($data['salary']);

        if ($salaryError !== null) {
            return $salaryError;
        }

        return null;
    }


    public static function validateUpdate(array $data): ?array
    {
        if (array_key_exists('email', $data)) {
            $error = self::validateEmail($data['email']);

            if ($error !== null) {
                return $error;
            }
        }

        if (array_key_exists('gender', $data)) {
            $error = self::validateGender($data['gender']);

            if ($error !== null) {
                return $error;
            }
        }

        if (array_key_exists('status', $data)) {
            $error = self::validateStatus($data['status']);

            if ($error !== null) {
                return $error;
            }
        }

        if (array_key_exists('department', $data)) {
            $error = self::validateDepartment(
                $data['department']
            );

            if ($error !== null) {
                return $error;
            }
        }

        if (array_key_exists('salary', $data)) {
            $error = self::validateSalary($data['salary']);

            if ($error !== null) {
                return $error;
            }
        }

        return null;
    }


    private static function validateEmail(mixed $email): ?array
    {
        $errors = EmailValidator::validate(
            trim((string) $email)
        );

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => $errors[0],
                'statusCode' => 400
            ];
        }

        return null;
    }


    private static function validateGender(mixed $gender): ?array
    {
        $gender = trim((string) $gender);

        if (!in_array(
            $gender,
            self::ALLOWED_GENDERS,
            true
        )) {
            return [
                'success' => false,
                'message' => 'Gender must be Male, Female, or Other.',
                'statusCode' => 400
            ];
        }

        return null;
    }


    private static function validateStatus(mixed $status): ?array
    {
        $status = trim((string) $status);

        if (!in_array(
            $status,
            self::ALLOWED_STATUSES,
            true
        )) {
            return [
                'success' => false,
                'message' => 'Status must be active or inactive.',
                'statusCode' => 400
            ];
        }

        return null;
    }


    private static function validateDepartment(
        mixed $department
    ): ?array {
        $department = trim((string) $department);

        if (!in_array(
            $department,
            self::ALLOWED_DEPARTMENTS,
            true
        )) {
            return [
                'success' => false,
                'message' => 'Department is invalid.',
                'statusCode' => 400
            ];
        }

        return null;
    }
    public static function validateFilters(
    ?string $status,
    ?string $department
): ?array {
    $validStatuses = [
        'active',
        'inactive'
    ];

    $validDepartments = [
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

    if ($status !== null && !in_array($status, $validStatuses, true)) {
        return [
            'success' => false,
            'message' => 'Invalid status filter.',
            'statusCode' => 400
        ];
    }

    if (
        $department !== null &&
        !in_array($department, $validDepartments, true)
    ) {
        return [
            'success' => false,
            'message' => 'Invalid department filter.',
            'statusCode' => 400
        ];
    }

    return null;
}


    private static function validateSalary(
        mixed $salary
    ): ?array {
        if (!is_numeric($salary)) {
            return [
                'success' => false,
                'message' => 'Salary must be a valid number.',
                'statusCode' => 400
            ];
        }

        // if ((float) $salary < 0) {
        //     return [
        //         'success' => false,
        //         'message' => 'Salary cannot be negative.',
        //         'statusCode' => 400
        //     ];
        // }

        return null;
    }
}