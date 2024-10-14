<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if the authenticated user has the 'consultant' role using Spatie's hasRole method
        if (Auth::check() && Auth::user()->hasRole('consultant')) {
            return view('staff.dashboard');
        }

        // Redirect unauthorized users back to the login page with an error message
        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
