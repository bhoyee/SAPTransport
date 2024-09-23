// Function to toggle password visibility
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const eyeIcon = passwordField.nextElementSibling.querySelector('i');
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove('bi-eye-slash');
        eyeIcon.classList.add('bi-eye');
    } else {
        passwordField.type = "password";
        eyeIcon.classList.remove('bi-eye');
        eyeIcon.classList.add('bi-eye-slash');
    }
}

// Form Validation and Button Activation
const formFields = document.querySelectorAll('#name, #email, #phone, #gender, #password, #confirm-password');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm-password');
const signupBtn = document.getElementById('signupBtn');
const passwordError = document.getElementById('passwordError');
const termsCheck = document.getElementById('termsCheck'); // Checkbox for terms and conditions

// Initially disable the signup button
signupBtn.disabled = true;

// Check form validity and enable/disable button
function checkFormFields() {
    const allFieldsFilled = [...formFields].every(input => input.value !== '');
    const passwordsMatch = passwordInput.value === confirmPasswordInput.value;
    const termsAccepted = termsCheck.checked; // Check if the checkbox is selected

    if (allFieldsFilled && passwordsMatch && termsAccepted) {
        signupBtn.disabled = false;
        signupBtn.classList.remove('disabled-btn');
        signupBtn.classList.add('btn-primary');
        passwordError.style.display = 'none';
    } else {
        signupBtn.disabled = true;
        signupBtn.classList.remove('btn-primary');
        signupBtn.classList.add('disabled-btn');
        if (!passwordsMatch) {
            passwordError.style.display = 'block';
        } else {
            passwordError.style.display = 'none';
        }
    }
}

// Add input event listeners to check form fields
formFields.forEach(input => {
    input.addEventListener('input', checkFormFields);
});

// Add event listener for the checkbox
termsCheck.addEventListener('change', checkFormFields);

// Add blur event listener to confirm password field to show error when passwords do not match
confirmPasswordInput.addEventListener('blur', () => {
    if (passwordInput.value !== confirmPasswordInput.value) {
        passwordError.style.display = 'block';
    } else {
        passwordError.style.display = 'none';
    }
});

// Restrict phone input to numbers only
const phoneInput = document.getElementById('phone');
phoneInput.addEventListener('input', function() {
    if (!this.value.match(/^[0-9]*$/)) {
        this.value = this.value.replace(/[^0-9]/g, ''); // Remove any non-numeric characters
    }
});

// Function to hide the alert notification
function hideAlertAfterTimeout() {
    setTimeout(() => {
        let successMessage = document.getElementById('success-message');
        let errorMessage = document.getElementById('error-message');
        let errorList = document.getElementById('error-list');

        if (successMessage) {
            successMessage.style.display = 'none';
        }
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
        if (errorList) {
            errorList.style.display = 'none';
        }
    }, 5000); // Hide after 5 seconds
}

// Call the hideAlertAfterTimeout function when the page loads
window.onload = hideAlertAfterTimeout;

// Function to reset form fields
function resetFormFields() {
    setTimeout(() => {
        let signupForm = document.getElementById('signupForm');
        if (signupForm) {
            signupForm.reset(); // Clear the form fields
        }
    }, 500); // Delay to ensure form reset
}

// Reset the form fields regardless of success or failure
window.onload = function() {
    hideAlertAfterTimeout();
    resetFormFields();
};

// Spinner and form submission handling
document.addEventListener('DOMContentLoaded', function () {
    const signupForm = document.getElementById('signupForm');
    const signupBtn = document.getElementById('signupBtn');

    // Listen for form submission
    signupForm.addEventListener('submit', function (event) {
        // Show spinner and disable button immediately
        signupBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Signing up...";
        signupBtn.disabled = true;

        // The form will naturally submit to the server
    });
});
