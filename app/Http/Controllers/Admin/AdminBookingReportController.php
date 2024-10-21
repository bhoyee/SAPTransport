<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use PDF;

class AdminBookingReportController extends Controller
{
    //
    public function index()
    {
        // Daily report - Bookings created today and filtered by created_at for total bookings
        $today = now();
    
        // Total bookings created today
        $totalBookings = Booking::whereDate('created_at', $today)->count();
    
        // Bookings by status with updated_at for status tracking
        $totalPending = Booking::whereDate('updated_at', $today)->where('status', 'pending')->count();
        $totalConfirmed = Booking::whereDate('updated_at', $today)->where('status', 'confirmed')->count();
        $totalCancelled = Booking::whereDate('updated_at', $today)->where('status', 'cancelled')->count();
        $totalCompleted = Booking::whereDate('updated_at', $today)->where('status', 'completed')->count();
    
        // Pass necessary data to the view (e.g., for the cards)
        return view('admin.bookings.booking_report', [
            'totalBookings' => $totalBookings,
            'totalPending' => $totalPending,
            'totalConfirmed' => $totalConfirmed,
            'totalCancelled' => $totalCancelled,
            'totalCompleted' => $totalCompleted,
        ]);
    }
    

    public function generatePdf(Request $request)
    {
        // Get the filters from the request and ensure default values are provided
        $status = $request->input('status', 'All'); // Default to 'All' if not provided
        $serviceType = $request->input('service_type', 'All'); // Default to 'All' if not provided
        $fromDate = $request->input('date_from');
        $toDate = $request->input('date_to');
    
        // Log the applied filters
        \Log::info('Filters applied', ['status' => $status, 'serviceType' => $serviceType, 'fromDate' => $fromDate, 'toDate' => $toDate]);
    
        // Query the bookings based on the filters
        $query = Booking::query();
    
        // Filter by status if not 'All'
        if ($status !== 'All') {
            \Log::info('Filtering by status', ['status' => $status]);
            $query->where('status', $status);
        }
    
        // Filter by service type if not 'All'
        if ($serviceType !== 'All') {
            \Log::info('Filtering by service type', ['serviceType' => $serviceType]);
            $query->where('service_type', $serviceType);
        }
    
        // Filter by date range (using 'updated_at' as the reference field)
        if ($fromDate && $toDate) {
            \Log::info('Filtering by date range', ['fromDate' => $fromDate, 'toDate' => $toDate]);
            $query->whereBetween('updated_at', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            \Log::info('Filtering by fromDate', ['fromDate' => $fromDate]);
            $query->whereDate('updated_at', '>=', $fromDate);
        } elseif ($toDate) {
            \Log::info('Filtering by toDate', ['toDate' => $toDate]);
            $query->whereDate('updated_at', '<=', $toDate);
        }
    
        // Fetch the bookings with the applied filters
        $bookings = $query->with('invoice')->get();
    
        \Log::info('Bookings found', ['count' => $bookings->count()]);
    
        // If no records are found, return JSON response with error
        if ($bookings->isEmpty()) {
            return response()->json(['error' => 'No records found for the selected criteria.'], 404);
        }
    
        // Generate the PDF if records are found
        try {
            $pdf = PDF::loadView('admin.bookings.booking_report_pdf', [
                'bookings' => $bookings,
                'companyName' => 'SAP Transport and Logistics', // Set dynamically if needed
                'bookingStatus' => $status === 'All' ? 'All Bookings' : ucfirst($status) . ' Bookings',
                'dateFrom' => $fromDate ?? 'N/A',
                'dateTo' => $toDate ?? 'N/A',
                'generatedAt' => now()->format('Y-m-d H:i:s'),
            ])->setPaper('letter', 'landscape');
    
            // Generate the filename with the current timestamp
            $filename = 'booking_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';
    
            \Log::info('PDF generated successfully', ['filename' => $filename]);
    
            // Return the PDF file with the filename in the headers
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF.'], 500);
        }
    }
    
    
    
    public function getReportData($range)
    {
        \Log::info('getReportData called with range: ' . $range); // Log the selected range
    
        try {
            // Initialize booking query
            $bookingsQuery = Booking::query();
    
            // Fetch booking data for the total bookings based on 'created_at'
            if ($range === 'daily') {
                $totalBookings = Booking::whereDate('created_at', today())->count();
                $bookings = $bookingsQuery->whereDate('updated_at', today())->get();
            } elseif ($range === 'weekly') {
                $totalBookings = Booking::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $bookings = $bookingsQuery->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
            } elseif ($range === 'monthly') {
                $totalBookings = Booking::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count();
                $bookings = $bookingsQuery->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)->get();
            } elseif ($range === 'yearly') {
                $totalBookings = Booking::whereYear('created_at', now()->year)->count();
                $bookings = $bookingsQuery->whereYear('updated_at', now()->year)->get();
            } else {
                // Default case if the range is invalid or not provided
                $totalBookings = Booking::count();  // Total of all bookings
                $bookings = $bookingsQuery->get();
            }
    
            \Log::info('Bookings found: ' . $bookings->count()); // Log how many bookings found
    
            // Calculate totals based on status and 'updated_at'
            $totalPending = $bookings->where('status', 'pending')->count();
            $totalConfirmed = $bookings->where('status', 'confirmed')->count();
            $totalCancelled = $bookings->where('status', 'cancelled')->count();
            $totalCompleted = $bookings->where('status', 'completed')->count();
    
            \Log::info('Returning report data'); // Log the success
    
            // Return the data as JSON
            return response()->json([
                'totalBookings' => $totalBookings,
                'totalPending' => $totalPending,
                'totalConfirmed' => $totalConfirmed,
                'totalCancelled' => $totalCancelled,
                'totalCompleted' => $totalCompleted,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getReportData: ' . $e->getMessage()); // Log the error
            return response()->json(['error' => 'Unable to fetch report data'], 500);
        }
    }
    



}
