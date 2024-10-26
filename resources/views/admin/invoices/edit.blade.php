@extends('admin.layouts.admin-layout')

@section('title', 'Edit Invoice')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="fw-bold">Edit Invoice</h4>
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

            <!-- Edit Invoice Form -->
            <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST" id="update-invoice-form">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="invoice_number" class="form-label">Invoice Number</label>
                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{ $invoice->invoice_number }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="invoice_date" class="form-label">Invoice Date</label>
                    <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}">
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ $invoice->amount }}">
                </div>

                <div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select class="form-select" id="status" name="status">
        <option value="paid" {{ trim($invoice->status) === 'Paid' ? 'selected' : '' }}>Paid</option>
        <option value="Unpaid" {{ trim($invoice->status) === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
        <option value="Refunded" {{ trim($invoice->status) === 'Refunded' ? 'selected' : '' }}>Refunded</option>
    </select>
</div>



                <div class="text-end">
                    <button type="submit" class="btn btn-primary" id="update-invoice-btn">
                        <span id="btn-text">Update Invoice</span>
                        <span id="btn-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    </button>
                    <a href="{{ route('admin.invoices.manage') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Spinner and Form Submission Script -->
<script>
    document.getElementById('update-invoice-form').addEventListener('submit', function() {
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        
        btnText.style.display = 'none';
        btnSpinner.style.display = 'inline-block';
    });
</script>

@endsection
