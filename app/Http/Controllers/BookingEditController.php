<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;

class BookingEditController extends Controller
{
    // Show the edit form with the current booking details
    public function edit($id)
    {
        try {
            \Log::info("Edit method accessed for booking ID: " . $id);

            // Fetch the booking based on the ID
            $booking = Booking::findOrFail($id);

            // Normalize service_type and trip_type to match the front-end values
            if ($booking->service_type == 'Charter Service') {
                $booking->service_type = 'Charter';
            } elseif ($booking->service_type == 'Airport Transfer') {
                $booking->service_type = 'AirportTransfer';
            }

            if ($booking->trip_type == 'Round Trip') {
                $booking->trip_type = 'round_trip';
            } elseif ($booking->trip_type == 'One Way') {
                $booking->trip_type = 'oneway';
            } elseif ($booking->trip_type == 'Airport Pickup') {
                $booking->trip_type = 'airport_pickup';
            } elseif ($booking->trip_type == 'Airport Drop-Off') {
                $booking->trip_type = 'airport_dropoff';
            }

            // Check if the authenticated user is the owner of the booking
            if ($booking->user_id !== Auth::id()) {
                return redirect()->route('passenger.dashboard')->with('error', 'Unauthorized access.');
            }

            // Log the booking data to verify everything is being passed correctly
            Log::info('Booking data for edit:', $booking->toArray());

            // Point to the correct view in the 'passenger' folder
            return view('passenger.editbooking', compact('booking'));
        } catch (\Exception $e) {
            Log::error('Error fetching booking for editing: ' . $e->getMessage());
            return redirect()->route('passenger.dashboard')->with('error', 'Unable to load booking details. Please try again later.');
        }
    }

    // Handle the update request
    public function update(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
    
            // Check if the authenticated user is the owner of the booking
            if ($booking->user_id !== Auth::id()) {
                return redirect()->route('passenger.dashboard')->with('error', 'Unauthorized access.');
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
                'return_pickup_time' => 'nullable',        // Only for round trip
    
                // New fields validation
                'security_coverage' => 'required|in:yes,no',
                'mobile_police_count' => 'nullable|integer|min:2|max:10',
                'with_van' => 'nullable|in:yes,no',
            ]);
    
            // Handle nullification of pickup and dropoff address based on trip type
            $pickup_address = $request->input('pickup_address');
            $dropoff_address = $request->input('dropoff_address');
    
            if ($request->input('service_type') === 'AirportTransfer') {
                if ($request->input('trip_type') === 'airport_pickup') {
                    // Nullify pickup address when trip type is "Airport Pickup"
                    $pickup_address = null;
                } elseif ($request->input('trip_type') === 'airport_dropoff') {
                    // Nullify dropoff address when trip type is "Airport Drop-Off"
                    $dropoff_address = null;
                }
            }
    
            // Check if the security coverage is being changed to 'no'
            $securityCoverageChangedToNo = $booking->security_coverage === 'yes' && $request->input('security_coverage') === 'no';
    
            // If security_coverage is being set to 'no', reset mobile_police_count and with_van to null
            $mobile_police_count = $securityCoverageChangedToNo ? null : $request->input('mobile_police_count');
            $with_van = $securityCoverageChangedToNo ? null : $request->input('with_van');
    
            // Update the booking with the new values
            $booking->update([
                'service_type' => $request->input('service_type'),
                'trip_type' => $request->input('trip_type'),
                'airport_name' => $request->input('airport_name'),
                'vehicle_type' => $request->input('vehicle_type'),
                'pickup_address' => $pickup_address,
                'dropoff_address' => $dropoff_address,
                'pickup_date' => $request->input('pickup_date'),
                'pickup_time' => $request->input('pickup_time'),
                'number_adults' => $request->input('number_adults'),
                'number_children' => $request->input('number_children'),
                'return_pickup_date' => $request->input('return_pickup_date'),
                'return_pickup_time' => $request->input('return_pickup_time'),
                'security_coverage' => $request->input('security_coverage'),
                'mobile_police_count' => $mobile_police_count,
                'with_van' => $with_van,
                'updated_by' => auth()->user()->email, // Track who updated the booking
            ]);
    
            // Log the user activity after successful update
            ActivityLogger::log('Booking Updated', 'Booking ID ' . $booking->id . ' updated by user: ' . auth()->user()->email);
    
            // Flash success message
            return redirect()->route('booking.edit', $booking->id)->with('success', 'Booking updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating booking: ' . $e->getMessage());
    
            // Flash error message if something goes wrong
            return redirect()->route('booking.edit', $id)->with('error', 'Failed to update booking. Please try again.');
        }
    }
    

    // Show booking details
    public function show($id, Request $request)
    {
        try {

            \Log::info("Show method accessed for booking ID: " . $id);

            // Fetch the booking with the related invoice
            $booking = Booking::with('invoice')->findOrFail($id);

            // Check if the 'from' parameter is set in the request
            $from = $request->query('from', null);

            // Pass the 'booking' and 'from' parameters to the view
            return view('passenger.details', compact('booking', 'from'));
        } catch (\Exception $e) {
            Log::error('Error fetching booking details: ' . $e->getMessage());
            return redirect()->route('passenger.dashboard')->with('error', 'Unable to load booking details. Please try again later.');
        }
    }
}
