<?php

// app/Services/ReportService.php
namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Payment;

class ReportService
{
    public function generateStatistics()
    {
        return [
            'totalActivePassengers' => User::role('passenger')->where('status', 'active')->count(),
            'totalInactivePassengers' => User::role('passenger')->where('status', 'inactive')->count(),
            'totalPassengers' => User::role('passenger')->count(),
            'bookingsLastMonth' => Booking::whereMonth('created_at', Carbon::now()->subMonth()->month)->count(),
            'bookingsThisYear' => Booking::whereYear('created_at', Carbon::now()->year)->count(),
            'passengersLastMonth' => User::role('passenger')->whereMonth('created_at', Carbon::now()->subMonth()->month)->count(),
            'totalSalesThisYear' => Payment::whereYear('created_at', Carbon::now()->year)->sum('amount'),
            'totalSalesLastMonth' => Payment::whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('amount'),
            'closedTicketsThisYear' => Contact::whereYear('created_at', Carbon::now()->year)->where('status', 'closed')->count(),
            'openTicketsThisYear' => Contact::whereYear('created_at', Carbon::now()->year)->where('status', 'open')->count(),
            'completedBookingsThisMonth' => Booking::whereMonth('created_at', Carbon::now()->subMonth()->month)->where('status', 'completed')->count(),
            'completedBookingsThisYear' => Booking::whereYear('created_at', Carbon::now()->year)->where('status', 'completed')->count(),
            'canceledBookings' => Booking::whereMonth('created_at', Carbon::now()->subMonth()->month)->where('status', 'cancelled')->count(),

        ];
    }
    
}
