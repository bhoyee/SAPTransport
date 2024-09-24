<?php

// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Add this line
use Illuminate\Validation\ValidationException;
use App\Services\ActivityLogger;


class LoginController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle the login request
    public function login(Request $request)
    {
        // Log the login attempt
        Log::info('Login attempt initiated', ['email' => $request->email]);

        // Validate the login credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        Log::info('Login validation passed');

        // Attempt to log the user in
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            // Log success
            Log::info('User authenticated', ['email' => $request->email]);

            // Check if the user has verified their email
            if (Auth::user()->email_verified_at === null) {
                Log::info('Redirecting to verification.notice', ['email' => Auth::user()->email]);
                
                     // Log unverified email login attempt
            ActivityLogger::log('Login Attempt', 'Email not verified: ' . $request->email);


                return redirect()->route('verification.notice')->with('error', 'You need to verify your email before logging in.');
            }

            // Log verified email
            Log::info('User email verified', ['email' => Auth::user()->email]);
            
                    // Log successful login activity
        ActivityLogger::log('Login Success', 'User logged in: ' . $request->email);

            // Set the session 'is_locked' to false after successful login
            session()->put('is_locked', false);

            // Redirect the user to the passenger dashboard if email is verified
            return redirect()->route('passenger.dashboard');
        }

        // Log failed login attempt
        Log::info('Login attempt failed', ['email' => $request->email]);
        
          // Log failed login activity
    ActivityLogger::log('Login Failed', 'Invalid credentials: ' . $request->email);

        // If login attempt fails, throw validation error
        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Logout the user
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect to the home page after logout
        return redirect('/');
    }
}
