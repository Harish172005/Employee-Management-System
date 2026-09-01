document.addEventListener('DOMContentLoaded', function () {
    const user = JSON.parse(localStorage.getItem('user') || 'null');
    if (user && user.name) {
        const userDisplay = document.getElementById('userDisplay');
        if (userDisplay) {
            userDisplay.textContent = `Welcome, ${user.name}`;
        }
    }

    async function getCsrfToken() {
        const response = await fetch('/api/auth/csrf-token', {
            method: 'GET',
            credentials: 'include'
        });

        const data = await response.json();

        if (!response.ok || !data.token) {
            throw new Error('Unable to initialize secure form.');
        }

        return data.token;
    }

    const form = document.getElementById('addEmployeeForm');
    const messageBox = document.getElementById('formMessage');

    if (!form) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const payload = {
            employee_id: document.getElementById('employee_id').value.trim(),
            first_name: document.getElementById('first_name').value.trim(),
            last_name: document.getElementById('last_name').value.trim(),
            email: document.getElementById('email').value.trim(),
            phone: document.getElementById('phone').value.trim(),
            date_of_birth: document.getElementById('date_of_birth').value,
            gender: document.getElementById('gender').value,
            date_of_joining: document.getElementById('date_of_joining').value,
            department: document.getElementById('department').value.trim(),
            designation: document.getElementById('designation').value.trim(),
            salary: document.getElementById('salary').value,
            address: document.getElementById('address').value.trim(),
            profile_photo: document.getElementById('profile_photo').value.trim(),
            status: document.getElementById('status').value
        };

        messageBox.className = 'alert d-none mt-4';
        messageBox.textContent = '';

        try {
            const csrfToken = await getCsrfToken();

            const response = await fetch('/api/employees/create', {
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
