<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;





class PassengerHomeController extends Controller
{


    public function getRecentBookings()
    {
        try {
            // Fetch recent bookings for the logged-in user
            $bookings = Booking::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->take(4) // Limit the number of recent bookings fetched
                ->get();
    
            // Check if any bookings need to be marked as expired
            foreach ($bookings as $booking) {
                if ($booking->status === 'pending' && $booking->pickup_date < now()->toDateString()) {
                    // Update the booking status to 'expired' if pickup_date is in the past
                    $booking->status = 'expired';
                    $booking->save();
                }
            }
    
            return response()->json($bookings);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error fetching recent bookings: ' . $e->getMessage());
            
            // Return a 500 response with error message
            return response()->json(['error' => 'An error occurred while fetching recent bookings.'], 500);
        }
    }

    public function getPaymentHistory()
    {
        try {
            $userId = Auth::id();
    
            // Fetch the last 4 'paid' invoices with the associated booking for the logged-in user
            $invoices = Invoice::with('booking')
                ->whereHas('booking', function($query) use ($userId) {
                    $query->where('user_id', $userId);  // Filter by the user's bookings
                })
                ->orderBy('invoice_date', 'desc')
                ->limit(4)  // Limit the results to 4
                ->get();
    
            // Log the invoices for debugging
            Log::info('Fetched invoices:', $invoices->toArray());
    
            return response()->json($invoices);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error fetching payment history: ' . $e->getMessage());
    
            return response()->json(['error' => 'Unable to fetch payment history'], 500);
        }
    }
    


        
    public function fetchDashboardData()
    {
        $userId = Auth::id();
    
        // Fetch totals, and if no data is found, it will return 0
        $totalTrips = Booking::where('user_id', $userId)->count() ?? 0;
        
        $cancelledTrips = Booking::where('user_id', $userId)
                                 ->where('status', 'cancelled')
                                 ->count() ?? 0;
        
        $upcomingTrips = Booking::where('user_id', $userId)
                                ->whereIn('status', ['pending', 'confirmed'])  // Only Pending or Confirmed
                                ->where('pickup_date', '>=', now()->startOfDay())  // Pickup date today or in the future
                                ->count() ?? 0;
    
        // Calculate the total amount the user has paid (excluding unpaid or refunded)
        $totalAmountPaid = Payment::where('user_id', $userId)
                                  ->where('status', 'paid')  // Only consider 'paid' payments
                                  ->sum('amount') ?? 0;
    
        // Return JSON with default 0 values if no data is returned
        return response()->json([
            'totalTrips' => $totalTrips,
            'cancelledTrips' => $cancelledTrips,
            'upcomingTrips' => $upcomingTrips,
            'totalAmountPaid' => $totalAmountPaid,  // Include total paid amount
        ]);
    }
    
        
 }