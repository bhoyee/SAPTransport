<!DOCTYPE html>
<html>
<head>
    <title>Booking Report</title>
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
           /* Footer style for page numbering */
           .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="report-header">
            <!-- Report Header -->
    <div class="report-header">
        <h1>{{ $companyName }} - 
            @if ($bookingStatus === 'All') 
                All 
            @else 
                {{ ucfirst($bookingStatus) }} Bookings Report 
            @endif
        </h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>Report Date Range: From {{ $dateFrom }} to {{ $dateTo }}</p>
    </div>



    <table class="report-table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Booking Ref</th>
                <th>Booking Date</th>
                <th>Status</th>
                <th>Service Type</th>
                <th>Invoice Number</th>
                <th>Amount(NGN )</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                <tr>
                    <td>{{ $loop->iteration }}</td> <!-- Automatically generated S/N -->
                    <td>{{ $booking->booking_reference }}</td>
                    <td>{{ $booking->updated_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ ucfirst($booking->status) }}</td>
                    <td>{{ $booking->service_type }}</td>
                    <td>{{ $booking->invoice->invoice_number ?? 'N/A' }}</td>
                    <td>
    @if($booking->invoice && $booking->invoice->amount)
        NGN {{ number_format($booking->invoice->amount, 2) }}
    @else
        N/A
    @endif
</td>
                </tr>
            @endforeach
        </tbody>
    </table>

       <!-- Footer section for page number -->
<!-- Footer for page numbering -->
<div class="footer">
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_script('
                    if ($PAGE_COUNT > 1) {
                        $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                        $size = 10;
                        $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                        $y = 15;
                        $x = 520;
                        $pdf->text($x, $y, $pageText, $font, $size);
                    }
                ');
            }
        </script>
    </div>
</body>
</html>
