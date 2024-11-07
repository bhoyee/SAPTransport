<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PDF;

class AdminReportController extends Controller
{
    // Show the sales report page
    public function showSalesReport()
    {
        return view('admin.reports.sales-report');
    }

    // Fetch sales data for the cards
    public function fetchSalesData()
    {
        try {
            $today = Invoice::whereDate('updated_at', Carbon::today())
                            ->where('status', 'Paid')
                            ->sum('amount');
    
            $week = Invoice::whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                           ->where('status', 'Paid')
                           ->sum('amount');
    
            $month = Invoice::whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->where('status', 'Paid')
                            ->sum('amount');
    
            $year = Invoice::whereBetween('updated_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
                           ->where('status', 'Paid')
                           ->sum('amount');
    
            Log::info("Fetched sales data - Today: $today, Week: $week, Month: $month, Year: $year");
    
            return response()->json([
                'today' => $today,
                'week' => $week,
                'month' => $month,
                'year' => $year,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sales data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch sales data'], 500);
        }
    }
    
    public function downloadSalesReport(Request $request)
    {
        $fromDate = $request->query('from');
        $toDate = $request->query('to');
    
        try {
            if (!$fromDate || !$toDate) {
                return back()->withErrors(['error' => 'Please provide both from and to dates.']);
            }
    
            // Fetch sales data within the date range
            $salesData = Invoice::whereBetween('updated_at', [$fromDate, $toDate])
                                ->where('status', 'Paid')
                                ->get();
    
            // Check if data is available
            if ($salesData->isEmpty()) {
                return back()->withErrors(['error' => 'No data found for the specified date range.']);
            }
    
            // Load the view for PDF generation
            $pdf = PDF::loadView('admin.reports.sales-pdf', [
                'salesData' => $salesData,
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ])->setPaper('a4', 'landscape');
    
            // Return the generated PDF as a response
            return response()->streamDownload(
                fn () => print($pdf->output()),
                'sales_report_' . $fromDate . '_to_' . $toDate . '.pdf',
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            Log::error('Error generating sales report: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to generate the sales report.']);
        }
    }
    
    
    

}
