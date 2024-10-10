<!DOCTYPE html>
<html>
<head>
    <title>New Booking Notification</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>New Booking Created</h1>
    <p>A new booking has been made by {{ $userName }}.</p>

    <p><strong>Booking Reference:</strong> <span style="font-size: 1.5em; font-weight: bold;">{{ $bookingReference }}</span></p>
    <p><strong>Booking Status:</strong> <span style="color: red; font-size: 1.5em; font-weight: bold;">{{ $status }}</span></p>

    <!-- Booking Details Table -->
    <table>
        @if(!empty($bookingDetails->service_type))
        <tr>
            <th>Service Type:</th>
            <td>{{ $bookingDetails->service_type }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->trip_type))
        <tr>
            <th>Trip Type:</th>
            <td>{{ $bookingDetails->trip_type }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->airport_name))
        <tr>
            <th>Airport:</th>
            <td>{{ $bookingDetails->airport_name }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->vehicle_type))
        <tr>
            <th>Vehicle Type:</th>
            <td>{{ $bookingDetails->vehicle_type }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->pickup_date))
        <tr>
            <th>Pickup Date:</th>
            <td>{{ $bookingDetails->pickup_date }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->pickup_time))
        <tr>
            <th>Pickup Time:</th>
            <td>{{ $bookingDetails->pickup_time }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->pickup_address))
        <tr>
            <th>Pickup Address:</th>
            <td>{{ $bookingDetails->pickup_address }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->dropoff_address))
        <tr>
            <th>Dropoff Address:</th>
            <td>{{ $bookingDetails->dropoff_address }}</td>
        </tr>
        @endif

        @if(!empty($returnPickupDate))
        <tr>
            <th>Return Pickup Date:</th>
            <td>{{ $returnPickupDate }}</td>
        </tr>
        @endif

        @if(!empty($returnPickupTime))
        <tr>
            <th>Return Pickup Time:</th>
            <td>{{ $returnPickupTime }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->number_adults))
        <tr>
            <th>Number of Adults:</th>
            <td>{{ $bookingDetails->number_adults }}</td>
        </tr>
        @endif

        @if(!empty($bookingDetails->number_children))
        <tr>
            <th>Number of Children:</th>
            <td>{{ $bookingDetails->number_children ?? 'None' }}</td>
        </tr>
        @endif
    </table>

    <p>Please log in to the admin panel to review this booking.</p>
</body>
</html>
