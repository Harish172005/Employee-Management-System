/**
 * Shared CSRF helper.
 * Load this script BEFORE any script that calls window.getCsrfToken().
 */
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

window.getCsrfToken = getCsrfToken;
