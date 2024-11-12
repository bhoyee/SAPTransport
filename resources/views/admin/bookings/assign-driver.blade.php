@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('title', 'Assign Driver to Booking')

@section('content')
    <h1 class="app-page-title">Assign Driver to Booking</h1>

    <!-- Booking Search Form -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.bookings.assign-driver-search') }}" id="search-form">
            <div class="form-group mb-3">
                <label for="booking-ref">Enter Booking Reference:</label>
                <input type="text" name="booking_ref" id="booking-ref" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" id="search-btn">
                Search
                <span class="spinner-border spinner-border-sm" id="search-spinner" style="display:none;" role="status" aria-hidden="true"></span>
            </button>
        </form>
    </div>

    <!-- Success and error messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Display booking details and form if booking is found -->
    @if(isset($booking))
        <h3>Booking Details</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr><th>Booking Reference</th><td>{{ $booking->booking_reference }}</td></tr>
                    <tr><th>Booker's Name</th><td>{{ $booking->user->name ?? 'N/A' }}</td></tr>
                    <tr><th>Booker's Phone</th><td>{{ $booking->user->phone ?? 'N/A' }}</td></tr>
                    <tr><th>Service Type</th><td>{{ $booking->service_type }}</td></tr>
                    <tr><th>Pickup Date</th><td>{{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M, Y') }}</td></tr>
                    <tr><th>Status</th><td>{{ ucfirst($booking->status) }}</td></tr>

                    <!-- Assigned Driver Section -->
                    <tr><th>Assigned Driver</th>
                        <td>
                            @if($booking->driver_name && $booking->vehicle_details)
                                <strong>Driver:</strong> {{ $booking->driver_name }}<br>
                                <strong>Vehicle:</strong> {{ $booking->vehicle_details }}
                            @else
                                <em>No driver assigned yet.</em>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Alert if driver is already assigned, and this is not a new assignment -->
        @if($booking->driver_name && $booking->vehicle_details && !session('driver_assigned'))
            <div class="alert alert-info mt-3">
                <strong>Note:</strong> A driver has already been assigned to this booking (Reference: {{ $booking->booking_reference }}).
            </div>
        @endif

        <!-- Driver Assignment Form - Only show if no driver is assigned -->
        @if(!$booking->driver_name && !$booking->vehicle_details)
            <form method="POST" action="{{ route('admin.bookings.assign-driver.update', ['id' => $booking->id]) }}" id="assign-driver-form">
                @csrf
                <div class="form-group my-3">
                    <label for="driver_name">Driver's Full Name</label>
                    <input type="text" name="driver_name" id="driver_name" class="form-control" placeholder="Enter driver's full name" required>
                </div>
                <div class="form-group my-3">
                    <label for="vehicle_details">Vehicle Details</label>
                    <input type="text" name="vehicle_details" id="vehicle_details" class="form-control" placeholder="Enter vehicle details" required>
                </div>
                <button type="submit" class="btn btn-primary" id="assign-btn">
                    Update Booking
                    <span class="spinner-border spinner-border-sm" id="assign-spinner" style="display:none;" role="status" aria-hidden="true"></span>
                </button>
            </form>
        @endif
    @endif
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const searchForm = document.getElementById('search-form');
    const searchButton = document.getElementById('search-btn');
    const searchSpinner = document.getElementById('search-spinner');

    searchForm.addEventListener('submit', function () {
        searchSpinner.style.display = 'inline-block';
        searchButton.disabled = true;
        searchButton.innerHTML = 'Searching...';
    });

    const assignForm = document.getElementById('assign-driver-form');
    const assignButton = document.getElementById('assign-btn');
    const assignSpinner = document.getElementById('assign-spinner');

    if (assignForm) {
        assignForm.addEventListener('submit', function () {
            assignSpinner.style.display = 'inline-block';
            assignButton.disabled = true;
            assignButton.innerHTML = 'Updating...';
        });
    }
</script>
@endpush
