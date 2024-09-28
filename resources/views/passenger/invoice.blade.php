@extends('layouts.passenger')

@section('title', 'Invoice')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm invoice-card">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row invoice-header">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

                <div class="col-md-6">
                    <img src="{{ asset('img/logo.png') }}" alt="SAPTransport" class="img-fluid">
                </div>
                <div class="col-md-6 text-end">
                    <h4 class="fw-bold">Invoice</h4>
                    <p>Invoice Number: {{ $invoice->invoice_number }}</p>
                    <p>Issue Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</p>
                    <p>Due Date: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                    <p>Due Amount: ₦{{ number_format($invoice->amount, 2) }}</p>

                    <!-- Invoice Status -->
                    <p>
                        <strong>Status: 
                            <span class="badge 
                                @if($invoice->status === 'paid') bg-success 
                                @else bg-danger 
                                @endif">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </strong>
                    </p>
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
                    <p>SAPTransport & Logistics</p>
                    <p>10 Awolowo Road Ikoyi</p>
                    <p>Lagos, Nigeria</p>
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

            <!-- Pay Now Button (Paystack) -->
            @if($invoice->status === 'unpaid')
                <div class="text-end mt-4">
                <form method="POST" action="{{ route('pay') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <input type="hidden" name="email" value="{{ $invoice->booking->user->email }}">
                    <input type="hidden" name="amount" value="{{ $invoice->amount * 100 }}"> {{-- Amount in kobo --}}
                    <input type="hidden" name="reference" value="{{ $invoice->invoice_number }}"> {{-- Invoice number as reference --}}
                    <button class="btn btn-success" type="submit" value="Pay Now!">
                        <i class="fa fa-plus-circle"></i> Pay Now!
                    </button>
                </form>


                    
                </div>
            @endif

            <!-- Notes and Signature -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">Notes</h6>
                    <p>Thank you for your business. Please contact us if you have any questions about this invoice.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p><strong>Authorized Signature</strong></p>
                    <img src="{{ asset('path-to-signature/signature.png') }}" alt="Signature" class="img-fluid" width="100">
                </div>
            </div>

            <!-- Back and Download Buttons -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <a href="{{ route('passenger.makepayments') }}" class="btn btn-secondary">Back to Payments</a>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('passenger.downloadInvoice', $invoice->id) }}" class="btn btn-info">Download PDF</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Style adjustments for responsiveness -->
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
        max-width: 350px;
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
            max-width: 150px;
        }

        .table-responsive {
            font-size: 14px;
        }
    }
</style>
@endsection
