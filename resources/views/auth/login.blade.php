@extends('layouts.login-layout')

@section('title', 'Login | SAP Transportation and Logistics')

@section('content')
    <!-- Include your specific page content here -->
     
    <!-- ========== Form Section ========== -->
    <section>
        <h2 class="text-center mb-3">Login</h2>

        <!-- Error and Success Messages -->
        <!-- Error and Success Messages -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        
        

        <!-- Login Form -->
        <form id="loginForm" method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf
            <!-- Email Field -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control form-control-lg" name="email" placeholder="Enter your email" value="{{ old('email') }}" required autofocus />
            </div>

            <!-- Password Field -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" class="form-control form-control-lg" placeholder="Enter your password" name="password" required />
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn disabled-btn w-100 mb-3" id="loginBtn" disabled>Login</button>

            <!-- Social Login Buttons -->
            <div class="text-center mb-3">
                <p>Or login with:</p>
                <a href="{{ route('auth.google') }}"><button type="button" class="btn btn-danger w-100 mb-2">Login with Google</button></a>
                <!--<a href="{{ route('auth.facebook') }}"><button type="button" class="btn btn-primary w-100">Login with Facebook</button></a>-->
            </div>

            <!-- Forgot Password and Sign Up Links -->
            <p class="text-center"><a href="{{ url('forgot-password') }}">Forgot Password?</a></p>
            <p class="text-center">Don't have an account? <a href="{{ url('register') }}">Sign Up</a></p>
        </form>
        
        
    </section>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const signupForm = document.getElementById('loginForm');
        const signupBtn = document.getElementById('loginBtn');
        
        // Listen for form submission
        signupForm.addEventListener('submit', function(event) {
            // Show spinner and disable button immediately
            signupBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Login in...";
            loginBtn.disabled = true;

            // Allow the form to submit naturally to the backend
        });
        
            // Auto-hide the error alert after 5 seconds
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const errorList = document.getElementById('error-list');

    if (errorMessage) {
        setTimeout(() => {
            errorMessage.classList.remove('show');
            errorMessage.style.display = 'none';
        }, 5000);  // Auto-hide after 5 seconds
    }

    if (successMessage) {
        setTimeout(() => {
            successMessage.classList.remove('show');
            successMessage.style.display = 'none';
        }, 5000);  // Auto-hide after 5 seconds
    }

    if (errorList) {
        setTimeout(() => {
            errorList.classList.remove('show');
            errorList.style.display = 'none';
        }, 5000);  // Auto-hide after 5 seconds
    }
    });
</script>
@endsection
