<?php



namespace App\Http\Controllers;



use App\Models\Booking;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;

use App\Mail\BookingConfirmation;

use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Services\ActivityLogger;
use App\Models\Notification;
use App\Mail\BookingAdminNotification; 
use App\Mail\BookingCancellationAdminNotification;
use App\Mail\BookingCancellation;



class BookingController extends Controller

{

    public function store(Request $request)
    {
        // Check if the user is logged in
        if (!Auth::check()) {
            return back()->with('error', 'You need to login before booking a trip.');
        }
    
        // Check if the user's email is verified
        if (Auth::user()->email_verified_at === null) {
            return redirect()->route('verification.notice')
                ->with('error', 'You need to verify your email before booking a trip.');
        }
    
        // Validate the form inputs
        $request->validate([
            'service_type' => 'required',
            'trip_type' => 'required',
            'airport_name' => 'nullable|string',
            'vehicle_type' => 'required|string',
            'pickup_address' => 'nullable|string',
            'dropoff_address' => 'nullable|string',
            'pickup_date' => 'nullable|date',
            'pickup_time' => 'nullable',
            'number_adults' => 'required|integer|min:1',
            'number_children' => 'nullable|integer',
            'return_pickup_date' => 'nullable|date',  // Only for round trip
            'return_pickup_time' => 'nullable'        // Only for round trip
        ]);
    
        \Log::info('BookingController@store invoked.');
    
        // Create the booking record
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_type' => $request->input('service_type'),
            'trip_type' => $request->input('trip_type'),
            'status' => 'pending',
            'airport_name' => $request->input('airport_name'),
            'vehicle_type' => $request->input('vehicle_type'),
            'pickup_address' => $request->input('pickup_address'),
            'dropoff_address' => $request->input('dropoff_address'),
            'pickup_date' => $request->input('pickup_date'),
            'pickup_time' => $request->input('pickup_time'),
            'return_pickup_date' => $request->input('return_pickup_date'),
            'return_pickup_time' => $request->input('return_pickup_time'),
            'number_adults' => $request->input('number_adults'),
            'number_children' => $request->input('number_children'),
        ]);
    
        \Log::info('Booking created successfully.', ['booking_id' => $booking->id]);
    
        // Log the booking activity
        ActivityLogger::log('Booking Created', 'Booking created for user: ' . Auth::user()->email . ', Booking Reference: ' . $booking->booking_reference);
    
        $user = Auth::user(); // Get the authenticated user
    
        \Log::info('Attempting to send email to: ' . $user->email);
    
        // Send the confirmation email
        try {
            $status = $booking->status;
            Mail::to($user->email)->send(new BookingConfirmation($booking, $user, $status));
            \Log::info('Email sent successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }
    
        // Log the successful booking completion
        ActivityLogger::log('Booking Completed', 'Booking successfully completed for user: ' . Auth::user()->email);
    
        // Push notification to all admin and consultant users
        $adminConsultantUsers = User::whereIn('role', ['admin', 'consultant'])->get();
        foreach ($adminConsultantUsers as $adminConsultant) {
            Notification::create([
                'user_id' => $adminConsultant->id,
                'message' => 'A new booking has been made by ' . Auth::user()->name . '. Booking Reference: ' . $booking->booking_reference,
                'type' => 'booking',
                'status' => 'unread',
                'related_user_name' => Auth::user()->name, 

            ]);
        }

    // Send an email notification to the admin email
        try {
            $adminEmail = config('mail.admin_email');  // Fetch email from config
            Mail::to($adminEmail)->send(new BookingAdminNotification($booking));
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification to admin: ' . $e->getMessage());
        }
    
        // Send an email notification to the admin email
        // try {
        //     Mail::to('no-reply@saptransportationandlogistics.ng')->send(new BookingAdminNotification($booking));
        //     \Log::info('Email notification sent to admin.');
        // } catch (\Exception $e) {
        //     \Log::error('Failed to send email notification to admin: ' . $e->getMessage());
        // }
    
        // Return to the same page with success message and booking reference
        return back()->with(['success' => 'Booking successfully completed!', 'booking_reference' => $booking->booking_reference]);
    }
    
    

    public function checkStatus(Request $request)

    {

        $bookingReference = trim($request->input('booking_reference'));

        

        \Log::info('Booking reference entered: ' . $bookingReference);

    

        // Search for the booking in the database using the reference number

        $booking = Booking::where('booking_reference', $bookingReference)->first();

    

        if ($booking) {

            \Log::info('Booking found: ' . json_encode($booking));

    

            return response()->json([

                'status' => 'success',

                'booking_reference' => $booking->booking_reference,

                'service_type' => $booking->service_type,

                'status' => $booking->status,

                'date' => $booking->pickup_date, // or dropoff_date based on logic

                'vehicle_type' => $booking->vehicle_type,

            ]);

        } else {

            \Log::error('No booking found with reference: ' . $bookingReference);

    

            return response()->json([

                'status' => 'error',

                'message' => 'No booking found with that reference number.'

            ]);

        }

    }

// public function getRecentBookings()
// {
//     try {
//         // Fetch recent bookings for the logged-in user
//         $bookings = Booking::where('user_id', auth()->id())
//             ->orderBy('created_at', 'desc')
//             ->take(10) // Limit the number of recent bookings fetched
//             ->get();

//         return response()->json($bookings);
//     } catch (\Exception $e) {
//         // Log the error
//         \Log::error('Error fetching recent bookings: ' . $e->getMessage());
        
//         // Return a 500 response with error message
//         return response()->json(['error' => 'An error occurred while fetching recent bookings.'], 500);
//     }
// }

    public function cancelBooking(Request $request, $id)
    {
        try {
            // Find the booking by its ID
            $booking = Booking::findOrFail($id);
    
            // Check if the booking is cancelable (only pending status can be cancelled)
            if ($booking->status !== 'pending') {
                return response()->json(['error' => 'Booking cannot be cancelled.'], 400);
            }
    
            // Update the booking status to cancelled
            $booking->update(['status' => 'cancelled']);
    
            // Log the user activity for cancellation
            ActivityLogger::log('Booking Cancelled', 'Booking cancelled by user: ' . auth()->user()->email . ', Booking Reference: ' . $booking->booking_reference);
    
            $user = auth()->user(); // Get the authenticated user
    
            // Send cancellation email to the user
            try {
                Mail::to($user->email)->send(new BookingCancellation($booking, $user));
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation email to user: ' . $e->getMessage());
            }
    
            // Notify admin and consultants about the cancellation
            $adminConsultantUsers = User::whereIn('role', ['admin', 'consultant'])->get();
            foreach ($adminConsultantUsers as $adminConsultant) {
                Notification::create([
                    'user_id' => $adminConsultant->id,
                    'message' => 'Booking cancelled by ' . $user->name . '. Booking Reference: ' . $booking->booking_reference,
                    'type' => 'booking',
                    'status' => 'unread',
                    'related_user_name' => $user->name,
                ]);
            }
    
            // Send cancellation email to admin
            // try {
            //     Mail::to('no-reply@saptransportationandlogistics.ng')->send(new BookingCancellationAdminNotification($booking, $adminConsultantUsers));
            // } catch (\Exception $e) {
            //     \Log::error('Failed to send cancellation email to admin: ' . $e->getMessage());
            // }
            // Send cancellation email to admin using config-based admin email
            try {
                $adminEmail = config('mail.admin_email');  // Fetch email from config
                Mail::to($adminEmail)->send(new BookingCancellationAdminNotification($booking, $adminConsultantUsers));
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation email to admin: ' . $e->getMessage());
            }
        
            return response()->json(['success' => true, 'message' => 'Booking cancelled successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error cancelling booking: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while cancelling the booking.'], 500);
        }
    }


    public function myBookings()
    {
        $userId = auth()->id();
    
        // Fetch bookings and order by 'created_at' in descending order (most recent first)
        $bookings = Booking::where('user_id', $userId)
                    ->orderBy('created_at', 'desc') // Add the orderBy clause
                    ->get();
    
        return view('passenger.my-bookings', compact('bookings'));
    }
    

}

