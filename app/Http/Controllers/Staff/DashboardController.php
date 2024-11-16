<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use App\Models\Booking;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::check() && Auth::user()->hasRole('consultant')) {
            // Fetch recent customer interactions (all open tickets), including the 'id' field
            $recentInteractions = Contact::where('status', 'open')
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get(['id', 'fullname as customer_name', 'updated_at as last_interaction_date', 'status']);

            // Fetch active bookings with 'pending' status, including the 'id' field
            $activeBookings = Booking::where('status', 'pending')
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'booking_reference', 'service_type', 'pickup_date', 'status', 'updated_at as last_updated_by']);

            return view('staff.dashboard', compact('recentInteractions', 'activeBookings'));
        }

        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }

    public function fetchRecentInteractions()
    {
        $recentInteractions = Contact::where('status', 'open')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'fullname as customer_name', 'ticket_num','updated_at as last_interaction_date', 'status']);
    
        return response()->json($recentInteractions);
    }
    
    public function fetchActiveBookings()
    {
        $activeBookings = Booking::where('status', 'pending')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'booking_reference', 'service_type', 'pickup_date', 'status', 'updated_at as last_updated_by']);
    
        return response()->json($activeBookings);
    }
    
        // Fetch booking status distribution for the consultant
        public function getBookingStatusDistribution(Request $request)
        {
            // Determine the timeframe from the request
            $timeframe = $request->input('timeframe', 'today');
            
            // Initialize the query
            $query = Booking::selectRaw('status, COUNT(*) as count')->groupBy('status');
        
            // Apply date filtering based on timeframe
            switch ($timeframe) {
                case 'weekly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'yearly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                    break;
                default: // 'today' or any other value defaults to today's data
                    $query->whereDate('created_at', Carbon::today());
                    break;
            }
        
            // Execute the query and return the data
            $statusCounts = $query->pluck('count', 'status');
            return response()->json($statusCounts);
        }
    
        // Fetch service type usage for the consultant
        public function getServiceTypeUsage(Request $request)
        {
            // Determine the timeframe from the request
            $timeframe = $request->input('timeframe', 'today');
            
            // Initialize the query
            $query = Booking::selectRaw('service_type, COUNT(*) as count')->groupBy('service_type');
        
            // Apply date filtering based on timeframe
            switch ($timeframe) {
                case 'weekly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'yearly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                    break;
                default: // 'today' or any other value defaults to today's data
                    $query->whereDate('created_at', Carbon::today());
                    break;
            }
        
            // Execute the query and return the data
            $serviceTypeCounts = $query->pluck('count', 'service_type');
            return response()->json($serviceTypeCounts);
        }


        public function getTodayDashboardStats()
{
    $today = Carbon::today();

    // Today's Total Bookings
    $totalBookingsToday = Booking::whereDate('created_at', $today)->count();

    // Today's Total Open Tickets
    $totalOpenTickets = Contact::where('status', 'open')->count();

    // Today's Total Canceled Bookings
    $totalCanceledToday = Booking::whereDate('updated_at', $today)
                                 ->where('status', 'cancelled')
                                 ->count();

    return response()->json([
        'totalBookingsToday' => $totalBookingsToday,
        'totalOpenTickets' => $totalOpenTickets,
        'totalCanceledToday' => $totalCanceledToday,
    ]);
}

}
