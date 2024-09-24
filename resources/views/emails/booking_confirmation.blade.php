<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
       <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Booking Confirmation</h1>
    
    <p>Hello {{ $userName }},</p>

    <p>Thank you for booking with SAP Transportation. Below is a summary of your booking:</p>
    
    <p>Your Booking Reference number is: <span style="font-size: 1.5em; font-weight: bold;">{{ $bookingReference }}</span></p>
    
    <p>Your Booking Status: <span style="color: red; font-size: 1.5em; font-weight: bold;">{{ $status }}</span></p>


 <ul>
        @if(!empty($bookingDetails->service_type))
        <li>Service Type: <b>{{ $bookingDetails->service_type }}</b></li>
        @endif

        @if(!empty($bookingDetails->trip_type))
        <li>Trip Type: <b>{{ $bookingDetails->trip_type }}</b></li>
        @endif

        @if(!empty($bookingDetails->airport_name))
        <li>Airport: <b>{{ $bookingDetails->airport_name }}</b></li>
        @endif

        @if(!empty($bookingDetails->vehicle_type))
        <li>Vehicle Type: <b>{{ $bookingDetails->vehicle_type }}</b></li>
        @endif

        @if(!empty($bookingDetails->pickup_date))
        <li>Pickup Date: <b>{{ $bookingDetails->pickup_date }}</b></li>
        @endif

        @if(!empty($bookingDetails->pickup_time))
        <li>Pickup Time: <b>{{ $bookingDetails->pickup_time }}</b></li>
        @endif

        @if(!empty($bookingDetails->pickup_address))
        <li>Pickup Address: <b>{{ $bookingDetails->pickup_address }}</b></li>
        @endif

        @if(!empty($bookingDetails->dropoff_address))
        <li>Dropoff Address: <b>{{ $bookingDetails->dropoff_address }}</b></li>
        @endif
        
        @if(!empty($returnPickupDate))
        <li>Return Pickup Date: <b>{{ $returnPickupDate }}</b></li>
        @endif

        @if(!empty($returnPickupTime))
        <li>Return Pickup Time: <b>{{ $returnPickupTime }}</b></li>
        @endif

        @if(!empty($bookingDetails->number_adults))
        <li>Number of Adults: <b>{{ $bookingDetails->number_adults }}</b></li>
        @endif

        @if(!empty($bookingDetails->number_children))
        <li>Number of Children: <b>{{ $bookingDetails->number_children ?? 'None' }}</b></li>
        @endif
    </ul>

    <p>One of our representatives will contact you soon.</p>

    <p>Thank you for choosing SAP Transportation!</p>

<a href="{{ route('passenger.dashboard') }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;">
    View Booking
</a>

    
    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
