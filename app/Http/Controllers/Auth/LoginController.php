<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            $user = Auth::user();

            // Log success
            Log::info('User authenticated', ['email' => $request->email, 'role' => $user->getRoleNames()]);

            // Check for account suspension
            if ($user->status === 'suspend') {
                Log::warning('Login attempt for suspended account', ['email' => $user->email]);

                // Log the suspension attempt
                ActivityLogger::log('Login Attempt', 'Suspended account: ' . $request->email);

                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been suspended, please contact support.');
            }

            // Check for account deletion
            if ($user->status === 'deleted') {
                Log::warning('Login attempt for deleted account', ['email' => $user->email]);

                // Log the deleted account attempt
                ActivityLogger::log('Login Attempt', 'Deleted account: ' . $request->email);

                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been removed from our system, contact support for further clarification.');
            }

            // Proceed with the logic based on the user's role using Spatie's `hasRole()`
            if ($user->hasRole('passenger')) {
                // Passengers must have verified email
                if ($user->email_verified_at === null) {
                    Log::info('Redirecting to verification.notice', ['email' => $user->email]);

                    // Log unverified email login attempt
                    ActivityLogger::log('Login Attempt', 'Email not verified: ' . $request->email);

                    return redirect()->route('verification.notice')->with('error', 'You need to verify your email before logging in.');
                }

                // Log successful passenger login
                Log::info('Passenger logged in', ['email' => $user->email]);

                ActivityLogger::customLog('Login Success', 'Passenger logged in: ' . $request->email, $user->id);

                // Set session lock to false and redirect to passenger dashboard
                session()->put('is_locked', false);
                $request->session()->regenerate();

                return redirect()->route('passenger.dashboard');

            } elseif ($user->hasRole('admin')) {
                // Admins do not need email verification, log and redirect
                Log::info('Admin logged in', ['email' => $user->email]);

                ActivityLogger::customLog('Login Success', 'Admin logged in: ' . $request->email, $user->id);

                session()->put('is_locked', false);
                $request->session()->regenerate();

                return redirect()->route('admin.dashboard');

            } elseif ($user->hasRole('consultant')) {
                // Consultants do not need email verification, log and redirect
                Log::info('Consultant logged in', ['email' => $user->email]);

                ActivityLogger::customLog('Login Success', 'Consultant logged in: ' . $request->email, $user->id);

                session()->put('is_locked', false);
                $request->session()->regenerate();

                return redirect()->route('staff.dashboard');
            }

            // If user has an unknown role, log and redirect to login
            Log::warning('Unknown role attempted login', ['email' => $user->email, 'role' => $user->getRoleNames()]);

            return redirect()->route('login')->withErrors(['error' => 'Unknown role, access denied.']);
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
        $user = Auth::user();

        // Log logout action
        Log::info('User logged out', ['user_id' => $user ? $user->id : null]);

        ActivityLogger::log('Logout', 'User logged out: ' . ($user ? $user->email : 'Unknown'));

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the home page after logout
        return redirect('/');
    }
}
