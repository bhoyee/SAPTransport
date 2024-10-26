<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Email</title>
    <style>
        .email-container {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .email-container h1 {
            color: #333;
        }
        .email-container p {
            font-size: 16px;
            color: #555;
        }
        .email-container .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Invoice from SAPTransport</h1>

        <p>Dear {{ $invoice->full_name }},</p>

        <p>Thank you for using our services. Please find your invoice details below:</p>

        <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}<br>
        <strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}<br>
        <strong>Service Type:</strong> {{ $invoice->service_type }}<br>
        <strong>Amount:</strong> ₦{{ number_format($invoice->amount, 2) }}</p>

        <p>
            <a href="{{ route('admin.invoices.download', $invoice->id) }}" class="button">Download Invoice</a>
        </p>

        <p>If you have any questions, please contact us.</p>

        <p>Thank you,<br>
        {{ config('app.name') }}</p>
    </div>
</body>
</html>
