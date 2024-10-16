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


class UserController extends Controller
{
    public function index()
    {
        // Fetch active passengers, consultants, and suspended users using Spatie's role system
        $activePassengers = User::role('passenger')
            ->where('status', 'active')
            ->count();
    
        $activeConsultants = User::role('consultant')
            ->where('status', 'active')
            ->count();
    
        $suspendedUsers = User::where('status', 'suspend')->count();
    
        // Fetch all users for the DataTable, excluding those with status 'deleted'
        $users = User::select('id', 'name', 'email', 'phone', 'status', 'created_at')
            ->where('status', '!=', 'deleted')  // Exclude users with 'deleted' status
            ->get();
    
        return view('admin.users.index', compact('activePassengers', 'activeConsultants', 'suspendedUsers', 'users'));
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
        if (!Auth::user()->hasRole('admin')) {
            return redirect('/')->with('error', 'Unauthorized access');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:passenger,consultant,admin',
        ]);

        try {
            // Update the user details and sync roles
            $user->update($request->all());
            $user->syncRoles([$request->role]);

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the user.');
        }
    }

    public function delete(Request $request)
    {
        $user = User::find($request->user_id);

        if ($user) {
            try {
                // Log the deletion in the user_deletes table
                UserDelete::create([
                    'user_id' => $user->id,
                    'deleted_by' => Auth::user()->email,
                    'deleted_at' => now(),
                ]);

                // Update the user's status to 'deleted'
                $user->status = 'deleted';
                $user->save();

                return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
            } catch (\Exception $e) {
                \Log::error('Error deleting user: ' . $e->getMessage());
                return redirect()->route('admin.users.index')->with('error', 'An error occurred while deleting the user.');
            }
        }

        return redirect()->route('admin.users.index')->with('error', 'User not found.');
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

    public function create()
    {
        // Allow only admins to create new users
        if (!Auth::user()->hasRole('admin')) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        return view('admin.create');
    }

    public function store(Request $request)
    {
        // Ensure only admins can store new users
        if (!Auth::user()->hasRole('admin')) {
            return redirect('/')->with('error', 'Unauthorized access');
        }

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
            $generatedPassword = Str::random(10); // You can adjust the length as needed


            // Create the new user and assign roles using Spatie
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($generatedPassword), // Hash the generated password
                'gender' => $request->gender,
                'created_by' => $createdBy,
                'status' => $status,
            ]);

            $user->assignRole($request->role);

            // Send the email with login credentials
            Mail::to($user->email)->send(new \App\Mail\UserCreatedNotification($user, $generatedPassword));

             // If the user role is 'passenger', send the email verification link
            if ($request->role === 'passenger') {
                $user->sendEmailVerificationNotification();
            }

            return redirect()->route('admin.users.create')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            \Log::error('User creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the user.');
        }
    }
}
