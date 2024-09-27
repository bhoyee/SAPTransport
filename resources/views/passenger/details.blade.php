@extends('layouts.passenger')

@section('title', 'Booking Details')

@section('content')
    <h1 class="app-page-title">Booking Details - {{ $booking->booking_reference }}</h1>

    <div class="app-card app-card-details shadow-sm mb-4">
        <div class="app-card-body p-4">
            <h3 class="mb-3">Booking Information</h3>
            <div class="row gx-5 gy-3">
                
                <!-- Service Type -->
                @if (!empty($booking->service_type))
                    <div class="col-md-6">
                        <strong>Service Type:</strong>
                        <p>{{ $booking->service_type }}</p>
                    </div>
                @endif

                <!-- Airport -->
                @if (!empty($booking->airport_name))
                    <div class="col-md-6">
                        <strong>Airport:</strong>
                        <p>{{ $booking->airport_name }}</p>
                    </div>
                @endif

                <!-- Booking Date -->
                @if (!empty($booking->created_at))
                    <div class="col-md-6">
                        <strong>Booking Date:</strong>
                        <p>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M, Y') }}</p>
                    </div>
                @endif

                <!-- Vehicle Type -->
                @if (!empty($booking->vehicle_type))
                    <div class="col-md-6">
                        <strong>Vehicle Type:</strong>
                        <p>{{ $booking->vehicle_type }}</p>
                    </div>
                @endif

                <!-- Status -->
                @if (!empty($booking->status))
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p>{{ $booking->status }}</p>
                    </div>
                @endif

                <!-- Pickup Address -->
                @if (!empty($booking->pickup_address))
                    <div class="col-md-6">
                        <strong>Pickup Address:</strong>
                        <p>{{ $booking->pickup_address }}</p>
                    </div>
                @endif

                <!-- Dropoff Address -->
                @if (!empty($booking->dropoff_address))
                    <div class="col-md-6">
                        <strong>Dropoff Address:</strong>
                        <p>{{ $booking->dropoff_address }}</p>
                    </div>
                @endif

                <!-- Pickup Date -->
                @if (!empty($booking->pickup_date))
                    <div class="col-md-6">
                        <strong>Pickup Date:</strong>
                        <p>{{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M, Y') }}</p>
                    </div>
                @endif

                <!-- Pickup Time -->
                @if (!empty($booking->pickup_time))
                    <div class="col-md-6">
                        <strong>Pickup Time:</strong>
                        <p>{{ $booking->pickup_time }}</p>
                    </div>
                @endif

                <!-- Number of Adults -->
                @if (!empty($booking->number_adults))
                    <div class="col-md-6">
                        <strong>Number of Adults:</strong>
                        <p>{{ $booking->number_adults }}</p>
                    </div>
                @endif

                <!-- Number of Children -->
                @if (!empty($booking->number_children))
                    <div class="col-md-6">
                        <strong>Number of Children:</strong>
                        <p>{{ $booking->number_children }}</p>
                    </div>
                @endif

                <!-- Return Pickup Date -->
                @if (!empty($booking->return_pickup_date))
                    <div class="col-md-6">
                        <strong>Return Pickup Date:</strong>
                        <p>{{ $booking->return_pickup_date }}</p>
                    </div>
                @endif

                <!-- Return Pickup Time -->
                @if (!empty($booking->return_pickup_time))
                    <div class="col-md-6">
                        <strong>Return Pickup Time:</strong>
                        <p>{{ $booking->return_pickup_time }}</p>
                    </div>
                @endif

                <!-- More fields can be added in the same manner -->
            </div>

            <div class="row gx-5 gy-3">
                <!-- Existing Booking Details -->

                <!-- Invoice Details -->
                @if ($booking->invoice)
                    <h3 class="mb-3">Payment Information</h3>
                    <div class="col-md-6">
                        <strong>Invoice Number:</strong>
                        <p>{{ $booking->invoice->invoice_number }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Invoice Date:</strong>
                        <p>{{ \Carbon\Carbon::parse($booking->invoice->invoice_date)->format('d M, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Due Amount:</strong>
                        <p>₦{{ number_format($booking->invoice->amount, 2) }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-4">
                @if ($from === 'makepayments')
                    <!-- Use the invoice for Pay Now button -->
                    @if ($booking->invoice)
                        <a href="{{ route('payment.pay', $booking->invoice->id) }}" class="btn btn-success">Pay Now</a>
                        <a href="{{ route('passenger.invoice', $booking->invoice->id) }}" class="btn btn-info">View Invoice</a>
                    @endif

                    <a href="{{ route('passenger.makepayments') }}" class="btn btn-secondary">Close</a>
                @else
                    <a href="{{ route('booking.edit', $booking->id) }}" class="btn btn-warning">Edit Booking</a>
                    <a href="{{ route('passenger.dashboard') }}" class="btn btn-secondary">Close</a>
                @endif
            </div>

        </div>
    </div>
@endsection
