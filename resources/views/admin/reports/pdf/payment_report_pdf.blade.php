<!DOCTYPE html>
<html>
<head>
    <title>Payment Report</title>
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
        .company-name {
            font-size: 16px;
            font-weight: bold;
        }
        .report-subtitle {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .date-range {
            font-size: 12px;
            color: #555;
        }
        /* Website style in footer */
        .website {
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="company-name">SAP Transport and Logistics</div>
        <div class="report-subtitle">
            @if($status === 'All')
                Payment Report
            @else
                {{ ucfirst($status) }} Payment Report
            @endif
        </div>
        <div class="date-range">
            Report Data From: {{ $date_from }} to {{ $date_to }}
        </div>
        <div class="date-range">
            Generated on: {{ now()->format('d M, Y H:i:s') }}
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Booking Reference</th>
                <th>Payment Reference</th>
                <th>Amount(NGN)</th>
                <th>Status</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
            <tr> 
                <td>{{ $index + 1 }}</td>
                <td>{{ $payment->booking->booking_reference }}</td>
                <td>{{ $payment->payment_reference }}</td>
                <td>NGN {{ number_format($payment->amount, 2) }}</td>
                <td>{{ ucfirst($payment->status) }}</td>
                <td>{{ $payment->updated_at->format('d M, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total"><strong>Total</strong></td>
                <td class="total"><strong>NGN {{ number_format($totalAmount, 2) }}</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer for page numbers and website -->
    <div class="footer">
        <!-- Page: {PAGE_NUM} of {PAGE_COUNT} -->
        <br>
        <span class="website">www.saptransportationandlogistics.ng</span>
    </div>
    <script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "bold");
        $pdf->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
    }
</script> 
</body>
</html>
