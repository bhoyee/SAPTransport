<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;




class PassengerHomeController extends Controller
{


        public function getRecentBookings()
        {
            try {
                // Fetch recent bookings for the logged-in user
                $bookings = Booking::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->take(10) // Limit the number of recent bookings fetched
                    ->get();

                return response()->json($bookings);
            } catch (\Exception $e) {
                // Log the error
                \Log::error('Error fetching recent bookings: ' . $e->getMessage());
                
                // Return a 500 response with error message
                return response()->json(['error' => 'An error occurred while fetching recent bookings.'], 500);
            }
        }

        public function fetchDashboardData()
        {
            $userId = Auth::id();
        
            // Fetch totals, and if no data is found, it will return 0
            $totalTrips = Booking::where('user_id', $userId)->count() ?? 0;
            $cancelledTrips = Booking::where('user_id', $userId)
                                     ->where('status', 'Cancelled')
                                     ->count() ?? 0;
            $upcomingTrips = Booking::where('user_id', $userId)
                                    ->whereIn('status', ['pending', 'confirmed'])  // Only Pending or Confirmed
                                    ->where('pickup_date', '>=', now())            // Pickup date is in the future or today
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