<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ActivityLogger;

class InvoiceController extends Controller
{
    // Fetch unpaid invoices for logged-in users (passengers)
    public function unpaidPayments(Request $request)
    {
        // Log the request type
        Log::info('unpaidPayments method triggered', ['is_ajax' => $request->ajax()]);
    
        if ($request->ajax()) {
            // Log before fetching invoices
            Log::info('Fetching unpaid invoices for user', ['user_id' => Auth::id()]);
    
            $unpaidInvoices = Invoice::with('booking')
                ->whereHas('booking', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('status', 'Unpaid')  // Ensure 'Unpaid' matches your database enum
                ->get();
    
            // Log the unpaid invoices fetched
            Log::info('Unpaid invoices fetched', ['invoice_count' => $unpaidInvoices->count()]);
    
            // Convert data for DataTable
            $data = $unpaidInvoices->map(function($invoice) {
                return [
                    'booking_reference' => $invoice->booking->booking_reference,
                    'invoice_number' => $invoice->invoice_number,
                    'created_at' => $invoice->booking->created_at,
                    'service_type' => $invoice->booking->service_type,
                    'amount' => $invoice->amount,
                    'status' => $invoice->status,
                    'id' => $invoice->id,
                    'booking_id' => $invoice->booking->id,
                    'user_email' => $invoice->booking->user->email,
                ];
            });
    
            // Log the response data before returning
            Log::info('Returning unpaid invoice data', ['data' => $data]);
    
            return response()->json(['data' => $data]);
        }
    
        // Log for non-AJAX requests
        Log::info('Non-AJAX request for unpaidPayments view');
    
        // Regular non-AJAX response
        $unpaidInvoices = Invoice::with('booking')
            ->whereHas('booking', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status', 'Unpaid')
            ->get();
    
        return view('passenger.makepayments', compact('unpaidInvoices'));
    }

    // Show an invoice
    public function showInvoice($id)
    {
        $user = Auth::user();  // Fetch the logged-in user
        $invoice = Invoice::with('booking')->findOrFail($id);

        // Check if the user is an admin, consultant, or the owner of the invoice
        if (!$this->canAccessInvoice($invoice, $user)) {
            return redirect()->route('passenger.makepayments')->with('error', 'Unauthorized access to invoice.');
        }

        $booking = $invoice->booking;

        return view('passenger.invoice', compact('user', 'invoice', 'booking'));
    }

    // Download an invoice
    public function downloadInvoice($id)
    {
        $invoice = Invoice::with('booking')->findOrFail($id);
        $booking = $invoice->booking;
        $user = $booking->user;

        if (!$this->canAccessInvoice($invoice, $user)) {
            return redirect()->back()->with('error', 'Unauthorized access to invoice.');
        }

        $pdf = Pdf::loadView('passenger.invoice-pdf', compact('invoice', 'booking', 'user'));
        return $pdf->download('invoice_' . $invoice->invoice_number . '.pdf');
    }

    // Fetch all invoices for logged-in users, admins, and consultants
    public function index()
    {
        $user = Auth::user();

        // Admins and consultants should view all invoices
        if ($user->hasRole(['admin', 'consultant'])) {
            $invoices = Invoice::with('booking')->get();
        } else {
            // Passengers only view their own invoices
            $invoices = Invoice::with('booking')->whereHas('booking', function ($query) {
                $query->where('user_id', Auth::id());
            })->get();
        }

        return view('passenger.invoices', compact('invoices'));
    }

    // View a specific invoice
    public function view($id)
    {
        $invoice = Invoice::with('booking')->findOrFail($id);
        $user = Auth::user();
        $booking = $invoice->booking;

        if (!$this->canAccessInvoice($invoice, $user)) {
            return redirect()->route('passenger.dashboard')->with('error', 'Unauthorized access to invoice.');
        }

        return view('passenger.invoice', compact('invoice', 'user', 'booking'));
    }

    // Handle payments
    public function pay(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        $booking = $invoice->booking;

        // Generate a unique transaction reference
        $transactionReference = $invoice->invoice_number . '-' . uniqid();

        // Create a payment record with initial 'unpaid' status
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'amount' => $invoice->amount,
            'status' => 'unpaid',
            'payment_method' => 'paystack',
            'payment_reference' => $transactionReference,
        ]);

        // Redirect to Paystack payment page
        return Paystack::getAuthorizationUrl([
            'email' => $booking->user->email,
            'amount' => $invoice->amount * 100,
            'reference' => $transactionReference,
        ])->redirectNow();
    }

    // Helper function to determine access to an invoice
    private function canAccessInvoice($invoice, $user)
    {
        return $user->hasRole(['admin', 'consultant']) || $invoice->booking->user_id === $user->id;
    }
}
