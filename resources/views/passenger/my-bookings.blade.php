@extends('layouts.passenger')

@section('title', 'My Trips')

@section('content')
<div class="app-page-title">
    <h1>My Trips / Bookings</h1>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-body">
        <table id="bookings-table" class="table table-striped table-bordered table-responsive">
        <thead>
            <tr>
                <th>Booking Ref</th>
                <th style="display:none;">Raw Booking Date</th> <!-- Hidden raw booking date for sorting -->
                <th>Booking Date</th>
                <th>Service Type</th>
                <th>Pickup Date</th>
                <th>Status</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td>{{ $booking->booking_reference }}</td>
                <td style="display:none;">{{ \Carbon\Carbon::parse($booking->created_at)->format('Y-m-d') }}</td> <!-- Hidden raw date -->
                <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M, Y') }}</td> <!-- Display formatted date -->
                <td>{{ $booking->service_type }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->pickup_date)->format('d M, Y') }}</td>
                <td>
                    <span class="badge 
                        @if(strtolower($booking->status) == 'completed') bg-success
                        @elseif(strtolower($booking->status) == 'pending') bg-warning
                        @elseif(strtolower($booking->status) == 'cancelled') bg-danger
                        @elseif(strtolower($booking->status) == 'expired') bg-secondary
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($booking->updated_at)->format('d M, Y') }}</td>
                <td>
                    <a href="{{ route('booking.edit', $booking->id) }}" 
                        class="btn btn-warning btn-sm {{ in_array(strtolower($booking->status), ['expired', 'cancelled', 'completed']) ? 'disabled' : '' }}" 
                        {{ in_array(strtolower($booking->status), ['expired', 'cancelled', 'completed']) ? 'aria-disabled="true"' : '' }}>
                        Edit
                    </a>
                    <button class="btn btn-danger btn-sm {{ in_array(strtolower($booking->status), ['expired', 'cancelled', 'completed']) ? 'disabled' : '' }}" 
                        {{ in_array(strtolower($booking->status), ['expired', 'cancelled', 'completed']) ? 'aria-disabled="true"' : '' }}
                        data-id="{{ $booking->id }}" type="button">
                        Cancel
                    </button>
                    <a href="{{ route('booking.view', $booking->id) }}" class="btn btn-primary btn-sm">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>

        </table>
    </div>
</div>

<!-- Modal for Booking Cancellation -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" role="dialog" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel">Confirm Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this booking?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" id="confirmCancelButton" class="btn btn-danger">Yes, Cancel Booking</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#bookings-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
            order: [[1, 'desc']], // Use the hidden raw booking date column for sorting
            columnDefs: [
                { targets: 1, visible: false } // Hide the raw date column
            ]
        });

        // Handle booking cancellation
        let bookingIdToCancel = null;

        // Delegate the event handler for the Cancel button click
        $('#bookings-table').on('click', '.btn-danger', function() {
            bookingIdToCancel = $(this).data('id');
            var cancelModal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
            cancelModal.show(); // Show the modal using Bootstrap 5's native modal function
        });

        $('#confirmCancelButton').on('click', function() {
            if (bookingIdToCancel) {
                $(this).prop('disabled', true).html("<i class='fas fa-spinner fa-spin'></i> Cancelling...");
                
                fetch(`/booking/cancel/${bookingIdToCancel}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#cancelBookingModal').modal('hide');
                        location.reload(); // Refresh the page to reflect the cancellation
                    } else {
                        alert('Failed to cancel booking. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error cancelling booking:', error);
                })
                .finally(() => {
                    $('#confirmCancelButton').prop('disabled', false).html('Yes, Cancel Booking');
                });
            }
        });
    });
</script>
@endpush
