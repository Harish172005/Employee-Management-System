// ============================================================
// Page Initialization
// ============================================================

document.addEventListener(
    'DOMContentLoaded',
    async function () {

        const searchInput = document.getElementById(
            'employeeSearch'
        );

        const statusFilter = document.getElementById(
            'statusFilter'
        );

        const departmentFilter = document.getElementById(
            'departmentFilter'
        );

        const editForm = document.getElementById(
            'editEmployeeForm'
        );


        // Search
        searchInput.addEventListener(
            'input',
            function () {

                paginationState.currentPage = 1;

                fetchEmployees();
            }
        );
        // Status filter
        statusFilter.addEventListener(
            'change',
            function () {
                paginationState.currentPage = 1;
                fetchEmployees();
            }
        );

        // Department filter
        departmentFilter.addEventListener(
            'change',
            function () {

                paginationState.currentPage = 1;

                fetchEmployees();
            }
        );


        // Edit form
        editForm.addEventListener(
            'submit',
            submitEditEmployee
        );
        await fetchEmployees();
    }
);
