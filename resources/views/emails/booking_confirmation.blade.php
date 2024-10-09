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
        .btn-primary {
            display: inline-block;
            padding: 8px 12px;
            font-size: 14px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
            text-align: center;
        }

        /* Responsive styling */
        @media (max-width: 600px) {
            table, table th, table td {
                width: 100%;
                display: block;
            }

            .btn-primary {
                width: 100%; /* Make the button responsive */
                text-align: center;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Booking Confirmation</h1>

    <p>Hello {{ $userName }},</p>

    <p>Thank you for booking with SAP Transportation. Below is a summary of your booking:</p>

    <p>Your Booking Reference number is: <span style="font-size: 1.5em; font-weight: bold;">{{ $bookingReference }}</span></p>

    <p>Your Booking Status: <span style="color: red; font-size: 1.5em; font-weight: bold;">{{ $status }}</span></p>

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

    <!-- Text and button below the table -->
    <div style="clear: both; margin-top: 20px;">
        <p>One of our representatives will contact you soon.</p>
        <p>Thank you for choosing SAP Transportation!</p>

        <!-- Smaller, left-aligned button -->
        <a href="{{ route('passenger.dashboard') }}" class="btn-primary">
            View Booking
        </a>

        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>

</body>
</html>
