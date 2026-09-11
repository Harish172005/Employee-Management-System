<?php

require_once __DIR__ . '/BaseRepository.php';

class DashboardRepository extends BaseRepository
{
    public function getEmployeeSummary(): array
    {
        $stmt = $this->getConnection()->query(
            "SELECT
    (SELECT COUNT(*) FROM employees) AS total_employees,
    (SELECT COUNT(*) FROM employees WHERE status = 'active') AS active_employees,
    (SELECT COUNT(*) FROM employees WHERE status = 'inactive') AS inactive_employees;"
        );

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getDepartmentCount(): int
    {
        return (int) $this->getConnection()
            ->query('SELECT COUNT(*) FROM departments')
            ->fetchColumn();
    }

    public function getEmployeeCountByDepartment(): array
    {
        $stmt = $this->getConnection()->query(
            'SELECT
                d.id,
                d.department_name,
                d.status,
                COUNT(e.id) AS employee_count
             FROM departments d
             LEFT JOIN employees e ON e.department_id = d.id
             GROUP BY d.department_name
             ORDER BY d.department_name ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
