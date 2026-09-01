document.addEventListener('DOMContentLoaded', function () {

    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('error-message');

    if (loginForm) {

        loginForm.addEventListener('submit', async function (e) {

            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            errorMessage.textContent = '';
            errorMessage.classList.add('d-none');

            if (!username || !password) {
                showError('Please enter both username and password');
                return;
            }

            try {

                const response = await fetch('/api/auth/login', {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json'
                    },

                    credentials: 'include',

                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {

                    console.log('Login successful');

                    const role = data.user.role;

                    if (role === 'admin') {
                        window.location.href = '/admin';
                    } else {
                        window.location.href = '/employee';
                    }

                } else {

                    showError(
                        data.error ||
                        data.message ||
                        'Login failed'
                    );
                }

            } catch (error) {

                console.error('Error:', error);

                showError(
                    'An error occurred. Please try again.'
                );
            }
        });
    }

    function showError(message) {

        errorMessage.textContent = message;
        errorMessage.classList.remove('d-none');
    }

    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    if (usernameInput) {
        usernameInput.addEventListener('focus', clearError);
    }

    if (passwordInput) {
        passwordInput.addEventListener('focus', clearError);
    }

    function clearError() {

        errorMessage.textContent = '';
        errorMessage.classList.add('d-none');
    }
});
