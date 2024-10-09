@extends('layouts.signup-layout')



@section('title', 'Thank you | SAP Transportation and Logistics')



@section('content')

<div class="container">

    <h3>Registration Successful!</h3>

    <p>Please check your email, including your inbox and spam folder, for the verification link to verify your email address..</p>

    

    <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>

</div>

@endsection

