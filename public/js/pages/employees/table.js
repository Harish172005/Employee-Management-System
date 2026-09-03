// ============================================================
// Fetch Employees
// ============================================================

async function fetchEmployees() {

    const tableBody = document.getElementById(
        'employeesTableBody'
    );

    const searchInput = document.getElementById(
        'employeeSearch'
    );

    const statusFilter = document.getElementById(
        'statusFilter'
    );

    const departmentFilter = document.getElementById(
        'departmentFilter'
    );

    try {

        const params = new URLSearchParams();

        const search = searchInput.value.trim();
        const status = statusFilter.value;
        const department = departmentFilter.value;

        if (search) {
            params.set('search', search);
        }

        if (status) {
            params.set('status', status);
        }

        if (department) {
            params.set('department', department);
        }

        params.set(
            'page',
            paginationState.currentPage
        );

        const response = await fetch(
            `/api/employees?${params.toString()}`,
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message ||
                'Unable to load employees.'
            );
        }

        const employees = result.data || [];
        const pagination = result.pagination || {};

        paginationState.currentPage =
            pagination.currentPage || 1;

        paginationState.totalPages =
            pagination.totalPages || 1;

        // Update department filter
        updateDepartmentFilter(
            employees,
            department
        );

        // No employees
        if (!employees.length) {

            tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="9"
                        class="text-center text-muted">
                        No employees found.
                    </td>
                </tr>
            `;

            updatePaginationControls();

            return;
        }

        // Display employees
        tableBody.innerHTML = employees
            .map(employee => `
                <tr>

                    <td>${employee.id}</td>

                    <td>
                        ${employee.first_name}
                        ${employee.last_name}
                    </td>

                    <td>${employee.email}</td>

                    <td>${employee.phone || '-'}</td>

                    <td>${employee.department}</td>

                    <td>${employee.designation || '-'}</td>

                    <td>
                        <span class="badge ${
                            employee.status === 'active'
                                ? 'bg-success'
                                : 'bg-secondary'
                        }">
                            ${employee.status}
                        </span>
                    </td>

                    <td>
                        ${Number(
                            employee.salary || 0
                        ).toLocaleString(
                            undefined,
                            {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }
                        )}
                    </td>

                    <td>

                        <button
                            class="btn btn-sm btn-info text-white"
                            onclick="viewEmployeeInfo(${employee.id})">
                            View
                        </button>

                        <button
                            class="btn btn-sm btn-warning text-dark"
                            onclick="editEmployee(${employee.id})">
                            Edit
                        </button>

                        ${
                            employee.status === 'active'
                                ? `
                                    <button
                                        class="btn btn-sm btn-danger"
                                        onclick="deactivateEmployee(${employee.id})">
                                        Deactivate
                                    </button>
                                  `
                                : `
                                    <span class="badge bg-secondary">
                                        Inactive
                                    </span>
                                  `
                        }

                    </td>

                </tr>
            `)
            .join('');

        updatePaginationControls();

    } catch (error) {

        tableBody.innerHTML = `
            <tr>
                <td
                    colspan="9"
                    class="text-center text-danger">
                    ${error.message}
                </td>
            </tr>
        `;
    }
}


// ============================================================
// Department Filter
// ============================================================

function updateDepartmentFilter(
    employees,
    selectedDepartment
) {

    const departmentFilter = document.getElementById(
        'departmentFilter'
    );

    const departments = [
        ...new Set(
            employees
                .map(employee => employee.department)
                .filter(Boolean)
        )
    ];

    departmentFilter.innerHTML = `
        <option value="">
            All departments
        </option>
    `;

    departments.forEach(department => {

        const option = document.createElement('option');

        option.value = department;
        option.textContent = department;

        departmentFilter.appendChild(option);
    });

    if (departments.includes(selectedDepartment)) {
        departmentFilter.value = selectedDepartment;
    }
}
