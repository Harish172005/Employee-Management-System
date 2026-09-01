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


    form.addEventListener('submit', async function (event) {

        event.preventDefault();

        message.classList.add('d-none');


        // Check new passwords match
        if (newPassword.value !== confirmPassword.value) {

            showMessage(
                'New passwords do not match.',
                'danger'
            );

            return;
        }


        try {

            const response = await fetch(
                '/api/auth/change-password',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json'
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

        message.className =
            `alert alert-${type}`;

    }

});