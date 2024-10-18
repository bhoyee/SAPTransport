<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
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


class WalkinPayController extends Controller
{
    // Search for booking based on reference
    public function search(Request $request)
    {
        $bookingRef = $request->input('booking_ref');
        \Log::info('Search initiated', ['booking_reference' => $bookingRef]);
    
        // Fetch booking based on the reference
        $booking = Booking::with('user')->where('booking_reference', $bookingRef)->first();
    
        if (!$booking) {
            \Log::warning('Booking not found', ['booking_reference' => $bookingRef]);
    
            // Check if the request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found.'
                ]);
            }
    
            // For non-AJAX requests, return the view with error
            return view('walkinpay.payment', ['error' => 'Booking not found.']);
        }
    
        \Log::info('Booking found', ['booking' => $booking]);
    
        // Fetch invoice for the booking
        $invoice = Invoice::where('booking_id', $booking->id)->first();
    
        if (!$invoice) {
            \Log::warning('Invoice not found for booking', ['booking_id' => $booking->id]);
    
            // Check if the request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invoice not found for this booking.'
                ]);
            }
    
            // For non-AJAX requests, return the view with error
            return view('walkinpay.payment', ['error' => 'Invoice not found for this booking.']);
        }
    
        \Log::info('Invoice found', ['invoice' => $invoice]);
    
        // For AJAX requests, return JSON
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'booking' => $booking,
                'invoice' => $invoice
            ]);
        }
    
        // For non-AJAX requests, render the view
        return view('walkinpay.payment', compact('booking', 'invoice'));
    }
    
    
    // Process payment (integrate Paystack or other payment gateway)
    public function processPayment(Request $request)
    {
        $invoiceId = $request->input('invoice_id');
        $invoice = Invoice::findOrFail($invoiceId);

        // Here, you'd integrate with Paystack or any other gateway
        // Redirect to Paystack payment page or handle the payment accordingly

        return redirect()->back()->with('success', 'Payment processed successfully!');
    }

    public function pay(Request $request)
    {
        try {
            // Find the invoice by its ID
            $invoice = Invoice::findOrFail($request->invoice_id);
            $booking = $invoice->booking;
    
            // Generate a new unique reference for Paystack, even if a payment already exists
            $transactionReference = $invoice->invoice_number . '-' . uniqid();
    
            // Check if an unpaid payment already exists for this booking
            $existingPayment = Payment::where('booking_id', $booking->id)->where('status', 'unpaid')->first();
    
            if ($existingPayment) {
                // Update the existing payment with the new reference
                $existingPayment->update([
                    'payment_reference' => $transactionReference,
                    'payment_method' => 'paystack',
                ]);
                Log::info("Existing unpaid payment updated with new reference: $transactionReference");
            } else {
                // Create a new unpaid payment record
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
    
            // Metadata for Paystack (without isAdmin as per your request)
            $metadata = [
                'booking_id' => $booking->id,
            ];
    
            // Redirect to Paystack payment page
            return Paystack::getAuthorizationUrl([
                'email' => $booking->user->email,
                'amount' => $invoice->amount * 100,  // Amount in kobo
                'reference' => $transactionReference,
                'metadata' => $metadata,
            ])->redirectNow();
    
        } catch (\Exception $e) {
            Log::error('Error initiating payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to process payment. Please try again.');
        }
    }
    


// public function handleGatewayCallback()
// {
//     try {
//         // Get payment details from Paystack
//         $paymentDetails = Paystack::getPaymentData();
//         \Log::info('Payment details from Paystack: ', $paymentDetails);

//         // Extract relevant information
//         $reference = $paymentDetails['data']['reference'];
//         $amountPaid = $paymentDetails['data']['amount'] / 100;  // Convert kobo to Naira
//         $paymentStatus = $paymentDetails['data']['status'];
//         $bookingId = $paymentDetails['data']['metadata']['booking_id'];

//         // Find the corresponding invoice
//         $invoice = Invoice::where('booking_id', $bookingId)->first();
//         if (!$invoice) {
//             \Log::error("Invoice not found for booking_id: $bookingId");
//             return redirect()->route('payment.failed')->with('error', 'Invoice not found.');
//         }

//         // Handle successful payment
//         if ($paymentStatus === 'success' && $invoice->amount == $amountPaid) {
//             // Check for an unpaid payment record for this booking
//             $payment = Payment::where('booking_id', $bookingId)->where('status', 'unpaid')->first();

//             if ($payment) {
//                 // If an unpaid payment exists, update it to 'paid'
//                 $payment->update([
//                     'status' => 'paid',
//                     'payment_reference' => $reference,
//                     'payment_method' => $paymentDetails['data']['channel'], // e.g., card, bank
//                     'payment_date' => now(),
//                 ]);
//                 \Log::info("Updated unpaid payment to 'paid' for booking_id: $bookingId");
//             } else {
//                 // If no unpaid payment record exists, create a new payment record as 'paid'
//                 Payment::create([
//                     'booking_id' => $bookingId,
//                     'user_id' => $invoice->booking->user_id,
//                     'amount' => $amountPaid,
//                     'status' => 'paid',
//                     'payment_reference' => $reference,
//                     'payment_method' => $paymentDetails['data']['channel'],
//                     'payment_date' => now(),
//                 ]);
//                 \Log::info("Created new payment record for booking_id: $bookingId");
//             }

//             // Mark the invoice as "Paid"
//             $invoice->update(['status' => 'Paid']);
//             \Log::info("Invoice {$invoice->invoice_number} marked as paid");

//             // Logging before sending email
//             \Log::info('Sending payment confirmation email', ['booking' => $payment->booking, 'amount' => $payment->amount]);

//             // Ensure the booking object is loaded before sending the email
//             if (!$payment->booking) {
//                 \Log::error('Booking not found for payment');
//                 return; // Optionally handle this case
//             }


//                 // Use session flash to pass the invoice number and success message
//             session()->flash('invoice_number', $invoice->invoice_number);
//             session()->flash('success', 'Payment Successful!');
        
                            
//             // Log user activity for the successful payment
//             ActivityLogger::log('Payment Completed', 'Payment completed for booking reference: ' . $payment->booking->booking_reference . ' by user: ' . $payment->user->email);

//             // Send notifications to admin and consultants
//             $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
//             foreach ($adminConsultantUsers as $adminConsultant) {
//                 Notification::create([
//                     'user_id' => $adminConsultant->id,
//                     'message' => 'Payment completed for booking reference: ' . $payment->booking->booking_reference . ' by user: ' . $payment->user->name,
//                     'type' => 'payment',
//                     'status' => 'unread',
//                     'related_user_name' => $payment->user->name,
//                 ]);
//             }

//             // Send payment confirmation email to the user
//             try {
//                 Mail::to($payment->user->email)->send(new PaymentConfirmation($payment->booking));
//                 \Log::info('Payment confirmation email sent to user: ' . $payment->user->email);
//             } catch (\Exception $e) {
//                 \Log::error('Failed to send payment confirmation email to user: ' . $e->getMessage());
//             }

//             // Send notification email to the admin
//             try {
//                 $adminEmail = config('mail.admin_email');
//                 Mail::to($adminEmail)->send(new PaymentAdminNotification($payment->booking));
//                 \Log::info('Payment notification email sent to admin.');
//             } catch (\Exception $e) {
//                 \Log::error('Failed to send payment notification email to admin: ' . $e->getMessage());
//             }

//             // Handle redirection based on role or non-login status
//             if (auth()->check()) {
//                 $user = auth()->user();

//                 // Redirect based on user role
//                 if ($user->hasRole('admin')) {
//                     return redirect()->route('admin.invoice.paid', ['invoice' => $invoice->id])->with('success', 'Payment Successful!');
//                 } elseif ($user->hasRole('consultant')) {
//                     return redirect()->route('consultant.invoice.paid', ['invoice' => $invoice->id])->with('success', 'Payment Successful!');
//                 } elseif ($user->hasRole('passenger')) {
//                     return redirect()->route('invoice.paid', ['invoice' => $invoice->id])->with('success', 'Payment Successful!');
//                 }
//             } else {
//                 // Non-logged-in user, redirect to general success page
//                 return redirect()->route('payment.success')->with('success', 'Payment Successful!');
//             }
//         } else {
//             // Handle payment failure
//             \Log::error("Payment failed for reference: $reference");
//             return redirect()->route('payment.failed')->with('error', 'Payment failed or verification mismatch.');
//         }
//     } catch (\Exception $e) {
//         \Log::error("Error during payment verification: " . $e->getMessage());
//         return redirect()->route('payment.failed')->with('error', 'Payment verification failed.');
//     }
// }



}
