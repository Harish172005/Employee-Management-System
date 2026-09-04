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
