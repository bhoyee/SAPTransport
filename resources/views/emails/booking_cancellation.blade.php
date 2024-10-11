<!DOCTYPE html>
<html>
<head>
    <title>Booking Cancellation</title>
</head>
<body>
    <h1>Booking Cancellation Notice</h1>
    <p>Dear {{ $user->name }},</p>

    <p>Your booking with reference <strong>{{ $booking->booking_reference }}</strong> has been successfully cancelled.</p>

   <p> Your booking was cancelled on  {{ $booking->updated_at }}. 

   <p>Few Details:</p>
    <ul>
        <li><strong>Booking Reference:</strong> {{ $booking->booking_reference}}</li>
        <li><strong>Service Type:</strong>  {{ $booking->service_type }}</li>
        <li><strong>Vehicle Type::</strong> {{ $booking->vehicle_type }}</li>

    <p>If you have any questions, please contact our support team.</p>

    <p>Thank you,</p>
    <p>SAP Transportation Team</p>
</body>
</html>
