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

    public static function validateCreate(array $data): ?array
    {
        $emailError = self::validateEmail(
            $data['email'] ?? null
        );

        if ($emailError !== null) {
            return $emailError;
        }

        $phoneError = self::validatePhone(
                $data['phone'] ?? null
            );

            if ($phoneError !== null) {
                return $phoneError;
        }

        $genderError = self::validateGender(
            $data['gender'] ?? null
        );

        if ($genderError !== null) {
            return $genderError;
        }

        $statusError = self::validateStatus(
            $data['status'] ?? null
        );

        if ($statusError !== null) {
            return $statusError;
        }

        $salaryError = self::validateSalary(
            $data['salary'] ?? null
        );

        if ($salaryError !== null) {
            return $salaryError;
        }

        $departmentError = self::validateDepartmentId(
            $data['department_id'] ?? null
        );

        if ($departmentError !== null) {
            return $departmentError;
        }

        return null;
    }

    public static function validateUpdate(array $data): ?array
    {
        if (array_key_exists('email', $data)) {

            $emailError = self::validateEmail(
                $data['email']
            );

            if ($emailError !== null) {
                return $emailError;
            }
        }

        if (array_key_exists('gender', $data)) {

            $genderError = self::validateGender(
                $data['gender']
            );

            if ($genderError !== null) {
                return $genderError;
            }
        }

        if (array_key_exists('phone', $data)) {

            $phoneError = self::validatePhone(
                $data['phone']
            );

            if ($phoneError !== null) {
                return $phoneError;
            }
        }

        if (array_key_exists('status', $data)) {

            $statusError = self::validateStatus(
                $data['status']
            );

            if ($statusError !== null) {
                return $statusError;
            }
        }

        if (array_key_exists('salary', $data)) {

            $salaryError = self::validateSalary(
                $data['salary']
            );

            if ($salaryError !== null) {
                return $salaryError;
            }
        }

        if (array_key_exists('department_id', $data)) {

            $departmentError =
                self::validateDepartmentId(
                    $data['department_id']
                );

            if ($departmentError !== null) {
                return $departmentError;
            }
        }

        return null;
    }

    public static function validateFilters(
        ?string $status,
        ?int $departmentId
    ): ?array {

        if (
            $status !== null &&
            !in_array(
                $status,
                self::ALLOWED_STATUSES,
                true
            )
        ) {
            return [
                'success' => false,
                'message' => 'Invalid status filter.',
                'statusCode' => 400
            ];
        }

        if (
            $departmentId !== null &&
            $departmentId <= 0
        ) {
            return [
                'success' => false,
                'message' => 'Invalid department filter.',
                'statusCode' => 400
            ];
        }

        return null;
    }

    private static function validateDepartmentId(
        mixed $departmentId
    ): ?array {

        if (
            filter_var(
                $departmentId,
                FILTER_VALIDATE_INT
            ) === false ||
            (int) $departmentId <= 0
        ) {
            return [
                'success' => false,
                'message' => 'Department ID must be a valid positive integer.',
                'statusCode' => 400
            ];
        }

        return null;
    }

    private static function validateEmail(
        mixed $email
    ): ?array {

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

    private static function validatePhone(mixed $phone): ?array
   {
    $phone = trim((string) $phone);

    if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
        return [
            'success' => false,
            'message' => 'Phone number must be a valid 10-digit mobile number.',
            'statusCode' => 400
        ];
    }

    return null;
   }

    private static function validateGender(
        mixed $gender
    ): ?array {

        $gender = trim((string) $gender);

        if (
            !in_array(
                $gender,
                self::ALLOWED_GENDERS,
                true
            )
        ) {
            return [
                'success' => false,
                'message' => 'Gender must be Male, Female, or Other.',
                'statusCode' => 400
            ];
        }

        return null;
    }

    private static function validateStatus(
        mixed $status
    ): ?array {

        $status = trim((string) $status);

        if (
            !in_array(
                $status,
                self::ALLOWED_STATUSES,
                true
            )
        ) {
            return [
                'success' => false,
                'message' => 'Status must be active or inactive.',
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

        return null;
    }
}