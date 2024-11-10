<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDelete;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\Notification;
use App\Mail\UserDeletedMail;
use App\Services\ActivityLogger;


class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            \Log::info('AJAX request received for user data.');
    
            // Filter users based on role
            if (auth()->user()->hasRole('consultant')) {
                // If the user is a consultant, only fetch users with the 'passenger' role
                $users = User::role('passenger')
                    ->select('id', 'name', 'email', 'phone', 'status', 'created_at')
                    ->where('status', '!=', 'deleted')
                    ->get();
            } else {
                // If the user is an admin, fetch all users excluding those with 'deleted' status
                $users = User::select('id', 'name', 'email', 'phone', 'status', 'created_at')
                    ->where('status', '!=', 'deleted')
                    ->get();
            }
    
            \Log::info('Users fetched:', ['users' => $users->toArray()]);
    
            // Format the data for DataTables
            $formattedUsers = $users->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'actions' => '<a href="' . route('admin.users.show', $user->id) . '" class="btn btn-primary btn-sm">View</a>' .
                                 '<button class="btn btn-danger btn-sm" data-user-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>'
                ];
            });
    
            \Log::info('Formatted users for DataTables:', ['formattedUsers' => $formattedUsers]);
    
            return response()->json(['data' => $formattedUsers]);
        }
    
        // Fetch statistics for the cards
        $totalPassengers = User::role('passenger')->where('status', 'active')->count();
        $inactivePassengers = User::role('passenger')->where('status', 'inactive')->count();
        $totalStaff = User::role('consultant')->where('status', 'active')->count();
        $suspendedUsers = User::where('status', 'suspend')->count();
    
        // Render the view with all required variables
        return view('admin.users.index', compact('totalPassengers', 'inactivePassengers', 'totalStaff', 'suspendedUsers'));
    }
    
    
    public function show(User $user)
    {
        // Fetch the user details and activity logs
        $userDetails = $user;
        $userActivities = \DB::table('activity_logs')
            ->where('user_id', $user->id)
            ->select('action', 'description', 'ip_address', 'created_at', 'updated_at')
            ->get();
    
        // Fetch the user's bookings and invoice details
        $userBookings = \DB::table('bookings')
            ->leftJoin('invoices', 'bookings.id', '=', 'invoices.booking_id')
            ->where('bookings.user_id', $user->id)
            ->select(
                'bookings.booking_reference',
                'bookings.created_at as booking_date',
                'bookings.status as booking_status',
                'invoices.invoice_number',
                'invoices.amount as invoice_amount',
                'invoices.status as invoice_status'
            )
            ->get();
    
        return view('admin.users.show', compact('userDetails', 'userActivities', 'userBookings'));
    }
    

    public function edit(User $user)
    {
        // Only allow admins to edit users
        if (!Auth::user()->hasRole(['admin', 'consultant'])) {
            return redirect('/')->with('error', 'Unauthorized access');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Define validation rules for all fields that should be updated
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'gender' => 'required|in:male,female',  // Add gender to validation
            'status' => 'required|in:active,inactive,suspend', // Add status to validation
        ];
    
        // Only require 'role' if the authenticated user is not a consultant
        if (!auth()->user()->hasRole('consultant')) {
            $rules['role'] = 'required|in:passenger,consultant,admin';
        }
    
        // Validate the request data
        $validatedData = $request->validate($rules);
    
        try {
            // Update user details using the validated data
            $user->update($validatedData);
    
            // Sync roles only if the role field is included in the validated data
            if (isset($validatedData['role']) && !auth()->user()->hasRole('consultant')) {
                $user->syncRoles([$validatedData['role']]);
            }
    
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the user.');
        }
    }
    
    public function delete(Request $request)
    {
        \Log::info('Delete request received. User ID:', ['user_id' => $request->user_id]);
    
        // Default response setup
        $response = ['success' => false, 'message' => 'User not found.'];
        $statusCode = 404;
    
        // Validate request
        if (!$request->has('user_id')) {
            \Log::warning('No user_id provided in request.');
            $response = ['success' => false, 'message' => 'User ID is required.'];
            $statusCode = 400;
        } else {
            $user = User::find($request->user_id);
    
            if ($user) {
                try {
                    \Log::info('User found. Proceeding with deletion.', ['user_id' => $user->id, 'user_email' => $user->email]);
    
                    $deletedBy = Auth::user();
    
                    // Log the deletion in the user_deletes table
                    UserDelete::create([
                        'user_id' => $user->id,
                        'deleted_by' => $deletedBy->email ?? 'unknown',
                        'deleted_at' => now(),
                    ]);
    
                    // Update the user's status to 'deleted'
                    $user->status = "deleted";
                    $user->save();
    
                    // Activity Logging for the logged-in user
                    ActivityLogger::log(
                        'User Deleted',
                        'User ' . $user->name . ' was deleted by ' . $deletedBy->name . ' on ' . now()
                    );
    
                    // Prepare immediate success response
                    $response = ['success' => true, 'message' => 'User deleted successfully.'];
                    $statusCode = 200;
    
                    // Notify all admins about the deletion
                    $adminUsers = User::role(['admin'])->get();
                    foreach ($adminUsers as $admin) {
                        Notification::create([
                            'user_id' => $admin->id,
                            'message' => 'User ' . $user->name . ' was deleted by ' . $deletedBy->name,
                            'type' => 'message',
                            'status' => 'unread',
                            'related_user_name' => $deletedBy->name,
                        ]);
                        \Log::info("Notification sent to admin ID: {$admin->id}");
                    }
    
                    // Send email to the admin about the deletion
                    try {
                        $adminEmail = config('mail.admin_email'); // Get admin email from config
                        Mail::to($adminEmail)->send(new UserDeletedMail($user, $deletedBy));
                        \Log::info("Deletion email sent to admin: {$adminEmail}");
                    } catch (\Exception $e) {
                        \Log::error('Failed to send deletion email to admin: ' . $e->getMessage());
                    }
    
                } catch (\Exception $e) {
                    \Log::error('Error during user deletion:', ['error' => $e->getMessage(), 'user_id' => $user->id]);
                    $response = ['success' => false, 'message' => 'An error occurred while deleting the user.'];
                    $statusCode = 500;
                }
            }
        }
    
        return response()->json($response, $statusCode);
    }
    


    public function suspend(User $user)
    {
        try {
            // Toggle suspend/active/inactive status
            if ($user->status === 'active' || $user->status === 'inactive') {
                $user->status = 'suspend';
            } elseif ($user->status === 'suspend') {
                $user->status = 'active';
            }

            $user->save();

            return redirect()->route('admin.users.index')->with('success', 'User status updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error suspending user: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'An error occurred while updating the user status.');
        }
    }

    //user creste page
    public function create()
    {
        \Log::info(Auth::user()->roles);
        // Allow only admins and consultants to create new users
        if (!Auth::user()->hasAnyRole(['admin', 'consultant'])) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }
    
        return view('admin.create');
    }
    
    public function store(Request $request)
    {
        // Ensure only admins or consultants can store new users
        if (!Auth::user()->hasAnyRole(['admin', 'consultant'])) {
            return redirect('/')->with('error', 'Unauthorized access');
        }
    
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:passenger,consultant,admin',
            'gender' => 'required|in:male,female',
        ]);
    
        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->with('error', 'User with this email already exists.');
        }
    
        try {
            $status = ($request->role === 'passenger') ? 'inactive' : 'active';
            $createdBy = Auth::user()->email;
    
            // Generate a random password
            $generatedPassword = Str::random(10);
    
            // Create the new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($generatedPassword),
                'gender' => $request->gender,
                'created_by' => $createdBy,
                'status' => $status,
            ]);
    
            // Assign role
            $user->assignRole($request->role);
    
            // Dispatch job for sending email in the background
            dispatch(new \App\Jobs\SendUserCreationEmail($user, $generatedPassword));
    
            // Success message
            return redirect()->route('admin.users.create')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            \Log::error('User creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the user.');
        }
    }
    
    // admin delete user page 


    public function showDeletedUsers()
    {
        Log::info('Fetching total number of temporarily deleted users.');
    
        try {
            // Count deleted users from the user_deletes table
            $totalDeletedUsers = UserDelete::count();
            Log::info('Total deleted users fetched successfully.', ['totalDeletedUsers' => $totalDeletedUsers]);
    
            // Pass the count to the view
            return view('admin.users.deleted-users', compact('totalDeletedUsers'));
        } catch (\Exception $e) {
            Log::error('Error fetching total deleted users.', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Unable to fetch deleted users at this time.');
        }
    }
    
    

    public function fetchDeletedStats()
    {
        Log::info('Fetching real-time total of deleted users.');
    
        try {
            // Count deleted users from the user_deletes table
            $totalDeletedUsers = UserDelete::count();
            Log::info('Real-time total deleted users fetched successfully.', ['totalDeletedUsers' => $totalDeletedUsers]);
    
            return response()->json(['totalDeletedUsers' => $totalDeletedUsers]);
        } catch (\Exception $e) {
            Log::error('Error fetching real-time deleted user stats.', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching real-time stats.'], 500);
        }
    }
    
    public function getDeletedUsers()
    {
        Log::info('Fetching list of temporarily deleted users from user_deletes table.');
    
        try {
            $deletedUsers = \DB::table('user_deletes')
                ->leftJoin('users', 'user_deletes.user_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.status',
                    'users.created_by',
                    'user_deletes.deleted_by',
                    'user_deletes.deleted_at'
                )
                ->orderBy('user_deletes.deleted_at', 'desc')
                ->get();
    
            // Format data for DataTables
            $formattedUsers = $deletedUsers->map(function ($user) {
                $creator = User::where('email', $user->created_by)->first();
                $deleter = User::where('email', $user->deleted_by)->first();
    
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => ucfirst($user->status),
                    'created_by' => $creator ? $creator->name : 'N/A',
                    'deleted_by' => $deleter ? $deleter->name : 'N/A',
                ];
            });
    
            Log::info('Formatted deleted users data for DataTable.', ['totalDeletedUsers' => count($formattedUsers)]);
    
            return response()->json(['data' => $formattedUsers]);
        } catch (\Exception $e) {
            Log::error('Error fetching deleted users list from user_deletes table.', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching deleted users list.'], 500);
        }
    }
    
    

    public function permanentDelete(Request $request)
    {
        Log::info('Attempting to permanently delete user.', ['user_id' => $request->user_id]);

        try {
            $user = User::find($request->user_id);

            if ($user) {
                $user->delete();
                Log::info('User permanently deleted successfully.', ['user_id' => $user->id]);

                return response()->json(['success' => true]);
            } else {
                Log::warning('User not found for permanent deletion.', ['user_id' => $request->user_id]);
                return response()->json(['success' => false, 'message' => 'User not found.']);
            }
        } catch (\Exception $e) {
            Log::error('Error permanently deleting user.', ['error' => $e->getMessage(), 'user_id' => $request->user_id]);
            return response()->json(['success' => false, 'message' => 'Error deleting user.'], 500);
        }
    }

    public function restore(Request $request)
    {
        $userId = $request->input('user_id');
        \Log::info("Attempting to restore user with ID:", ['user_id' => $userId]);
    
        // Find the user in the users table
        $user = User::find($userId);
    
        if ($user && $user->status === 'deleted') {
            try {
                // Update user status back to active
                $user->status = 'active';
                $user->save();
    
                // Optional: Clean up the user_deletes table if needed
                UserDelete::where('user_id', $userId)->delete();
    
                \Log::info("User restored successfully", ['user_id' => $userId]);
    
                return response()->json(['success' => true, 'message' => 'User restored successfully.']);
            } catch (\Exception $e) {
                \Log::error('Error restoring user: ' . $e->getMessage(), ['user_id' => $userId]);
                return response()->json(['success' => false, 'message' => 'An error occurred while restoring the user.'], 500);
            }
        }
    
        return response()->json(['success' => false, 'message' => 'User not found or already active.'], 404);
    }
    

}
