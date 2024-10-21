<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Paystack;
use Illuminate\Support\Facades\Redirect;
use App\Services\ActivityLogger;
use App\Models\Notification;
use App\Mail\PaymentAdminNotification;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\AdminRefundRequestNotification;
use App\Mail\UserRefundRequestNotification;


class PaymentController extends Controller
{
    // Fetch payment history for passengers, consultants, and admins
    public function getPaymentHistory()
    {
        try {
            $user = Auth::user();

            if ($user->hasRole('passenger')) {
                // Passengers only see their own payment history
                $payments = Payment::with('booking')
                    ->where('user_id', $user->id)
                    ->orderBy('payment_date', 'desc')
                    ->get();
            } elseif ($user->hasRole('consultant') || $user->hasRole('admin')) {
                // Consultants and admins can see all payment records
                $payments = Payment::with('booking')
                    ->orderBy('payment_date', 'desc')
                    ->get();
            }

            // Log the payments for debugging
            Log::info('Fetched payments:', $payments->toArray());

            return response()->json($payments);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error fetching payment history: ' . $e->getMessage());

            return response()->json(['error' => 'Unable to fetch payment history'], 500);
        }
    }

    // Show payment history page with role-based access
    public function paymentHistory()
    {
        $user = Auth::user();

        if ($user->hasRole('passenger')) {
            // Passengers only see their own payments
            $payments = Payment::with('booking', 'booking.invoice')
                ->where('user_id', $user->id)
                ->orderBy('payment_date', 'desc')
                ->paginate(10);
        } elseif ($user->hasRole('consultant') || $user->hasRole('admin')) {
            // Consultants and admins can see all payment records
            $payments = Payment::with('booking', 'booking.invoice')
                ->orderBy('payment_date', 'desc')
                ->paginate(10);
        }

        return view('passenger.payment-history', compact('payments'));
    }

    // Request refund for a specific payment

    public function requestRefund(Request $request)
    {
        $paymentId = $request->input('payment_id');
        
        // Log the refund request initiation
        Log::info("Refund request initiated for payment ID: {$paymentId}");
    
        // Find the payment record
        $payment = Payment::find($paymentId);
    
        if ($payment && $payment->status == 'paid') {
            try {
                // Update payment status to refund pending
                $payment->status = 'refund-pending';
                $payment->save();
    
                // Log the status update
                Log::info("Payment status updated to refund-pending for payment ID: {$paymentId}");
    
                // Redirect the user immediately after saving the refund request
                session()->flash('success', 'Refund requested successfully.');
                $response = redirect()->route('payment.history');
    
                // After the redirect response is triggered, continue processing notifications and emails
                $this->processNotificationsAndEmails($payment);
    
                // Return the redirect response
                return $response;
    
            } catch (\Exception $e) {
                // Log the exception
                Log::error("Error occurred while processing refund request for payment ID: {$paymentId}", ['error' => $e->getMessage()]);
    
                return redirect()->route('payment.history')->with('error', 'An error occurred while requesting the refund.');
            }
        }
    
        return redirect()->route('payment.history')->with('error', 'Refund request failed.');
    }
    
    /**
     * Process notifications and emails after refund request
     */
    protected function processNotificationsAndEmails($payment)
    {
        try {
            // Push notifications to admin and consultant users
            $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
            foreach ($adminConsultantUsers as $adminConsultant) {
                Notification::create([
                    'user_id' => $adminConsultant->id,
                    'message' => 'Refund requested for Booking Ref: ' . $payment->booking->booking_reference . ' with Invoice No: ' . $payment->invoice->invoice_number,
                    'type' => 'payment',
                    'status' => 'unread',
                    'related_user_name' => $payment->user->name,
                ]);
    
                Log::info("Push notification sent to {$adminConsultant->name} for payment ID: {$payment->id}");
            }
    
            // Push notification to the user who initiated the refund request
            Notification::create([
                'user_id' => $payment->user_id,
                'message' => 'Your refund request has been initiated for Booking Ref: ' . $payment->booking->booking_reference . ' with Invoice No: ' . $payment->invoice->invoice_number,
                'type' => 'payment',
                'status' => 'unread',
                'related_user_name' => $payment->user->name,
            ]);
    
            Log::info("Push notification sent to user {$payment->user->name} for payment ID: {$payment->id}");
    
            // Send email notification to the admin
            $adminEmail = config('mail.admin_email');
            Mail::to($adminEmail)->send(new AdminRefundRequestNotification($payment->booking, $payment->payment_reference, $payment->invoice->invoice_number));
    
            Log::info("Email notification sent to admin for payment ID: {$payment->id}");
    
            // Send email notification to the user
            Mail::to($payment->user->email)->send(new UserRefundRequestNotification($payment->booking, $payment->payment_reference, $payment->invoice->invoice_number));
    
            Log::info("Email notification sent to user {$payment->user->email} for payment ID: {$payment->id}");
        } catch (\Exception $e) {
            Log::error("Error occurred while sending notifications or emails for payment ID: {$payment->id}", ['error' => $e->getMessage()]);
        }
    }
    


    
    // Show unpaid payments (specific to logged-in users or all for consultants and admins)
    public function unpaidPayments()
    {
        $user = Auth::user();

        if ($user->hasRole('passenger')) {
            // Passengers see only their own unpaid payments
            $unpaidPayments = Payment::with('booking')
                ->where('user_id', $user->id)
                ->where('status', 'unpaid')
                ->get();
        } elseif ($user->hasRole('consultant') || $user->hasRole('admin')) {
            // Consultants and admins can see all unpaid payments
            $unpaidPayments = Payment::with('booking')
                ->where('status', 'unpaid')
                ->get();
        }

        // Return the view with unpaid payments
        return view('passenger.makepayments', compact('unpaidPayments'));
    }

  
    public function pay(Request $request)
    {
        try {
            // Find the invoice by its ID
            $invoice = Invoice::findOrFail($request->invoice_id);
            $booking = $invoice->booking;
    
            // Check if an unpaid payment already exists for this booking
            $existingPayment = Payment::where('booking_id', $booking->id)->first();
    
            if ($existingPayment && $existingPayment->status === 'paid') {
                return redirect()->back()->with('error', 'Payment has already been completed for this booking.');
            }
    
            // Generate a unique reference for Paystack
            $transactionReference = $invoice->invoice_number . '-' . uniqid();
    
            // Update or create an unpaid payment record
            if ($existingPayment && $existingPayment->status === 'unpaid') {
                $existingPayment->update([
                    'payment_reference' => $transactionReference,
                    'payment_method' => 'paystack',
                ]);
                Log::info("Existing unpaid payment updated with new reference: $transactionReference");
            } else {
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
    
            // Add metadata to specify whether this is an admin or a passenger payment
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
        } catch (\Exception $e) {
            // Log any errors that occur
            Log::error('Error initiating payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to process payment. Please try again.');
        }
    }
    
    
    public function handleGatewayCallback()
{
    try {
        // Get payment details from Paystack
        $paymentDetails = Paystack::getPaymentData();
        \Log::info('Payment details from Paystack: ', $paymentDetails);

        // Extract relevant information
        $reference = $paymentDetails['data']['reference'];
        $amountPaid = $paymentDetails['data']['amount'] / 100;  // Convert kobo to Naira
        $paymentStatus = $paymentDetails['data']['status'];
        $bookingId = $paymentDetails['data']['metadata']['booking_id'];

        \Log::info('Payment details from Paystack: ', $paymentDetails);


        // Find the corresponding invoice
        $invoice = Invoice::where('booking_id', $bookingId)->first();
        if (!$invoice) {
            \Log::error("Invoice not found for booking_id: $bookingId");
            return redirect()->route('payment.failed')->with('error', 'Invoice not found.');
        }

        // Handle successful payment
        if ($paymentStatus === 'success' && $invoice->amount == $amountPaid) {
            // Check for an unpaid payment record for this booking
            $payment = Payment::where('booking_id', $bookingId)->where('status', 'unpaid')->first();

            if ($payment) {
                // If an unpaid payment exists, update it to 'paid'
                $payment->update([
                    'status' => 'paid',
                    'payment_reference' => $reference,
                    'payment_method' => $paymentDetails['data']['channel'], // e.g., card, bank
                    'payment_date' => now(),
                ]);
                \Log::info("Updated unpaid payment to 'paid' for booking_id: $bookingId");
            } else {
                // If no unpaid payment record exists, create a new payment record as 'paid'
                Payment::create([
                    'booking_id' => $bookingId,
                    'user_id' => $invoice->booking->user_id,
                    'amount' => $amountPaid,
                    'status' => 'paid',
                    'payment_reference' => $reference,
                    'payment_method' => $paymentDetails['data']['channel'],
                    'payment_date' => now(),
                ]);
                \Log::info("Created new payment record for booking_id: $bookingId");
            }

            // Mark the invoice as "Paid"
            $invoice->update(['status' => 'Paid']);
            \Log::info("Invoice {$invoice->invoice_number} marked as paid");

            // Logging before sending email
            \Log::info('Sending payment confirmation email', ['booking' => $payment->booking, 'amount' => $payment->amount]);

            // Ensure the booking object is loaded before sending the email
            if (!$payment->booking) {
                \Log::error('Booking not found for payment');
                return; // Optionally handle this case
            }


                // Use session flash to pass the invoice number and success message
            session()->flash('invoice_number', $invoice->invoice_number);
            session()->flash('success', 'Payment Successful!');
        
                            
            // Log user activity for the successful payment
            ActivityLogger::log('Payment Completed', 'Payment completed for booking reference: ' . $payment->booking->booking_reference . ' by user: ' . $payment->user->email);

            // Send notifications to admin and consultants
            $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
            foreach ($adminConsultantUsers as $adminConsultant) {
                Notification::create([
                    'user_id' => $adminConsultant->id,
                    'message' => 'Payment completed for booking reference: ' . $payment->booking->booking_reference . ' by user: ' . $payment->user->name,
                    'type' => 'payment',
                    'status' => 'unread',
                    'related_user_name' => $payment->user->name,
                ]);
            }

            // Send payment confirmation email to the user
            try {
                Mail::to($payment->user->email)->send(new PaymentConfirmation($payment->booking));
                \Log::info('Payment confirmation email sent to user: ' . $payment->user->email);
            } catch (\Exception $e) {
                \Log::error('Failed to send payment confirmation email to user: ' . $e->getMessage());
            }

            // Send notification email to the admin
            try {
                $adminEmail = config('mail.admin_email');
                Mail::to($adminEmail)->send(new PaymentAdminNotification($payment->booking));
                \Log::info('Payment notification email sent to admin.');
            } catch (\Exception $e) {
                \Log::error('Failed to send payment notification email to admin: ' . $e->getMessage());
            }

            // Handle redirection based on role or non-login status
            if (auth()->check()) {
                $user = auth()->user();

                // Redirect based on user role
                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.invoice.paid', ['invoice' => $invoice->id])->with('success', 'Payment Successful!');
                } elseif ($user->hasRole('consultant')) {
                    return redirect()->route('consultant.invoice.paid', ['invoice' => $invoice->id])->with('success', 'Payment Successful!');
                } elseif ($user->hasRole('passenger')) {
                    return redirect()->route('invoice.paid', ['invoice' => $invoice->id])->with('success', 'Payment Successful!');
                }
            } else {
                // Non-logged-in user, redirect to general success page
                return redirect()->route('payment.success')->with('success', 'Payment Successful!');
            }
        } else {
            // Handle payment failure
            \Log::error("Payment failed for reference: $reference");
            return redirect()->route('payment.failed')->with('error', 'Payment failed or verification mismatch.');
        }
    } catch (\Exception $e) {
        \Log::error("Error during payment verification: " . $e->getMessage());
        return redirect()->route('payment.failed')->with('error', 'Payment verification failed.');
    }
}


        /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|integer',
        ]);

        try {
            return Paystack::getAuthorizationUrl()->redirectNow();
        } catch (\Exception $e) {
            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }
    }

    
    public function failedInvoice()
    {
        // Log the payment failure
        \Log::error('Payment failed or verification mismatch.');
    
        // Return the view with an error message
        return view('passenger.invoice-failed')->with('error', 'Payment verification failed or mismatched.');
    }
    

    public function paidInvoice($invoiceId)
        {
            // Find the invoice by its ID
            $invoice = Invoice::findOrFail($invoiceId);

            // Ensure the invoice has a booking
            if (!$invoice->booking) {
                return redirect()->back()->with('error', 'Booking not found for this invoice.');
            }

            // Check if the authenticated user is the owner of the invoice
            // if (auth()->user()->id !== $invoice->booking->user_id) {
            //     abort(403, 'You are not authorized to view this invoice.');
            // }

            // Display the successful payment page
            return view('passenger.invoice-paid', compact('invoice'));
        }

            


    
    

}
