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
}
