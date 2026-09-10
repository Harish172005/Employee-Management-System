let employeeData = null;

document.addEventListener('DOMContentLoaded', function () {
    loadEmployeeProfile();

    const form = document.getElementById('employeeProfileForm');

    if (form) {
        form.addEventListener('submit', updateEmployeeProfile);
    }
});


async function loadEmployeeProfile() {

    try {

        const response = await fetch(
            '/api/employee/profile',
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message || 'Unable to load profile.'
            );
        }

        displayEmployeeProfile(result.data);

    } catch (error) {

        console.error('Profile loading error:', error);

        showProfileMessage(
            error.message,
            'danger'
        );
    }
}


function displayEmployeeProfile(employee) {

    document.getElementById('employeeId').value =
        employee.id ?? '';

    document.getElementById('employeeFirstName').value =
        employee.first_name ?? '';

    document.getElementById('employeeLastName').value =
        employee.last_name ?? '';

    document.getElementById('employeeEmail').value =
        employee.email ?? '';

    document.getElementById('employeePhone').value =
        employee.phone ?? '';

    document.getElementById('employeeDateOfBirth').value =
        employee.date_of_birth ?? '';

    document.getElementById('employeeGender').value =
        employee.gender ?? '';

    document.getElementById('employeeDepartment').value =
        employee.department ?? '';

    document.getElementById('employeeDesignation').value =
        employee.designation ?? '';

    document.getElementById('employeeDateOfJoining').value =
        employee.date_of_joining ?? '';

    document.getElementById('employeeSalary').value =
        employee.salary ?? '';

    document.getElementById('employeeAddress').value =
        employee.address ?? '';

    document.getElementById('employeeStatus').value =
        employee.status ?? '';

    displayProfilePhoto(employee.profile_photo);
}

function displayProfilePhoto(photoPath) {
    const profilePhoto = document.getElementById('profilePhoto');
    const noProfilePhoto = document.getElementById('noProfilePhoto');

    if (!profilePhoto || !noProfilePhoto) {
        return;
    }

    if (photoPath) {
        profilePhoto.src = photoPath;
        profilePhoto.classList.remove('d-none');
        noProfilePhoto.classList.add('d-none');
    } else {
        profilePhoto.src = '';
        profilePhoto.classList.add('d-none');
        noProfilePhoto.classList.remove('d-none');
    }
}

async function updateEmployeeProfile(event) {

    event.preventDefault();

    const formData = new FormData();

    formData.append(
        'phone',
        document.getElementById('employeePhone').value
    );

    formData.append(
        'date_of_birth',
        document.getElementById('employeeDateOfBirth').value
    );

    formData.append(
        'gender',
        document.getElementById('employeeGender').value
    );

    formData.append(
        'address',
        document.getElementById('employeeAddress').value.trim()
    );

    const photo =
        document.getElementById('profilePhotoInput').files[0];

    if (photo) {
        formData.append('profile_photo', photo);
    }

    try {

        const csrfToken = await getCsrfToken();

        const response = await fetch(
            '/api/employee/profile',
            {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                body: formData
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message ||
                'Unable to update profile.'
            );
        }

        showProfileMessage(
            result.message ||
            'Profile updated successfully.',
            'success'
        );

        await loadEmployeeProfile();

        document.getElementById(
            'profilePhotoInput'
        ).value = '';

    } catch (error) {

        console.error(
            'Profile update error:',
            error
        );

        showProfileMessage(
            error.message,
            'danger'
        );
    }
}

function showProfileMessage(message, type) {

    const messageBox =
        document.getElementById('profileMessage');

    messageBox.className =
        `alert alert-${type}`;

    messageBox.textContent = message;
}