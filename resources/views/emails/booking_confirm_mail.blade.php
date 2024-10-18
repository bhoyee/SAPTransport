<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Table styling */
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th, table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            font-weight: bold;
            width: 30%;
        }

        table td {
            width: 70%;
        }

        /* Button styling */
        .btn-primary, .btn-secondary {
            display: inline-block;
            padding: 8px 12px;
            font-size: 14px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
            text-align: center;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        /* Responsive styling */
        @media (max-width: 600px) {
            table, table th, table td {
                width: 100%;
                display: block;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                text-align: center;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Booking Confirmation</h1>

    <p>Hello {{ $booking->user->name ?? 'Customer' }},</p>

    <p>Your booking with SAP Transportation has been confirmed. Below are your booking and invoice details:</p>

    <!-- Booking Details Table -->
    <h3>Few Booking Details</h3>
    <table>
        <tr>
            <th>Booking Reference:</th>
            <td>{{ $booking->booking_reference }}</td>
        </tr>
        <tr>
            <th>Service Type:</th>
            <td>{{ ucfirst($booking->service_type) }}</td>
        </tr>
        @if(!empty($booking->trip_type))
        <tr>
            <th>Trip Type:</th>
            <td>{{ ucfirst($booking->trip_type) }}</td>
        </tr>
        @endif
        <tr>
            <th>Pickup Date:</th>
            <td>{{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M, Y') }}</td>
        </tr>
        <tr>
            <th>Pickup Time:</th>
            <td>{{ $booking->pickup_time }}</td>
        </tr>
    </table>

    <!-- Invoice Details Table -->
    <h3>Invoice Details</h3>
    <table>
        <tr>
            <th>Invoice Number:</th>
            <td>{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <th>Amount:</th>
            <td>₦{{ number_format($invoice->amount, 2) }}</td>
        </tr>
        <tr>
            <th>Payment Status:</th>
            <td>{{ ucfirst($invoice->status) }}</td>
        </tr>
    </table>

    <!-- Payment Options -->
    <p>You can choose to pay now using our online platform, view your invoice details, or pay in cash on/after the trip.</p>

    <div>
        <!-- View Invoice Button -->
        <a href="{{ route('passenger.invoice', ['id' => $invoice->id]) }}" class="btn-secondary">
            View Invoice
        </a>

        <!-- Pay Now Button -->
        <a href="{{ route('payment.search') }}" class="btn-primary">
            Pay Now
        </a>
    </div>

    <p>If you have any questions, feel free to contact our support team.</p>

    <p>Thank you for choosing SAP Transportation!</p>

    <p>Best regards,<br>{{ config('app.name') }} Team</p>
</body>
</html>
