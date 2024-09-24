@extends('layouts.login-layout')

@section('title', 'Forgot-Password | SAP Transportation and Logistics')

@section('content')
<div class="container mt-3">
    <h2>Forgot Password</h2>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label mt-3">Email address</label>
            <input type="email" class="form-control mt-2" id="email" name="email" required autofocus>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" id="resetBtn" class="btn btn-primary disabled-btn">Send Password Reset Link</button>
        <p class="text-center mt-3">Remembered your password? <a href="{{ url('login') }}">Login</a></p>
    </form>
</div>

<script src="{{ asset('js/forgot-password.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const signupForm = document.getElementById('forgotForm');
        const signupBtn = document.getElementById('resetBtn');
        
        // Listen for form submission
        signupForm.addEventListener('submit', function(event) {
            // Show spinner and disable button immediately
            resetBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Connecting ...";
            resetBtn.disabled = true;

            // Allow the form to submit naturally to the backend
        });
    });
</script>

@endsection
