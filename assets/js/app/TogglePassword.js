const togglePassword = {
    initEventListeners: () => {
        // Password toggle script
        document.addEventListener('DOMContentLoaded', function() {
            const togglePasswordButtons = document.querySelectorAll('.toggle-password');

            Array.from(togglePasswordButtons).forEach((togglePassword) => {
                togglePassword.addEventListener('click', function () {
                    const passwordInput = togglePassword.parentElement.querySelector('.password-input');
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    const icon = togglePassword.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            });

            // Form validation
            const form = document.querySelector('.auth-needs-validation');
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            }
        });
    },
};

togglePassword.initEventListeners();
