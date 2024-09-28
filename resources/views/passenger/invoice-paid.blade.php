@extends('layouts.passenger')

@section('title', 'Invoice Paid')

@section('content')
    <div class="alert alert-success">
        <h1>Payment Successful!</h1>
        <p>Thank you for your payment. Your invoice ({{ $invoice->invoice_number }}) has been marked as paid.</p>
        <a href="{{ route('passenger.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
    </div>
@endsection
