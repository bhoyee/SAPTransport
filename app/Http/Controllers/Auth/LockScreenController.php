<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LockScreenController extends Controller
{
    // Show the lock screen view
    public function show()
    {
        // Check if the session has expired
        if (!session()->has('lastActivityTime')) {
            // If the session expired, redirect to the login page
            return redirect()->route('login')->withErrors(['message' => 'Your session has expired. Please log in again.']);
        }

        // Check if the session is locked
        if (session()->get('is_locked')) {
            return view('auth.lockscreen');
        }

        // If not locked, redirect to the dashboard or last page
        $previousUrl = session()->get('previousUrl', route('passenger.dashboard'));
        return redirect($previousUrl);
    }

    // Handle unlocking with password
    public function unlock(Request $request)
    {
        // Handle GET requests gracefully (when users refresh the unlock page)
        if ($request->isMethod('get')) {
            return redirect()->route('login')->withErrors(['message' => 'Session expired. Please log in again.']);
        }

        $user = Auth::user();

        // Check if a user is authenticated
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User not authenticated. Please log in again.']);
        }

        // Check if the password is null or invalid
        if (is_null($request->password) || !Hash::check($request->password, $user->password)) {
            return redirect()->route('lockscreen.show')->withErrors(['password' => 'Invalid password.']);
        }

        // Unlock the session if password is correct
        session()->put('is_locked', false);

        // Redirect to the last page the user was on (or the dashboard if not available)
        $previousUrl = session()->get('previousUrl', route('passenger.dashboard'));
        return redirect($previousUrl);
    }

    // Handle social login unlock
    public function handleGoogleUnlock()
    {
        // Re-authenticate using Google for unlock
        try {
            // Assuming Google login session is still valid
            $user = Auth::user();

            // If the user is authenticated via Google, unlock the screen
            if ($user) {
                session()->put('is_locked', false);
                return redirect()->route('passenger.dashboard');
            }

            return redirect()->route('login')->withErrors(['error' => 'Google authentication failed.']);
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['error' => 'Unable to unlock via Google. Please try again.']);
        }
    }

    // Function to handle Google unlock redirect
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback for Google unlock
    public function googleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $socialUser->email)->first();

            // Log the user in and unlock the session
            if ($user) {
                Auth::login($user);
                session()->put('is_locked', false);
                return redirect()->route('passenger.dashboard');
            }

            return redirect()->route('login')->withErrors(['error' => 'No account associated with this Google login.']);
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['error' => 'Google authentication failed. Please try again.']);
        }
    }
}
