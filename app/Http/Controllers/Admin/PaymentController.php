<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Paystack;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;
use App\Models\Notification;
use App\Mail\AdminRefundNotification;
use App\Mail\UserRefundNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\User;


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

    //Manage payment page
    public function managePayments()
    {
        $payments = Payment::with(['booking', 'invoice'])  // Load the invoice relationship
            ->orderBy('payment_date', 'desc')
            ->get();
    
        return view('admin.payment.index', compact('payments'));
    }

    // fetching realtime paymnt
    public function fetchPayments()
    {
        // Fetch payments with necessary relationships, ordered by updated_at in descending order
        $payments = Payment::with(['booking', 'invoice'])
            ->orderBy('updated_at', 'desc')
            ->get();
    
        // Format the data as per DataTables requirements
        $formattedPayments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'booking_reference' => $payment->booking->booking_reference ?? 'N/A',
                'amount' => $payment->amount,
                'status' => $payment->status,
                'payment_date' => $payment->payment_date,
                'payment_method' => ucfirst($payment->payment_method),
                'payment_reference' => $payment->payment_reference,
                'updated_at' => $payment->updated_at->toDateString(),
            ];
        });
    
        return response()->json(['data' => $formattedPayments]);
    }
    



    // Process refund

    public function processRefund($id, Request $request)
    {
        try {
            // Log payment ID
            Log::info("Processing refund for payment ID: {$id}");

            // Find the payment
            $payment = Payment::findOrFail($id);

            // Log payment reference
            Log::info("Processing refund for payment reference: {$payment->payment_reference}");

            // Check if the payment reference starts with 'CASH-'
            if (str_starts_with($payment->payment_reference, 'CASH-')) {
                Log::warning("Cannot refund a cash transaction: {$payment->payment_reference}");
                return response()->json(['success' => false, 'message' => 'This transaction was paid by cash and cannot be refunded via Paystack.']);
            }

            // Check if the payment has already been refunded
            if ($payment->status === 'refunded') {
                return response()->json(['success' => false, 'message' => 'This transaction has already been fully refunded.']);
            }

            // Get Paystack secret key from env
            $paystackSecretKey = env('PAYSTACK_SECRET_KEY');

            // Prepare the refund request data
            $refundData = [
                'transaction' => $payment->payment_reference,  // Use the payment reference from the payment table
                'amount' => $payment->amount * 100,  // Amount in kobo
                'currency' => 'NGN',
                'customer_note' => 'Refund for transaction ' . $payment->payment_reference,
                'merchant_note' => 'Refund for transaction ' . $payment->payment_reference . ' initiated by admin.'
            ];

            // Make the POST request to Paystack Refund API
            Log::info('Sending refund request to Paystack', $refundData);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecretKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/refund', $refundData);

            // Parse the response
            $responseBody = $response->json();

            Log::info('Paystack refund response: ', $responseBody);

            if ($responseBody['status']) {
                // Update payment status to refunded
                $payment->status = 'refunded';
                $payment->payment_date = now();  // Update the payment date to the refund date
                $payment->save();

                // Update the corresponding invoice status to refunded
                if ($payment->invoice) {
                    $invoice = $payment->invoice;
                    $invoice->status = 'refunded';
                    $invoice->updated_at = now();  // Update the invoice's updated date
                    $invoice->save();
                }

                // Log the refund action
                Log::info("Refund initiated successfully for payment: {$payment->payment_reference}");

                // Log the activity using ActivityLogger
                ActivityLogger::log('Refund Processed', 'Refund processed for payment reference: ' . $payment->payment_reference);

                // Push notification to admin and consultant users
                $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
                foreach ($adminConsultantUsers as $adminConsultant) {
                    Notification::create([
                        'user_id' => $adminConsultant->id,
                        'message' => 'Refund processed for Booking Ref: ' . $payment->booking->booking_reference . ' with Invoice No: ' . $invoice->invoice_number,
                        'type' => 'payment',
                        'status' => 'unread',
                        'related_user_name' => $payment->user->name,
                    ]);
                }

                // Push notification to the payment owner (the user who made the payment)
                Notification::create([
                    'user_id' => $payment->user_id,
                    'message' => 'Your refund has been processed for Booking Ref: ' . $payment->booking->booking_reference . ' with Invoice No: ' . $invoice->invoice_number,
                    'type' => 'payment',
                    'status' => 'unread',
                    'related_user_name' => $payment->user->name,
                ]);

                // Send email notification to the admin
                $adminEmail = config('mail.admin_email');
                Mail::to($adminEmail)->send(new AdminRefundNotification($payment->booking, $payment->payment_reference, $invoice->invoice_number));

                // Send email notification to the payment owner
                Mail::to($payment->user->email)->send(new UserRefundNotification($payment->booking, $payment->payment_reference, $invoice->invoice_number));

                // Return a proper JSON response for AJAX
                return response()->json(['success' => true, 'message' => 'Refund has been successfully processed.']);
            } else {
                // Log the error
                Log::error("Refund failed for payment: {$payment->payment_reference}. Message: " . $responseBody['message']);

                // Return a failure response for AJAX
                return response()->json(['success' => false, 'message' => $responseBody['message']]);
            }

        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error processing refund: ' . $e->getMessage());

            // Return a generic error response for AJAX
            return response()->json(['success' => false, 'message' => 'An error occurred while processing the refund.']);
        }
    }

    
    
    /// decliend refund

    public function declineRefund($id)
    {
        try {
            // Fetch the payment record
            $payment = Payment::findOrFail($id);

            // Check if the status is already 'paid'
            if ($payment->status === 'paid') {
                return response()->json(['success' => false, 'message' => 'This payment is already marked as paid.']);
            }

            // Set the status back to "paid"
            $payment->status = 'paid';
            $payment->save();

            // Return the success message
            return response()->json(['success' => true, 'message' => 'Refund process declined successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while declining the refund.']);
        }
    }





    // public function refundPayment(Request $request)
    // {
    //     $invoice = Invoice::findOrFail($request->invoice_id);
        
    //     // Only allow refunds for paid invoices
    //     if ($invoice->status !== 'paid') {
    //         return back()->with('error', 'Refund can only be processed for paid invoices.');
    //     }

    //     // Process refund logic (this can be specific to your payment gateway or manual process)
    //     // E.g., call the payment gateway's refund API if available

    //     $invoice->update(['status' => 'refund_pending']);

    //     return back()->with('success', 'Refund request has been submitted.');
    // }

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

    public function refundCash($id)
    {
        try {
            \Log::info("Processing cash refund for payment ID: {$id}");

            // Find the payment record
            $payment = Payment::findOrFail($id);

            // Log payment reference and status
            \Log::info("Payment found", ['payment_reference' => $payment->payment_reference, 'status' => $payment->status]);

            // Check if it's already refunded
            if ($payment->status === 'Refunded') {
                \Log::warning("Payment already refunded", ['payment_reference' => $payment->payment_reference]);
                return response()->json(['success' => false, 'message' => 'This payment has already been refunded.']);
            }

            // Ensure it's a cash payment
            if (strpos($payment->payment_reference, 'CASH-') !== 0) {
                \Log::warning("This is not a cash payment", ['payment_reference' => $payment->payment_reference]);
                return response()->json(['success' => false, 'message' => 'This is not a cash payment.']);
            }

            // Update payment status to refunded
            $payment->status = 'Refunded';
            $payment->save();
            \Log::info("Payment status updated to refunded", ['payment_reference' => $payment->payment_reference]);

            // Update invoice status to refunded if it exists
            if ($payment->invoice) {
                $invoice = $payment->invoice;
                $invoice->status = 'Refunded';
                $invoice->save();
                \Log::info("Invoice status updated to refunded", ['invoice_number' => $invoice->invoice_number]);
            }

            // Return success response
            return response()->json(['success' => true, 'message' => 'Cash payment refunded successfully.']);
        } catch (\Exception $e) {
            \Log::error("Error occurred during cash refund", ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred while processing the cash refund.']);
        }
    }

    //cash payment 
    public function showCashPaymentForm(Request $request)
    {
        // Check if a search query is submitted
        if ($request->has('booking_ref')) {
            // Search for the booking by reference
            $booking = Booking::where('booking_reference', $request->booking_ref)->first();
    
            // If booking is not found
            if (!$booking) {
                return redirect()->route('admin.payment.cash')->with('error', 'Booking not found.');
            }
    
            // Find the invoice related to this booking
            $invoice = Invoice::where('booking_id', $booking->id)->first();
    
            // If no invoice is found
            if (!$invoice) {
                return redirect()->route('admin.payment.cash')->with('error', 'Invoice not found.');
            }
    
            // Check if a payment record already exists for this booking and invoice
            $existingPayment = Payment::where('booking_id', $booking->id)
                                      ->where('amount', $invoice->amount)
                                      ->where('status', 'paid')
                                      ->first();
    
            // Return the view with booking, invoice, and existingPayment data
            return view('admin.payment.cash_payment', compact('booking', 'invoice', 'existingPayment'));
        }
    
        return view('admin.payment.cash_payment');
    }
    
    public function recordCashPayment(Request $request)
    {
        // Log the raw request data to check what's being passed
        \Log::info('recordCashPayment invoked', ['request_data' => $request->all()]);
    
        // Validate the request
        $validated = $request->validate([
            'booking_ref' => 'required|string',
            'invoice_number' => 'required|string',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
        ]);
    
        // Log validation success
        \Log::info('Request validated successfully', ['validated_data' => $validated]);
    
        // Find the booking using the booking reference
        $booking = Booking::where('booking_reference', $request->booking_ref)->first();
    
        // Find the invoice using the invoice number and booking ID
        $invoice = Invoice::where('invoice_number', $request->invoice_number)->where('booking_id', $booking->id ?? null)->first();
    
        if (!$booking || !$invoice) {
            \Log::error('Booking or invoice not found', ['booking_ref' => $request->booking_ref, 'invoice_number' => $request->invoice_number]);
            return redirect()->back()->with('error', 'Booking or invoice not found.');
        }
    
        if ($invoice->status === 'paid') {
            \Log::warning('Invoice already paid', ['invoice_number' => $invoice->invoice_number]);
            return redirect()->back()->with('error', 'This invoice has already been paid.');
        }
    
        // Check if a payment record already exists for this invoice in the payments table
        $existingPayment = Payment::where('booking_id', $booking->id)
                                   ->where('amount', $request->amount)
                                   ->where('status', 'paid')
                                   ->first();
    
        if ($existingPayment) {
            \Log::warning('Payment already recorded for this invoice', ['invoice_number' => $invoice->invoice_number]);
            return redirect()->back()->with('error', 'Payment has already been recorded for this invoice.');
        }
    
        try {
            // Record payment in the payments table
            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'amount' => $request->amount,
                'status' => 'paid',
                'payment_method' => 'cash',
                'payment_reference' => 'CASH-' . strtoupper(uniqid()),
                'payment_date' => $request->payment_date,
            ]);
    
            // Update the invoice status to 'paid'
            $invoice->status = 'paid';
            $invoice->save();
    
            \Log::info('Cash payment recorded successfully', ['booking_id' => $booking->id, 'invoice_id' => $invoice->id]);
    
            return redirect()->route('admin.payment.cash')->with('success', 'Cash payment recorded successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to record cash payment', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to record cash payment. Please try again.');
        }
    }

    

    
}