<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\ActivityLogger;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        Log::info('Registration request initiated', [
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        // Check if the user already exists based on email
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            Log::info('User with this email already exists', ['email' => $request->email]);
            
            // Log the duplicate registration attempt
            ActivityLogger::log('Registration Failed', 'A user with this email address already exists: ' . $request->email);

            // Flash a session error message and redirect back
            return redirect()->back()->with('error', 'A user with this email address already exists.');
        }

        // Validate the form data
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'required|string|max:11',
                'gender' => 'required|string|in:male,female',
            ]);
        } catch (ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors()]);
            
            // Log the validation error
            ActivityLogger::log('Registration Validation Failed', json_encode($e->errors()));

            // Flash validation errors and redirect back
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        try {
            // Create a new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => 'inactive',
                'created_by' => $request->email,  // Set created_by to the user's own email
            ]);

            Log::info('User registered successfully', ['email' => $user->email]);
            
            // Log the successful registration
            ActivityLogger::log('Registration Success', 'User registered: ' . $user->email);

            // Assign a default role (you can modify the role name as needed)
            $user->assignRole('passenger');  // Assuming 'passenger' is the default role

            // Send the email verification link
            $user->sendEmailVerificationNotification();
            Log::info('Verification email sent to user', ['email' => $user->email]);

            // Log email verification notification
            ActivityLogger::log('Email Verification Sent', 'Verification email sent to: ' . $user->email);

            // Flash a success message and redirect to a "thank you" page
            return redirect()->route('register.thankyou')->with('success', "Registration successful. Confirm your email address to activate your account, don't forget to check SPAM folder too.");

        } catch (\Exception $e) {
            // Log the error
            Log::error('Error during registration or email sending', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);

            // Log the registration failure
            ActivityLogger::log('Registration Failed', 'Error during registration for email: ' . $request->email);

            // Flash an error message and redirect back
            return redirect()->back()->with('error', 'Something went wrong during registration. Please try again.');
        }
    }

    // Thank you page after successful registration
    public function thankyou()
    {
        return view('auth.thankyou');
    }
}
