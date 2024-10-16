@extends('admin.layouts.admin-layout')

@section('content')
<style>
    <style>
/* General button styling to add space */
.action-buttons .btn {
    margin-bottom: 5px;
}

/* Responsive design for mobile view */
@media (max-width: 576px) {
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    /* Show only 2 buttons per line */
    .action-buttons .btn {
        flex: 0 0 48%; /* Each button takes 48% of the width to allow two per row */
        margin-right: 2%; /* Add spacing between buttons */
        margin-bottom: 5px; /* Space between rows of buttons */
    }
}
</style>

</style>
<h1 class="app-page-title">Manage Bookings</h1>

<!-- Display Success Message -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Booking Table -->
<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
        <table id="bookings-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Booking Ref</th>
                    <th>Booking Date</th>
                    <th>Service Type</th>
                    <!-- <th>Pickup Date</th> -->
                    <th>Status</th>
                    <th>Created By</th>
                    <!-- <th>Updated At</th> -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $index => $booking)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $booking->booking_reference }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('Y-m-d') }}</td>
                        <td>{{ $booking->service_type }}</td>
                        <!-- <td>
                            <span class="badge bg-{{ $booking->status == 'pending' ? 'warning' : ($booking->status == 'confirmed' ? 'primary' : 'success') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td> -->



                     <td>
                        <span class="badge 
                            {{ $booking->status == 'pending' ? 'bg-warning' : 
                            ($booking->status == 'expired' ? 'bg-secondary' : 
                            ($booking->status == 'confirmed' ? 'bg-info' : 
                            ($booking->status == 'cancelled' ? 'bg-danger' : 
                            ($booking->status == 'completed' ? 'bg-success' : '')))) }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>


                        <td>
                            @if($booking->creator && $booking->creator->roles->isNotEmpty())
                                {{ $booking->creator->roles->pluck('name')->first() }}
                            @else
                                N/A
                            @endif
                        </td>
                        <!-- <td>{{ \Carbon\Carbon::parse($booking->updated_at)->format('Y-m-d') }}</td> -->
                        <td>
                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <a href="{{ route('admin.bookings.view', $booking->id) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                <!-- Only show the Cancel button if the booking is not cancelled -->
                                @if($booking->status != 'cancelled')
                                    <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal" data-booking-id="{{ $booking->id }}">Cancel</a>
                                @endif

                                <a href="#" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#deleteModal" data-booking-id="{{ $booking->id }}">Delete</a>

                                <!-- Show Complete button only if booking is confirmed -->
                                @if ($booking->status == 'confirmed')
                                <button class="btn btn-sm btn-success complete-btn" data-bs-toggle="modal" data-bs-target="#completeModal" data-booking-id="{{ $booking->id }}">Complete</button>

                                    <!-- <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="action" value="complete">
                                        <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                    </form> -->
                                @endif
                            </div>
                        </td>


                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<!-- Cancel Confirmation Modal -->
<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this booking?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Close</button>
                <button type="button" class="btn btn-danger" id="confirmCancelButton" data-booking-id="">Yes, Cancel</button>
            </div>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this booking? All related invoices and payments will also be deleted.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Close</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton" data-booking-id="">
                    <span class="spinner-border spinner-border-sm" id="deleteSpinner" style="display:none;" role="status" aria-hidden="true"></span>
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Confirmation Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeModalLabel">Confirm Completion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure the trip is completed?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmCompleteButton" data-booking-id="">
                    <span class="spinner-border spinner-border-sm" id="completeSpinner" style="display:none;" role="status" aria-hidden="true"></span>
                    Yes, Complete
                </button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>


    <script>
   $(document).ready(function() {
    $('#bookings-table').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        order: [[ 2, 'desc' ]], // Order by Booking Date column (descending)
        columnDefs: [
            { width: '5%', targets: 0 },  // S/N column
            { width: '10%', targets: 1 }, // Booking Ref column
            { width: '10%', targets: 2 }, // Booking Date column
            { width: '10%', targets: 3 }, // Service Type column
            { width: '10%', targets: 4 }, // Status column
            { width: '10%', targets: 5 }, // Created By column
            { width: '35%', targets: 6 }  // Action column (last column)
        ]
    });


         // When the modal is opened, get the booking ID from the cancel button
        $('#cancelModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var bookingId = button.data('booking-id'); // Extract info from data-* attributes
            $('#confirmCancelButton').data('booking-id', bookingId); // Set booking ID to confirm button
        });

        // Handle the confirmation of booking cancellation
        $('#confirmCancelButton').on('click', function() {
            var bookingId = $(this).data('booking-id'); // Get the booking ID

            console.log('Attempting to cancel booking ID:', bookingId);

            // Add spinner to the cancel button
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cancelling...').prop('disabled', true);

            // Make AJAX request to cancel the booking
            $.ajax({
                url: '/admin/bookings/' + bookingId + '/cancel', // Correct route URL
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}' // Include CSRF token
                },
                success: function(response) {
                    console.log('Success response:', response);
                    alert('Booking successfully cancelled.');
                    location.reload(); // Refresh or redirect after successful cancellation
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error response:', xhr);
                    console.log('Error status:', status);
                    console.log('Error responseText:', xhr.responseText);
                    alert('Failed to cancel booking. Please try again.');
                    $('#confirmCancelButton').html('Yes, Cancel').prop('disabled', false);
                }
            });
        });





        //delete modal
        // Show delete modal and pass booking ID
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var bookingId = button.data('booking-id'); // Extract booking ID from data-* attribute

            // Log the booking ID to console for debugging
            console.log('Booking ID for deletion:', bookingId);

            // Attach booking ID to the confirm delete button
            $('#confirmDeleteButton').data('booking-id', bookingId);
        });

        // Handle the confirmation of booking deletion
        $('#confirmDeleteButton').on('click', function() {
            var bookingId = $(this).data('booking-id'); // Get the booking ID from the button's data attribute

            // Log the booking ID to console to ensure it's being passed
            console.log('Confirming deletion for booking ID:', bookingId);

            $('#deleteSpinner').show();
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Deleting...');

            $.ajax({
                    url: '/admin/bookings/' + bookingId + '/delete', 
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Booking deleted successfully.');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Failed to delete booking. Please try again.');
                        $('#deleteSpinner').hide();
                        $('#confirmDeleteButton').prop('disabled', false).text('Yes, Delete');
                    }
                });

        });

        // When the modal is opened, set the booking ID on the 'Yes, Complete' button
        $('#completeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var bookingId = button.data('booking-id'); // Extract booking ID from data-* attributes
            $('#confirmCompleteButton').data('booking-id', bookingId); // Set booking ID to confirm button
        });

        // Handle the completion of booking
        $('#confirmCompleteButton').on('click', function() {
            var bookingId = $(this).data('booking-id'); // Get the booking ID

            // Show spinner and disable button
            $('#completeSpinner').show();
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Completing...');

            // Make AJAX request to complete the booking
            $.ajax({
                url: '/admin/bookings/' + bookingId + '/complete',  // The route to complete the booking
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Booking marked as completed successfully.');
                    location.reload();  // Refresh the page after success
                },
                error: function(xhr) {
                    alert('Failed to complete booking. Please try again.');
                    $('#completeSpinner').hide();  // Hide spinner
                    $('#confirmCompleteButton').prop('disabled', false).html('Yes, Complete');  // Reset button state
                }
            });
        });

    });
    </script>
@endpush
