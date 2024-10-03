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
        if (!session()->has('lastActivityTime')) {
            // If the session expired, redirect to the login page
            return redirect()->route('login')->withErrors(['message' => 'Your session has expired. Please log in again.']);
        }

        return view('auth.lockscreen');
    }

    // Handle unlocking with password
    public function unlock(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('login')->withErrors(['message' => 'Session expired. Please log in again.']);
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User not authenticated. Please log in again.']);
        }

        if (is_null($request->password) || !Hash::check($request->password, $user->password)) {
            return redirect()->route('lockscreen.show')->withErrors(['password' => 'Invalid password.']);
        }

        // Unlock the session
        session()->put('is_locked', false);
        return redirect()->route('passenger.dashboard');
    }

    // Handle social login unlock (optional)
    public function handleGoogleUnlock()
    {
        $user = Auth::user();

        if ($user) {
            session()->put('is_locked', false);
            return redirect()->route('passenger.dashboard');
        }

        return redirect()->route('login')->withErrors(['error' => 'Google authentication failed.']);
    }
}
