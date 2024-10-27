<!-- resources/views/admin/reports/sales-pdf.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
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
        .website {
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="company-name">SAP Transport and Logistics</div>
        <div class="report-subtitle">Sales Report</div>
        <div class="date-range">
            From: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} To: {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
        </div>
        <div class="date-range">
            Generated on: {{ now()->format('d M, Y H:i:s') }}
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Invoice Number</th>
                <th>Issue Date</th>
                <th>Amount (NGN)</th>

                <!-- <th>Status</th> -->
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach($salesData as $index => $sale)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sale->invoice_number }}</td>
               
                <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}</td>
                <td>NGN {{ number_format($sale->amount, 2) }}</td>
                <!-- <td>{{ ucfirst($sale->status) }}</td> -->
            </tr>
            @php $totalAmount += $sale->amount; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td><strong>NGN {{ number_format($totalAmount, 2) }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer for page numbers and website -->
    <div class="footer">
        <span class="website">www.saptransportationandlogistics.ng</span>
    </div>

    <script type="text/php">
    if ( isset($pdf) ) {
        $font = $pdf->getFontMetrics()->get_font("helvetica", "bold");
        $pdf->page_text(72, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0,0,0));
    }
    </script>
</body>
</html>
