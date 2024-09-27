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
    
    public function unpaidPayments()
    {
        // Fetch all unpaid bookings for the logged-in user
        $unpaidPayments = Payment::with('booking')
            ->where('user_id', Auth::id())
            ->where('status', 'unpaid')
            ->get();

        // Return the view with unpaid bookings
        return view('passenger.makepayments', compact('unpaidPayments'));
    }

      // Function to handle the payment process
      public function pay($id)
      {
          // Find the unpaid payment
          $payment = Payment::findOrFail($id);
  
          if ($payment->status === 'unpaid') {
              // Logic for processing payment (e.g., integration with a payment gateway)
  
              // After successful payment, update the status
              $payment->update(['status' => 'paid', 'payment_date' => now()]);
  
              return redirect()->route('payments.unpaid')->with('success', 'Payment completed successfully.');
          }
  
          return redirect()->route('payments.unpaid')->with('error', 'Payment could not be processed.');
      }
}
