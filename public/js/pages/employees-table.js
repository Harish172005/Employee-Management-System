let paginationState = {
    currentPage: 1,
    totalPages: 1
};


// ============================================================
// View Employee
// ============================================================

async function viewEmployeeInfo(employeeId) {

    try {

        const response = await fetch(
            `/api/employees/${employeeId}`,
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message ||
                'Unable to load employee details.'
            );
        }

        const employee = result.data;

        const profilePhotoHtml = employee.profile_photo
            ? `
                <div class="text-center mb-3">
                    <img
                        src="${employee.profile_photo}"
                        alt="Profile Photo"
                        class="rounded"
                        style="
                            width: 150px;
                            height: 150px;
                            object-fit: cover;
                        "
                    >
                </div>
              `
            : `
                <p class="text-muted text-center">
                    No profile photo available
                </p>
              `;

        const modalContent = `
            ${profilePhotoHtml}

            <div class="row mb-3">

                <div class="col-md-6">
                    <h6 class="text-muted">First Name</h6>
                    <p>${employee.first_name || '-'}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Last Name</h6>
                    <p>${employee.last_name || '-'}</p>
                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-6">
                    <h6 class="text-muted">Email</h6>
                    <p>${employee.email || '-'}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Phone</h6>
                    <p>${employee.phone || '-'}</p>
                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-6">
                    <h6 class="text-muted">Date of Birth</h6>
                    <p>${employee.date_of_birth || '-'}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Gender</h6>
                    <p>${employee.gender || '-'}</p>
                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-6">
                    <h6 class="text-muted">Department</h6>
                    <p>${employee.department || '-'}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Designation</h6>
                    <p>${employee.designation || '-'}</p>
                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-6">
                    <h6 class="text-muted">Date of Joining</h6>
                    <p>${employee.date_of_joining || '-'}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Salary</h6>
                    <p>
                        ${Number(employee.salary || 0).toLocaleString(
                            undefined,
                            {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }
                        )}
                    </p>
                </div>

            </div>

            <div class="mb-3">
                <h6 class="text-muted">Address</h6>
                <p>${employee.address || '-'}</p>
            </div>

            <div class="row">

                <div class="col-md-6">

                    <h6 class="text-muted">Status</h6>

                    <span class="badge ${
                        employee.status === 'active'
                            ? 'bg-success'
                            : 'bg-secondary'
                    }">
                        ${employee.status}
                    </span>

                </div>

                <div class="col-md-6">

                    <h6 class="text-muted">Employee ID</h6>

                    <p>${employee.id}</p>

                </div>

            </div>
        `;

        document.getElementById(
            'employeeDetailsContent'
        ).innerHTML = modalContent;

        const modal = new bootstrap.Modal(
            document.getElementById('employeeModal')
        );

        modal.show();

    } catch (error) {

        alert('Error: ' + error.message);

    }
}


// ============================================================
// Edit Employee - Load Existing Data
// ============================================================

async function editEmployee(employeeId) {

    try {

        const response = await fetch(
            `/api/employees/${employeeId}`,
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message ||
                'Unable to load employee details.'
            );
        }

        const employee = result.data;

        // Set employee ID
        document.getElementById(
            'editEmployeeId'
        ).value = employee.id;

        // Populate form
        document.getElementById(
            'editFirstName'
        ).value = employee.first_name || '';

        document.getElementById(
            'editLastName'
        ).value = employee.last_name || '';

        document.getElementById(
            'editEmail'
        ).value = employee.email || '';

        document.getElementById(
            'editPhone'
        ).value = employee.phone || '';

        document.getElementById(
            'editGender'
        ).value = employee.gender || '';

        document.getElementById(
            'editDepartment'
        ).value = employee.department || '';

        document.getElementById(
            'editDesignation'
        ).value = employee.designation || '';

        document.getElementById(
            'editSalary'
        ).value = employee.salary || '';

        document.getElementById(
            'editAddress'
        ).value = employee.address || '';

        document.getElementById(
            'editStatus'
        ).value = employee.status || 'active';

        // Clear previous selected file
        document.getElementById(
            'editProfilePhoto'
        ).value = '';

        // Display current photo
        const photoPreview = document.getElementById(
            'editPhotoPreview'
        );

        if (employee.profile_photo) {

            photoPreview.innerHTML = `
                <img
                    src="${employee.profile_photo}"
                    alt="Current Profile Photo"
                    class="rounded"
                    style="
                        width: 120px;
                        height: 120px;
                        object-fit: cover;
                    "
                >
                <p class="text-muted small mt-2">
                    Select a new photo to replace it.
                </p>
            `;

        } else {

            photoPreview.innerHTML = `
                <p class="text-muted">
                    No profile photo
                </p>
            `;
        }

        // Clear previous error
        document.getElementById(
            'editError'
        ).classList.add('d-none');

        // Show modal
        const modal = new bootstrap.Modal(
            document.getElementById('editEmployeeModal')
        );

        modal.show();

    } catch (error) {

        alert('Error: ' + error.message);

    }
}


// ============================================================
// Deactivate Employee
// ============================================================

async function deactivateEmployee(employeeId) {

    if (
        !confirm(
            'Are you sure you want to deactivate this employee?'
        )
    ) {
        return;
    }

    try {

        const response = await fetch(
            `/api/employees/${employeeId}`,
            {
                method: 'DELETE',
                credentials: 'include'
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message ||
                'Unable to deactivate employee.'
            );
        }

        alert(
            result.message ||
            'Employee deactivated successfully.'
        );

        await fetchEmployees();

    } catch (error) {

        alert('Error: ' + error.message);

    }
}


// ============================================================
// Pagination
// ============================================================

function previousPage(event) {

    event.preventDefault();

    if (paginationState.currentPage <= 1) {
        return;
    }

    paginationState.currentPage--;

    fetchEmployees();
}


function nextPage(event) {

    event.preventDefault();

    if (
        paginationState.currentPage >=
        paginationState.totalPages
    ) {
        return;
    }

    paginationState.currentPage++;

    fetchEmployees();
}


function updatePaginationControls() {

    const container = document.getElementById(
        'paginationContainer'
    );

    const pageIndicator = document.getElementById(
        'pageIndicator'
    );

    const prevBtn = document.getElementById(
        'prevBtn'
    );

    const nextBtn = document.getElementById(
        'nextBtn'
    );

    if (paginationState.totalPages <= 1) {

        container.style.display = 'none';

        return;
    }

    container.style.display = 'block';

    pageIndicator.textContent =
        `Page ${paginationState.currentPage} of ${paginationState.totalPages}`;

    prevBtn.classList.toggle(
        'disabled',
        paginationState.currentPage === 1
    );

    nextBtn.classList.toggle(
        'disabled',
        paginationState.currentPage ===
        paginationState.totalPages
    );
}


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


// ============================================================
// Edit Employee - Submit
// ============================================================

async function submitEditEmployee(event) {

    event.preventDefault();

    const form = event.target;

    const employeeId = document.getElementById(
        'editEmployeeId'
    ).value;

    const editError = document.getElementById(
        'editError'
    );

    editError.classList.add('d-none');

    /*
     * FormData automatically collects:
     *
     * first_name
     * last_name
     * email
     * phone
     * gender
     * department
     * designation
     * salary
     * address
     * status
     * profile_photo (if selected)
     */
    const formData = new FormData(form);

    try {

        const response = await fetch(
            `/api/employees/${employeeId}`,
            {
                method: 'POST',
                credentials: 'include',
                body: formData
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

        alert(
            result.message ||
            'Employee updated successfully.'
        );

        // Close modal
        const modalElement = document.getElementById(
            'editEmployeeModal'
        );

        const modal = bootstrap.Modal.getInstance(
            modalElement
        );

        modal.hide();

        // Refresh list
        await fetchEmployees();

    } catch (error) {

        editError.textContent =
            'Error: ' + error.message;

        editError.classList.remove('d-none');
    }
}


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