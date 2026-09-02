// Global function to view employee info - must be in global scope for onclick handler
async function viewEmployeeInfo(employeeId) {
    try {
        const response = await fetch(`/api/employees/${employeeId}`, {
            method: 'GET',
            credentials: 'include'
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Unable to load employee details.');
        }

        const employee = result.data;
        const profilePhotoHtml = employee.profile_photo
            ? `<div class="text-center mb-3"><img src="${employee.profile_photo}" alt="Profile Photo" class="img-fluid rounded" style="max-width: 200px; height: auto;"></div>`
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
                    <p>${Number(employee.salary).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
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
                    <p><span class="badge ${employee.status === 'active' ? 'bg-success' : 'bg-secondary'}">${employee.status}</span></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Employee ID</h6>
                    <p>${employee.id}</p>
                </div>
            </div>
        `;

        const modalContent_div = document.getElementById('employeeDetailsContent');
        if (modalContent_div) {
            modalContent_div.innerHTML = modalContent;
        }

        const modal = new bootstrap.Modal(document.getElementById('employeeModal'));
        modal.show();
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    const user = JSON.parse(localStorage.getItem('user') || 'null');
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
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');

    async function fetchEmployees() {
        try {
            const params = new URLSearchParams();

            const searchTerm = (searchInput?.value || '').trim();
            const selectedStatus = statusFilter?.value || '';
            const selectedDepartment = departmentFilter?.value || '';

            if (searchTerm) params.set('search', searchTerm);
            if (selectedStatus) params.set('status', selectedStatus);
            if (selectedDepartment) params.set('department', selectedDepartment);

            const response = await fetch(`/api/employees?${params.toString()}`, {
                method: 'GET',
                credentials: 'include'
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Unable to load employees.');
            }

            const employees = result.data || [];

            const uniqueDepartments = [...new Set(employees.map(employee => employee.department).filter(Boolean))];
            departmentFilter.innerHTML = '<option value="">All departments</option>' +
                uniqueDepartments.map(value => `<option value="${value}">${value}</option>`).join('');

            if (selectedDepartment && !uniqueDepartments.includes(selectedDepartment)) {
                departmentFilter.value = '';
            } else if (selectedDepartment) {
                departmentFilter.value = selectedDepartment;
            }

            if (!employees.length) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No employees found.</td></tr>';
                return;
            }

            tableBody.innerHTML = employees.map(employee => `
                <tr>
                    <td>${employee.id}</td>
                    <td>${employee.first_name} ${employee.last_name}</td>
                    <td>${employee.email}</td>
                    <td>${employee.phone}</td>
                    <td>${employee.department}</td>
                    <td>${employee.designation}</td>
                    <td>
                        <span class="badge ${employee.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                            ${employee.status}
                        </span>
                    </td>
                    <td>${Number(employee.salary).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td>
                        <button class="btn btn-sm btn-info text-white" onclick="viewEmployeeInfo(${employee.id})">
                            View Info
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">${error.message}</td></tr>`;
            }
        }
    }

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', () => fetchEmployees());
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

    await fetchEmployees();
});
