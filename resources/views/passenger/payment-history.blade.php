@extends('layouts.passenger')

@section('title', 'Payment History')

@section('content')
<div class="app-page-title">
    <h1>Payment History</h1>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-body">
        <table id="payment-history-table" class="table table-striped table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Booking Ref</th>
                    <th>Invoice Num</th>
                    <th>Booking Date</th>
                    <th>Service Type</th>
                    <th>Amount</th> <!-- Add the Amount header -->
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- The body will be filled dynamically via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundModalLabel">Refund Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to request a refund for this booking?
            </div>
            <div class="modal-footer">
                <form method="POST" id="refundForm" action="{{ route('payment.refund') }}">
                    @csrf
                    <input type="hidden" name="payment_id" id="refundPaymentId">

                    <!-- Cancel Button -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <!-- Confirm Refund Button with Spinner -->
                    <button type="submit" id="confirmRefundButton" class="btn btn-warning">
                        <span class="spinner-border spinner-border-sm d-none" id="refundSpinner" role="status" aria-hidden="true"></span>
                        Confirm Refund
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Include Moment.js for date formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize the DataTable with AJAX source
        let table = $('#payment-history-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
            ajax: {
                url: "{{ route('payment.history') }}", // Route that returns the JSON data
                type: 'GET',
                dataSrc: 'data', // Data source is the 'data' array in the JSON response
            },
            columns: [
                { data: 'booking.booking_reference' }, // Booking reference
                { data: 'booking.invoice.invoice_number', defaultContent: 'N/A' }, // Invoice number, default to 'N/A'
                { data: 'booking.created_at', render: function(data) { 
                    return moment(data).format('D MMM, YYYY'); // Format the date using moment.js
                }},
                { data: 'booking.service_type' }, // Service type
                { data: 'amount', render: function(data) {
                    return '₦' + parseFloat(data).toLocaleString(); // Format the amount with commas
                }},
                { data: 'status', render: function(data) {
                    let badgeClass = data === 'paid' ? 'success' : data === 'refund-pending' ? 'warning' : data === 'refunded' ? 'info' : 'danger';
                    return `<span class="badge bg-${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }},
                { 
                    data: null, 
                    render: function(data, type, row) {
                        // Create the URL dynamically in JavaScript for the View button
                        let viewUrl = `/passenger/booking/${row.booking.id}/view`; // Adjust URL as needed
                        let actions = `
                            <a href="${viewUrl}" class="btn btn-primary btn-sm">View</a>
                        `;

                        if (row.status === 'paid') {
                            actions += `<button class="btn btn-danger btn-sm" onclick="showRefundModal('${row.id}')">Request Refund</button>`;
                        }
                        return actions;
                    }
                }
            ]

        });

        // Function to show the refund modal
        window.showRefundModal = function(paymentId) {
            $('#refundPaymentId').val(paymentId);
            $('#refundModal').modal('show');
        }

        // Show spinner and disable the Confirm Refund button on form submit
        $('#refundForm').on('submit', function() {
            $('#confirmRefundButton').prop('disabled', true); // Disable the button
            $('#refundSpinner').removeClass('d-none'); // Show the spinner
        });

        // Optional: Poll the table to reload every 30 seconds to ensure real-time updates
        setInterval(function() {
            table.ajax.reload(null, false); // false to keep current paging
        }, 30000); // Reload every 30 seconds
    });
</script>
@endpush
