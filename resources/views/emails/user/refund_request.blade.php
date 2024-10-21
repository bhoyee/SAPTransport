<!DOCTYPE html>
<html>
<head>
    <title>Your Refund Request Has Been Received</title>
</head>
<body>
    <h1>Refund Request Received</h1>
    <p>Dear {{ $booking->user->name }},</p>
    <p>We have received your refund request for the following booking:</p>

    <table>
        <tr>
            <th>Booking Reference:</th>
            <td>{{ $bookingReference }}</td>
        </tr>
        <tr>
            <th>Payment Reference:</th>
            <td>{{ $paymentReference }}</td>
        </tr>
        <tr>
            <th>Invoice Number:</th>
            <td>{{ $invoiceNumber }}</td>
        </tr>
    </table>

    <p>We will notify you once the refund has been processed.</p>

    <p>Thank you for your patience.</p>
    <p>Your Company Name</p>
</body>
</html>
