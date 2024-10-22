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
                <th>S/N</th>
                <th>Booking Ref</th>
                <th>Booking Date</th>
                <th>Service Type</th>
                <th>Pickup Date</th>
                <th>Status</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Table rows will be appended here via JS -->
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
<!-- Include moment.js from a CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
$(document).ready(function() {
    let bookingIdToCancel = null; // To store the ID of the booking to be canceled

    // Manually fetch the data and render the table
    $.ajax({
        url: "{{ route('my-bookings') }}",
        type: 'GET',
        success: function(response) {
            console.log('Received response:', response);

            if (response && response.data) {
                // Clear the table before appending data
                $('#bookings-table tbody').empty();

                // Sort bookings by updated_at in descending order (most recent first)
                let bookings = response.data.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));

                // Loop through the sorted bookings data and construct table rows
                bookings.forEach(function(booking, index) {
                    // Format dates using moment.js
                    let bookingDate = moment(booking.created_at).format('D MMM, YYYY');
                    let pickupDate = moment(booking.pickup_date).format('D MMM, YYYY');
                    let updatedDate = moment(booking.updated_at).format('D MMM, YYYY');

                    // Define status badge
                    let badgeClass = booking.status === 'completed' ? 'success' :
                                     booking.status === 'pending' ? 'warning' :
                                     booking.status === 'cancelled' ? 'danger' :
                                     booking.status === 'expired' ? 'secondary' : 'primary';

                    // Construct the action buttons
                    let actionButtons = `
                        <a href="/passenger/booking/${booking.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                        <a href="/passenger/booking/${booking.id}/view" class="btn btn-primary btn-sm">View</a>
                    `;

                    // Only show the Cancel button if the status is "pending"
                    if (booking.status === 'pending') {
                        actionButtons += `<button class="btn btn-danger btn-sm" data-id="${booking.id}" type="button" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">Cancel</button>`;
                    }

                    // Construct the table row HTML with S/N and Action buttons
                    let rowHtml = `
                        <tr>
                            <td>${index + 1}</td> <!-- Serial Number based on updated_at ordering -->
                            <td>${booking.booking_reference}</td>
                            <td>${bookingDate}</td>
                            <td>${booking.service_type}</td>
                            <td>${pickupDate}</td>
                            <td><span class="badge bg-${badgeClass}">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</span></td>
                            <td>${updatedDate}</td>
                            <td>${actionButtons}</td> <!-- Action Buttons (Edit, View, and Cancel) -->
                        </tr>`;

                    // Append the row to the table body
                    $('#bookings-table tbody').append(rowHtml);
                });

                // Initialize DataTable after data is appended, and sort by updated_at
                $('#bookings-table').DataTable({
                    responsive: true,
                    paging: true,
                    searching: true,
                    ordering: false,  // Disable DataTable's internal ordering since we are manually sorting
                    lengthChange: true,
                });
            } else {
                console.warn('No data found in the response');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching data:', textStatus, errorThrown);
        }
    });

    // Handle booking cancellation button click
    $('#bookings-table').on('click', '.btn-danger', function() {
        bookingIdToCancel = $(this).data('id'); // Get the booking ID from the button's data-id attribute
        var cancelModal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
        cancelModal.show(); // Show the modal using Bootstrap 5's native modal function
    });

    // Confirm cancellation action
    $('#confirmCancelButton').on('click', function() {
        if (bookingIdToCancel) {
            $(this).prop('disabled', true).html("<i class='fas fa-spinner fa-spin'></i> Cancelling...");
            
            // Make the POST request to cancel the booking
            fetch(`/passenger/booking/cancel/${bookingIdToCancel}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response was not ok: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received response:', data);
                if (data.success) {
                    $('#cancelBookingModal').modal('hide');
                    location.reload(); // Refresh the page to reflect the cancellation
                } else {
                    console.error('Failed to cancel booking:', data);
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
