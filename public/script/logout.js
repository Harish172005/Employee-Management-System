async function logout() {
    try {
        const csrfResponse = await fetch('/api/auth/csrf-token', {
            method: 'GET',
            credentials: 'include'
        });

        const csrfData = await csrfResponse.json();

        if (!csrfResponse.ok || !csrfData.token) {
            throw new Error('Unable to initialize logout.');
        }

        const response = await fetch('/api/auth/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-CSRF-Token': csrfData.token
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