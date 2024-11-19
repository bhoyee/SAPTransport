@extends('layouts.signup-layout')



@section('title', 'Thank you | SAP Transportation and Logistics')



@section('content')

<div class="container">

    @if (session('success'))
        <h3>Registration Successful!</h3>
        <p>Please check your email, including your inbox and spam folder, for the verification link to verify your email address.</p>
    @elseif (session('error'))
        <h3>Registration Failed!</h3>
        <p>{{ session('error') }}</p> 
    @endif

    <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>

</div>

@endsection

