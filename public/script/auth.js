async function logout() {

    try {

        const response = await fetch('/api/auth/logout', {
            method: 'POST',
            credentials: 'include'
        });

        const data = await response.json();

        console.log(data);

        if (response.ok && data.success) {

            console.log('Logout successful');

            // Redirect to login
            window.location.href = '/login';

        } else {

            console.error('Logout failed:', data.message);

        }

    } catch (error) {

        console.error('Logout request failed:', error);

    }

}