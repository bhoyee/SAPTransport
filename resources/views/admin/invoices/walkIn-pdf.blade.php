<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Invoice PDF</title>
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
    <div class="invoice-header">
        <div>
            <img src="{{ asset('img/logo.png') }}" alt="Company Logo">
        </div>
        <div class="text-end">
            <h4 class="fw-bold">Invoice</h4>
            <p>Invoice Number: {{ $invoice->invoice_number }}</p>
            <p>Issue Date: {{ \Carbon\Carbon::parse($invoice->issue_date)->format('d M Y') }}</p>
            <p>Due Date: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
            <p>Due Amount: NGN{{ number_format($invoice->due_amount, 2) }}</p>
            <p>Status: {{ ucfirst($invoice->status) }}</p>
        </div>
    </div>

    <hr>

    <div>
        <h5>Billed to</h5>
        <p><strong>{{ $invoice->full_name }}</strong></p>
        <p>{{ $invoice->email }}</p>
        <p>{{ $invoice->phone }}</p>

        <h5 class="text-end">Invoice From</h5>
        <p class="text-end">SAPTransport & Logistics</p>
        <p class="text-end">10 Awolowo Road Ikoyi</p>
        <p class="text-end">Lagos, Nigeria</p>
    </div>

    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th>Service Type</th>
            <th>Pickup Date</th>
            <th>Pickup Time</th>
            <th>Pickup Address</th>
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
            <td>{{ $invoice->pickup_address }}</td>
            <td>{{ $invoice->dropoff_address }}</td>
            <td>₦{{ number_format($invoice->amount, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <div class="text-end">
        <h5>Summary</h5>
        <p>Total: NGN{{ number_format($invoice->amount, 2) }}</p>
    </div>

    <div>
        <h6>Notes</h6>
        <p>Thank you for your business. Please contact us if you have any questions about this invoice.</p>
    </div>
</div>
</body>
</html>
