<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Paystack;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // Search Booking by Reference
    // Search Booking by Reference
    public function searchBooking(Request $request)
    {
        $booking = null;
        $invoice = null;
    
        if ($request->has('booking_ref')) {
            $request->validate([
                'booking_ref' => 'required|string',
            ]);
    
            $booking = Booking::where('booking_reference', $request->booking_ref)->first();
    
            if (!$booking) {
                return back()->with('error', 'Booking not found.');
            }
    
            $invoice = Invoice::where('booking_id', $booking->id)->first();
    
            // If the invoice or amount is missing, show an appropriate error message
            if (!$invoice || is_null($invoice->amount)) {
                return back()->with('error', 'No amount assigned to this booking.');
            }
        }
    
        return view('admin.payment.makepayment', compact('booking', 'invoice'));
    }
    

    // Handle payment process

    public function pay(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        $booking = $invoice->booking;

        // Check if a payment has already been made for this booking
        $existingPayment = Payment::where('booking_id', $booking->id)->first();

        // If a payment record exists and the status is 'paid', show an error message
        if ($existingPayment && $existingPayment->status === 'paid') {
            return redirect()->back()->with('error', 'Payment has already been completed for this booking.');
        }

        // Generate a unique reference for Paystack
        $transactionReference = $invoice->invoice_number . '-' . uniqid();

        // If the payment record exists but is not paid, update the reference
        if ($existingPayment && $existingPayment->status === 'unpaid') {
            $existingPayment->update([
                'payment_reference' => $transactionReference,
                'payment_method' => 'paystack',
            ]);
            Log::info("Existing unpaid payment updated with new reference: $transactionReference");
        } else {
            // Create a new payment record for unpaid status
            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'amount' => $invoice->amount,
                'status' => 'unpaid',
                'payment_method' => 'paystack',
                'payment_reference' => $transactionReference,
            ]);
            Log::info("New unpaid payment created with reference: $transactionReference");
        }

        // Add metadata to specify whether this is an admin or a regular user payment
        $metadata = [
            'booking_id' => $booking->id,
            'is_admin' => auth()->user()->hasRole('admin') ? true : false,  // Add this field to metadata
        ];

        // Redirect to Paystack payment page
        return Paystack::getAuthorizationUrl([
            'email' => $booking->user->email,
            'amount' => $invoice->amount * 100,  // Amount in kobo
            'reference' => $transactionReference,
            'metadata' => $metadata,
        ])->redirectNow();
    }


    public function showPaidInvoice($id)
    {
        // Retrieve the invoice by its ID
        $invoice = Invoice::findOrFail($id);
    
        // Log the invoice object to verify the data
        Log::info('Showing invoice details: ', $invoice->toArray());
    
        // Pass the invoice details to the view
        return view('admin.payment.paid', compact('invoice'));
    }
    
    

    public function paidInvoice(Invoice $invoice)
    {
        // Log the invoice being accessed
        \Log::info('Accessing paid invoice', ['invoice_id' => $invoice->id]);
    
        // Ensure the invoice has a booking
        if (!$invoice->booking) {
            \Log::error('Booking not found for invoice', ['invoice_id' => $invoice->id]);
            return redirect()->back()->with('error', 'Booking not found for this invoice.');
        }
    
        // Log booking details
        \Log::info('Booking found for invoice', ['booking_id' => $invoice->booking->id]);
    
        // Check if the user is an admin and allowed to view the invoice
        if (auth()->user()->hasRole('admin')) {
            \Log::info('User is an admin and allowed to view the invoice', ['user_id' => auth()->user()->id, 'invoice_id' => $invoice->id]);
    
            // Render the appropriate view for the paid invoice
            return view('admin.payment.paid', compact('invoice'));
        }
    
        // Log if the user does not have the right role
        \Log::warning('User does not have the right role to view this invoice', [
            'user_id' => auth()->user()->id, 
            'user_roles' => auth()->user()->getRoleNames()->toArray(),
            'invoice_id' => $invoice->id
        ]);
    
        // If the user is not an admin, abort with a 403 Forbidden error
        abort(403, 'User does not have the right roles to view this invoice.');
    }
    
        
    public function failedInvoice()
    {
        return view('admin.payment.failed')->with('error', 'Payment failed. Please try again.');
    }

    public function unpaidInvoices()
    {
        $user = auth()->user();

        // Admins can see all unpaid invoices, users can only see their own
        if ($user->hasRole('admin')) {
            $invoices = Invoice::where('status', 'unpaid')->get();
        } else {
            $invoices = Invoice::where('user_id', $user->id)->where('status', 'unpaid')->get();
        }

        return view('admin.payment.unpaid', compact('invoices'));
    }

    public function refundPayment(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        
        // Only allow refunds for paid invoices
        if ($invoice->status !== 'paid') {
            return back()->with('error', 'Refund can only be processed for paid invoices.');
        }

        // Process refund logic (this can be specific to your payment gateway or manual process)
        // E.g., call the payment gateway's refund API if available

        $invoice->update(['status' => 'refund_pending']);

        return back()->with('success', 'Refund request has been submitted.');
    }

    public function updateInvoice(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $invoice->update([
            'amount' => $request->input('amount'),
            'status' => $request->input('status'),
        ]);

        return back()->with('success', 'Invoice updated successfully.');
    }

    public function listInvoices()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $invoices = Invoice::all();
        } else {
            $invoices = Invoice::where('user_id', $user->id)->get();
        }

        return view('admin.payment.invoices', compact('invoices'));
    }

    public function viewInvoice(Invoice $invoice)
    {
        // Only allow access to admin or the user who owns the invoice
        if (!auth()->user()->hasRole('admin') && auth()->user()->id !== $invoice->user_id) {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.payment.view', compact('invoice'));
    }

}