<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use PDF; // Assuming you're using dompdf
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserReportController extends Controller
{
    public function index()
    {
        // Check if the authenticated user has the 'admin' role using Spatie's role-based system
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            \Log::info('Loading view: admin.users.report');
            return view('admin.users.report');
        }

        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
 
    public function generatePDF(Request $request)
    {
        // Validate the request
        $request->validate([
            'role' => 'nullable|in:passenger,consultant',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'email_verified' => 'nullable|in:verified,not_verified',
        ]);
    
        // Initialize the query and exclude users with 'deleted' status
        $query = User::where('status', '!=', 'deleted');
    
        // Role filter using Spatie's roles
        if (!empty($request->role)) {
            $query->role($request->role); // Use Spatie's role method instead of direct column access
        }
    
        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }
    
        // Email verified filter
        if ($request->email_verified === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($request->email_verified === 'not_verified') {
            $query->whereNull('email_verified_at');
        }
    
        // Fetch the filtered users
        $users = $query->get();
    
        // Check if no records were found
        if ($users->isEmpty()) {
            return response()->json(['error' => 'No records found'], 404);
        }
    
        // Prepare the role display name
        if (empty($request->role)) {
            $roleDisplayName = 'All'; // Show 'All' if no specific role is selected
        } else {
            $roleDisplayName = $request->role === 'consultant' ? 'Staff' : 'Passenger';
        }
    
        // Generate the PDF with pagination support
        $pdf = PDF::loadView('admin.users.report_pdf', [
            'users' => $users,
            'roleDisplayName' => $roleDisplayName,
            'companyName' => 'SAP Transport and Logistics',
            'logoPath' => public_path('assets/images/logo_old.png'),
            'generatedAt' => Carbon::now()->format('Y-m-d H:i:s'),
            'dateFrom' => $request->date_from ?? 'N/A',
            'dateTo' => $request->date_to ?? 'N/A',
        ])->setPaper('letter', 'landscape');
    
        // Add dynamic filename with current date and time
        $fileName = 'user_report_' . Carbon::now()->format('Ymd_His') . '.pdf';
    
        // Return the PDF file for download
        return response()->streamDownload(
            fn () => print($pdf->output()), 
            $fileName,
            ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="' . $fileName . '"']
        );
    }

        public function showReportPage()
        {
            // Query for statistics
            $totalUsers = User::where('status', '!=', 'deleted')->count();
            $totalPassengers = User::role('passenger')->where('status', '!=', 'deleted')->count();
            $totalStaff = User::role('consultant')->where('status', '!=', 'deleted')->count();
            $unverifiedUsers = User::where('status', '!=', 'deleted')
                ->whereNull('email_verified_at')
                ->whereDoesntHave('roles', function($query) {
                    $query->whereIn('name', ['consultant', 'admin']);
                })
                ->count();
        
            // Return the view with the initial data
            return view('admin.users.report', compact('totalUsers', 'totalPassengers', 'totalStaff', 'unverifiedUsers'));
        }
        
        // This method is called via AJAX to fetch updated stats in real time
        public function fetchStats()
        {
            try {
                // Log the stats fetching process
                Log::info('Fetching real-time user statistics.');
        
                // Query for updated statistics
                $totalUsers = User::where('status', '!=', 'deleted')->count();
                $totalPassengers = User::role('passenger')->where('status', '!=', 'deleted')->count();
                $totalStaff = User::role('consultant')->where('status', '!=', 'deleted')->count();
                $unverifiedUsers = User::where('status', '!=', 'deleted')
                    ->whereNull('email_verified_at')
                    ->whereDoesntHave('roles', function($query) {
                        $query->whereIn('name', ['consultant', 'admin']);
                    })
                    ->count();
        
                // Return JSON response
                return response()->json([
                    'totalUsers' => $totalUsers,
                    'totalPassengers' => $totalPassengers,
                    'totalStaff' => $totalStaff,
                    'unverifiedUsers' => $unverifiedUsers
                ]);
            } catch (\Exception $e) {
                // Log the error and return an error response
                Log::error('Error fetching real-time user statistics', ['error' => $e->getMessage()]);
        
                return response()->json([
                    'success' => false,
                    'message' => 'Error fetching user statistics.'
                ], 500);
            }
        }
        

}
