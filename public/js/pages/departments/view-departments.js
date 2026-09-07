function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}


async function fetchDepartments() {

    const departmentContainer =
        document.getElementById(
            'departmentContainer'
        );

    const searchInput =
        document.getElementById(
            'departmentSearch'
        );

    const statusFilter =
        document.getElementById(
            'departmentStatusFilter'
        );

    if (!departmentContainer) {
        return;
    }

    const params = new URLSearchParams();

    const search =
        searchInput
            ? searchInput.value.trim()
            : '';

    const status =
        statusFilter
            ? statusFilter.value
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

    departmentContainer.innerHTML = `
        <div class="col-12 text-center">
            Loading...
        </div>
    `;

    try {

        const queryString =
            params.toString();

        const url =
            queryString
                ? `/api/departments?${queryString}`
                : '/api/departments';

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
                'Unable to load departments.'
            );
        }

        const departments =
            Array.isArray(result.data)
                ? result.data
                : [];

        renderDepartments(
            departments
        );

    } catch (error) {

        console.error(
            'Department loading error:',
            error
        );

        departmentContainer.innerHTML = `
            <div class="col-12 text-center text-danger">
                ${escapeHtml(error.message)}
            </div>
        `;
    }
}

function renderDepartments(departments) {

    const departmentContainer =
        document.getElementById(
            'departmentContainer'
        );

    if (!departmentContainer) {
        return;
    }

    if (departments.length === 0) {

        departmentContainer.innerHTML = `
            <div class="col-12 text-center text-muted">
                No departments found.
            </div>
        `;

        return;
    }

    departmentContainer.innerHTML =
        departments
            .map(department => {

                const statusBadge =
                    department.status === 'active'
                        ? 'bg-success'
                        : 'bg-secondary';

                const statusText =
                    department.status
                        ? department.status
                            .charAt(0)
                            .toUpperCase() +
                          department.status.slice(1)
                        : '';

                const deactivateButton =
                    department.status === 'active'
                        ? `
                            <button
                                type="button"
                                class="btn btn-sm btn-danger"
                                onclick="deactivateDepartment(${Number(department.id)})">
                                Deactivate
                            </button>
                          `
                        : '';

                return `
                    <div class="col-md-6 col-lg-4">

                        <div class="card h-100 shadow-sm">

                            <div class="card-body d-flex flex-column">

                                <div class="d-flex justify-content-between align-items-start">

                                    <div>

                                        <h5 class="card-title mb-1">
                                            ${escapeHtml(
                                                department.department_name
                                            )}
                                        </h5>

                                        <small class="text-muted">
                                            Department #${escapeHtml(
                                                department.id
                                            )}
                                        </small>

                                    </div>

                                    <span class="badge ${statusBadge}">
                                        ${escapeHtml(statusText)}
                                    </span>

                                </div>

                                <p class="text-muted mt-3 mb-4">
                                    ${escapeHtml(
                                        department.description ||
                                        'No description available.'
                                    )}
                                </p>

                                <div class="mt-auto">

                                    <div class="d-flex justify-content-end gap-2">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning"
                                            onclick="openEditDepartment(${Number(department.id)})">
                                            Edit
                                        </button>

                                        ${deactivateButton}

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                `;
            })
            .join('');
}



document.addEventListener(
    'DOMContentLoaded',
    function () {

        fetchDepartments();

        const searchInput =
            document.getElementById(
                'departmentSearch'
            );

        const statusFilter =
            document.getElementById(
                'departmentStatusFilter'
            );

        if (searchInput) {
            searchInput.addEventListener(
                'input',
                fetchDepartments
            );
        }

        if (statusFilter) {
            statusFilter.addEventListener(
                'change',
                fetchDepartments
            );
        }
    }
);