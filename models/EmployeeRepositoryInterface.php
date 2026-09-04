<?php

interface EmployeeRepositoryInterface
{
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
    ): bool;

    public function getAll(): array;

    public function getById(int $id): ?array;

    public function getFiltered(
        ?string $search = null,
        ?string $status = null,
        ?int $departmentId = null,
        int $limit = 10,
        int $offset = 0
    ): array;

    public function countFiltered(
        ?string $search = null,
        ?string $status = null,
        ?int $departmentId = null
    ): int;

    public function update(int $id, array $data): bool;

    public function deactivate(int $id): bool;
}