<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // Import the Request class

class PassengerController extends Controller
{

    public function dashboard(Request $request) // Add Request as an argument
    {
      //  dd($request->user()); // Dump the user object 
        // Ensure the user has the 'passenger' role 
        if (!Auth::user()->hasRole('passenger')) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // Show the passenger dashboard
        return view('passenger.dashboard');
    }
}