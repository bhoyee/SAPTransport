@extends('layouts.login-layout')

@section('title', 'Password Reset Successful | SAP Transportation and Logistics')

@section('content')
<div class="container">
    <h4>Password Reset Successful!</h4>
    <p>Your password has been successfully reset.</p>

    <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>
    <a href="{{ url('/') }}" class="btn btn-secondary">Go to Home</a>
</div>
@endsection
