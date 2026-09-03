document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('addEmployeeForm');
    const messageBox = document.getElementById('formMessage');

    if (!form) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData();
        const photoInput = document.getElementById('profile_photo');

        formData.append('first_name', document.getElementById('first_name').value.trim());
        formData.append('last_name', document.getElementById('last_name').value.trim());
        formData.append('email', document.getElementById('email').value.trim());
        formData.append('phone', document.getElementById('phone').value.trim());
        formData.append('date_of_birth', document.getElementById('date_of_birth').value);
        formData.append('gender', document.getElementById('gender').value);
        formData.append('date_of_joining', document.getElementById('date_of_joining').value);
        formData.append('department', document.getElementById('department').value.trim());
        formData.append('designation', document.getElementById('designation').value.trim());
        formData.append('salary', document.getElementById('salary').value);
        formData.append('address', document.getElementById('address').value.trim());
        formData.append('status', document.getElementById('status').value);

        if (photoInput && photoInput.files && photoInput.files[0]) {
            formData.append('profile_photo', photoInput.files[0]);
        }

        messageBox.className = 'alert d-none mt-4';
        messageBox.textContent = '';

        try {
            const csrfToken = await getCsrfToken();

            const response = await fetch('/api/employees/create', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                credentials: 'include',
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Unable to create employee.');
            }

            messageBox.className = 'alert alert-success mt-4';
            messageBox.textContent = data.message || 'Employee created successfully.';
            form.reset();
        } catch (error) {
            messageBox.className = 'alert alert-danger mt-4';
            messageBox.textContent = error.message;
        }
    });
});
