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
use App\Models\Setting;
use App\Mail\BookingUpdateMail;

class BookingController extends Controller
{
    // public function store(Request $request)
    // {
    //     // Check if the user is logged in
    //     \Log::info('BookingController@store invoked.');

    // // Check if the user is logged in
    // if (!Auth::check()) {
    //     \Log::info('User not authenticated.');
    //     return response()->json([
    //         'success' => false,
    //         'error' => 'You need to login before booking a trip.',
    //     ], 401); // Use appropriate HTTP status code
    // }
    
        
    //     $user = Auth::user();
    
    //     // Check if the user has the 'passenger' role and if their email is verified

    //     if ($user->hasRole('passenger') && $user->email_verified_at === null) {
    //         \Log::info('User needs to verify their email before booking.');
    //         return redirect()->route('verification.notice')
    //             ->with('error', 'You need to verify your email before booking a trip.');
    //     }
    
    //     // Validate the form inputs
    //     $request->validate([
    //         'service_type' => 'required',
    //         'trip_type' => 'required',
    //         'airport_name' => 'nullable|string',
    //         'vehicle_type' => 'required|string',
    //         'pickup_address' => 'nullable|string',
    //         'dropoff_address' => 'nullable|string',
    //         'pickup_date' => 'nullable|date',
    //         'pickup_time' => 'nullable',
    //         'number_adults' => 'required|integer|min:1',
    //         'number_children' => 'nullable|integer',
    //         'return_pickup_date' => 'nullable|date',  // Only for round trip
    //         'return_pickup_time' => 'nullable'        // Only for round trip
    //     ]);
    
    //     \Log::info('BookingController@store invoked.');
    
    //     // Create the booking record
    //     $booking = Booking::create([
    //         'user_id' => Auth::id(),
    //         'service_type' => $request->input('service_type'),
    //         'trip_type' => $request->input('trip_type'),
    //         'status' => 'pending',
    //         'airport_name' => $request->input('airport_name'),
    //         'vehicle_type' => $request->input('vehicle_type'),
    //         'pickup_address' => $request->input('pickup_address'),
    //         'dropoff_address' => $request->input('dropoff_address'),
    //         'pickup_date' => $request->input('pickup_date'),
    //         'pickup_time' => $request->input('pickup_time'),
    //         'return_pickup_date' => $request->input('return_pickup_date'),
    //         'return_pickup_time' => $request->input('return_pickup_time'),
    //         'number_adults' => $request->input('number_adults'),
    //         'number_children' => $request->input('number_children'),
    //         'created_by' => $user->email, // Add logged-in user's email to created_by field
    //     ]);
    
    //     // Return response immediately to avoid blocking the user while processing other tasks
    //     $response = response()->json(['success' => true, 'booking_reference' => $booking->booking_reference]);
    
    //     // Use register_shutdown_function to handle email, logging, and notifications after the response is sent
    //     register_shutdown_function(function () use ($user, $booking) {
    //         // Log the booking activity
    //         ActivityLogger::log('Booking Created', 'Booking created for user: ' . $user->email . ', Booking Reference: ' . $booking->booking_reference);
    
    //         \Log::info('Attempting to send email to: ' . $user->email);
    
    //         // Send the confirmation email to the user
    //         try {
    //             Mail::to($user->email)->send(new BookingConfirmation($booking, $user, $booking->status));
    //             \Log::info('Email sent successfully.');
    //         } catch (\Exception $e) {
    //             \Log::error('Failed to send email: ' . $e->getMessage());
    //         }

    //         // Send notification email to admin
    //         try {
    //             $adminEmail = config('mail.admin_email'); // Assuming admin email is defined in .env
    //             if ($adminEmail) {
    //                 Mail::to($adminEmail)->send(new BookingAdminNotification($booking, $user));
    //                 \Log::info('Email sent successfully to admin.');
    //             } else {
    //                 \Log::warning('Admin email not set in .env.');
    //             }
    //         } catch (\Exception $e) {
    //             \Log::error('Failed to send email to admin: ' . $e->getMessage());
    //         }
    
    //         // Log the successful booking completion
    //         ActivityLogger::log('Booking Completed', 'Booking successfully completed for user: ' . $user->email);
    
    //         // Push notifications to all admin and consultant users
    //         $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
    //         foreach ($adminConsultantUsers as $adminConsultant) {
    //             Notification::create([
    //                 'user_id' => $adminConsultant->id,
    //                 'message' => 'A new booking has been made by ' . $user->name . '. Booking Reference: ' . $booking->booking_reference,
    //                 'type' => 'booking',
    //                 'status' => 'unread',
    //                 'related_user_name' => $user->name,
    //             ]);
    //         }
    //     });
    
    //     // Return the response immediately
    //     return $response;
    // }
    

    public function store(Request $request)
{
    // Check if the user is logged in
    \Log::info('BookingController@store invoked.');

    // Check if the user is logged in
    if (!Auth::check()) {
        \Log::info('User not authenticated.');
        return response()->json([
            'success' => false,
            'error' => 'You need to login before booking a trip.',
        ], 401); // Use appropriate HTTP status code
    }
    
    $user = Auth::user();

    // Check if the user has the 'passenger' role and if their email is verified
    if ($user->hasRole('passenger') && $user->email_verified_at === null) {
        \Log::info('User needs to verify their email before booking.');
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
        'created_by' => $user->email, // Add logged-in user's email to created_by field
    ]);

    // Return response immediately to avoid blocking the user while processing other tasks
    $response = response()->json(['success' => true, 'booking_reference' => $booking->booking_reference]);

    // Use register_shutdown_function to handle email, logging, and notifications after the response is sent
    register_shutdown_function(function () use ($user, $booking) {
        // Log the booking activity
        ActivityLogger::log('Booking Created', 'Booking created for user: ' . $user->email . ', Booking Reference: ' . $booking->booking_reference);

        \Log::info('Attempting to send email to: ' . $user->email);

        // Send the confirmation email to the user
        try {
            Mail::to($user->email)->send(new BookingConfirmation($booking, $user, $booking->status));
            \Log::info('Email sent successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        // Send notification email to admin
        try {
            $adminEmail = config('mail.admin_email'); // Assuming admin email is defined in .env
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new BookingAdminNotification($booking, $user));
                \Log::info('Email sent successfully to admin.');
            } else {
                \Log::warning('Admin email not set in .env.');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send email to admin: ' . $e->getMessage());
        }

        // Log the successful booking completion
        ActivityLogger::log('Booking Completed', 'Booking successfully completed for user: ' . $user->email);

        // Push notifications to all admin and consultant users
        $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
        foreach ($adminConsultantUsers as $adminConsultant) {
            Notification::create([
                'user_id' => $adminConsultant->id,
                'message' => 'A new booking has been made by ' . $user->name . '. Booking Reference: ' . $booking->booking_reference,
                'type' => 'booking',
                'status' => 'unread',
                'related_user_name' => $user->name,
            ]);
        }
    });

    // Return the response immediately
    return $response;
}

    public function checkStatus(Request $request)
    {
        \Log::info('checkStatus method called.');

        $bookingReference = trim($request->input('booking_reference'));
        \Log::info('Booking reference entered: ' . $bookingReference);

        if (empty($bookingReference)) {
            \Log::error('No booking reference provided.');
            return response()->json([
                'status' => 'error',
                'message' => 'Booking reference is required.'
            ]);
        }

        // Attempt to fetch the booking using the booking reference
        $booking = Booking::where('booking_reference', $bookingReference)->first();

        if ($booking) {
            \Log::info('Booking found: ' . json_encode($booking));
            return response()->json([
                'status' => 'success',
                'booking_reference' => $booking->booking_reference,
                'service_type' => $booking->service_type,
                'status' => $booking->status,
                'date' => $booking->pickup_date,
                'vehicle_type' => $booking->vehicle_type,
            ]);
        } else {
            \Log::info('No booking found with reference: ' . $bookingReference);
            return response()->json([
                'status' => 'error',
                'message' => 'No booking found with that reference number.'
            ]);
        }
    }

    
    
    public function cancelBooking(Request $request, $id)
    {
        \Log::info("Cancel request received for booking ID: {$id}, method: {$request->method()}");
    
        try {
            // Find the booking by its ID
            $booking = Booking::findOrFail($id);
    
            // Log the current status of the booking
            \Log::info("Booking found with status: {$booking->status}");
    
            // Check if the booking is cancelable (only pending status can be cancelled)
            if ($booking->status !== 'pending') {
                \Log::info("Booking cannot be cancelled, current status is: {$booking->status}");
                return response()->json(['error' => 'Booking cannot be cancelled.'], 400);
            }
    
            // Update the booking status to cancelled
            $booking->update(['status' => 'cancelled']);
    
            // Log the success of the status update
            \Log::info("Booking ID: {$id} status updated to cancelled");
    
            // Log the user activity for cancellation
            ActivityLogger::log('Booking Cancelled', 'Booking cancelled by user: ' . auth()->user()->email . ', Booking Reference: ' . $booking->booking_reference);
    
            $user = auth()->user(); // Get the authenticated user
    
            // Send cancellation email to the user
            try {
                Mail::to($user->email)->send(new BookingCancellation($booking, $user));
                \Log::info("Cancellation email sent to user: {$user->email}");
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation email to user: ' . $e->getMessage());
            }
    
            // Notify admin and consultants about the cancellation
            $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
            foreach ($adminConsultantUsers as $adminConsultant) {
                Notification::create([
                    'user_id' => $adminConsultant->id,
                    'message' => 'Booking cancelled by ' . $user->name . '. Booking Reference: ' . $booking->booking_reference,
                    'type' => 'booking',
                    'status' => 'unread',
                    'related_user_name' => $user->name,
                ]);
                \Log::info("Notification sent to admin/consultant ID: {$adminConsultant->id}");
            }
    
            // Send cancellation email to admin using config-based admin email
            try {
                $adminEmail = config('mail.admin_email');  // Fetch email from config
                Mail::to($adminEmail)->send(new BookingCancellationAdminNotification($booking, $adminConsultantUsers));
                \Log::info("Cancellation email sent to admin: {$adminEmail}");
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation email to admin: ' . $e->getMessage());
            }
    
            return response()->json(['success' => true, 'message' => 'Booking cancelled successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error cancelling booking: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while cancelling the booking.'], 500);
        }
    }
    
    

    public function myBookings(Request $request)
    {

        $userId = auth()->id();

        // Log user ID
        Log::info('Fetching bookings for user', ['user_id' => $userId]);

        try {
            // Fetch bookings and order by 'created_at' in descending order (most recent first)
            $bookings = Booking::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

            // Log the number of bookings fetched
            Log::info('Bookings fetched', ['booking_count' => $bookings->count()]);

            // If this is an AJAX request, return JSON data for DataTables
            if ($request->ajax()) {
                Log::info('AJAX request detected, returning JSON data for DataTables');

                // Log the bookings being returned
                Log::info('Returning bookings data', ['bookings' => $bookings->toArray()]);

                return response()->json(['data' => $bookings]);
            }

            // Log that it is a non-AJAX request
            Log::info('Non-AJAX request, returning view with bookings');

            // Otherwise, return the view
            return view('passenger.my-bookings', compact('bookings'));

        } catch (\Exception $e) {
            // Log any errors that occur
            Log::error('Error fetching bookings', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to load data'], 500);
        }
    }

    // getting booking button status 
    public function getBookingStatus()
    {
        $setting = Setting::where('key', 'booking_status')->first();
        if ($setting) {
            return response()->json(['status' => $setting->value]);
        }
        return response()->json(['status' => 'unknown'], 404);
    }
    

    public function showAssignDriverPage()
    {
        // Simply load the view without any booking dependency
        return view('admin.bookings.assign-driver'); 
    }
    
    public function searchBookingByReference(Request $request)
    {
        $bookingRef = $request->input('booking_ref');
        $booking = Booking::where('booking_reference', $bookingRef)->first();
    
        if (!$booking) {
            return redirect()->route('admin.bookings.assign-driver')->with('error', 'Booking not found.');
        }
    
        // Pass the booking to the assign-driver view
        return view('admin.bookings.assign-driver', compact('booking'));
    }
    

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_name' => 'required|string|max:255',
            'vehicle_details' => 'required|string|max:255',
        ]);
    
        $booking = Booking::findOrFail($id);
    
        // Update booking with driver information
        $booking->update([
            'driver_name' => $request->driver_name,
            'vehicle_details' => $request->vehicle_details,
        ]);


         // Set a session flash message to indicate that the driver was just assigned
        session()->flash('driver_assigned', true);
    
        // Notify the passenger and admins
        $passenger = $booking->user;
        Mail::to($passenger->email)->send(new BookingUpdateMail($booking));

                // Create push notification for the passenger
                Notification::create([
                    'user_id' => $passenger->id,
                    'message' => 'Your booking (Reference: ' . $booking->booking_reference . ') has been updated with driver details. Driver: ' . $request->driver_name . ', Vehicle: ' . $request->vehicle_details,
                    'type' => 'booking',
                    'status' => 'unread',
                    'related_user_name' => $request->driver_name,
                ]);
    
        // Notify all admins
        $adminUsers = User::role(['admin'])->get();
        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'message' => 'Booking (Reference: ' . $booking->booking_reference . ') has been assigned to driver ' . $request->driver_name . ' with vehicle details: ' . $request->vehicle_details,
                'type' => 'booking',
                'status' => 'unread',
                'related_user_name' => $request->driver_name,
            ]);
            \Log::info("Notification sent to admin ID: {$admin->id}");
        }
    
        return redirect()->route('admin.bookings.assign-driver-search', ['booking_ref' => $booking->booking_reference])
            ->with('success', 'Driver assigned and passenger notified successfully.');
    }
    



}
