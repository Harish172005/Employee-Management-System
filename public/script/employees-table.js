// Global function to view employee info - must be in global scope for onclick handler
async function viewEmployeeInfo(employeeId) {
    try {
        const response = await fetch(`/api/employees/${employeeId}`, {
            method: 'GET',
            credentials: 'include'
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message || 'Unable to load employee details.'
            );
        }

        const employee = result.data;

        const profilePhotoHtml = employee.profile_photo
            ? `<div class="text-center mb-3">
                    <img
                        src="${employee.profile_photo}"
                        alt="Profile Photo"
                        class="img-fluid rounded"
                        style="max-width: 200px; height: auto;"
                    >
               </div>`
            : '<p class="text-muted text-center">No profile photo available</p>';

        const modalContent = `
            ${profilePhotoHtml}

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">First Name</h6>
                    <p>${employee.first_name}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Last Name</h6>
                    <p>${employee.last_name}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Email</h6>
                    <p>${employee.email}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Phone</h6>
                    <p>${employee.phone}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Date of Birth</h6>
                    <p>${employee.date_of_birth}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Gender</h6>
                    <p>${employee.gender}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Department</h6>
                    <p>${employee.department}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Designation</h6>
                    <p>${employee.designation}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Date of Joining</h6>
                    <p>${employee.date_of_joining}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Salary</h6>
                    <p>
                        ${Number(employee.salary).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}
                    </p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <h6 class="text-muted">Address</h6>
                    <p>${employee.address}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Status</h6>
                    <p>
                        <span
                            class="badge ${
                                employee.status === 'active'
                                    ? 'bg-success'
                                    : 'bg-secondary'
                            }"
                        >
                            ${employee.status}
                        </span>
                    </p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Employee ID</h6>
                    <p>${employee.id}</p>
                </div>
            </div>
        `;

        const modalContentDiv = document.getElementById(
            'employeeDetailsContent'
        );

        if (modalContentDiv) {
            modalContentDiv.innerHTML = modalContent;
        }

        const modal = new bootstrap.Modal(
            document.getElementById('employeeModal')
        );

        modal.show();
    } catch (error) {
        alert('Error: ' + error.message);
    }
}


// Global function to edit employee
async function editEmployee(employeeId) {
    try {
        const response = await fetch(`/api/employees/${employeeId}`, {
            method: 'GET',
            credentials: 'include'
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message || 'Unable to load employee details.'
            );
        }

        const employee = result.data;

        // Populate the edit form
        document.getElementById('editEmployeeId').value = employee.id;
        document.getElementById('editFirstName').value = employee.first_name;
        document.getElementById('editLastName').value = employee.last_name;
        document.getElementById('editEmail').value = employee.email;
        document.getElementById('editPhone').value = employee.phone;
        document.getElementById('editGender').value = employee.gender;
        document.getElementById('editDepartment').value = employee.department;
        document.getElementById('editDesignation').value = employee.designation;
        document.getElementById('editSalary').value = employee.salary;
        document.getElementById('editAddress').value = employee.address;
        document.getElementById('editStatus').value = employee.status;

        const modal = new bootstrap.Modal(
            document.getElementById('editEmployeeModal')
        );

        modal.show();
    } catch (error) {
        alert('Error: ' + error.message);
    }
}


// Global function to deactivate employee
async function deactivateEmployee(employeeId) {
    if (!confirm('Are you sure you want to deactivate this employee?')) {
        return;
    }

    try {
        const response = await fetch(`/api/employees/${employeeId}`, {
            method: 'DELETE',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message || 'Unable to deactivate employee.'
            );
        }

        alert('Employee deactivated successfully.');

        // Reload the employee list
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

let paginationState = {
    currentPage: 1,
    totalPages: 1
};


function previousPage(event) {
    event.preventDefault();

    if (paginationState.currentPage > 1) {
        paginationState.currentPage--;

        // Trigger fetch with new page
        window.fetchEmployeesForPagination();
    }
}


function nextPage(event) {
    event.preventDefault();

    if (paginationState.currentPage < paginationState.totalPages) {
        paginationState.currentPage++;

        // Trigger fetch with new page
        window.fetchEmployeesForPagination();
    }
}


document.addEventListener('DOMContentLoaded', async function () {
    const user = JSON.parse(
        localStorage.getItem('user') || 'null'
    );

    if (user && user.name) {
        const userDisplay = document.getElementById('userDisplay');

        if (userDisplay) {
            userDisplay.textContent = `Welcome, ${user.name}`;
        }
    }

    const tableBody = document.getElementById('employeesTableBody');
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('employeeSearch');


    function updatePaginationControls() {
        const paginationContainer = document.getElementById(
            'paginationContainer'
        );

        const pageIndicator = document.getElementById('pageIndicator');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (paginationState.totalPages > 1) {
            paginationContainer.style.display = 'block';

            pageIndicator.innerHTML = `
                <span class="page-link">
                    Page ${paginationState.currentPage}
                    of ${paginationState.totalPages}
                </span>
            `;

            prevBtn.classList.toggle(
                'disabled',
                paginationState.currentPage === 1
            );

            nextBtn.classList.toggle(
                'disabled',
                paginationState.currentPage === paginationState.totalPages
            );
        } else {
            paginationContainer.style.display = 'none';
        }
    }


    async function fetchEmployees() {
        try {
            const params = new URLSearchParams();

            const searchTerm = (
                searchInput?.value || ''
            ).trim();

            const selectedStatus = statusFilter?.value || '';
            const selectedDepartment = departmentFilter?.value || '';

            if (searchTerm) {
                params.set('search', searchTerm);
            }

            if (selectedStatus) {
                params.set('status', selectedStatus);
            }

            if (selectedDepartment) {
                params.set('department', selectedDepartment);
            }

            params.set('page', paginationState.currentPage);

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
                    result.message || 'Unable to load employees.'
                );
            }

            const employees = result.data || [];
            const pagination = result.pagination || {};

            paginationState.currentPage =
                pagination.currentPage || 1;

            paginationState.totalPages =
                pagination.totalPages || 1;


            const uniqueDepartments = [
                ...new Set(
                    employees
                        .map(employee => employee.department)
                        .filter(Boolean)
                )
            ];

            departmentFilter.innerHTML =
                '<option value="">All departments</option>' +
                uniqueDepartments
                    .map(
                        value =>
                            `<option value="${value}">${value}</option>`
                    )
                    .join('');


            if (
                selectedDepartment &&
                !uniqueDepartments.includes(selectedDepartment)
            ) {
                departmentFilter.value = '';
            } else if (selectedDepartment) {
                departmentFilter.value = selectedDepartment;
            }


            if (!employees.length) {
                tableBody.innerHTML = `
                    <tr>
                        <td
                            colspan="9"
                            class="text-center text-muted"
                        >
                            No employees found.
                        </td>
                    </tr>
                `;

                updatePaginationControls();
                return;
            }


            tableBody.innerHTML = employees
                .map(
                    employee => `
                        <tr>
                            <td>${employee.id}</td>

                            <td>
                                ${employee.first_name}
                                ${employee.last_name}
                            </td>

                            <td>${employee.email}</td>

                            <td>${employee.phone}</td>

                            <td>${employee.department}</td>

                            <td>${employee.designation}</td>

                            <td>
                                <span
                                    class="badge ${
                                        employee.status === 'active'
                                            ? 'bg-success'
                                            : 'bg-secondary'
                                    }"
                                >
                                    ${employee.status}
                                </span>
                            </td>

                            <td>
                                ${Number(employee.salary).toLocaleString(
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
                                    onclick="viewEmployeeInfo(${employee.id})"
                                >
                                    View
                                </button>

                                <button
                                    class="btn btn-sm btn-warning text-dark"
                                    onclick="editEmployee(${employee.id})"
                                >
                                    Edit
                                </button>

                                ${
                                    employee.status === 'active'
                                        ? `
                                            <button
                                                class="btn btn-sm btn-danger"
                                                onclick="deactivateEmployee(${employee.id})"
                                            >
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
                    `
                )
                .join('');

            updatePaginationControls();
        } catch (error) {
            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td
                            colspan="9"
                            class="text-center text-danger"
                        >
                            ${error.message}
                        </td>
                    </tr>
                `;
            }
        }
    }


    if (searchInput) {
        searchInput.addEventListener('input', function () {
            fetchEmployees();
        });
    }


    if (statusFilter) {
        statusFilter.addEventListener('change', function () {
            fetchEmployees();
        });
    }


    if (departmentFilter) {
        departmentFilter.addEventListener('change', function () {
            fetchEmployees();
        });
    }


    // Handle edit form submission
    const editForm = document.getElementById('editEmployeeForm');

    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const employeeId = document.getElementById(
                'editEmployeeId'
            ).value;

            const editError = document.getElementById('editError');

            editError.classList.add('d-none');

            const updateData = {
                first_name: document
                    .getElementById('editFirstName')
                    .value
                    .trim(),

                last_name: document
                    .getElementById('editLastName')
                    .value
                    .trim(),

                email: document
                    .getElementById('editEmail')
                    .value
                    .trim(),

                phone: document
                    .getElementById('editPhone')
                    .value
                    .trim(),

                gender: document
                    .getElementById('editGender')
                    .value,

                department: document
                    .getElementById('editDepartment')
                    .value,

                designation: document
                    .getElementById('editDesignation')
                    .value
                    .trim(),

                salary:
                    parseFloat(
                        document.getElementById('editSalary').value
                    ) || 0,

                address: document
                    .getElementById('editAddress')
                    .value
                    .trim(),

                status: document
                    .getElementById('editStatus')
                    .value
            };


            try {
                const response = await fetch(
                    `/api/employees/${employeeId}`,
                    {
                        method: 'PUT',
                        credentials: 'include',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(updateData)
                    }
                );

                const result = await response.json();

                if (!response.ok) {
                    editError.textContent =
                        result.message ||
                        'Failed to update employee.';

                    editError.classList.remove('d-none');

                    return;
                }

                alert('Employee updated successfully.');

                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('editEmployeeModal')
                );

                modal.hide();

                fetchEmployees();
            } catch (error) {
                editError.textContent =
                    'Error: ' + error.message;

                editError.classList.remove('d-none');
            }
        });
    }

    await fetchEmployees();
});