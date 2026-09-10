<?php

require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../models/DashboardRepository.php';

class DashboardService
{
    public function getDashboard(): array
    {
        try {
            $repository = new DashboardRepository(
                DBConfig::getConnection()
            );

            $employeeSummary = $repository->getEmployeeSummary();
            $departments = $repository->getEmployeeCountByDepartment();

            $formattedDepartments = [];

            foreach ($departments as $department) {
                $formattedDepartments[] = [
                    'id' => (int) $department['id'],
                    'name' => $department['department_name'],
                    'status' => $department['status'],
                    'employeeCount' => (int) $department['employee_count']
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'totalEmployees' => (int) (
                        $employeeSummary['total_employees'] ?? 0
                    ),
                    'activeEmployees' => (int) (
                        $employeeSummary['active_employees'] ?? 0
                    ),
                    'inactiveEmployees' => (int) (
                        $employeeSummary['inactive_employees'] ?? 0
                    ),
                    'totalDepartments' => $repository->getDepartmentCount(),
                    
                    'departments' => $formattedDepartments,
                ],
                'statusCode' => 200
            ];
        } catch (Throwable $e) {
            error_log($e->getMessage());

            return [
                'success' => false,
                'message' => 'Unable to load dashboard data.',
                'statusCode' => 500
            ];
        }
    }
}
