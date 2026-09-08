document.addEventListener('DOMContentLoaded', function () {
    loadMyDepartment();
});


async function loadMyDepartment() {

    const messageBox =
        document.getElementById('departmentMessage');

    try {

        const response = await fetch(
            '/api/employee/department',
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message || 'Unable to load department.'
            );
        }

        const department = result.data;

        document.getElementById('departmentId').textContent =
            department.id ?? '-';

        document.getElementById('departmentName').textContent =
            department.department_name ?? '-';

        document.getElementById('departmentDescription').textContent =
            department.description || 'No description available.';

        const statusElement =
            document.getElementById('departmentStatus');

        statusElement.textContent =
            department.status ?? '-';

        statusElement.className =
            department.status === 'active'
                ? 'badge bg-success'
                : 'badge bg-secondary';

        document.getElementById('employeeCount').textContent =
            department.employee_count ?? 0;

        renderDepartmentEmployees(
            department.employees || []
        );

    } catch (error) {

        console.error(
            'Department loading error:',
            error
        );

        messageBox.className =
            'alert alert-danger';

        messageBox.textContent =
            error.message;
    }
}


function renderDepartmentEmployees(employees) {

    const employeeContainer =
        document.getElementById('departmentEmployees');

    if (employees.length === 0) {

        employeeContainer.innerHTML = `
            <tr>
                <td
                    colspan="5"
                    class="text-center text-muted py-4"
                >
                    No employees found.
                </td>
            </tr>
        `;

        return;
    }

    employeeContainer.innerHTML =
        employees.map(employee => {

            const statusClass =
                employee.status === 'active'
                    ? 'bg-success'
                    : 'bg-secondary';

            return `
                <tr>

                    <td>
                        ${escapeHtml(employee.id)}
                    </td>

                    <td>
                        ${escapeHtml(
                            `${employee.first_name ?? ''} ${employee.last_name ?? ''}`
                        )}
                    </td>

                    <td>
                        ${escapeHtml(employee.email)}
                    </td>

                    <td>
                        ${escapeHtml(employee.designation)}
                    </td>

                    <td>
                        <span class="badge ${statusClass}">
                            ${escapeHtml(employee.status)}
                        </span>
                    </td>

                </tr>
            `;

        }).join('');
}

function escapeHtml(value) {
    const div = document.createElement('div');

    div.textContent = value ?? '';

    return div.innerHTML;
}