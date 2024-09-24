<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\ActivityLogger;


class LockScreenController extends Controller
{
    //
        // Show the lock screen view
    public function show()
    {
        return view('auth.lockscreen');
    }

  // Handle unlocking
public function unlock(Request $request)
{
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

    // Redirect to the dashboard
    return redirect()->route('passenger.dashboard');
}


}