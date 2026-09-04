document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('addDepartmentForm');
    const messageBox = document.getElementById('formMessage');

    if (!form) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const payload = {
            department_name: document.getElementById('department_name').value.trim(),
            description: document.getElementById('description').value.trim(),
            status: document.getElementById('status').value
        };

        messageBox.className = 'alert d-none';
        messageBox.textContent = '';

        try {
            const csrfToken = await getCsrfToken();

            const response = await fetch('/api/departments/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                credentials: 'include',
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Unable to create department.');
            }

            messageBox.className = 'alert alert-success';
            messageBox.textContent = data.message || 'Department created successfully.';
            form.reset();
        } catch (error) {
            messageBox.className = 'alert alert-danger';
            messageBox.textContent = error.message;
        }
    });
});
