<!-- resources/views/admin/invoices/custom-view.blade.php -->

@extends('admin.layouts.admin-layout')

@section('title', 'Custom Invoice')

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
                    <p>Issue Date: {{ \Carbon\Carbon::parse($invoice->issue_date)->format('d M Y') }}</p>
                    <p>Due Date: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                    <p>Due Amount: ₦{{ number_format($invoice->amount, 2) }}</p>

                    <!-- Invoice Status -->
                    <p>
                        <strong>Status: 
                            <span class="badge 
                                @if(trim(strtolower($invoice->status)) === 'paid') 
                                    bg-success
                                @elseif(trim(strtolower($invoice->status)) === 'unpaid') 
                                    bg-danger
                                @elseif(trim(strtolower($invoice->status)) === 'refunded') 
                                    bg-info
                                @else 
                                    bg-secondary
                                @endif fs-4">
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
                    <p><strong>{{ $invoice->name }}</strong></p>
                    <p>{{ $invoice->email }}</p>
                    <p>{{ $invoice->phone }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="fw-bold">Invoice From</h5>
                    <p>SAPTransport & Logistics</p>
                    <p>10 Awolowo Road Ikoyi</p>
                    <p>Lagos, Nigeria</p>
                </div>
            </div>

            <!-- Service Summary -->
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
                            <td>{{ $invoice->service_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->pickup_date)->format('d M Y') }}</td>
                            <td>{{ $invoice->pickup_time }}</td>
                            <td>{{ $invoice->dropoff_address }}</td>
                            <td>₦{{ number_format($invoice->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Payment Details -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5 class="fw-bold">Payment Details</h5>
                    <p>Payment Method: Walk-in</p>
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="fw-bold">Summary</h5>
                    <p>Subtotal: ₦{{ number_format($invoice->amount, 2) }}</p>
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
                    <img src="{{ asset('path-to-signature/signature.png') }}" alt="Signature" class="img-fluid" width="100">
                </div>
            </div>

            <!-- Back and Download Buttons -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <a href="{{ route('admin.customInvoices') }}" class="btn btn-secondary">Back to Invoices</a>
                </div>
                <div class="col-md-6 text-end">
                    <button id="download-pdf-btn" class="btn btn-info">
                        <span id="download-text">Download PDF</span>
                        <span id="download-spinner" class="spinner-border spinner-border-sm" style="display: none;" role="status" aria-hidden="true"></span>
                    </button>
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

<script>
    document.getElementById('download-pdf-btn').addEventListener('click', function() {
        // Show spinner and hide the text
        document.getElementById('download-text').style.display = 'none';
        document.getElementById('download-spinner').style.display = 'inline-block';

        // Trigger the download
        window.location.href = "{{ route('admin.customInvoices.download', $invoice->id) }}";

        // Set a timeout to hide the spinner after a delay (e.g., 3 seconds)
        setTimeout(function() {
            document.getElementById('download-text').style.display = 'inline-block';
            document.getElementById('download-spinner').style.display = 'none';
        }, 3000); // Adjust the delay based on your file size (3 seconds here)
    });
</script>

@endsection
