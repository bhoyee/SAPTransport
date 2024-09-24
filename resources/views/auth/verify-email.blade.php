@extends('layouts.signup-layout')

@section('title', 'Verify | SAP Transportation and Logistics')

@section('content')
<div class="container mt-5">
    <h2>Verify Your Email Address</h2>

    <!-- Show the success message when the verification link is resent -->
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            A fresh verification link has been successfully sent to your email address.
        </div>

        <!-- Display navigation buttons after the email is resent -->
        <div class="mt-3">
            <a href="{{ url('/') }}" class="btn btn-primary">Go to Home</a>
            <a href="{{ route('login') }}" class="btn btn-secondary">Go to Login</a>
        </div>
    @endif

    <p>
        Before proceeding, please check your email for a verification link.
        If you did not receive the email,
        <!-- Form to request a new verification link -->
        <form action="{{ route('verification.resend') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">click here to request another</button>.
        </form>
    </p>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
</div>
@endsection
