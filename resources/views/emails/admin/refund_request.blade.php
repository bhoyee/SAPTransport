<!DOCTYPE html>
<html>
<head>
    <title>Refund Request Initiated</title>
</head>
<body>
    <h1>Refund Request Initiated</h1>
    <p>A refund request has been initiated for the following booking:</p>

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

    <p>Please log into the admin panel to review and process the refund.</p>

    <p>Thank you,</p>
    <p>Your Company Name</p>
</body>
</html>
