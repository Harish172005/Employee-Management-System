const username = document.getElementById('username');
const email = document.getElementById('email');
const password = document.getElementById('password');
const role = document.getElementById('role');
const status = document.getElementById('status');
const submitBtn = document.getElementById('submitBtn');

submitBtn.addEventListener('click', async function (event) {
    event.preventDefault();
    const response = await fetch('/api/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        httpOnly: true,
        body: JSON.stringify({
            username: username.value,
            password: password.value,      
        })
    });
    });