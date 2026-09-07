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

        document.getElementById('employeeFirstName').textContent =
            employee.first_name || '-';

        document.getElementById('employeeLastName').textContent =
            employee.last_name || '-';

        document.getElementById('employeeEmail').textContent =
            employee.email || '-';

        document.getElementById('employeePhone').textContent =
            employee.phone || '-';

        document.getElementById('employeeDateOfBirth').textContent =
            employee.date_of_birth || '-';

        document.getElementById('employeeGender').textContent =
            employee.gender || '-';

        document.getElementById('employeeDepartment').textContent =
            employee.department || '-';

        document.getElementById('employeeDesignation').textContent =
            employee.designation || '-';

        document.getElementById('employeeDateOfJoining').textContent =
            employee.date_of_joining || '-';

        document.getElementById('employeeSalary').textContent =
            Number(employee.salary || 0).toLocaleString(
                undefined,
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );

        document.getElementById('employeeAddress').textContent =
            employee.address || '-';

        const statusElement =
            document.getElementById('employeeStatus');

        statusElement.textContent =
            employee.status || '-';

        statusElement.className =
            employee.status === 'active'
                ? 'badge bg-success'
                : 'badge bg-secondary';

        const photo =
            document.getElementById('employeeProfilePhoto');

        const noPhoto =
            document.getElementById('noProfilePhoto');

        if (employee.profile_photo) {

            photo.src = employee.profile_photo;
            photo.style.display = 'block';
            noPhoto.style.display = 'none';

        } else {

            photo.style.display = 'none';
            noPhoto.style.display = 'block';

        }

        const modal = new bootstrap.Modal(
            document.getElementById('employeeModal')
        );

        modal.show();

    } catch (error) {

        alert('Error: ' + error.message);

    }
}