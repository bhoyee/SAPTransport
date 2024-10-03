<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use Carbon\Carbon;

class PassengerHomeController extends Controller
{
    // Fetch recent bookings
    public function getRecentBookings()
    {
        try {
            $bookings = Booking::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->take(4) // Limit the number of recent bookings fetched
                ->get();
    
            foreach ($bookings as $booking) {
                if ($booking->status === 'pending' && $booking->pickup_date < now()->toDateString()) {
                    $booking->status = 'expired';
                    $booking->save();
                }
            }
    
            return response()->json($bookings);
        } catch (\Exception $e) {
            Log::error('Error fetching recent bookings: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching recent bookings.'], 500);
        }
    }

    // Fetch payment history
    public function getPaymentHistory()
    {
        try {
            $userId = Auth::id();
    
            $invoices = Invoice::with('booking')
                ->whereHas('booking', function($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->orderBy('invoice_date', 'desc')
                ->limit(4)
                ->get();
    
            Log::info('Fetched invoices:', $invoices->toArray());
    
            return response()->json($invoices);
        } catch (\Exception $e) {
            Log::error('Error fetching payment history: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch payment history'], 500);
        }
    }

    // Fetch dashboard data
    public function fetchDashboardData()
    {
        $userId = Auth::id();
    
        $totalTrips = Booking::where('user_id', $userId)->count() ?? 0;
        $cancelledTrips = Booking::where('user_id', $userId)
            ->where('status', 'cancelled')
            ->count() ?? 0;
        $upcomingTrips = Booking::where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('pickup_date', '>=', now()->startOfDay())
            ->count() ?? 0;
        $totalAmountPaid = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->sum('amount') ?? 0;
    
        return response()->json([
            'totalTrips' => $totalTrips,
            'cancelledTrips' => $cancelledTrips,
            'upcomingTrips' => $upcomingTrips,
            'totalAmountPaid' => $totalAmountPaid,
        ]);
    }

    // Fetch chart data
    public function getChartData(Request $request)
    {
        $filter = $request->get('filter', 'week');
        $userId = Auth::id();
    
        $labels = [];
        $completedBookings = [];
        $cancelledBookings = [];
    
        if ($filter === 'week') {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    
            foreach (range(0, 6) as $day) {
                $currentDay = $startOfWeek->copy()->addDays($day);
                $completedCount = Booking::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->whereDate('pickup_date', $currentDay->toDateString())
                    ->count();
                $cancelledCount = Booking::where('user_id', $userId)
                    ->where('status', 'cancelled')
                    ->whereDate('pickup_date', $currentDay->toDateString())
                    ->count();
                $completedBookings[] = $completedCount;
                $cancelledBookings[] = $cancelledCount;
            }
        } elseif ($filter === 'month') {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
    
            foreach (range(0, 3) as $week) {
                $startOfWeek = $startOfMonth->copy()->addWeeks($week)->startOfWeek();
                $endOfWeek = $startOfWeek->copy()->endOfWeek();
                $completedCount = Booking::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->whereBetween('pickup_date', [$startOfWeek, $endOfWeek])
                    ->count();
                $cancelledCount = Booking::where('user_id', $userId)
                    ->where('status', 'cancelled')
                    ->whereBetween('pickup_date', [$startOfWeek, $endOfWeek])
                    ->count();
                $completedBookings[] = $completedCount;
                $cancelledBookings[] = $cancelledCount;
            }
        } elseif ($filter === 'year') {
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
            foreach (range(1, 12) as $month) {
                $startOfMonth = Carbon::createFromDate(null, $month, 1)->startOfMonth();
                $endOfMonth = $startOfMonth->copy()->endOfMonth();
                $completedCount = Booking::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->whereBetween('pickup_date', [$startOfMonth, $endOfMonth])
                    ->count();
                $cancelledCount = Booking::where('user_id', $userId)
                    ->where('status', 'cancelled')
                    ->whereBetween('pickup_date', [$startOfMonth, $endOfMonth])
                    ->count();
                $completedBookings[] = $completedCount;
                $cancelledBookings[] = $cancelledCount;
            }
        }
    
        return response()->json([
            'labels' => $labels,
            'completedBookings' => $completedBookings,
            'cancelledBookings' => $cancelledBookings
        ]);
    }

    // Fetch user activities with pagination
    public function getUserActivities(Request $request)
    {
        $userId = Auth::id();
        $page = $request->get('page', 1);
        $perPage = 5;

        $activities = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $totalActivities = ActivityLog::where('user_id', $userId)->count();
        $hasNextPage = ($totalActivities > ($page * $perPage));

        return response()->json([
            'activities' => $activities,
            'page' => $page,
            'hasNextPage' => $hasNextPage
        ]);
    }
}
