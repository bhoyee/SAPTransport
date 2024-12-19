<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\URL;


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
        Log::info('Login attempt initiated', ['email' => $request->email]);

        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            Log::info('Login validation passed');

            if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
                $user = Auth::user();

                Log::info('User authenticated', ['email' => $request->email, 'role' => $user->getRoleNames()]);

                if ($user->status === 'suspend') {
                    Log::warning('Login attempt for suspended account', ['email' => $user->email]);
                    ActivityLogger::log('Login Attempt', 'Suspended account: ' . $request->email);
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Your account has been suspended, please contact support.');
                }

                if ($user->status === 'deleted') {
                    Log::warning('Login attempt for deleted account', ['email' => $user->email]);
                    ActivityLogger::log('Login Attempt', 'Deleted account: ' . $request->email);
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Your account has been removed from our system, contact support for further clarification.');
                }

                if ($user->hasRole('passenger')) {
                    \Log::info(Auth::user()->roles);

                    // if ($user->email_verified_at === null) {
                    //     Log::info('Redirecting to verification.notice', ['email' => $user->email]);
                    //     ActivityLogger::log('Login Attempt', 'Email not verified: ' . $request->email);

                    //     // Ensure Auth::logout() is called BEFORE the redirect
                    //     Auth::logout();  
                    //     return redirect()->route('verification.notice')->with('error', 'You need to verify your email before logging in.');
                    // }

                                // If the user is inactive or their email is not verified, redirect to email verification notice
                        if ($user->status === 'inactive' || $user->email_verified_at === null) {
                            Log::info('Redirecting to verification.notice', ['email' => $user->email]);
                            ActivityLogger::log('Login Attempt', 'Inactive account or email not verified: ' . $request->email);
                            return redirect()->route('verification.notice')->with('error', 'You need to verify your email before logging in.');
                        }

                    Log::info('Passenger logged in', ['email' => $user->email]);
                    ActivityLogger::customLog('Login Success', 'Passenger logged in: ' . $request->email, $user->id);
                    session()->put('is_locked', false);
                    $request->session()->regenerate();
                    return redirect()->route('passenger.dashboard');

                } elseif ($user->hasRole('admin')) {
                    \Log::info(Auth::user()->roles);
                    Log::info('Admin logged in', ['email' => $user->email]);
                    ActivityLogger::customLog('Login Success', 'Admin logged in: ' . $request->email, $user->id);
                    session()->put('is_locked', false);
                    $request->session()->regenerate();
                    return redirect()->route('admin.dashboard');

                } elseif ($user->hasRole('consultant')) {
                    \Log::info(Auth::user()->roles);
                    Log::info('Consultant logged in', ['email' => $user->email]);
                    ActivityLogger::customLog('Login Success', 'Consultant logged in: ' . $request->email, $user->id);
                    session()->put('is_locked', false);
                    $request->session()->regenerate();
                    return redirect()->route('staff.dashboard');
                }

                Log::warning('Unknown role attempted login', ['email' => $user->email, 'role' => $user->getRoleNames()]);
                return redirect()->route('login')->withErrors(['error' => 'Unknown role, access denied.']);
            }

            Log::info('Login attempt failed', ['email' => $request->email]);
            ActivityLogger::log('Login Failed', 'Invalid credentials: ' . $request->email);
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error during login', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);

            // Redirect based on user role, even if there's an error
            if (isset($user) && $user->hasRole('passenger') && $user->email_verified_at === null) {
                return redirect()->route('verification.notice')->with('error', 'Something went wrong. Please try again.');
            } elseif (isset($user) && $user->hasRole('admin')) {
                return redirect()->route('admin.dashboard')->with('error', 'Something went wrong. Please try again.');
            } elseif (isset($user) && $user->hasRole('consultant')) {
                return redirect()->route('staff.dashboard')->with('error', 'Something went wrong. Please try again.');
            }

            return redirect()->route('login')->with('error', 'Something went wrong. Please try again.');
        }
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
