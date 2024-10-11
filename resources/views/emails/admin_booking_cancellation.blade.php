<!DOCTYPE html>
<html>
<head>
    <title>Booking Cancellation</title>
</head>
<body>
    <h1>Booking Cancellation Notice</h1>
    <p>A booking with reference <strong>{{ $booking->booking_reference }}</strong> has been cancelled by {{ $booking->user->name }} on {{ $booking->updated_at }}.</p>
    <p>Booking details:</p>
    <ul>
        <li>Service Type: {{ $booking->service_type }}</li>
        <li>Vehicle Type: {{ $booking->vehicle_type }}</li>
        <!-- Add more details as necessary -->
    </ul>
</body>
</html>
