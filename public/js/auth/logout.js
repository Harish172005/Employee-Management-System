
async function logout() {
    try {
        const csrfToken = await getCsrfToken();

        const response = await fetch('/api/auth/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-CSRF-Token': csrfToken
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            window.location.href = '/login';
        }

    } catch (error) {
        window.location.href = '/login';
    }
}
