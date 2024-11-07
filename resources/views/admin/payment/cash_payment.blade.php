@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('title', 'Record Cash Payment')

@section('content')
    <h1 class="app-page-title">Record Cash Payment</h1>

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.payment.cash') }}" id="search-form">
            @csrf
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

    <!-- Display Booking Details or Booking Not Found Message -->
    <div id="result-section">
        @if(session('error'))
            <div class="alert alert-danger mt-4 alert-dismissible fade show" role="alert" id="error-message">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success mt-4 alert-dismissible fade show" role="alert" id="success-message">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

      
        @if(isset($booking) && isset($invoice) && !session('error'))
    <h3>Booking Details</h3>
    <div class="table-responsive" id="booking-details-section">
        <table class="table table-bordered">
            <tr><th>Booking Reference</th><td>{{ $booking->booking_reference }}</td></tr>
            <tr><th>Booker's Name</th><td>{{ $booking->user->name ?? 'N/A' }}</td></tr>
            <tr><th>Booker's Phone</th><td>{{ $booking->user->phone ?? 'N/A' }}</td></tr>
            <tr><th>Service Type</th><td>{{ $booking->service_type }}</td></tr>
            <tr><th>Trip Type</th><td>{{ $booking->trip_type }}</td></tr>
            <tr><th>Pickup Date</th><td>{{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M, Y') }}</td></tr>
            <tr><th>Invoice Number</th><td>{{ $invoice->invoice_number }}</td></tr>
            <tr><th>Amount</th><td><strong>₦{{ number_format($invoice->amount, 2) }}</strong></td></tr>
            <tr><th>Invoice Status</th><td><strong>{{ ucfirst($invoice->status) }}</strong></td></tr>
        </table>
    </div>

    <!-- Record Cash Payment Form -->
    @if($invoice->status !== 'paid' && !$existingPayment)
    <form method="POST" action="{{ route('admin.payment.cash.update') }}">
        @csrf
        <input type="hidden" name="booking_ref" value="{{ $booking->booking_reference }}">
        <input type="hidden" name="invoice_number" value="{{ $invoice->invoice_number }}">
        <input type="hidden" name="amount" value="{{ $invoice->amount }}">
        <div class="form-group mb-3">
            <label for="payment_date">Payment Date:</label>
            <input type="date" id="payment_date" name="payment_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success btn-lg">
            Pay by Cash
            <span class="spinner-border spinner-border-sm" id="pay-spinner" style="display:none;" role="status" aria-hidden="true"></span>
        </button>
    </form>
@else
    <div class="alert alert-success">
        <strong>Payment Completed:</strong> The invoice has already been paid.
    </div>
@endif


@endif

    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const searchForm = document.getElementById('search-form');
    const searchButton = document.getElementById('search-btn');
    const searchSpinner = document.getElementById('search-spinner');
    const resultSection = document.getElementById('result-section');

    // Handle search form submission
    searchForm.addEventListener('submit', function(event) {
        // Clear previous results
        resultSection.innerHTML = ''; // Clear previous booking details or error message

        // Show spinner and disable button
        searchSpinner.style.display = 'inline-block';
        searchButton.disabled = true;
        searchButton.innerHTML = 'Searching...';
    });

    // Handle "Pay by Cash" button spinner
    $('form').on('submit', function(event) {
        const paySpinner = $('#pay-spinner');
        const payButton = $(this).find('button[type="submit"]');

        // Show spinner and disable the button
        paySpinner.show();
        payButton.prop('disabled', true);
        payButton.html('Processing...');
    });
});
</script>
@endpush
