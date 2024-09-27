@extends('layouts.passenger')

@section('title', 'Invoice')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ asset('path-to-logo/logo.png') }}" alt="Company Logo" width="150">
                </div>
                <div class="col-md-6 text-end">
                    <h4 class="fw-bold">Invoice</h4>
                    <p>Invoice Number: {{ $invoice->invoice_number }}</p>
                    <p>Issue Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</p>
                    <p>Due Date: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                    <p>Due Amount: ₦{{ number_format($invoice->amount, 2) }}</p>
                </div>
            </div>

            <hr>

            <!-- Billing Information -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5 class="fw-bold">Billed to</h5>
                    <p><strong>{{ $user->name }}</strong></p>
                    <p>{{ $user->email }}</p>
                    <p>{{ $user->address }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="fw-bold">Invoice From</h5>
                    <p>Company Name</p>
                    <p>Address line 1</p>
                    <p>Address line 2</p>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th>Service Type</th>
                            <th>Pickup Date</th>
                            <th>Pickup Time</th>
                            <th>Dropoff Address</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Transport Service</td>
                            <td>{{ $booking->service_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M Y') }}</td>
                            <td>{{ $booking->pickup_time }}</td>
                            <td>{{ $booking->dropoff_address }}</td>
                            <td>₦{{ number_format($invoice->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Payment Details -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5 class="fw-bold">Payment Details</h5>
                    <p>Payment Method: {{ $invoice->payment_method ?? 'Not Provided' }}</p>
                    <p>Card Number: **** **** **** 1234</p>
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="fw-bold">Summary</h5>
                    <p>Subtotal: ₦{{ number_format($invoice->subtotal ?? $invoice->amount, 2) }}</p>
                    <p>Taxable: ₦{{ number_format($invoice->taxable ?? 0, 2) }}</p>
                    <p>Discount: ₦{{ number_format($invoice->discount ?? 0, 2) }}</p>
                    <p class="fw-bold">Total: ₦{{ number_format($invoice->amount, 2) }}</p>
                </div>
            </div>

            <!-- Notes and Signature -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">Notes</h6>
                    <p>Thank you for your business. Please contact us if you have any questions about this invoice.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p><strong>Authorized Signature</strong></p>
                    <img src="{{ asset('path-to-signature/signature.png') }}" alt="Signature" width="100">
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .invoice-card {
    border: 1px solid #e5e5e5;
    border-radius: 10px;
    padding: 20px;
    background-color: #fff;
}

.invoice-header {
    display: flex;
    justify-content: space-between;
}

.invoice-header img {
    max-width: 150px;
}

.table-responsive {
    margin-top: 20px;
}

h5 {
    font-size: 1.25rem;
}

@media (max-width: 768px) {
    .invoice-header {
        flex-direction: column;
        text-align: center;
    }

    .invoice-header img {
        margin-bottom: 10px;
    }

    .table-responsive {
        font-size: 14px;
    }
}

</style>
@endsection
