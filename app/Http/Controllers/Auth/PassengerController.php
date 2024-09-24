<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller as BaseController; // Correctly import the base Controller
use Illuminate\Http\Request;


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
        return view('passenger.dashboard');  // Ensure you have a passenger.dashboard view
    }
}
