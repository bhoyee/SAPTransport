<!DOCTYPE html>
<html>
<head>
    <title>User Payment Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <h2>SAP Transport and Logistics</h2>
        <h3>User Payment Report</h3>
        <p>Report generated from: {{ $request->date_from }} to {{ $request->date_to }}</p>
        <p>Generated on: {{ now()->format('d M, Y H:i:s') }}</p>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>S/N</th> <!-- Add S/N Column -->
                <th>Name</th>
                <th>Email</th>
                <th>Total Bookings</th>
                <th>Total Amount Spent (NGN)</th>
                <th>Total Refunded (NGN)</th>
                <th>Total Failed (NGN)</th>
                <th>Successful Trips</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userReportData as $index => $data) <!-- Add $index for numbering -->
            <tr>
                <td>{{ $index + 1 }}</td> <!-- Display S/N starting from 1 -->
                <td>{{ $data['name'] }}</td>
                <td>{{ $data['email'] }}</td>
                <td>{{ $data['totalBookings'] }}</td>
                <td>NGN {{ number_format($data['totalAmountSpent'], 2) }}</td>
                <td>NGN {{ number_format($data['totalRefunded'], 2) }}</td>
                <td>NGN {{ number_format($data['totalFailed'], 2) }}</td>
                <td>{{ $data['successfulTrips'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Totals</strong></td> <!-- Adjust colspan for totals -->
                <td><strong>{{ $totals['totalBookings'] }}</strong></td>
                <td><strong>NGN {{ number_format($totals['totalAmountSpent'], 2) }}</strong></td>
                <td><strong>NGN {{ number_format($totals['totalRefunded'], 2) }}</strong></td>
                <td><strong>NGN {{ number_format($totals['totalFailed'], 2) }}</strong></td>
                <td><strong>{{ $totals['totalSuccessfulTrips'] }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        https://saptransportationandlogistics.ng/
    </div>
</body>
</html>
