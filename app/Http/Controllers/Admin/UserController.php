<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // Add this line to import the Auth facade
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDelete; // Import the UserDelete model


class UserController extends Controller
{
    public function index()
    {
        // Fetch active passengers, consultants, and suspended users
        $activePassengers = User::where('role', 'passenger')
            ->where('status', 'active')
            ->count();
    
        $activeConsultants = User::where('role', 'consultant')
            ->where('status', 'active')
            ->count();
    
        $suspendedUsers = User::where('status', 'suspend')->count();
    
        // Fetch all users for the DataTable, excluding those with status 'deleted'
        $users = User::select('id', 'name', 'email', 'phone', 'role', 'status', 'created_at')
            ->where('status', '!=', 'deleted')  // Exclude users with 'deleted' status
            ->get();
    
        return view('admin.users.index', compact('activePassengers', 'activeConsultants', 'suspendedUsers', 'users'));
    }
    

    public function show(User $user)
    {
        // Fetch the user details
        $userDetails = $user;

        // Fetch the user's activity logs from the activity_logs table
        $userActivities = \DB::table('activity_logs')
            ->where('user_id', $user->id)
            ->select('action', 'description', 'ip_address', 'created_at', 'updated_at')
            ->get();

        return view('admin.users.show', compact('userDetails', 'userActivities'));
    }

    public function edit(User $user)
    {
        // Check if the user is an admin
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return redirect('/')->with('error', 'Unauthorized access');
        }

        // Show the edit form for the specific user
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
            // Update the user details
            $user->update($request->all());

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the user.');
        }
    }

    
    // public function destroy(User $user)
    // {
    //     try {
    //         $user->delete();
    //         return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    //     } catch (\Exception $e) {
    //         \Log::error('Error deleting user: ' . $e->getMessage());
    //         return redirect()->route('admin.users.index')->with('error', 'An error occurred while deleting the user.');
    //     }
    // }

    public function delete(Request $request)
    {
        $user = User::find($request->user_id);
    
        if ($user) {
            try {
                // Log the deletion in the user_deletes table
                UserDelete::create([
                    'user_id' => $user->id, // ID of the deleted user
                    'deleted_by' => Auth::user()->email, // Email of the admin/consultant who deleted the user
                    'deleted_at' => now(), // Current timestamp
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
    

    //
    public function create()
    {
        // Role check: Allow only admin users to create new users
        if (Auth::check() && Auth::user()->role === 'admin') {
            return view('admin.create');
        }

        // Redirect back with an unauthorized error
        return redirect('/login')->with('error', 'Unauthorized access');
    }

    public function store(Request $request)
    {
        // Role check: Ensure only admin users can store new users
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return redirect('/')->with('error', 'Unauthorized access');
        }
    
        // Validate the form data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:passenger,consultant,admin',
            'gender' => 'required|in:male,female',  // Add gender validation
        ]);
    
        // Check if the user with the same email already exists
        $existingUser = User::where('email', $request->email)->first();
    
        if ($existingUser) {
            return redirect()->back()->with('error', 'User with this email already exists.');
        }
    
        // Determine the creator: If admin or consultant is logged in, use their email; otherwise, use the user's own email
        $createdBy = Auth::check() ? Auth::user()->email : $request->email;
    
        // Log the captured email of the creator for debugging purposes
        \Log::info('Creating user with the following details: ', [
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,  // Log gender as well
            'created_by' => $createdBy,  // Log the creator's email
        ]);
    
        try {
            // Set the status based on the role
            $status = ($request->role === 'passenger') ? 'inactive' : 'active';
    
            // Create the new user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password), // Hash the password
                'role' => $request->role,
                'gender' => $request->gender,  // Save gender
                'created_by' => $createdBy,  // Automatically store the creator's email
                'status' => $status, // Set the status based on the role
            ]);
    
            // Log the successful creation of the user
            \Log::info('User created successfully by: ' . $createdBy);
    
            return redirect()->route('admin.users.create')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User creation failed: ' . $e->getMessage());
    
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the user.');
        }
    }
    
}
