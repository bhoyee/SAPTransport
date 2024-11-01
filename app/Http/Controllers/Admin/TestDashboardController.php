<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Contact;

class TestDashboardController extends Controller
{
    // Displays initial dashboard view
    public function index()
    {
        Log::info('TestDashboardController@index reached');
    
        // Retrieve data and log each step
        $totalActivePassengers = User::role('passenger')->where('status', 'active')->count();
        Log::info('Total Active Passengers retrieved: ' . $totalActivePassengers);
    
        $totalBookingsToday = Booking::whereDate('created_at', Carbon::today())->count();
        Log::info('Total Bookings Today retrieved: ' . $totalBookingsToday);
    
        $totalSalesToday = Payment::where('status', 'paid')->whereDate('payment_date', Carbon::today())->sum('amount');
        Log::info('Total Sales Today retrieved: ' . $totalSalesToday);
    
        $openTickets = Contact::where('status', 'open')->count();
        Log::info('Total Open Tickets retrieved: ' . $openTickets);
    
        return view('admin.dashboard', compact('totalActivePassengers', 'totalBookingsToday', 'totalSalesToday', 'openTickets'));
    }

    // Endpoint to provide dashboard data in JSON format for AJAX polling for real time
    public function getDashboardData()
    {
        Log::info('Fetching real-time dashboard data via AJAX');

        // Retrieve data for each card and log the retrieval
        $totalActivePassengers = User::role('passenger')->where('status', 'active')->count();
        Log::info('Total Active Passengers (AJAX): ' . $totalActivePassengers);

        $totalBookingsToday = Booking::whereDate('created_at', Carbon::today())->count();
        Log::info('Total Bookings Today (AJAX): ' . $totalBookingsToday);

        $totalSalesToday = Payment::where('status', 'paid')->whereDate('payment_date', Carbon::today())->sum('amount');
        Log::info('Total Sales Today (AJAX): ' . $totalSalesToday);

        $openTickets = Contact::where('status', 'open')->count();
        Log::info('Total Open Tickets (AJAX): ' . $openTickets);

        // Return JSON response for AJAX update
        return response()->json([
            'totalActivePassengers' => $totalActivePassengers,
            'totalBookingsToday' => $totalBookingsToday,
            'totalSalesToday' => $totalSalesToday,
            'openTickets' => $openTickets,
        ]);
    }


    public function getBookingVolumeData(Request $request)
    {
        $timeFrame = $request->input('timeFrame', 'today');
        Log::info("Fetching booking volume data for time frame: $timeFrame");

        $labels = [];
        $data = [];

        if ($timeFrame === 'today') {
            // Define labels for each hour of the day (00:00 to 23:00)
            $labels = [
                '00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', 
                '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', 
                '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', 
                '21:00', '22:00', '23:00'
            ];
            
            $data = Booking::whereDate('created_at', Carbon::today())
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->pluck('count', 'hour')
                ->toArray();

            Log::info('Hourly booking count fetched for today:', $data);

            // Fill in missing hours with zero values
            $data = array_replace(array_fill(0, 24, 0), $data);

        } elseif ($timeFrame === 'weekly') {
            // Define labels for each day of the week
            $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $startOfWeek = Carbon::now()->startOfWeek();
            
            $data = Booking::whereBetween('created_at', [$startOfWeek, Carbon::now()])
                ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
                ->groupBy('day')
                ->pluck('count', 'day')
                ->toArray();

            Log::info('Daily booking count fetched for this week:', $data);

            // Ensure we have all days from 1 (Sun) to 7 (Sat)
            $data = array_replace(array_fill(0, 7, 0), $data);
            // Adjust array to start from Monday
            $data = array_values(array_slice($data, 1, 6, true) + array_slice($data, 0, 1, true));

        } elseif ($timeFrame === 'monthly') {
            // Define labels for each week of the month
            $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            $startOfMonth = Carbon::now()->startOfMonth();
            
            $data = Booking::whereBetween('created_at', [$startOfMonth, Carbon::now()])
                ->selectRaw('WEEK(created_at, 1) - WEEK(DATE_SUB(created_at, INTERVAL DAYOFMONTH(created_at)-1 DAY), 1) + 1 as week, COUNT(*) as count')
                ->groupBy('week')
                ->pluck('count', 'week')
                ->toArray();

            Log::info('Weekly booking count fetched for this month:', $data);

            // Fill in missing weeks with zero values
            $data = array_replace(array_fill(0, 4, 0), $data);

        } elseif ($timeFrame === 'yearly') {
            // Define labels for each month of the year
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            $data = Booking::whereYear('created_at', Carbon::now()->year)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

            Log::info('Monthly booking count fetched for this year:', $data);

            // Fill in missing months with zero values
            $data = array_replace(array_fill(0, 12, 0), $data);
        }

        Log::info('Final booking volume data:', ['labels' => $labels, 'data' => array_values($data)]);
        
        return response()->json([
            'labels' => $labels,
            'data' => array_values($data)  // Ensure only the counts are passed in order
        ]);
    }

    public function getRevenueDistributionData()
    {
        Log::info('getRevenueDistributionData accessed');

        try {
            // Fetch revenue distribution by service type using `bookings.id` for joining
            $revenueDistribution = Booking::join('invoices', 'bookings.id', '=', 'invoices.booking_id')
                ->selectRaw('bookings.service_type, COUNT(bookings.id) as booking_count, SUM(invoices.amount) as total_revenue')
                ->where('invoices.status', 'Paid')
                ->groupBy('bookings.service_type')
                ->get();

            // Prepare chart data with formatted total revenue
            $chartData = [
                'labels' => $revenueDistribution->pluck('service_type'),
                'bookingCounts' => $revenueDistribution->pluck('booking_count'),
                'totalRevenue' => $revenueDistribution->pluck('total_revenue')->map(function ($amount) {
                    return 'NGN ' . number_format($amount, 2);
                })
            ];

            Log::info('Revenue distribution data generated successfully', [
                'labels' => $chartData['labels'],
                'bookingCounts' => $chartData['bookingCounts'],
                'totalRevenue' => $chartData['totalRevenue'],
            ]);

            return response()->json($chartData);

        } catch (\Exception $e) {
            Log::error('Error fetching revenue distribution data:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Server error fetching revenue distribution data'], 500);
        }
    }

    public function getBookingCompletionRateData(Request $request)
    {
        $timeFrame = $request->input('timeFrame', 'today');
        Log::info('Fetching booking completion rate data for time frame:', ['timeFrame' => $timeFrame]);
    
        try {
            $completed = 0;
            $cancelled = 0;
            $confirmed = 0;
    
            switch ($timeFrame) {
                case 'today':
                    $completed = Booking::where('status', 'completed')
                        ->whereDate('updated_at', Carbon::today())->count();
                    $cancelled = Booking::where('status', 'cancelled')
                        ->whereDate('updated_at', Carbon::today())->count();
                    $confirmed = Booking::where('status', 'confirmed')
                        ->whereDate('updated_at', Carbon::today())->count();
                    break;
    
                case 'weekly':
                    $completed = Booking::where('status', 'completed')
                        ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
                    $cancelled = Booking::where('status', 'cancelled')
                        ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
                    $confirmed = Booking::where('status', 'confirmed')
                        ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
                    break;
    
                case 'monthly':
                    $completed = Booking::where('status', 'completed')
                        ->whereMonth('updated_at', Carbon::now()->month)->count();
                    $cancelled = Booking::where('status', 'cancelled')
                        ->whereMonth('updated_at', Carbon::now()->month)->count();
                    $confirmed = Booking::where('status', 'confirmed')
                        ->whereMonth('updated_at', Carbon::now()->month)->count();
                    break;
    
                case 'yearly':
                    $completed = Booking::where('status', 'completed')
                        ->whereYear('updated_at', Carbon::now()->year)->count();
                    $cancelled = Booking::where('status', 'cancelled')
                        ->whereYear('updated_at', Carbon::now()->year)->count();
                    $confirmed = Booking::where('status', 'confirmed')
                        ->whereYear('updated_at', Carbon::now()->year)->count();
                    break;
            }
    
            Log::info('Booking completion rate data generated successfully', [
                'completed' => $completed,
                'cancelled' => $cancelled,
                'confirmed' => $confirmed,
            ]);
    
            return response()->json([
                'completed' => $completed,
                'cancelled' => $cancelled,
                'confirmed' => $confirmed,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching booking completion rate data:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Server error fetching booking completion rate data'], 500);
        }
    }
     
    
    // Fetch recent bookings data for DataTable
    public function getRecentBookingsData()
    {
        Log::info('Fetching recent bookings data');
    
        try {
            // Explicitly select 'id' along with other fields
            $recentBookings = Booking::select('id', 'booking_reference', 'created_at as booking_date', 'service_type', 'created_by', 'status')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
    
            Log::info('Recent bookings data retrieved:', ['data' => $recentBookings->toArray()]);
    
            // Transform the data to format the date
            $recentBookings->transform(function ($booking) {
                $booking->booking_date = Carbon::parse($booking->booking_date)->format('Y-m-d H:i:s');
                $booking->created_by = $booking->creator ? $booking->creator->roles->pluck('name')->first() : 'N/A';

                return $booking;
            });
    
            Log::info('Transformed recent bookings data for response', $recentBookings->toArray());
    
            return response()->json($recentBookings);
        } catch (\Exception $e) {
            Log::error('Error fetching recent bookings data', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch recent bookings data'], 500);
        }
    }
    
  
    // Fetch recent payments data for DataTable
    public function getRecentPaymentsData()
    {
        Log::info('getRecentPaymentsData method called');
    
        try {
            // Retrieve data by joining the bookings and invoices tables with payments
            $recentPayments = Payment::select(
                    'bookings.booking_reference',  // Select booking_reference from bookings table
                    'invoices.invoice_number',
                    'payments.amount',
                    'payments.payment_date as invoice_date',
                    'payments.status'
                )
                ->join('bookings', 'payments.booking_id', '=', 'bookings.id') // Join with bookings table
                ->join('invoices', 'bookings.id', '=', 'invoices.booking_id') // Join with invoices table
                ->orderBy('payments.updated_at', 'desc')
                ->limit(6)
                ->get();
    
            Log::info('Recent payments data retrieved', ['count' => $recentPayments->count()]);
    
            // Transform the data if necessary
            $recentPayments->transform(function ($payment) {
                $payment->invoice_date = Carbon::parse($payment->invoice_date)->format('Y-m-d H:i:s');
                return $payment;
            });
    
            Log::info('Transformed recent payments data', $recentPayments->toArray());
            return response()->json($recentPayments);
        } catch (\Exception $e) {
            Log::error('Error in getRecentPaymentsData', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch recent payments data'], 500);
        }
    }
    
    
}
