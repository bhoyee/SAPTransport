<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- Add this line

class PaymentController extends Controller
{
    public function getPaymentHistory()
    {
        try {
            $userId = Auth::id();

            // Fetch payments with the associated booking for the logged-in user
            $payments = Payment::with('booking')
                               ->where('user_id', $userId)
                               ->orderBy('payment_date', 'desc')
                               ->get();

            // Log the payments for debugging
            Log::info('Fetched payments:', $payments->toArray());

            return response()->json($payments);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error fetching payment history: ' . $e->getMessage());

            return response()->json(['error' => 'Unable to fetch payment history'], 500);
        }
    }
}
