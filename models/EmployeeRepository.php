<?php

class EmployeeRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

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
        $stmt = $this->conn->prepare(
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
}
