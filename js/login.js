        // JavaScript to handle the form field check and enable the login button
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');

        // Function to check if both email and password fields are filled
        function checkFormFields() {
            if (emailInput.value !== '' && passwordInput.value !== '') {
                loginBtn.disabled = false;
                
                loginBtn.classList.remove('disabled-btn');
                loginBtn.classList.add('btn-primary');
            } else {
                loginBtn.disabled = true;
                loginBtn.classList.remove('btn-primary');
                loginBtn.classList.add('disabled-btn');
            }
        }

        // Event listeners to call the check function whenever user types in the form
        emailInput.addEventListener('input', checkFormFields);
        passwordInput.addEventListener('input', checkFormFields);


function loginWithGoogle() {
    // Add your Google login logic here
}

function loginWithFacebook() {
    // Add your Facebook login logic here
}

function signupWithGoogle() {
    // Add your Google signup logic here
}

function signupWithFacebook() {
    // Add your Facebook signup logic here
}
