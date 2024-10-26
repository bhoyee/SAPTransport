@extends('admin.layouts.admin-layout')

@section('title', 'Edit Custom Invoice')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="fw-bold">Edit Custom Invoice</h4>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.customInvoices.update', $invoice->id) }}" method="POST" id="update-invoice-form">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $invoice->name }}" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $invoice->email }}" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $invoice->phone }}" required>
                </div>
                <div class="mb-3">
                    <label for="issue_date" class="form-label">Issue Date</label>
                    <input type="date" class="form-control" id="issue_date" name="issue_date" value="{{ $invoice->issue_date }}" required>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="{{ $invoice->due_date }}" required>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ $invoice->amount }}" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Paid" {{ $invoice->status === 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Unpaid" {{ $invoice->status === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="Refunded" {{ $invoice->status === 'Refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="service_type" class="form-label">Service Type</label>
                    <select class="form-control" id="service_type" name="service_type" required>
                        <option value="Airport Transfer" {{ $invoice->service_type == 'Airport Transfer' ? 'selected' : '' }}>Airport Transfer</option>
                        <option value="Charter" {{ $invoice->service_type == 'Charter' ? 'selected' : '' }}>Charter</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pickup_date" class="form-label">Pickup Date</label>
                    <input type="date" class="form-control" id="pickup_date" name="pickup_date" value="{{ $invoice->pickup_date }}" required>
                </div>
                <div class="mb-3">
                    <label for="pickup_time" class="form-label">Pickup Time</label>
                    <input type="time" class="form-control" id="pickup_time" name="pickup_time" value="{{ $invoice->pickup_time }}" required>
                </div>
                <div class="mb-3">
                    <label for="pickup_address" class="form-label">Pickup Address</label>
                    <input type="text" class="form-control" id="pickup_address" name="pickup_address" value="{{ $invoice->pickup_address }}" required>
                </div>
                <div class="mb-3">
                    <label for="dropoff_address" class="form-label">Dropoff Address</label>
                    <input type="text" class="form-control" id="dropoff_address" name="dropoff_address" value="{{ $invoice->dropoff_address }}" required>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary" id="update-button">
                        <span id="update-text">Update</span>
                        <span id="update-spinner" class="spinner-border spinner-border-sm" style="display: none;" role="status" aria-hidden="true"></span>
                    </button>
                    <a href="{{ route('admin.customInvoices') }}" class="btn btn-secondary">Back to Custom Invoice List</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('update-invoice-form').addEventListener('submit', function() {
        const updateText = document.getElementById('update-text');
        const updateSpinner = document.getElementById('update-spinner');

        updateText.style.display = 'none';
        updateSpinner.style.display = 'inline-block';
    });
</script>
@endsection
