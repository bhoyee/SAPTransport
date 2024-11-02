<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch booking status from the settings
        $bookingStatus = Setting::where('key', 'booking_status')->value('value') ?? 'open'; 

        // Pass the booking status to the view
        return view('index', compact('bookingStatus'));
    }
}
