<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if the authenticated user has the 'admin' role using Spatie's hasRole method
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return view('admin.dashboard');
        }

        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
