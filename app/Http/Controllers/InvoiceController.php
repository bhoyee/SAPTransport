<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- Add this line
use Barryvdh\DomPDF\Facade\Pdf;

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

    

    public function downloadInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $booking = $invoice->booking;
        $user = $booking->user;

        $pdf = Pdf::loadView('passenger.invoice-pdf', compact('invoice', 'booking', 'user'));
        return $pdf->download('invoice_'.$invoice->invoice_number.'.pdf');
    }

    public function index()
    {
        // Fetch all invoices for the logged-in user
        $userId = Auth::id();
        $invoices = Invoice::with('booking')->whereHas('booking', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return view('passenger.invoices', compact('invoices'));
    }

    // View a specific invoice
    public function view($id)
    {
        $invoice = Invoice::with('booking')->findOrFail($id);
        $user = Auth::user();  // Get the logged-in user
        $booking = $invoice->booking;  // Get the related booking
    
        // Ensure booking is found
        if (!$booking) {
            return redirect()->route('passenger.dashboard')->with('error', 'Booking not found.');
        }
    
        return view('passenger.invoice', compact('invoice', 'user', 'booking'));
    }
    


    public function pay(Request $request)
    {
        // Find the invoice and booking details
        $invoice = Invoice::findOrFail($request->invoice_id);
        $booking = $invoice->booking;

        // Generate a unique reference by appending a timestamp or random string to the invoice number
        $transactionReference = $invoice->invoice_number . '-' . uniqid();

        // Insert the payment record with "unpaid" status
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'amount' => $invoice->amount,
            'status' => 'unpaid',  // Initial unpaid status
            'payment_method' => 'paystack',  // Default method until confirmed
            'payment_reference' => $transactionReference,  // Store the unique reference
        ]);

        // Redirect to Paystack payment page with the unique reference
        return Paystack::getAuthorizationUrl([
            'email' => $booking->user->email,
            'amount' => $invoice->amount * 100,  // Amount in kobo
            'reference' => $transactionReference,  // Use the unique reference
        ])->redirectNow();
    }




}
