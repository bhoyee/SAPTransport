<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Update</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Booking Update</h2>

    <p>Dear {{ $booking->user->name }},</p>

    <p>We are pleased to inform you that your booking (Reference: <strong>{{ $booking->booking_reference }}</strong>) has been updated with the driver details.</p>

    <h3>Booking Details:</h3>
    <ul>
        <li><strong>Service Type:</strong> {{ $booking->service_type }}</li>
        <li><strong>Trip Type:</strong> {{ $booking->trip_type }}</li>
        <li><strong>Pickup Date:</strong> {{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M, Y') }}</li>
    </ul>

    <h3>Driver Details:</h3>
    <ul>
        <li><strong>Driver's Name:</strong> {{ $booking->driver_name }}</li>
        <li><strong>Vehicle Details:</strong> {{ $booking->vehicle_details }}</li>
    </ul>

    <p>If you have any questions or need further assistance, please feel free to contact us.</p>

    <p>Thank you for choosing our services!</p>

    <p>Best regards,<br>
    SAP Transportation and Logistics Team</p>
</body>
</html>
