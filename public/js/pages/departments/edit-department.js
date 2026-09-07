let departmentId = null;

async function openEditDepartment(id) {

    departmentId = id;

    const modal = document.getElementById('editDepartmentModal');
    const messageBox = document.getElementById('editFormMessage');

    const departmentNameInput =
        document.getElementById('edit_department_name');

    const descriptionInput =
        document.getElementById('edit_description');

    const statusInput =
        document.getElementById('edit_status');

    if (!modal) {
        console.error('Edit department modal not found.');
        return;
    }

    try {
        const response = await fetch(
            `/api/departments/${id}`,
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

        departmentNameInput.value =
            department.department_name || '';

        descriptionInput.value =
            department.description || '';

        statusInput.value =
            department.status || 'active';

        messageBox.className = 'alert d-none';
        messageBox.textContent = '';

        const editModal =
            bootstrap.Modal.getOrCreateInstance(modal);

        editModal.show();

    } catch (error) {

        console.error(
            'Department loading error:',
            error
        );

        if (messageBox) {
            messageBox.className = 'alert alert-danger';
            messageBox.textContent = error.message;
        }
    }
}


document.addEventListener('DOMContentLoaded', function () {

    const form =
        document.getElementById('editDepartmentForm');

    if (!form) return;

    const messageBox =
        document.getElementById('editFormMessage');

    const departmentNameInput =
        document.getElementById('edit_department_name');

    const descriptionInput =
        document.getElementById('edit_description');

    const statusInput =
        document.getElementById('edit_status');

    form.addEventListener('submit', async function (event) {

        event.preventDefault();

        if (!departmentId) {
            messageBox.className =
                'alert alert-danger';

            messageBox.textContent =
                'Department ID is missing.';

            return;
        }

        const payload = {
            department_name:
                departmentNameInput.value.trim(),

            description:
                descriptionInput.value.trim(),

            status:
                statusInput.value
        };

        messageBox.className = 'alert d-none';
        messageBox.textContent = '';

        try {

            const csrfToken =
                await getCsrfToken();

            const response = await fetch(
                `/api/departments/${departmentId}`,
                {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    credentials: 'include',
                    body: JSON.stringify(payload)
                }
            );

            const result =
                await response.json();

            if (!response.ok) {
                throw new Error(
                    result.message ||
                    'Unable to update department.'
                );
            }

            messageBox.className =
                'alert alert-success';

            messageBox.textContent =
                result.message ||
                'Department updated successfully.';

            if (typeof fetchDepartments === 'function') {
                await fetchDepartments();
            }

            setTimeout(function () {

                const modal =
                    document.getElementById(
                        'editDepartmentModal'
                    );

                const editModal =
                    bootstrap.Modal.getInstance(modal);

                if (editModal) {
                    editModal.hide();
                }

                form.reset();
                departmentId = null;

            }, 1000);

        } catch (error) {

            console.error(
                'Department update error:',
                error
            );

            messageBox.className =
                'alert alert-danger';

            messageBox.textContent =
                error.message;
        }
    });
});