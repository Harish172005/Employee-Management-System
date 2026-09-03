document.addEventListener('DOMContentLoaded', function () {
    displayWelcomeUser();

    const form = document.getElementById('addUserForm');
    const messageBox = document.getElementById('formMessage');

    if (!form) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const payload = {
            name: document.getElementById('name').value.trim(),
            email: document.getElementById('email').value.trim(),
            username: document.getElementById('username').value.trim(),
            password: document.getElementById('password').value,
            role: document.getElementById('role').value,
            status: document.getElementById('status').value
        };

        messageBox.className = 'alert d-none';
        messageBox.textContent = '';

        try {
            const csrfToken = await getCsrfToken();

            const response = await fetch('/api/users/create', {
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
                throw new Error(data.message || 'Unable to create user.');
            }

            messageBox.className = 'alert alert-success';
            messageBox.textContent = data.message || 'User created successfully.';
            form.reset();
        } catch (error) {
            messageBox.className = 'alert alert-danger';
            messageBox.textContent = error.message;
        }
    });
});
