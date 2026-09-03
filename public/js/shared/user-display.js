/**
 * Shows "Welcome, <name>" using the cached user from localStorage.
 * Load this script BEFORE any script that calls window.displayWelcomeUser().
 */
function displayWelcomeUser() {
    const user = JSON.parse(localStorage.getItem('user') || 'null');

    if (user && user.name) {
        const userDisplay = document.getElementById('userDisplay');
        if (userDisplay) {
            userDisplay.textContent = `Welcome, ${user.name}`;
        }
    }
}

window.displayWelcomeUser = displayWelcomeUser;
