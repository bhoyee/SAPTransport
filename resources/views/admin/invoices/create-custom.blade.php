@extends('admin.layouts.admin-layout')

@section('title', 'Create Custom Invoice')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="fw-bold">Create Custom Invoice</h4>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.invoices.createCustom') }}" method="POST" id="create-invoice-form">
                @csrf
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="issue_date" class="form-label">Issue Date</label>
                    <input type="date" class="form-control" id="issue_date" name="issue_date" required>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                </div>
                <div class="mb-3">
                    <label for="service_type" class="form-label">Service Type</label>
                    <select class="form-control" id="service_type" name="service_type" required>
                        <option value="Airport Transfer">Airport Transfer</option>
                        <option value="Charter">Charter</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pickup_date" class="form-label">Pickup Date</label>
                    <input type="date" class="form-control" id="pickup_date" name="pickup_date" required>
                </div>
                <div class="mb-3">
                    <label for="pickup_time" class="form-label">Pickup Time</label>
                    <input type="time" class="form-control" id="pickup_time" name="pickup_time" required>
                </div>
                <div class="mb-3">
                    <label for="pickup_address" class="form-label">Pickup Address</label>
                    <input type="text" class="form-control" id="pickup_address" name="pickup_address" required>
                </div>
                <div class="mb-3">
                    <label for="dropoff_address" class="form-label">Dropoff Address</label>
                    <input type="text" class="form-control" id="dropoff_address" name="dropoff_address" required>
                </div>
                <div class="mb-3">
                    <label for="amount_paid" class="form-label">Amount Paid</label>
                    <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Refunded">Refunded</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Add this invoice to sales?</label>
                    <div>
                        <label><input type="checkbox" name="add_to_sales" value="1"> Yes</label>
                        <label><input type="checkbox" name="add_to_sales" value="0"> No</label>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" id="create-invoice-btn">
                        <span id="btn-text">Create Invoice</span>
                        <span id="btn-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script for Spinner -->
<script>
    document.getElementById('create-invoice-form').addEventListener('submit', function() {
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const createInvoiceBtn = document.getElementById('create-invoice-btn');

        // Hide button text and show spinner
        btnText.style.display = 'none';
        btnSpinner.style.display = 'inline-block';
        createInvoiceBtn.disabled = true; // Disable the button to prevent multiple submissions
    });
</script>
@endsection
