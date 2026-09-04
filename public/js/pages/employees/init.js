document.addEventListener('DOMContentLoaded', async function () {
    const searchInput = document.getElementById('employeeSearch');
    const statusFilter = document.getElementById('statusFilter');
    const departmentFilter = document.getElementById('departmentFilter');
    const editForm = document.getElementById('editEmployeeForm');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            paginationState.currentPage = 1;
            fetchEmployees();
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', function () {
            paginationState.currentPage = 1;
            fetchEmployees();
        });
    }

    if (departmentFilter) {
        departmentFilter.addEventListener('change', function () {
            paginationState.currentPage = 1;
            fetchEmployees();
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', submitEditEmployee);
    }

    await loadDepartmentFilter();
    await fetchEmployees();
});
