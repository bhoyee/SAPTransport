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
    let bookingIdToCancel = null;

    function fetchAndRenderBookings() {
        $.ajax({
            url: "{{ route('my-bookings') }}",
            type: 'GET',
            success: function(response) {
                if (response && response.data) {
                    // Clear and destroy existing DataTable instance for a fresh render
                    $('#bookings-table').DataTable().clear().destroy();
                    $('#bookings-table tbody').empty();

                    let bookings = response.data.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));

                    bookings.forEach(function(booking, index) {
                        let bookingDate = moment(booking.created_at).format('D MMM, YYYY');
                        let pickupDate = moment(booking.pickup_date).format('D MMM, YYYY');
                        let updatedDate = moment(booking.updated_at).format('D MMM, YYYY');

                        let badgeClass = booking.status === 'completed' ? 'success' :
                                         booking.status === 'pending' ? 'warning' :
                                         booking.status === 'cancelled' ? 'danger' :
                                         booking.status === 'confirmed' ? 'info' :
                                         booking.status === 'expired' ? 'secondary' : 'primary';

                        // Calculate real-time conditions
                        const now = new Date();
                        const pickupDateTime = new Date(`${booking.pickup_date}T${booking.pickup_time}`);
                        const within24Hours = (pickupDateTime - now) <= (24 * 60 * 60 * 1000);
                        const isPending = booking.status.toLowerCase() === 'pending';
                        const isConfirmed = booking.status.toLowerCase() === 'confirmed';

                        // Determine action button states
                        let editDisabled = isConfirmed || !isPending;
                        let cancelDisabled = !(isPending || (isConfirmed && !within24Hours));

                        // Construct action buttons with real-time conditions
                        let actionButtons = `
                            <a href="/passenger/booking/${booking.id}/edit" class="btn btn-warning btn-sm ${editDisabled ? 'disabled' : ''}" ${editDisabled ? 'aria-disabled="true"' : ''}>Edit</a>
                            <a href="/passenger/booking/${booking.id}/view" class="btn btn-primary btn-sm">View</a>
                            <button class="btn btn-danger btn-sm ${cancelDisabled ? 'disabled' : ''}" data-id="${booking.id}" type="button" ${cancelDisabled ? 'aria-disabled="true"' : ''}>Cancel</button>
                        `;

                        // Construct the table row HTML
                        let rowHtml = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${booking.booking_reference}</td>
                                <td>${bookingDate}</td>
                                <td>${booking.service_type}</td>
                                <td>${pickupDate}</td>
                                <td><span class="badge bg-${badgeClass}">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</span></td>
                                <td>${updatedDate}</td>
                                <td>${actionButtons}</td>
                            </tr>`;

                        $('#bookings-table tbody').append(rowHtml);
                    });

                    // Reinitialize DataTable to refresh the table with updated data and buttons
                    $('#bookings-table').DataTable({
                        responsive: true,
                        paging: true,
                        searching: true,
                        ordering: false,
                        lengthChange: true,
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching data:', textStatus, errorThrown);
            }
        });
    }

    // Fetch and render bookings initially
    fetchAndRenderBookings();

    // Refresh bookings every minute for real-time updates
    setInterval(fetchAndRenderBookings, 60000); // 60,000 ms = 1 minute

    // Handle booking cancellation button click
    $('#bookings-table').on('click', '.btn-danger', function() {
        bookingIdToCancel = $(this).data('id');
        var cancelModal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
        cancelModal.show();
    });

    // Confirm cancellation action
    $('#confirmCancelButton').on('click', function() {
        if (bookingIdToCancel) {
            $(this).prop('disabled', true).html("<i class='fas fa-spinner fa-spin'></i> Cancelling...");

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
                if (data.success) {
                    $('#cancelBookingModal').modal('hide');
                    fetchAndRenderBookings(); // Refresh bookings list after cancellation
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

