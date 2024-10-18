@extends('admin.layouts.admin-layout')

@section('title', 'Invoice Paid')

@section('content')
    <div class="alert alert-success">
        <h1>Payment Successful!</h1>
        <p>Invoice number : ({{ session('invoice_number') }}) has been marked as paid.</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
    </div>
@endsection
