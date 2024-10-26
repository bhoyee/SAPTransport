<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Mail\BookingConfirmation;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\BookingCancellation;
use App\Mail\BookingCancellationAdminNotification;
// use App\Models\ActivityLogger;
use App\Mail\BookingConfirmationMail;
use App\Models\Payment; 
use App\Services\ActivityLogger; // Correct





class AdminBookingController extends Controller
{
    // Function to show the booking form for someone
    public function showBookingForm()
    {
        return view('admin.bookings.make-booking'); // Adjust the path if necessary
    }

    public function checkUser(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json(['exists' => true, 'user_id' => $user->id]);
        }
        return response()->json(['exists' => false]);
    }

    // Function to handle the booking creation
      // Function to handle the booking creation
      public function store(Request $request)
      {
          Log::info('Booking Data:', $request->all());
      
          // Validation rules based on service_type
          $request->validate([
              'service_type' => 'required',
              'trip_type' => 'required_if:service_type,AirportTransfer,Charter|nullable',
              'airport_name' => 'required_if:service_type,AirportTransfer|nullable',
              'vehicle_type' => 'required|string',
              'pickup_address' => 'nullable|string', // This will handle both AirportTransfer and Charter addresses
              'dropoff_address' => 'nullable|string', // Same for dropoff address
              'pickup_date' => 'required|date',
              'pickup_time' => 'required|date_format:H:i',
              'number_adults' => 'required|integer|min:1',
              'number_children' => 'nullable|integer',
              'return_pickup_date' => 'nullable|date', // Required only for roundtrip Charter
              'return_pickup_time' => 'nullable|date_format:H:i', // Required only for roundtrip Charter
              'name' => 'nullable|string', // For new user registration
              'email' => 'nullable|email', // For new user registration
              'phone' => 'nullable|string', // For new user registration
              'gender' => 'nullable|in:male,female', // For new user registration

          ]);

            // Retrieve the currently authenticated user
            $loggedInUser = Auth::user();
            $loggedInUserEmail = $loggedInUser ? $loggedInUser->email : 'unknown';  // Fallback to 'unknown' if no user is authenticated

            // Log the authenticated user information
            Log::info('Authenticated user:', ['user' => $loggedInUser]);
            
          // Retrieve or create the user
          if ($request->user_id == null) {

                // Generate a random password
                $generatedPassword = Str::random(10);  // 10 character long random password

              // Create a new user if the user doesn't exist
              $user = User::create([
                  'name' => $request->name,
                  'email' => $request->email,
                  'phone' => $request->phone,
                  'gender' => $request->gender,
                  'password' => Hash::make($generatedPassword),  // Use the generated password for Default password for walk-in users
                  'status' => 'active',
                  'created_by' => $loggedInUserEmail, // Track who created the user

              ]);
      
              // Assign the 'passenger' role to the user
              $user->assignRole('passenger');

             // Send the email with login credentials
             Mail::to($user->email)->send(new \App\Mail\UserCreatedNotification($user, $generatedPassword));


              // Send the email verification link
              $user->sendEmailVerificationNotification();

          } else {
              // Retrieve the existing user by ID
              $user = User::find($request->user_id);
          }
      
          // Log booking creation
          \Log::info('Booking creation initiated by admin for user:', ['user_email' => $user->email]);
      
          // Determine the trip type
          $trip_type = $request->trip_type;
      
          // Create the booking for the user
          $booking = Booking::create([
              'user_id' => $user->id,
              'service_type' => $request->service_type,
              'trip_type' => $trip_type,
              'airport_name' => $request->airport_name,
              'vehicle_type' => $request->vehicle_type,
              'pickup_address' => $request->pickup_address,
              'dropoff_address' => $request->dropoff_address,
              'pickup_date' => $request->pickup_date,
              'pickup_time' => $request->pickup_time,
              'return_pickup_date' => $request->return_pickup_date,
              'return_pickup_time' => $request->return_pickup_time,
              'number_adults' => $request->number_adults,
              'number_children' => $request->number_children,
              'status' => 'pending',  // Default status is pending
              'created_by' => $loggedInUserEmail,  // This is where the email of the logged-in user is stored

          ]);
      
          // Log the booking activity
          \Log::info('Booking created successfully for user', ['user_email' => $user->email, 'booking_id' => $booking->id]);
      
          // Send booking confirmation email
          try {
              Mail::to($user->email)->send(new BookingConfirmation($booking, $user, 'pending'));
              \Log::info('Booking confirmation email sent to user', ['user_email' => $user->email]);
          } catch (\Exception $e) {
              \Log::error('Failed to send booking confirmation email', ['error' => $e->getMessage()]);
          }
      
          // Return the response back to the frontend
          return response()->json(['success' => true, 'booking_reference' => $booking->booking_reference]);
      }

      public function manageBookings()
      {
          // Fetch all bookings, load the user who created it and their roles, order by created_at with most recent first
          $bookings = Booking::with(['creator.roles'])->orderBy('created_at', 'desc')->get();
  
          return view('admin.bookings.manage', compact('bookings'));
      }
      
      // fetchBooking in real time
      public function fetchBookings()
      {
          // Load related data (e.g., creator roles) and order by updated_at
          $bookings = Booking::with(['creator.roles'])->orderBy('updated_at', 'desc')->get();
      
          // Map bookings to match the DataTable's expected structure
          $formattedBookings = $bookings->map(function ($booking) {
              return [
                  'id' => $booking->id,
                  'booking_reference' => $booking->booking_reference,
                  'created_at' => $booking->created_at->toDateString(),
                  'updated_at' => $booking->updated_at->toDateString(), // Ensure updated_at is included
                  'service_type' => $booking->service_type,
                  'status' => $booking->status,
                  'created_by' => $booking->creator ? $booking->creator->roles->pluck('name')->first() : 'N/A'
              ];
          });
      
          return response()->json(['data' => $formattedBookings]);
      }
      
    
      public function updateBookingStatus(Request $request, $id)
      {
          // Find the booking by ID
          $booking = Booking::find($id);
  
          // If booking not found, redirect with error
          if (!$booking) {
              return redirect()->route('admin.bookings.manage')->with('error', 'Booking not found.');
          }
  
          // Update status based on the action
          if ($request->action == 'confirm') {
              $booking->status = 'confirmed';
          } elseif ($request->action == 'complete') {
              $booking->status = 'completed';
          } else {
              return redirect()->route('admin.bookings.manage')->with('error', 'Invalid action.');
          }
  
          // Save the updated booking
          $booking->save();
  
          return redirect()->route('admin.bookings.manage')->with('success', 'Booking status updated successfully!');
      }


      public function viewBooking($id)
      {
          // Fetch the booking by ID with associated creator, user, and invoice
          $booking = \App\Models\Booking::with(['creator', 'user', 'invoice'])->findOrFail($id);
      
          return view('admin.bookings.view', compact('booking'));
      }
      

        public function editBooking($id)
        {
            // Fetch the booking by ID
            $booking = \App\Models\Booking::findOrFail($id);

            return view('admin.bookings.edit', compact('booking'));
        }
     
        public function cancelBooking($id)
        {
            try {
                // Find the booking by ID
                $booking = \App\Models\Booking::findOrFail($id);
        
                // Update the booking status to 'cancelled'
                $booking->status = 'cancelled';
                $booking->save();
        
                // Log the cancellation activity with the user who canceled it
                ActivityLogger::log('Booking Cancelled', 'Booking ID ' . $booking->id . ' was cancelled by user: ' . auth()->user()->email);
        
                // Fetch the user who made the booking
                $user = \App\Models\User::find($booking->user_id); // Assuming user_id references the booking owner
        
                // Send cancellation email to the user
                try {
                    Mail::to($user->email)->send(new BookingCancellation($booking, $user));
                } catch (\Exception $e) {
                    \Log::error('Failed to send cancellation email to user: ' . $e->getMessage());
                }
        
                // Send cancellation email to the admin
                try {
                    $adminEmail = config('mail.admin_email');  // Fetch admin email from config
                    $adminConsultantUsers = auth()->user();  // Assuming the currently authenticated user is admin/consultant
                    Mail::to($adminEmail)->send(new BookingCancellationAdminNotification($booking, $adminConsultantUsers));
                } catch (\Exception $e) {
                    \Log::error('Failed to send cancellation email to admin: ' . $e->getMessage());
                }
        
                // Redirect back with a success message
                return response()->json(['success' => true], 200);
        
            } catch (\Exception $e) {
                // Log the error and return a failure response
                \Log::error('Error cancelling booking: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to cancel booking. Please try again.'], 500);
            }
        }
        
        
        public function updateBooking(Request $request, $id)
        {
            try {
                $booking = \App\Models\Booking::findOrFail($id);
        
                // Merge to format time to 'H:i' before validation
                $request->merge([
                    'pickup_time' => \Carbon\Carbon::parse($request->pickup_time)->format('H:i'),
                    'return_pickup_time' => $request->return_pickup_time ? \Carbon\Carbon::parse($request->return_pickup_time)->format('H:i') : null,
                ]);
        
                // Validate the form inputs
                $request->validate([
                    'service_type' => 'required|string|in:AirportTransfer,Charter',
                    'trip_type' => 'required|string|in:airport_pickup,airport_dropoff,oneway,round_trip',
                    'airport_name' => 'nullable|string',
                    'vehicle_type' => 'required|string',
                    'pickup_address' => 'nullable|string|max:255',
                    'dropoff_address' => 'nullable|string|max:255',
                    'pickup_date' => 'required|date',
                    'pickup_time' => 'required|date_format:H:i',  // Using 'H:i' format here
                    'number_adults' => 'required|integer|min:1',
                    'number_children' => 'nullable|integer|min:0',
                    'return_pickup_date' => 'nullable|date',
                    'return_pickup_time' => 'nullable|date_format:H:i',  // Using 'H:i' format here
                    'status' => 'nullable|string|in:pending,expired,confirmed,cancelled,completed', // Validate the status



                ]);
        
                // Handle nullification of pickup and dropoff address based on trip type
                $pickup_address = $request->input('pickup_address');
                $dropoff_address = $request->input('dropoff_address');
        
                if ($request->input('trip_type') === 'airport_pickup') {
                    $pickup_address = null; // Clear pickup address for airport pickup
                } elseif ($request->input('trip_type') === 'airport_dropoff') {
                    $dropoff_address = null; // Clear dropoff address for airport drop-off
                }
        
                // Update the booking with the new values
                $booking->update([
                    'service_type' => $request->input('service_type'),
                    'trip_type' => $request->input('trip_type'),
                    'airport_name' => $request->input('airport_name'),
                    'vehicle_type' => $request->input('vehicle_type'),
                    'pickup_address' => $pickup_address,
                    'dropoff_address' => $dropoff_address,
                    'pickup_date' => $request->input('pickup_date'),
                    'pickup_time' => $request->input('pickup_time'),  // Time without seconds
                    'number_adults' => $request->input('number_adults'),
                    'number_children' => $request->input('number_children'),
                    'return_pickup_date' => $request->input('return_pickup_date'),
                    'return_pickup_time' => $request->input('return_pickup_time'),  // Time without seconds
                    'updated_by' => auth()->user()->email, // Track who updated the booking
                    'status' => $request->input('status'),  // Update the booking status

                ]);

                        // Log the user activity using ActivityLogger
                    \App\Services\ActivityLogger::log(
                        'Booking Updated', 
                        'Booking Reference: ' . $booking->booking_reference . ' updated by user: ' . auth()->user()->email
                    );

        
                \Log::info('Booking updated successfully', ['booking_id' => $booking->id]);
        
                return redirect()->route('admin.bookings.manage')->with('success', 'Booking updated successfully.');
            } catch (\Exception $e) {
                \Log::error('Error updating booking: ' . $e->getMessage());
        
                return redirect()->back()->with('error', 'Failed to update booking. Please try again.');
            }
        }


        // AdminBookingController.php
    
        public function searchBooking(Request $request)
        {
            // Check if the booking reference has been submitted via the search form
            if ($request->has('booking_ref')) {
                // Validate the form input
                $request->validate([
                    'booking_ref' => 'required|string',
                ]);

                // Find the booking by reference (no need to filter by status here)
                $booking = \App\Models\Booking::where('booking_reference', $request->booking_ref)->first();

                // If booking is not found, return with an error
                if (!$booking) {
                    return redirect()->route('admin.bookings.confirm-search')->with('error', 'Booking not found.');
                }

                // Pass the booking details to the view
                return view('admin.bookings.confirm-booking', compact('booking'));
            }

            // If no booking_ref is provided, just show the search form
            return view('admin.bookings.confirm-booking');
        }

    public function confirmBooking(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        try {
            // Find the booking by ID
            $booking = \App\Models\Booking::findOrFail($id);

            // Check if the booking status is already 'confirmed' or another final state
            if ($booking->status !== 'pending') {
                return redirect()->back()->with('error', 'This booking is not pending and cannot be confirmed.');
            }

            // Update the booking status to 'confirmed'
            $booking->status = 'confirmed';
            $booking->save();

            // Generate the invoice number (e.g., INV-2024-001)
            $year = now()->year;
            $latestInvoice = \App\Models\Invoice::whereYear('invoice_date', $year)->latest('id')->first();
            $invoiceNumber = 'INV-' . $year . '-' . str_pad(($latestInvoice ? $latestInvoice->id + 1 : 1), 3, '0', STR_PAD_LEFT);

            // Create an invoice
            $invoice = new \App\Models\Invoice();
            $invoice->booking_id = $booking->id;
            $invoice->generated_by = auth()->id();
            $invoice->invoice_number = $invoiceNumber;
            $invoice->invoice_date = now();
            $invoice->amount = $request->price;
            $invoice->status = 'Unpaid';
            $invoice->file_path = '/path/to/invoice_' . $invoiceNumber . '.pdf'; // Update this with actual file path
            $invoice->save();

            // Log the activity
            \DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'Booking Confirmed',
                'description' => 'Booking ' . $booking->booking_reference . ' was confirmed and invoice generated.',
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send confirmation email to the owner of the booking (optional)
            $user = $booking->user;
            Mail::to($user->email)->send(new BookingConfirmationMail($booking, $invoice));


            return redirect()->route('admin.bookings.confirm-search')->with('success', 'Booking confirmed and invoice generated.');
        } catch (\Exception $e) {
            \Log::error('Error confirming booking: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to confirm the booking. Please try again.');
        }
    }



        public function deleteBooking($id)
        {
            try {
                \Log::info('Attempting to delete booking with ID: ' . $id);
        
                // Find the booking
                $booking = Booking::findOrFail($id);
                \Log::info('Booking found: ' . $booking->booking_reference);
        
                // Initialize log description
                $logDescription = 'Deleted booking with reference: ' . $booking->booking_reference;
        
                // Check if the booking has an invoice, and delete it if exists
                if ($booking->invoice) {
                    \Log::info('Invoice found for booking: ' . $booking->invoice->invoice_number);
                    $logDescription .= '. Deleted invoice number: ' . $booking->invoice->invoice_number;
                    $booking->invoice()->delete();
                    \Log::info('Invoice deleted successfully.');
                } else {
                    \Log::info('No invoice found for this booking.');
                }
        
                // Check if there are any payments associated with the booking and delete them
                $payments = Payment::where('booking_id', $booking->id)->get();
                if ($payments->isNotEmpty()) {
                    \Log::info('Payments found for booking ID: ' . $booking->id);
                    Payment::where('booking_id', $booking->id)->delete();
                    $logDescription .= '. Deleted ' . $payments->count() . ' payment(s) associated with the booking.';
                    \Log::info('Payments deleted successfully.');
                } else {
                    \Log::info('No payments found for this booking.');
                }
        
                // Delete the booking itself
                $booking->delete();
                \Log::info('Booking deleted successfully.');
        
                // Log the activity
                \DB::table('activity_logs')->insert([
                    'user_id' => auth()->id(), // Log the current user
                    'action' => 'Booking Deleted',
                    'description' => $logDescription, // Include booking, invoice, and payment details
                    'ip_address' => request()->ip(), // Get the user's IP address
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        
                return response()->json(['success' => true], 200);
            } catch (\Exception $e) {
                \Log::error('Error deleting booking: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to delete booking. Please try again.'], 500);
            }
        }
        
        
        public function completeBooking($id)
        {
            try {
                $booking = \App\Models\Booking::findOrFail($id);
        
                // Update booking status to completed
                $booking->status = 'completed';
                $booking->save();
        
                // Log the completion activity
                \App\Services\ActivityLogger::log(
                    'Booking Completed', 
                    'Booking ID ' . $booking->id . ' was marked as completed by ' . auth()->user()->email,
                    auth()->user()->id  // Log the user ID who completed the booking
                );
        
                return response()->json(['success' => true], 200);
            } catch (\Exception $e) {
                \Log::error('Error completing booking: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to complete booking. Please try again.'], 500);
            }
        }
        

        
        
      
  }