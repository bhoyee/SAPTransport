<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PDF;
use Illuminate\Support\Facades\Mail;
use App\Models\WalkinInvoice;
use App\Models\Payment;




class AdminInvoiceController extends Controller
{
    // Method to show the Manage Invoices page
    public function manageInvoices()
    {
        Log::info('Manage Invoices page accessed by user: ' . auth()->user()->email);
        return view('admin.invoices.manage'); // Point to the 'manage' view
    }

    // Method to fetch invoices for the cards (dropdown)
    public function fetchInvoices(Request $request)
    {
        $filter = $request->input('filter', 'daily');
        Log::info('Fetching invoices with filter: ' . $filter);

        $dateRange = match ($filter) {
            'daily' => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
            'weekly' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'monthly' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'yearly' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
        };

        $totalPaid = Invoice::whereBetween('updated_at', $dateRange)->where('status', 'Paid')->count();
        $totalUnpaid = Invoice::whereBetween('updated_at', $dateRange)->where('status', 'Unpaid')->count();
        $totalRefunded = Invoice::whereBetween('updated_at', $dateRange)->where('status', 'Refunded')->count();

        Log::info('Invoice totals - Paid: ' . $totalPaid . ', Unpaid: ' . $totalUnpaid . ', Refunded: ' . $totalRefunded);

        return response()->json([
            'totalPaid' => $totalPaid,
            'totalUnpaid' => $totalUnpaid,
            'totalRefunded' => $totalRefunded,
        ]);
    }

    // Method to fetch all invoices for the DataTable
    public function fetchAllInvoices()
    {
        try {
            // Fetch all invoices with related booking and user information
            $invoices = Invoice::with(['booking', 'generatedByUser']) // Ensure 'generatedByUser' is defined in the model
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'booking_reference' => $invoice->booking->booking_reference ?? 'N/A',
                        'invoice_number' => $invoice->invoice_number,
                        'invoice_date' => Carbon::parse($invoice->invoice_date)->format('d M, Y'),
                        'amount' => $invoice->amount,
                        'status' => $invoice->status,
                        'updated_at' => $invoice->updated_at->format('d M, Y H:i'), // Ensure updated_at is returned
                        'generated_by' => $invoice->generatedByUser->name ?? 'N/A', // Fetch user name instead of user ID
                    ];
                });
    
            Log::info('Fetched ' . count($invoices) . ' invoices for DataTable.');
    
            return response()->json([
                'data' => $invoices,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching invoices: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch invoices'], 500); // Return error with status 500
        }
    }

        // Method to show the specific invoice
        public function showInvoice($id)
        {
            // Retrieve the invoice with related booking and user
            $invoice = Invoice::with(['booking', 'payment', 'booking.user'])->findOrFail($id);
    
            // Retrieve the associated user and booking
            $user = $invoice->booking->user;
            $booking = $invoice->booking;
    
            // Return the view for the invoice with the data
            return view('admin.invoices.view', compact('invoice', 'user', 'booking'));
        }

        public function downloadInvoice($id)
        {
            // Find the invoice by ID
            $invoice = Invoice::findOrFail($id);
        
            // Fetch the associated user and booking
            $user = $invoice->booking->user;
            $booking = $invoice->booking;
        
            // Load the PDF view with the necessary data
            $pdf = \PDF::loadView('admin.invoices.pdf', compact('invoice', 'user', 'booking'));
        
            // Return the generated PDF as a download
            return $pdf->download('invoice_' . $invoice->invoice_number . '.pdf');
        }

        // Method to show the edit form
        public function edit($id)
        {
            $invoice = Invoice::findOrFail($id); // Fetch the invoice by ID
            return view('admin.invoices.edit', compact('invoice')); // Return the edit view
        }
       
        public function update(Request $request, $id)
        {
            // Map for invoice and payments statuses
            $invoiceStatusMap = [
                'paid' => 'Paid',
                'unpaid' => 'Unpaid',
                'refunded' => 'Refunded',
            ];

            $paymentStatusMap = [
                'paid' => 'paid',
                'unpaid' => 'unpaid',
                'refunded' => 'refunded',
            ];

            // Log the raw status coming from the request
            Log::info('Raw status from request:', ['status' => $request->input('status')]);

            // Convert the status to the correct format
            $status = strtolower($request->input('status'));
            $invoiceStatus = $invoiceStatusMap[$status] ?? null;
            $paymentStatus = $paymentStatusMap[$status] ?? null;

            // Log the mapped status values for both invoice and payment
            Log::info('Mapped status values:', [
                'invoiceStatus' => $invoiceStatus,
                'paymentStatus' => $paymentStatus,
            ]);

            // If the status is invalid (not in the map), return with an error
            if (!$invoiceStatus || !$paymentStatus) {
                Log::error('Invalid status selected:', ['invoiceStatus' => $invoiceStatus, 'paymentStatus' => $paymentStatus]);
                return redirect()->back()->withErrors('Invalid status selected.');
            }

            // Manually validate other fields, excluding the status
            $request->validate([
                'invoice_date' => 'required|date',
                'amount' => 'required|numeric|min:0',
            ]);

            try {
                // Log before fetching the invoice
                Log::info('Fetching invoice with ID:', ['invoice_id' => $id]);

                // Find the invoice by ID
                $invoice = Invoice::findOrFail($id);

                // Update the invoice fields
                $invoice->invoice_date = $request->input('invoice_date');
                $invoice->amount = $request->input('amount');
                $invoice->status = $invoiceStatus;  // Update using the correctly formatted status

                // Log the updated invoice data before saving
                Log::info('Invoice data before saving:', [
                    'invoice_date' => $invoice->invoice_date,
                    'amount' => $invoice->amount,
                    'status' => $invoice->status,
                ]);

                // Check if a payment exists for the booking associated with the invoice
                $payment = \App\Models\Payment::where('booking_id', $invoice->booking_id)->first();

                if ($payment) {
                    Log::info('Payment record found for booking ID: ' . $invoice->booking_id);

                    // Update payment status and amount
                    $payment->amount = $request->input('amount');
                    $payment->status = $paymentStatus;  // Use the correct lowercase status for the payments table
                    $payment->save();

                    Log::info('Payment updated successfully.');
                } else {
                    Log::info('No payment record found for booking ID: ' . $invoice->booking_id);
                }

                // Save the updated invoice
                $invoice->save();

                Log::info('Invoice updated successfully by admin: ' . auth()->user()->email);

                // Return back with a success message
                return redirect()->back()->with('success', 'Invoice updated successfully.');
            } catch (\Exception $e) {
                Log::error('Error updating invoice: ' . $e->getMessage());
                return redirect()->back()->withErrors('Failed to update the invoice.');
            }
        }

        public function deleteInvoice($id)
        {
            try {
                // Fetch the invoice by ID
                $invoice = Invoice::findOrFail($id);

                // Fetch any related payment for the booking linked to this invoice
                $payment = \App\Models\Payment::where('booking_id', $invoice->booking_id)->first();

                // Log the delete process
                Log::info('Deleting invoice with ID: ' . $invoice->id);

                // If a payment exists, delete it
                if ($payment) {
                    $payment->delete();
                    Log::info('Deleted payment linked to booking ID: ' . $invoice->booking_id);
                }

                // Delete the invoice
                $invoice->delete();
                Log::info('Deleted invoice with ID: ' . $invoice->id);

                return response()->json(['success' => true, 'message' => 'Invoice and related payment deleted successfully']);
            } catch (\Exception $e) {
                Log::error('Error deleting invoice: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to delete the invoice'], 500);
            }
        }

        public function createCustomForm()
        {
            return view('admin.invoices.create-custom'); // Render the form to create a custom invoice
        }
        
        public function createCustomInvoice(Request $request)
        {
            $request->validate([
                'full_name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
                'service_type' => 'required|string',
                'pickup_date' => 'required|date',
                'pickup_time' => 'required',
                'pickup_address' => 'required|string',
                'dropoff_address' => 'required|string',
                'amount_paid' => 'required|numeric|min:0',
                'status' => 'required|in:Paid,Unpaid,Refunded',
            ]);
        
            try {
                // Create the walk-in invoice
                $invoice = new WalkinInvoice();
                $invoice->name = $request->input('full_name');
                $invoice->email = $request->input('email');
                $invoice->phone = $request->input('phone');
                $invoice->issue_date = $request->input('issue_date');
                $invoice->due_date = $request->input('due_date');
                $invoice->amount = $request->input('amount_paid');
                $invoice->status = $request->input('status');
                $invoice->service_type = $request->input('service_type');
                $invoice->invoice_number = 'CU-INV-' . date('Y') . '-' . rand(1000, 9999);
                $invoice->pickup_date = $request->input('pickup_date');
                $invoice->pickup_time = $request->input('pickup_time');
                $invoice->pickup_address = $request->input('pickup_address');
                $invoice->dropoff_address = $request->input('dropoff_address');
                $invoice->created_by = auth()->id();
                $invoice->save();
        
                if ($request->input('add_to_sales')) {
                    // Insert into invoices/payments table
                    $this->addInvoiceToSales($invoice);
                }
        
                // Send email with PDF attachment
                \Mail::to($invoice->email)->send(new \App\Mail\SendWalkinInvoice($invoice));
        
                return redirect()->back()->with('success', 'Custom invoice created successfully.');
            } catch (\Exception $e) {
                Log::error('Error creating custom invoice: ' . $e->getMessage());
                return redirect()->back()->withErrors('Failed to create the invoice.');
            }
        }
        
        private function addInvoiceToSales($invoice)
        {
            $salesInvoice = new Invoice();
            $salesInvoice->walkin_invoice_id = $invoice->id;  // Link with walkin_invoice_id
            $salesInvoice->booking_id = null; // Set to NULL for walk-in invoices
            $salesInvoice->invoice_number = $invoice->invoice_number;
            $salesInvoice->amount = $invoice->amount;
            $salesInvoice->status = $invoice->status;
            $salesInvoice->file_path = '';  // Provide a default value for `file_path`
            $salesInvoice->generated_by = auth()->id(); // Add the current user's ID as the `generated_by`

            $salesInvoice->save();
        
            // Add to Payments table if needed
            $payment = new Payment();
            $payment->booking_id = null; // Correspondingly set booking_id to NULL in payments for walk-ins
            $payment->walkin_invoice_id = $invoice->id;  // Link with walkin_invoice_id
            $payment->amount = $salesInvoice->amount;
            $payment->payment_reference = 'CU-' . random_int(1000, 9999);
            $payment->status = strtolower($invoice->status);
            $payment->payment_method = 'Walk-in'; // Set a default payment method for walk-in payments

            $payment->save();
        }

        // AdminInvoiceController.php

    public function manageCustomInvoices()
    {
        return view('admin.invoices.custom-manage'); // Return the view for managing custom invoices
    }

    public function fetchCustomInvoices()
    {
        try {
            // Fetch data from the walkin_invoices table
            $customInvoices = WalkinInvoice::orderBy('created_at', 'desc')->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'name' => $invoice->name,
                        'email' => $invoice->email,
                        'service_type' => $invoice->service_type,
                        'invoice_number' => $invoice->invoice_number,
                        'amount'=> $invoice->amount,
                        'issue_date' => Carbon::parse($invoice->issue_date)->format('d M, Y'),
                        'status' => $invoice->status,
                    ];
                });

            return response()->json(['data' => $customInvoices]);
        } catch (\Exception $e) {
            Log::error('Error fetching custom invoices: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch custom invoices'], 500);
        }
    }

    public function viewCustomInvoice($id)
    {
        try {
            // Fetch the custom invoice from the walkin_invoices table
            $invoice = WalkinInvoice::findOrFail($id);

            // Return the view with the invoice data
            return view('admin.invoices.custom-view', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error fetching custom invoice: ' . $e->getMessage());
            return redirect()->route('admin.customInvoices')->withErrors('Failed to load the custom invoice.');
        }
    }

    public function downloadCustomInvoice($id)
    {
        try {
            // Fetch the custom invoice from the walkin_invoices table
            $invoice = WalkinInvoice::findOrFail($id);
            
            // Generate the PDF from the view
            $pdf = \PDF::loadView('admin.invoices.walkIn-pdf', compact('invoice'));
            
            // Return the PDF as a download
            return $pdf->download('custom_invoice_' . $invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error downloading custom invoice: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to download the custom invoice.');
        }
    }


    public function editCustomInvoice($id)
    {
        try {
            // Fetch the custom invoice from the walkin_invoices table
            $invoice = WalkinInvoice::findOrFail($id);
            return view('admin.invoices.custom-edit', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error fetching custom invoice for editing: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to fetch the custom invoice for editing.');
        }
    }

    public function updateCustomInvoice(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:Paid,Unpaid,Refunded',
            'service_type' => 'required|string',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
            'pickup_address' => 'required|string',
            'dropoff_address' => 'required|string',
        ]);

        try {
            // Fetch the custom invoice from the walkin_invoices table
            $invoice = WalkinInvoice::findOrFail($id);

            // Update the walkin invoice fields
            $invoice->name = $request->input('name');
            $invoice->email = $request->input('email');
            $invoice->phone = $request->input('phone');
            $invoice->issue_date = $request->input('issue_date');
            $invoice->due_date = $request->input('due_date');
            $invoice->amount = $request->input('amount');
            $invoice->status = $request->input('status');
            $invoice->service_type = $request->input('service_type');
            $invoice->pickup_date = $request->input('pickup_date');
            $invoice->pickup_time = $request->input('pickup_time');
            $invoice->pickup_address = $request->input('pickup_address');
            $invoice->dropoff_address = $request->input('dropoff_address');
            
            // Check if there are corresponding records in invoices and payments tables
            $existingInvoice = \App\Models\Invoice::where('walkin_invoice_id', $invoice->id)->first();
            $existingPayment = \App\Models\Payment::where('walkin_invoice_id', $invoice->id)->first();

            if ($existingInvoice) {
                Log::info('Updating linked invoice with walkin_invoice_id: ' . $invoice->id);
                $existingInvoice->amount = $request->input('amount');
                $existingInvoice->status = $request->input('status');
                $existingInvoice->save();
            }

            if ($existingPayment) {
                Log::info('Updating linked payment with walkin_invoice_id: ' . $invoice->id);
                $existingPayment->amount = $request->input('amount');
                $existingPayment->status = strtolower($request->input('status')); // Ensure status is lowercase
                $existingPayment->save();
            }

            // Save the updated walkin invoice
            $invoice->save();

            Log::info('Custom invoice updated successfully by admin: ' . auth()->user()->email);

            return redirect()->route('admin.customInvoices.edit', $id)->with('success', 'Custom invoice updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating custom invoice: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to update the custom invoice.');
        }
    }

    public function deleteCustomInvoice($id)
    {
        try {
            // Log the incoming request
            Log::info('Attempting to delete custom invoice with ID: ' . $id);
    
            // Find the custom invoice
            $walkinInvoice = WalkinInvoice::findOrFail($id);
    
            // Log before checking related records
            Log::info('Found custom invoice with ID: ' . $id);
    
            // Check if the custom invoice exists in the invoices and payments tables
            $relatedInvoice = Invoice::where('walkin_invoice_id', $walkinInvoice->id)->first();
            $relatedPayment = Payment::where('walkin_invoice_id', $walkinInvoice->id)->first();
    
            // Delete related records if they exist
            if ($relatedInvoice) {
                $relatedInvoice->delete();
                Log::info('Deleted related invoice with ID: ' . $relatedInvoice->id);
            }
            if ($relatedPayment) {
                $relatedPayment->delete();
                Log::info('Deleted related payment with ID: ' . $relatedPayment->id);
            }
    
            // Delete the custom invoice itself
            $walkinInvoice->delete();
            Log::info('Deleted custom invoice with ID: ' . $walkinInvoice->id);
    
            return response()->json(['success' => true, 'message' => 'Custom invoice and related records deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting custom invoice: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete the custom invoice'], 500);
        }
    }
    



        





        


    
}
