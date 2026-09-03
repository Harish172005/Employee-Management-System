
const DEFAULT_AVATAR =
    'data:image/svg+xml;utf8,' + encodeURIComponent(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">' +
        '<rect width="120" height="120" fill="#e9ecef"/>' +
        '<circle cx="60" cy="45" r="22" fill="#adb5bd"/>' +
        '<path d="M20 105c5-25 30-35 40-35s35 10 40 35" fill="#adb5bd"/>' +
        '</svg>'
    );

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

        // Display current photo (or placeholder if none)
        document.getElementById('editPhotoPreview').src =
            employee.profile_photo || DEFAULT_AVATAR;

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
        
        console.log(error);

        alert('Error: ' + error.message);

    }
}


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
        console.log(error);

        editError.textContent =
            'Error: ' + error.message;

        editError.classList.remove('d-none');
    }
}


// ============================================================
// Live Preview - New Photo Selected
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    const photoInput = document.getElementById('editProfilePhoto');
    const photoPreview = document.getElementById('editPhotoPreview');

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function () {
            const file = photoInput.files && photoInput.files[0];

            if (file) {
                photoPreview.src = URL.createObjectURL(file);
            }
        });
    }
});
