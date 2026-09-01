document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('changePasswordForm');

    const currentPassword =
        document.getElementById('currentPassword');

    const newPassword =
        document.getElementById('newPassword');

    const confirmPassword =
        document.getElementById('confirmPassword');

    const message =
        document.getElementById('message');

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

    form.addEventListener('submit', async function (event) {

        event.preventDefault();

        message.classList.add('d-none');

        if (newPassword.value !== confirmPassword.value) {
            showMessage(
                'New passwords do not match.',
                'danger'
            );
            return;
        }

        try {
            const csrfToken = await getCsrfToken();

            const response = await fetch(
                '/api/auth/change-password',
                {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({
                        currentPassword:
                            currentPassword.value,

                        newPassword:
                            newPassword.value
                    })
                }
            );

            const data = await response.json();

            if (response.ok && data.success) {
                showMessage(
                    data.message,
                    'success'
                );

                form.reset();
            } else {
                showMessage(
                    data.message || 'Failed to change password.',
                    'danger'
                );
            }

        } catch (error) {
            console.error(error);

            showMessage(
                'An error occurred. Please try again.',
                'danger'
            );
        }

    });

    function showMessage(text, type) {
        message.textContent = text;
        message.className = `alert alert-${type}`;
    }

});