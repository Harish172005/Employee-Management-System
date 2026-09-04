const DEFAULT_AVATAR =
    'data:image/svg+xml;utf8,' + encodeURIComponent(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">' +
        '<rect width="120" height="120" fill="#e9ecef"/>' +
        '<circle cx="60" cy="45" r="22" fill="#adb5bd"/>' +
        '<path d="M20 105c5-25 30-35 40-35s35 10 40 35" fill="#adb5bd"/>' +
        '</svg>'
    );


async function editEmployee(employeeId) {

    const modalElement =
        document.getElementById('editEmployeeModal');

    const form =
        document.getElementById('editEmployeeForm');

    const errorBox =
        document.getElementById('editError');

    if (!modalElement || !form) {
        return;
    }

    errorBox.className =
        'alert alert-danger d-none';

    errorBox.textContent = '';

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
                'Unable to load employee.'
            );
        }

        const employee = result.data;

        document.getElementById(
            'editEmployeeId'
        ).value = employee.id;

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
            'editDateOfBirth'
        ).value = employee.date_of_birth || '';

        document.getElementById(
            'editGender'
        ).value = employee.gender || '';

        document.getElementById(
            'editDateOfJoining'
        ).value = employee.date_of_joining || '';

        await loadEditDepartments(
            employee.department_id
        );

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

        const photoPreview =
            document.getElementById(
                'editPhotoPreview'
            );

        if (photoPreview) {

            if (employee.profile_photo) {

                photoPreview.src = employee.profile_photo;

            } else {

                photoPreview.src =
                    DEFAULT_AVATAR;
            }
        }

        form.dataset.employeeId =
            employee.id;

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        modal.show();

    } catch (error) {

        errorBox.className =
            'alert alert-danger';

        errorBox.textContent =
            error.message;
    }
}


async function loadEditDepartments(
    selectedDepartmentId = ''
) {

    const departmentSelect =
        document.getElementById(
            'editDepartment'
        );

    if (!departmentSelect) {
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
            result.data || [];

        departmentSelect.innerHTML = `
            <option value="">
                Select Department
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
                        document.createElement(
                            'option'
                        );

                    option.value =
                        department.id;

                    option.textContent =
                        department.department_name;

                    departmentSelect.appendChild(
                        option
                    );
                }
            );

        departmentSelect.value =
            String(selectedDepartmentId);

    } catch (error) {

        departmentSelect.innerHTML = `
            <option value="">
                Unable to load departments
            </option>
        `;

        throw error;
    }
}


async function submitEditEmployee(event) {

    event.preventDefault();

    const employeeId =
        document.getElementById(
            'editEmployeeId'
        ).value;

    const errorBox =
        document.getElementById(
            'editError'
        );

    errorBox.className =
        'alert alert-danger d-none';

    errorBox.textContent = '';

    if (!employeeId) {

        errorBox.className =
            'alert alert-danger';

        errorBox.textContent =
            'Employee ID is missing.';

        return;
    }

    const formData =
        new FormData();

    formData.append(
        'first_name',
        document.getElementById(
            'editFirstName'
        ).value.trim()
    );

    formData.append(
        'last_name',
        document.getElementById(
            'editLastName'
        ).value.trim()
    );

    formData.append(
        'email',
        document.getElementById(
            'editEmail'
        ).value.trim()
    );

    formData.append(
        'phone',
        document.getElementById(
            'editPhone'
        ).value.trim()
    );

    formData.append(
        'date_of_birth',
        document.getElementById(
            'editDateOfBirth'
        ).value
    );

    formData.append(
        'gender',
        document.getElementById(
            'editGender'
        ).value
    );

    formData.append(
        'date_of_joining',
        document.getElementById(
            'editDateOfJoining'
        ).value
    );

    formData.append(
        'department_id',
        document.getElementById(
            'editDepartment'
        ).value
    );

    formData.append(
        'designation',
        document.getElementById(
            'editDesignation'
        ).value.trim()
    );

    formData.append(
        'salary',
        document.getElementById(
            'editSalary'
        ).value
    );

    formData.append(
        'address',
        document.getElementById(
            'editAddress'
        ).value.trim()
    );

    formData.append(
        'status',
        document.getElementById(
            'editStatus'
        ).value
    );

    const photo =
        document.getElementById(
            'editProfilePhoto'
        );

    if (
        photo &&
        photo.files &&
        photo.files[0]
    ) {

        formData.append(
            'profile_photo',
            photo.files[0]
        );
    }

    try {

        const csrfToken =
            await getCsrfToken();

        const response =
            await fetch(
                `/api/employees/${employeeId}/update`,
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token':
                            csrfToken
                    },
                    credentials: 'include',
                    body: formData
                }
            );

        const result =
            await response.json();

        if (!response.ok) {

            throw new Error(
                result.message ||
                'Unable to update employee.'
            );
        }
          messageBox.className =
                'alert alert-success mt-4';

            messageBox.textContent =
                data.message ||
                'Employee updated successfully.';

        const modalElement =
            document.getElementById(
                'editEmployeeModal'
            );

        const modal =
            bootstrap.Modal.getInstance(
                modalElement
            );

        if (
            typeof fetchEmployees ===
            'function'
        ) {
            await fetchEmployees();
        }

    } catch (error) {

        errorBox.className =
            'alert alert-danger';

        errorBox.textContent =
            error.message;
    }
}


document.addEventListener(
    'DOMContentLoaded',
    function () {

        const photoInput =
            document.getElementById(
                'editProfilePhoto'
            );

        const photoPreview =
            document.getElementById(
                'editPhotoPreview'
            );

        if (
            photoInput &&
            photoPreview
        ) {

            photoInput.addEventListener(
                'change',
                function () {

                    const file =
                        photoInput.files[0];

                    if (!file) {
                        return;
                    }

                    const allowedTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ];

                    if (
                        !allowedTypes.includes(
                            file.type
                        )
                    ) {

                        photoInput.value = '';

                        alert(
                            'Please select a JPEG, PNG, or WebP image.'
                        );

                        return;
                    }

                    const imageUrl =
                        URL.createObjectURL(
                            file
                        );

                    photoPreview.src =
                        imageUrl;
                }
            );
        }
    }
);