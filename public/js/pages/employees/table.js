async function loadDepartmentFilter(selectedDepartmentId = '') {

    const departmentFilter =
        document.getElementById('departmentFilter');

    if (!departmentFilter) {
        return;
    }

    try {

        const response = await fetch(
            '/api/departments',
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message ||
                'Unable to load departments.'
            );
        }

        const departments =
            Array.isArray(result.data)
                ? result.data
                : [];

        departmentFilter.innerHTML = `
            <option value="">
                All Departments
            </option>
        `;

        departments
            .filter(
                department =>
                    department.status === 'active'
            )
            .forEach(
                department => {

                    const option =
                        document.createElement('option');

                    option.value =
                        department.id;

                    option.textContent =
                        department.department_name;

                    departmentFilter.appendChild(
                        option
                    );
                }
            );

        departmentFilter.value =
            String(selectedDepartmentId);

    } catch (error) {

        console.error(
            'Unable to load departments:',
            error
        );

        departmentFilter.innerHTML = `
            <option value="">
                Unable to load departments
            </option>
        `;
    }
}


async function fetchEmployees() {

    const tableBody =
        document.getElementById(
            'employeeTableBody'
        );

    const searchInput =
        document.getElementById(
            'employeeSearch'
        );

    const statusFilter =
        document.getElementById(
            'statusFilter'
        );

    const departmentFilter =
        document.getElementById(
            'departmentFilter'
        );

    if (!tableBody) {
        return;
    }

    const params =
        new URLSearchParams();

    const search =
        searchInput
            ? searchInput.value.trim()
            : '';

    const status =
        statusFilter
            ? statusFilter.value
            : '';

    const departmentId =
        departmentFilter
            ? departmentFilter.value
            : '';

    if (search) {
        params.set(
            'search',
            search
        );
    }

    if (status) {
        params.set(
            'status',
            status
        );
    }

    if (departmentId) {
        params.set(
            'department_id',
            departmentId
        );
    }

    if (
        paginationState.currentPage > 0
    ) {
        params.set(
            'page',
            paginationState.currentPage
        );
    }

    tableBody.innerHTML = `
        <tr>
            <td colspan="10" class="text-center">
                Loading...
            </td>
        </tr>
    `;

    try {

        const queryString =
            params.toString();

        const url =
            queryString
                ? `/api/employees?${queryString}`
                : '/api/employees';

        const response =
            await fetch(
                url,
                {
                    method: 'GET',
                    credentials: 'include'
                }
            );

        const result =
            await response.json();

        if (!response.ok) {
            throw new Error(
                result.message ||
                'Unable to load employees.'
            );
        }

        const employees =
            Array.isArray(result.data)
                ? result.data
                : [];

        if (result.pagination) {

            paginationState.currentPage =
                Number(
                    result.pagination.currentPage
                ) || 1;

            paginationState.totalPages =
                Number(
                    result.pagination.totalPages
                ) || 1;
        }

        renderEmployees(
            employees
        );

        updatePaginationControls();

    } catch (error) {

        console.error(
            'Employee loading error:',
            error
        );

        tableBody.innerHTML = `
            <tr>
                <td
                    colspan="10"
                    class="text-center text-danger">
                    ${escapeHtml(error.message)}
                </td>
            </tr>
        `;
    }
}


function renderEmployees(employees) {

    const tableBody =
        document.getElementById(
            'employeeTableBody'
        );

    if (!tableBody) {
        return;
    }

    if (employees.length === 0) {

        tableBody.innerHTML = `
            <tr>
                <td
                    colspan="10"
                    class="text-center text-muted">
                    No employees found.
                </td>
            </tr>
        `;

        return;
    }

    tableBody.innerHTML =
        employees
            .map(
                employee => {

                    const fullName =
                        `${employee.first_name || ''} ${employee.last_name || ''}`
                            .trim();

                    return `
                        <tr>

                            <td>
                                ${escapeHtml(employee.id)}
                            </td>

                            <td>
                                ${escapeHtml(fullName)}
                            </td>

                            <td>
                                ${escapeHtml(
                                    employee.email || ''
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    employee.phone || ''
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    employee.department || ''
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    employee.designation || ''
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    employee.salary || ''
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    employee.status || ''
                                )}
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary me-1"
                                    onclick="viewEmployeeInfo(${Number(employee.id)})">
                                    View
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning me-1"
                                    onclick="editEmployee(${Number(employee.id)})">
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    onclick="deactivateEmployee(${Number(employee.id)})">
                                    Deactivate
                                </button>
                            </td>

                        </tr>
                    `;
                }
            )
            .join('');
}


function escapeHtml(value) {

    return String(value ?? '')
        .replace(
            /&/g,
            '&amp;'
        )
        .replace(
            /</g,
            '&lt;'
        )
        .replace(
            />/g,
            '&gt;'
        )
        .replace(
            /"/g,
            '&quot;'
        )
        .replace(
            /'/g,
            '&#039;'
        );
}