<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use PDF; // Assuming you're using dompdf
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class UserReportController extends Controller
{
    public function index()
    {
        // Check if the authenticated user has the 'admin' role
        if (Auth::check() && Auth::user()->role === 'admin') {
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
    
        // Role filter
        if (!empty($request->role)) {
            $query->where('role', $request->role);
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
        $totalUsers = User::where('status', '!=', 'deleted')->count();
        $totalPassengers = User::where('role', 'passenger')->where('status', '!=', 'deleted')->count();
        $totalStaff = User::where('role', '!=', 'passenger')->where('status', '!=', 'deleted')->count();
            // Total unverified users excluding 'consultant' and 'admin' roles
        $unverifiedUsers = User::where('status', '!=', 'deleted')
        ->whereNull('email_verified_at')
        ->whereNotIn('role', ['consultant', 'admin'])
        ->count();

        return view('admin.users.report', compact('totalUsers', 'totalPassengers', 'totalStaff', 'unverifiedUsers'));
    }
        
    
    }