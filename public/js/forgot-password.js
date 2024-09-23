// JavaScript to handle the form field check and enable the reset button
const emailInput = document.getElementById('email');
const resetBtn = document.getElementById('resetBtn');

// Function to check if the email field is filled
function checkFormFields() {
    if (emailInput.value !== '') {
        resetBtn.disabled = false;
        resetBtn.classList.remove('disabled-btn');
        resetBtn.classList.add('btn-primary');
    } else {
        resetBtn.disabled = true;
        resetBtn.classList.remove('btn-primary');
        resetBtn.classList.add('disabled-btn');
    }
}

// Event listener to call the check function whenever the user types in the form
emailInput.addEventListener('input', checkFormFields);
