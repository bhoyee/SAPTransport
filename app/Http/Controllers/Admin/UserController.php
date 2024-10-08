<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // Add this line to import the Auth facade
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
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
            // Create the new user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password), // Hash the password
                'role' => $request->role,
                'gender' => $request->gender,  // Save gender
                'created_by' => $createdBy,  // Automatically store the creator's email
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
