@extends('layouts.passenger')

@section('title', 'Payment Failed')

@section('content')
    <div class="alert alert-danger">
        <h1>Payment Failed</h1>
        <p>{{ session('error') ?? "Unfortunately, we couldn't verify your payment. Please try again." }}</p>
        <a href="{{ route('passenger.makepayments') }}" class="btn btn-primary">Go back to Payments</a>
    </div>
@endsection
