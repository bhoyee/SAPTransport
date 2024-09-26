@extends('layouts.passenger')

@section('title', 'Booking Details')

@section('content')
    <h1 class="app-page-title">Booking Details - {{ $booking->booking_reference }}</h1>

    <div class="app-card app-card-details shadow-sm mb-4">
        <div class="app-card-body p-4">
            <h3 class="mb-3">Booking Information</h3>
            <div class="row gx-5 gy-3">
                <div class="col-md-6">
                    <strong>Service Type:</strong>
                    <p>{{ $booking->service_type }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Booking Date:</strong>
                    <p>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M, Y') }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Vehicle Type:</strong>
                    <p>{{ $booking->vehicle_type }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong>
                    <p>{{ $booking->status }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Pickup Address:</strong>
                    <p>{{ $booking->pickup_address }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Dropoff Address:</strong>
                    <p>{{ $booking->dropoff_address }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Pickup Date:</strong>
                    <p>{{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M, Y') }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Pickup Time:</strong>
                    <p>{{ $booking->pickup_time }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Number of Adults:</strong>
                    <p>{{ $booking->number_adults }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Number of Children:</strong>
                    <p>{{ $booking->number_children }}</p>
                </div>
                <!-- Add more fields as needed -->
            </div>

            <div class="mt-4">
                <a href="{{ route('booking.edit', $booking->id) }}" class="btn btn-warning">Edit Booking</a>
                <a href="{{ route('passenger.dashboard') }}" class="btn btn-secondary">Close</a>
            </div>
        </div>
    </div>
@endsection
