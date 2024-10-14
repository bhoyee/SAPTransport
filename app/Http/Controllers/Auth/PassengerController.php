<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Traits\HasRoles;




class PassengerController extends BaseController
{
    public function __construct()
    {
        // Ensure the user is authenticated and their email is verified
        $this->middleware(['auth', 'verified']);
    }

    // Dashboard method to display the passenger dashboard
    public function dashboard()
    {
        // Check if the logged-in user has the 'passenger' role using Spatie's role system
        if (!Auth::user()->hasRole('passenger')) {
            // Redirect to a different page if the user is not a passenger
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

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
