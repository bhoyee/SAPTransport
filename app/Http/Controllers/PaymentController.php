<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- Add this line
use Paystack;
use Illuminate\Support\Facades\Redirect;


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

    public function paymentHistory()
    {
        $userId = Auth::id();

        // Fetch all payments for the logged-in user with their associated booking
        $payments = Payment::with('booking', 'booking.invoice') // Assuming you have a relationship to invoices
            ->where('user_id', $userId)
            ->orderBy('payment_date', 'desc')
            ->paginate(10); // Paginate the results

        return view('passenger.payment-history', compact('payments'));
    }

    public function requestRefund(Request $request)
    {
        $paymentId = $request->input('payment_id');
        
        // Find the payment record
        $payment = Payment::find($paymentId);

        if ($payment && $payment->status == 'paid') {
            // Update payment status or create a refund request (based on your business logic)
            $payment->status = 'refund-pending';  // Example of updating the status
            $payment->save();

            return redirect()->route('payment.history')->with('success', 'Refund requested successfully.');
        }

        return redirect()->route('payment.history')->with('error', 'Refund request failed.');
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
      
      public function pay(Request $request)
      {
          // Find the invoice and booking details
          $invoice = Invoice::findOrFail($request->invoice_id);
          $booking = $invoice->booking;
      
          // Generate a unique reference by appending a timestamp or random string to the invoice number
          $transactionReference = $invoice->invoice_number . '-' . uniqid();
      
          // Check if an unpaid payment already exists for this booking
          $existingPayment = Payment::where('booking_id', $booking->id)
              ->where('status', 'unpaid')
              ->first();
      
          if ($existingPayment) {
              // Update the existing unpaid payment record with the new reference
              $existingPayment->update([
                  'payment_reference' => $transactionReference,  // Store the new reference
                  'payment_method' => 'paystack',  // Default method until confirmed
              ]);
              \Log::info("Existing unpaid payment updated with new reference: $transactionReference");
          } else {
              // Insert the payment record with "unpaid" status if no existing record is found
              Payment::create([
                  'booking_id' => $booking->id,
                  'user_id' => $booking->user_id,
                  'amount' => $invoice->amount,
                  'status' => 'unpaid',  // Initial unpaid status
                  'payment_method' => 'paystack',  // Default method until confirmed
                  'payment_reference' => $transactionReference,  // Store the unique reference
              ]);
              \Log::info("New unpaid payment created with reference: $transactionReference");
          }
      
          // Redirect to Paystack payment page with the unique reference
          return Paystack::getAuthorizationUrl([
              'email' => $booking->user->email,
              'amount' => $invoice->amount * 100,  // Amount in kobo
              'reference' => $transactionReference,  // Use the unique reference
              'metadata' => [
                  'booking_id' => $booking->id,
              ],
          ])->redirectNow();
      }
      
      public function handleGatewayCallback()
      {
          try {
              // Get payment details from Paystack
              $paymentDetails = Paystack::getPaymentData();
      
              // Log Paystack payment details for debugging
              \Log::info('Payment details from Paystack: ', $paymentDetails);
      
              // Extract relevant information
              $reference = $paymentDetails['data']['reference'];
              $amountPaid = $paymentDetails['data']['amount'] / 100;  // Convert to Naira
              $paymentMethod = $paymentDetails['data']['channel'];  // e.g., card, bank
              $paymentStatus = $paymentDetails['data']['status'];  // success or failure
      
              // Ensure that booking_id is in the metadata
              if (!isset($paymentDetails['data']['metadata']['booking_id'])) {
                  \Log::error("Booking ID is missing in the metadata.");
                  return redirect()->route('invoice.failed')->with('error', 'Booking ID not found in payment metadata.');
              }
      
              $bookingId = $paymentDetails['data']['metadata']['booking_id'];
      
              // Find the existing unpaid payment record for this booking
              $payment = Payment::where('booking_id', $bookingId)
                  ->where('status', 'unpaid')
                  ->first();
      
              if (!$payment) {
                  \Log::error("Unpaid payment not found for booking_id: $bookingId");
                  return redirect()->route('invoice.failed')->with('error', 'Unpaid payment record not found.');
              }
      
              // Find the corresponding invoice
              $invoice = Invoice::where('booking_id', $payment->booking_id)->first();
      
              if (!$invoice) {
                  \Log::error("Invoice not found for booking_id: {$payment->booking_id}");
                  return redirect()->route('invoice.failed')->with('error', 'Invoice not found.');
              }
      
              // Handle success or failure of the payment
              if ($paymentStatus === 'success' && $invoice->amount == $amountPaid) {
                  // Update the invoice status to "Paid"
                  $invoice->update(['status' => 'Paid']);
                  \Log::info("Invoice {$invoice->invoice_number} marked as paid");
      
                  // Update the payment record with the successful payment details
                  $payment->update([
                      'status' => 'paid',
                      'payment_reference' => $reference,
                      'payment_method' => $paymentMethod,
                      'payment_date' => now(),
                  ]);
      
                  \Log::info("Payment updated for invoice {$invoice->invoice_number}");
      
                  // Redirect to a success page
                  return redirect()->route('invoice.paid', ['invoice' => $invoice->id])->with('success', 'Payment Successful!');
              } else {
                  // Update the existing unpaid payment with the new reference on failure
                  $payment->update([
                      'payment_reference' => $reference,
                      'payment_method' => $paymentMethod,
                  ]);
      
                  \Log::error("Payment failed for reference: $reference");
                  return redirect()->route('invoice.failed')->with('error', 'Payment failed or verification mismatch.');
              }
          } catch (\Exception $e) {
              // Log any errors during the process
              \Log::error("Error during payment verification: " . $e->getMessage());
      
              return redirect()->route('invoice.failed')->with('error', 'Payment verification failed.');
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
        // Log that the payment failed
        \Log::error('Payment failed or verification mismatch.');

        return view('passenger.invoice-failed')->with('error', 'Payment verification failed or mismatched.');
    }

    public function paidInvoice(Invoice $invoice)
    {
        return view('passenger.invoice-paid', compact('invoice'));
    }


    
    

}
