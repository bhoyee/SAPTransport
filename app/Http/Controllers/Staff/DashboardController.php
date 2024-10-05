<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // Check if the authenticated user has the 'consultant' role
        if (Auth::check() && Auth::user()->role === 'consultant') {
            return view('staff.dashboard');
        }

        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
