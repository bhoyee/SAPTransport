@extends('layouts.signup-layout')

@section('title', 'Verify | SAP Transportation and Logistics')

@section('content')
<div class="container">
    <h3>Email Verified Successfully</h3>
    <p>Your email address has been successfully verified.</p>

    <a href="{{ route('login') }}" class="btn btn-primary">Go to Sign In</a>
</div>
@endsection
