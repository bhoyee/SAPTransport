@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('title', 'Confirm Booking')

@section('content')
    <h1 class="app-page-title">Confirm Booking</h1>

    <!-- Booking Search Form -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.bookings.confirm-search') }}" id="search-form">
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

    <!-- Display booking details and form if booking is found -->
    @if(isset($booking))
        <h3>Booking Details</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>Booking Reference</th>
                        <td>{{ $booking->booking_reference }}</td>
                    </tr>
                    <tr>
                        <th>Booker's Name</th>
                        <td>{{ $booking->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Booker's Phone</th>
                        <td>{{ $booking->user->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Service Type</th>
                        <td>{{ $booking->service_type }}</td>
                    </tr>
                    <tr>
                        <th>Trip Type</th>
                        <td>{{ $booking->trip_type }}</td>
                    </tr>
                    <tr>
                        <th>Pickup Date</th>
                        <td>{{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst($booking->status) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Only show price input if the booking is pending -->
        @if($booking->status == 'pending')
        <form method="POST" action="{{ route('admin.bookings.confirm', $booking->id) }}" id="confirm-booking-form">
            @csrf
            <div class="form-group my-3">
                <label for="price" style="font-weight: bold;">Enter Price</label>
                <input type="number" name="price" id="price" class="form-control" placeholder="Enter cost of trip (e.g 5000)" required>
            </div>
            <button type="submit" class="btn btn-primary" id="confirm-btn">
                Confirm Booking
                <span class="spinner-border spinner-border-sm" id="confirm-spinner" style="display:none;" role="status" aria-hidden="true"></span>
            </button>
        </form>

        @else
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error! </strong> This booking is not pending and cannot be confirmed.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    @endif

    <!-- Success and error messages with close icons -->
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
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    console.log('JavaScript running and loaded.');

    // Handle form submission with spinner for search
    const searchForm = document.getElementById('search-form');
    const searchButton = document.getElementById('search-btn');
    const searchSpinner = document.getElementById('search-spinner');

    searchForm.addEventListener('submit', function (event) {
        searchSpinner.style.display = 'inline-block'; // Show the spinner
        searchButton.disabled = true; // Disable the button to prevent multiple submissions
        searchButton.innerHTML = 'Searching...'; // Change button text
    });

    // Handle form submission with spinner for confirm booking
    const confirmForm = document.getElementById('confirm-booking-form');
    const confirmButton = document.getElementById('confirm-btn');
    const confirmSpinner = document.getElementById('confirm-spinner');

    if (confirmForm) {
        confirmForm.addEventListener('submit', function (event) {
            confirmSpinner.style.display = 'inline-block'; // Show the spinner
            confirmButton.disabled = true; // Disable the button to prevent multiple submissions
            confirmButton.innerHTML = 'Confirming...'; // Change button text
        });
    }
</script>
@endpush
