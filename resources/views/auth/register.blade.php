@extends('layouts.signup-layout')



@section('title', 'Register | SAP Transportation and Logistics')



@section('content')

<section>

    <h3 class="text-center mb-4">Create your account</h3>



    <!-- Success and Error Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" id="success-message" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" id="error-message" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" id="error-list" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


    <form id="signupForm" method="POST" action="{{ route('register.post') }}">

        @csrf



        <div class="row g-3">

            <!-- Full Name Field -->

            <div class="col-md-6">

                <label for="name" class="form-label">Full Name</label>

                <input type="text" class="form-control" id="name" placeholder="Enter fullname" name="name" value="{{ old('name') }}" required>

            </div>



            <!-- Gender Field -->

            <div class="col-md-6">

                <label for="gender" class="form-label">Sex</label>

                <select class="form-select" id="gender" name="gender" required>

                    <option value="">Select gender</option>

                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>

                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>

                </select>

            </div>



            <!-- Email Field -->

            <div class="col-md-6">

                <label for="email" class="form-label">Email</label>

                <input type="email" class="form-control" id="email" placeholder="Example@gmail.com" name="email" value="{{ old('email') }}" required>

            </div>



            <!-- Phone Number Field -->

            <div class="col-md-6">

                <label for="phone" class="form-label">Phone Number</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <img src="https://flagcdn.com/w320/ng.png" alt="Nigeria Flag" class="flag-icon">

                    </span>

                    <input type="text" class="form-control" id="phone" name="phone"

                        placeholder="Enter phone number"

                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);"

                        required>

                </div>

            </div>



            <!-- Password Field -->

            <div class="col-md-6">

                <label for="password" class="form-label">Password</label>

                <div class="input-group">

                    <input type="password" class="form-control" placeholder="Enter password" id="password" name="password" required>

                    <span class="input-group-text eye-icon">

                        <i class="bi bi-eye-slash" id="togglePassword"></i>

                    </span>

                </div>

            </div>



            <!-- Confirm Password Field -->

            <div class="col-md-6">

                <label for="confirm-password" class="form-label">Confirm Password</label>

                <div class="input-group">

                    <input type="password" class="form-control" id="confirm-password" placeholder="Confirm password" name="password_confirmation" required>

                    <span class="input-group-text eye-icon">

                        <i class="bi bi-eye-slash" id="toggleConfirmPassword"></i>

                    </span>

                </div>

                <p class="error-message" id="passwordError">Passwords do not match.</p>

            </div>

        </div>



        <!-- Terms and Conditions Section -->

        <div class="form-check mt-4">

            <input class="form-check-input" type="checkbox" id="termsCheck" style="border-color:blue" required>

            <label class="form-check-label" for="termsCheck">



                By Signing Up, I agree to <a href="{{ url('/terms-and-conditions') }}" class="text-primary" target="_blank">Terms and Conditions</a>

                and <a href="{{ url('privacy-policy') }}" class="text-primary" target="_blank">Privacy Policy</a>.

            </label>

        </div>



        <!-- Submit Button -->

        <div class="d-grid gap-2 mt-4">

            <button type="submit" class="btn btn-primary" id="signupBtn">Sign Up</button>

        </div>



        <!-- Divider -->

        <div class="divider">

            <span>OR</span>

        </div>









    </form>

    

            <!-- Social Login Buttons -->

        <div class="d-flex justify-content-center mt-3">

            <div class="col-md-6">

                <a href="{{ route('auth.google') }}">

                    <button class="btn btn-danger w-100 mb-2">Sign Up with Google</button>

                </a>

            </div>

        </div>



                <!-- Already have an account -->

        <p class="text-center mt-4">Already have an account? <a href="{{ url('login') }}">Login</a></p>

    

</section>



<!-- Spinner and form submission JavaScript -->

<script>

    document.addEventListener('DOMContentLoaded', function() {

        const signupForm = document.getElementById('signupForm');

        const signupBtn = document.getElementById('signupBtn');

        

        // Listen for form submission

        signupForm.addEventListener('submit', function(event) {

            // Show spinner and disable button immediately

            signupBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Signing up...";

            signupBtn.disabled = true;



            // Allow the form to submit naturally to the backend

        });

    });

</script>

@endsection

