<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Log;

class PaymentReportController extends Controller
{
    // Show the payment report page
    public function showPaymentReportPage()
    {
        return view('admin.reports.payment_report');
    }

    // Fetch report data based on the selected timeframe (daily, weekly, monthly, yearly)
    public function fetchReportData($timeframe)
    {
        $today = now()->startOfDay();
        $timezone = config('app.timezone', 'UTC');
    
        // Log the selected timeframe and timezone
        \Log::info("Fetching payment report data", ['timeframe' => $timeframe, 'timezone' => $timezone]);
    
        // Query for total paid payments
        $paidQuery = Payment::where('status', 'paid');
        // Query for refunded payments
        $refundedQuery = Payment::where('status', 'refunded');
        // Query for unpaid payments
        $unpaidQuery = Payment::where('status', 'unpaid');
    
        // Apply date range filters to each query based on the selected timeframe
        switch ($timeframe) {
            case 'daily':
                \Log::info("Filtering for daily report");
                $paidQuery->whereDate('updated_at', $today);
                $refundedQuery->whereDate('updated_at', $today);
                $unpaidQuery->whereDate('updated_at', $today);
                break;
            case 'weekly':
                $weekStart = $today->copy()->startOfWeek();  // Start of week (Monday)
                $weekEnd = $today->copy()->endOfWeek()->endOfDay(); // End of week (Sunday 23:59:59)
    
                \Log::info("Filtering for weekly report", [
                    'week_start' => $weekStart->toDateTimeString(),
                    'week_end' => $weekEnd->toDateTimeString(),
                ]);
    
                // Filter queries between the start and end of the week
                $paidQuery->whereBetween('updated_at', [$weekStart, $weekEnd]);
                $refundedQuery->whereBetween('updated_at', [$weekStart, $weekEnd]);
                $unpaidQuery->whereBetween('updated_at', [$weekStart, $weekEnd]);
    
                // Log the query for weekly report
                \Log::info("Executed weekly refund query", [
                    'query' => $refundedQuery->toSql(), 
                    'bindings' => $refundedQuery->getBindings()
                ]);
                break;
            case 'monthly':
                \Log::info("Filtering for monthly report", ['month' => $today->month, 'year' => $today->year]);
                $paidQuery->whereMonth('updated_at', $today->month)->whereYear('updated_at', $today->year);
                $refundedQuery->whereMonth('updated_at', $today->month)->whereYear('updated_at', $today->year);
                $unpaidQuery->whereMonth('updated_at', $today->month)->whereYear('updated_at', $today->year);
                break;
            case 'yearly':
                \Log::info("Filtering for yearly report", ['year' => $today->year]);
                $paidQuery->whereYear('updated_at', $today->year);
                $refundedQuery->whereYear('updated_at', $today->year);
                $unpaidQuery->whereYear('updated_at', $today->year);
                break;
        }
    
        // Calculate the report data for display on the cards
        $totalPayments = $paidQuery->count();
        $totalRefunded = $refundedQuery->count();
        $totalUnpaid = $unpaidQuery->count();
    
        // Log the calculated data
        \Log::info("Report data calculated", [
            'totalPayments' => $totalPayments,
            'totalRefunded' => $totalRefunded,
            'totalUnpaid' => $totalUnpaid,
        ]);
    
        // Prepare the response data
        $data = [
            'totalPayments' => $totalPayments, // Count only 'paid' transactions
            'totalRefunded' => $totalRefunded, // Total refunded transactions
            'totalUnpaid' => $totalUnpaid, // Total unpaid transactions
        ];
    
        return response()->json($data);
    }
    

    // Generate PDF based on filters from the form
    // Controller method to generate PDF
public function generatePdf(Request $request)
{
    \Log::info('Generating PDF report', ['filters' => $request->all()]);

    $query = Payment::query();

    // Filter by payment status
    if ($request->status != 'All') {
        \Log::info('Filtering by payment status', ['status' => $request->status]);
        $query->where('status', $request->status);
    }

    // Filter by date range using updated_at
    if ($request->date_from && $request->date_to) {
        \Log::info('Filtering by date range', ['date_from' => $request->date_from, 'date_to' => $request->date_to]);
        $query->whereBetween('updated_at', [$request->date_from, $request->date_to]);
    }

    // Fetch the filtered payments
    $payments = $query->get();
    \Log::info('Number of payments found', ['count' => $payments->count()]);

    // If no payments found, return a JSON response
    if ($payments->isEmpty()) {
        \Log::warning('No payments found for the selected filters');
        return response()->json(['error' => 'No payments found for the selected filters.'], 404); // Return JSON response with error
    }

    // Calculate the total amount for the PDF footer
    $totalAmount = $payments->sum('amount');
    \Log::info('Total amount calculated', ['totalAmount' => $totalAmount]);

    // Prepare data for the PDF view
    $status = $request->status; // Either 'All', 'paid', 'unpaid', etc.
    $date_from = $request->date_from;
    $date_to = $request->date_to;

    // Get booking reference from the first payment entry
    $firstBookingReference = $payments->first()->booking->booking_reference ?? 'booking_reference';

    // Generate a dynamic filename: payment_report_bookingReference_datetime.pdf
    $timestamp = now()->format('Ymd_His'); // Example: 20241020_122530
    $filename = "payment_report_{$firstBookingReference}_{$timestamp}.pdf";

    // Generate the PDF with letter size and landscape orientation
    $pdf = PDF::loadView('admin.reports.pdf.payment_report_pdf', compact('payments', 'totalAmount', 'status', 'date_from', 'date_to'))
              ->setPaper('letter', 'landscape');

    \Log::info('PDF generation complete', ['filename' => $filename]);

    // Return the PDF with dynamic filename
    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->output();
    }, $filename);
}



}
