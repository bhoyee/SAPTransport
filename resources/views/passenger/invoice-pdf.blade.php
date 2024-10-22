<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .invoice-card {
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
            margin: 0 auto;
            width: 100%;
            max-width: 800px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .text-end {
            text-align: right;
        }
    </style>
</head>
<body>
<div class="invoice-card">
    <!-- Header Section -->
    <div class="invoice-header">
        <div>
            <img src="{{ asset('img/logo.png') }}" alt="SAPTransportation & Logistics">
        </div>
        <div class="text-end">
            <h4 class="fw-bold">Invoice</h4>
            <p>Invoice Number: {{ $invoice->invoice_number }}</p>
            <p>Issue Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</p>
            <p>Due Date: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
            <p>Due Amount: NGN{{ number_format($invoice->amount, 2) }}</p>

            <!-- Invoice Status -->
            <p>
                <strong>Status:
                <span style="color: 
                    @if(trim(strtolower($invoice->status)) === 'paid') 
                        green
                    @elseif(trim(strtolower($invoice->status)) === 'unpaid') 
                        red
                    @elseif(trim(strtolower($invoice->status)) === 'refunded') 
                        skyblue
                    @else 
                        gray
                    @endif;">
                    {{ ucfirst($invoice->status) }}
                </span>

                </strong>
            </p>
        </div>
    </div>

    <hr>

    <!-- Billing Information -->
    <div>
        <h5 class="fw-bold">Billed to</h5>
        <p><strong>{{ $user->name }}</strong></p>
        <p>{{ $user->email }}</p>
        <p>{{ $user->address }}</p>

        <h5 class="fw-bold text-end">Invoice From</h5>
        <p class="text-end">SAPTransport & Logistics</p>
        <p class="text-end">10 Awolowo Road Ikoyi</p>
        <p class="text-end">Lagos, Nigeria</p>
    </div>

    <!-- Booking Summary -->
    <table>
        <thead>
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

    <!-- Payment Details -->
    <div class="text-end">
        <h5 class="fw-bold">Payment Details</h5>
        @if($invoice->payment && $invoice->payment->payment_method)
                        <p>Payment Method: {{ $invoice->payment->payment_method }}</p>
                    @else
                        <p>Payment Method: Not Provided</p>
                    @endif
        <h5 class="fw-bold">Summary</h5>
        <p>Subtotal: NGN {{ number_format($invoice->subtotal ?? $invoice->amount, 2) }}</p>
        <p>Taxable: NGN {{ number_format($invoice->taxable ?? 0, 2) }}</p>
        <p>Discount: NGN {{ number_format($invoice->discount ?? 0, 2) }}</p>
        <p class="fw-bold">Total: NGN {{ number_format($invoice->amount, 2) }}</p>
    </div>

    <!-- Notes -->
    <div>
        <h6 class="fw-bold">Notes</h6>
        <p>Thank you for your business. Please contact us if you have any questions about this invoice.</p>
    </div>
</div>
</body>
</html>
