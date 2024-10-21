<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use PDF;
use Illuminate\Support\Facades\Log;
use App\Models\User; 



class UserPaymentReportController extends Controller
{

        // Method to show the user payment report page
        public function index()
        {
            return view('admin.reports.user_payment_report');
        }
    //
    public function generateUserPaymentReport(Request $request)
    {
        Log::info('Form has been submitted');
        Log::info('Request data', $request->all());
        \Log::info('Generating User Payment Report', ['filters' => $request->all()]);
    
        try {
            // Ensure the date range is valid
            if (!$request->date_from || !$request->date_to) {
                return redirect()->back()->with('error', 'Please specify a valid date range.');
            }
    
            // Query to fetch users along with their related bookings and payments, filtered by payment_date
            $users = User::with(['bookings', 'payments' => function ($query) use ($request) {
                // Filter payments by payment_date and status ('paid' or 'refunded')
                $query->whereIn('status', ['paid', 'refunded'])
                      ->whereBetween('payment_date', [$request->date_from, $request->date_to]);
            }])
            // Only retrieve users who have payments within the selected date range
            ->whereHas('payments', function ($query) use ($request) {
                $query->whereBetween('payment_date', [$request->date_from, $request->date_to]);
            })
            ->get();
    
            // Check if any users were found
            if ($users->isEmpty()) {
                return redirect()->back()->with('error', 'No records found for the selected date range.');
            }
    
            // Initialize totals
            $totalBookings = 0;
            $totalFailed = 0;
            $totalAmountSpent = 0;
            $totalRefunded = 0;  // To calculate total refunded
            $totalSuccessfulTrips = 0;
    
            // Format the data for the report
            $userReportData = $users->map(function ($user) use (&$totalBookings, &$totalAmountSpent, &$totalRefunded, &$totalFailed, &$totalSuccessfulTrips) {
                $userTotalBookings = $user->bookings->count();

                                // Calculate total amount for unpaid payments explicitly
                $userFailedAmountSpent = $user->payments()->where('status', 'unpaid')->sum('amount');
                
                // Calculate total amount for paid, refunded, and refund-pending payments
                $userTotalAmountSpent = $user->payments()->whereIn('status', ['paid', 'refunded', 'refund-pending'])->sum('amount');
                
                // Calculate total refunded amount
                $userTotalRefunded = $user->payments()->where('status', 'refunded')->sum('amount');
                
                // Calculate successful trips
                $userSuccessfulTrips = $user->bookings->where('status', 'completed')->count();

    
                
                // Add to the overall totals
                $totalBookings += $userTotalBookings;
                $totalAmountSpent += $userTotalAmountSpent;
                $totalRefunded += $userTotalRefunded;
                $totalFailed += $userFailedAmountSpent;
                $totalSuccessfulTrips += $userSuccessfulTrips;
    
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                
                    'totalBookings' => $userTotalBookings,
                    'totalAmountSpent' => $userTotalAmountSpent,
                    'totalRefunded' => $userTotalRefunded,  
                    'totalFailed' => $userFailedAmountSpent,
                    'successfulTrips' => $userSuccessfulTrips,
                ];
            });
    
            \Log::info('User Payment Report generated successfully');
    
            // Prepare the totals for the PDF footer
            $totals = [
                'totalBookings' => $totalBookings,
                'totalAmountSpent' => $totalAmountSpent,
                'totalRefunded' => $totalRefunded,  // Include total refunded in the totals
                'totalFailed' => $totalFailed,
                'totalSuccessfulTrips' => $totalSuccessfulTrips,
            ];
    
            // Generate dynamic filename using the current datetime
            $timestamp = now()->format('Ymd_His'); // Example: 20241020_122530
            $filename = 'user_payment_report_' . "_{$timestamp}.pdf";
    
            // Generate the PDF
            $pdf = PDF::loadView('admin.reports.pdf.user_payment_report_pdf', compact('userReportData', 'totals', 'request'))
                ->setPaper('letter', 'landscape');
    
            return $pdf->download($filename); // Use the dynamic filename here
    
        } catch (\Exception $e) {
            \Log::error('Error generating User Payment Report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate user payment report.');
        }
    }
    
    
    

}
