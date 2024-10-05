<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassengerController extends BaseController
{
    // Apply middleware in the constructor to protect the routes
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    // Dashboard method to display the passenger dashboard
    public function dashboard()
    {
        // Update last activity time to track user activity
        session()->put('lastActivityTime', now()->timestamp);
        
        // Check if the screen should be locked after 10 minutes (600 seconds) of inactivity
        if (now()->timestamp - session('lastActivityTime', now()->timestamp) > 600) {
            // Lock the screen by setting a session variable
            session()->put('is_locked', true);
            // Store the current URL to redirect back after unlocking
            session()->put('previousUrl', url()->current());
            // Redirect to the lock screen
            return redirect()->route('lockscreen.show');
        }

        // If not locked, show the dashboard
        return view('passenger.dashboard');  // Ensure you have a passenger.dashboard view
    }
}
