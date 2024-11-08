<!-- resources/views/emails/monthly_report.blade.php -->

<h1>Monthly System Performance Report</h1>

<p>Dear Admin,</p>

<p>Please find below the summary of the system's performance metrics for the past month. This report provides an overview of passenger activity, bookings, ticket resolutions, and revenue performance to aid in decision-making and ensure the ongoing efficiency of our operations.</p>

<table>
    <thead>
        <tr>
            <th colspan="2" style="padding: 10px 0; text-align: left;">Passenger Metrics</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Active Passengers:</td>
            <td><strong>{{ $reportData['totalActivePassengers'] }}</strong></td>
        </tr>
        <tr>
            <td>Total Inactive Passengers:</td>
            <td><strong>{{ $reportData['totalInactivePassengers'] }}</strong></td>
        </tr>
        <tr>
            <td>General Total Passengers:</td>
            <td><strong>{{ $reportData['totalPassengers'] }}</strong></td>
        </tr>
        <tr>
            <td>New Passengers Last Month:</td>
            <td><strong>{{ $reportData['passengersLastMonth'] }}</strong></td>
        </tr>
    </tbody>
</table>

<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="2" style="padding: 10px 0; text-align: left;">Booking Metrics</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Bookings Last Month:</td>
            <td><strong>{{ $reportData['bookingsLastMonth'] }}</strong></td>
        </tr>
        <tr>
            <td>Total Bookings This Year:</td>
            <td><strong>{{ $reportData['bookingsThisYear'] }}</strong></td>
        </tr>
        <tr>
            <td>Completed Bookings Last Month:</td>
            <td><strong>{{ $reportData['completedBookingsThisMonth'] }}</strong></td>
        </tr>
        <tr>
            <td>Completed Bookings This Year:</td>
            <td><strong>{{ $reportData['completedBookingsThisYear'] }}</strong></td>
        </tr>
        <tr>
            <td>Total Canceled Bookings This Month:</td>
            <td><strong>{{ $reportData['canceledBookings'] }}</strong></td>
        </tr>
    </tbody>
</table>

<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="2" style="padding: 10px 0; text-align: left;">Revenue Metrics</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Sales This Year:</td>
            <td><strong>₦{{ number_format($reportData['totalSalesThisYear'], 2) }}</strong></td>
        </tr>
        <tr>
            <td>Total Sales Last Month:</td>
            <td><strong>₦{{ number_format($reportData['totalSalesLastMonth'], 2) }}</strong></td>
        </tr>
    </tbody>
</table>

<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="2" style="padding: 10px 0; text-align: left;">Ticket Resolution Metrics</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Closed Tickets This Year:</td>
            <td><strong>{{ $reportData['closedTicketsThisYear'] }}</strong></td>
        </tr>
        <tr>
            <td>Total Open Tickets This Year:</td>
            <td><strong>{{ $reportData['openTicketsThisYear'] }}</strong></td>
        </tr>
    </tbody>
</table>

<p>We appreciate your efforts in maintaining and managing the platform to ensure smooth operations and a seamless experience for all users.</p>

<p>Best regards,</p>
<p><strong>The System Administration Team</strong></p>
