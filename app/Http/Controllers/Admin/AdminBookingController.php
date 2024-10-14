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

      
  }