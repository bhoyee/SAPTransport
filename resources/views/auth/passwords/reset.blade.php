@extends('layouts.login-layout')

@section('title', 'Password-Reset | SAP Transportation and Logistics')

@section('content')
<div class="container">
    <h2>Reset Password</h2>

    <form id="resetForm" method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Pre-fill the email address and make it readonly -->
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ request()->email }}" readonly>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password-confirm" class="form-control" required>
        </div>

        <button type="submit" id="resetBtnb" class="btn btn-primary mt-3">Reset Password</button>
    </form>
</div>

<script src="{{ asset('js/forgot-password.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const signupForm = document.getElementById('resetFormm');
        const signupBtn = document.getElementById('resetBtnb');
        
        // Listen for form submission
        signupForm.addEventListener('submit', function(event) {
            // Show spinner and disable button immediately
            resetBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Connecting ...";
            resetBtnb.disabled = true;

            // Allow the form to submit naturally to the backend
        });
    });
</script>

@endsection
