@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

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
                        <p>{{ ucfirst(str_replace('_', ' ', $booking->service_type)) }}</p>
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
                        <strong>Booking Status:</strong>
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

                                <!-- Updated at -->
                                @if (!empty($booking->updated_at))
                    <div class="col-md-6">
                        <strong>Updated At:</strong>
                        <p>{{ \Carbon\Carbon::parse($booking->updated_at)->format('d M, Y H:i') }}</p>
                    </div>
                @endif


                   <!-- Creator name -->

                @if (!empty($booking->created_by) && $booking->creator)
                    <div class="col-md-6">
                        <strong>Booked By:</strong>
                        <p>{{ $booking->creator->name }}</p>
                    </div>
                @endif


                <!-- Updated By (User who made updates) -->
                @if (!empty($booking->updater))
                    <div class="col-md-6">
                        <strong>Updated By:</strong>
                        <p>{{ $booking->updater->name }}</p> <!-- Assuming 'name' is a field in the User model -->
                    </div>
                @endif



            </div>
                       <!-- Existing Driver Name and Vehicle Details Fields (Only Visible if Data Exists) -->
                       @if ($booking->driver_name && $booking->vehicle_details)
                    <div class="form-group">
                    <h4 class="mb-3">Driver Information</h4>
                    <strong>Driver's Name:</strong>
                    <p>{{ $booking->driver_name}}</p>

                    </div>
                    <div class="col-md-6">
                    <strong>Vehicle Details:</strong>
                    <p>{{ $booking->vehicle_details}}</p>
                </div>


                @endif

            <!-- Invoice and Payment Details -->
            <div class="row gx-5 gy-3">
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
                <div class="col-md-6">
                    <strong>Invoice Status:</strong>
                    <p>{{ $booking->invoice->status}}</p>
                </div>
            @endif

            </div>

            <div class="mt-4">
            <a href="{{ route('admin.bookings.manage') }}" class="btn btn-secondary mb-2">Back to Manage Bookings</a>
            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-warning">Edit Booking</a>

            </div>

        </div>
    </div>
@endsection
