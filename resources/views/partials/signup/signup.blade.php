<section>
    <h3 class="text-center mb-4">Create your account</h3>
    <form id="signupForm">
        <div class="row g-3">
            <!-- Full Name Field -->
            <div class="col-md-6">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullname" placeholder="Enter fullname" required>
            </div>

            <!-- Sex Field -->
            <div class="col-md-6">
                <label for="sex" class="form-label">Sex</label>
                <select class="form-select" id="sex" required>
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <!-- Email Field -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Example@gmail.com" required>
            </div>

            <!-- Phone Number Field -->
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <img src="https://flagcdn.com/w320/ng.png" alt="Nigeria Flag" class="flag-icon">
                    </span>
                    <input type="tel" class="form-control" id="phone" placeholder="Enter phone number" required>
                </div>
            </div>

            <!-- Password Field -->
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                    <span class="input-group-text eye-icon" onclick="togglePassword('password')">
                        <i class="bi bi-eye-slash" id="togglePassword"></i>
                    </span>
                </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="col-md-6">
                <label for="confirm-password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm-password" placeholder="Confirm password" required>
                    <span class="input-group-text eye-icon" onclick="togglePassword('confirm-password')">
                        <i class="bi bi-eye-slash" id="toggleConfirmPassword"></i>
                    </span>
                </div>
                <p class="error-message" id="passwordError">Passwords do not match.</p>
            </div>
        </div>

        <!-- Terms and Conditions Section -->
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" id="termsCheck" required>
            <label class="form-check-label" for="termsCheck">
                By Signing Up, I agree to 
                <a href="{{ url('/terms-and-conditions') }}" class="text-primary" target="_blank">Terms and Conditions</a> 
                and <a href="{{ url('privacy-policy') }}" class="text-primary" target="_blank">Privacy Policy</a>.
            </label>
        </div>

        <!-- Submit Button -->
        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn disabled-btn" id="signupBtn" disabled>Sign Up</button>
        </div>
    </form>

    <!-- Divider -->
    <div class="divider">
        <span>OR</span>
    </div>

    <!-- Social Login Buttons in One Row -->
    <div class="row mt-3">
        <div class="col-md-6">
            <button class="btn btn-danger w-100 mb-2">Sign Up with Google</button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-primary w-100">Sign Up with Facebook</button>
        </div>
    </div>

    <!-- Already have an account -->
    <p class="text-center mt-4">Already have an account? <a href="{{ url('login') }}">Login</a></p>

    
</section>
