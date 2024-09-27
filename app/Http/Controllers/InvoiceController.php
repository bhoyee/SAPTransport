<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- Add this line

class InvoiceController extends Controller
{

    public function unpaidPayments()
    {
        // Fetch all unpaid invoices for the logged-in user
        $unpaidInvoices = Invoice::with('booking')
            ->whereHas('booking', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status', 'Unpaid')  // Ensure 'Unpaid' matches your database enum
            ->get();

        // Return the view with unpaid invoices
        return view('passenger.makepayments', compact('unpaidInvoices'));
    }

    public function showInvoice($id)
    {
        $user = Auth::user();  // Fetch the logged-in user
        $invoice = Invoice::with('booking')->findOrFail($id);  // Fetch the invoice along with booking details

        // Check if the invoice belongs to the logged-in user (optional)
        if ($invoice->booking->user_id !== $user->id) {
            return redirect()->route('passenger.makepayments')->with('error', 'Unauthorized access to invoice.');
        }

        $booking = $invoice->booking;  // Get the related booking

        // Return the invoice view
        return view('passenger.invoice', compact('user', 'invoice', 'booking'));
    }

}
