<?php

interface DepartmentRepositoryInterface
{
    public function findByName(string $departmentName): ?array;

    public function getAll(): array;

    public function getFiltered(
        ?string $search,
        ?string $status
    ): array;

    public function getById(int $id): ?array;

    public function create(
        string $departmentName,
        ?string $description,
        string $status
    ): bool;

    public function update(int $id, array $data): bool;

    public function deactivate(int $id): bool;
}
