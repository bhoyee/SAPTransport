@extends('admin.layouts.admin-layout')

@section('title', 'Manage Payments')

@section('content')
<h1 class="app-page-title">Manage Payments</h1>

<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
        <table id="payments-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Booking Ref</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Payment Mtd</th>
                    <th>Payment Ref</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td> <!-- Serial Number -->
                        <td>{{ $payment->booking->booking_reference ?? 'N/A' }}</td>
                        <td>₦{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            @if($payment->status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($payment->status === 'unpaid')
                                <span class="badge bg-danger">Unpaid</span>
                            @elseif($payment->status === 'refund-pending')
                                <span class="badge bg-warning">Refund Pending</span>
                            @elseif($payment->status === 'refunded')
                                <span class="badge bg-info">Refunded</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                        <td>{{ ucfirst($payment->payment_method) }}</td>
                        <td>{{ $payment->payment_reference }}</td>

                        <td>
    @if($payment->status === 'refund-pending')
        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#refundModal" data-id="{{ $payment->id }}">
            Process Refund
        </button>
    @elseif($payment->status === 'refunded')
        <button class="btn btn-info btn-sm" disabled>Refunded</button>
    @else
        <span>No Actions</span>
    @endif
</td>


          
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Refund Confirmation Modal -->
<!-- Refund Confirmation Modal -->
<!-- Refund Confirmation Modal -->
<!-- Refund Confirmation Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundModalLabel">Confirm Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Do you want to process the refund?</p>
                <input type="hidden" id="refund-payment-id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="decline-refund">
                    Decline Refund
                    <span class="spinner-border spinner-border-sm" id="decline-refund-spinner" style="display:none;" role="status" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-primary" id="confirm-refund">
                    Yes, Process Refund
                    <span class="spinner-border spinner-border-sm" id="confirm-refund-spinner" style="display:none;" role="status" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-warning" id="refund-cash">
                    Refund Cash
                    <span class="spinner-border spinner-border-sm" id="refund-cash-spinner" style="display:none;" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')

<script>
$(document).ready(function() {
    $('#payments-table').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        order: [[5, 'desc']] // Orders by payment date (descending)
    });

    // Modal triggers
    $('#refundModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var paymentId = button.data('id'); // Extract payment ID from data-id attribute
        $('#refund-payment-id').val(paymentId); // Set the hidden field value
    });

    // Confirm Refund with Spinner
    $('#confirm-refund').on('click', function() {
        var paymentId = $('#refund-payment-id').val();
        var refundButton = $(this);
        var refundSpinner = $('#confirm-refund-spinner');
        
        // Show spinner and disable the button
        refundSpinner.show();
        refundButton.prop('disabled', true);
        
        processRefund(paymentId, refundButton, refundSpinner); // Process refund
    });

    // Decline Refund with Spinner
    $('#decline-refund').on('click', function() {
        var paymentId = $('#refund-payment-id').val();
        var declineButton = $(this);
        var declineSpinner = $('#decline-refund-spinner');

        // Show spinner and disable the button
        declineSpinner.show();
        declineButton.prop('disabled', true);

        declineRefund(paymentId, declineButton, declineSpinner); // Decline refund
    });

    // Refund Cash with Spinner
    $('#refund-cash').on('click', function() {
        var paymentId = $('#refund-payment-id').val();
        var refundCashButton = $(this);
        var refundCashSpinner = $('#refund-cash-spinner');

        // Show spinner and disable the button
        refundCashSpinner.show();
        refundCashButton.prop('disabled', true);

        refundCash(paymentId, refundCashButton, refundCashSpinner); // Refund cash
    });

    // Function to process refund via AJAX
    function processRefund(paymentId, refundButton, refundSpinner) {
        $.ajax({
            url: "{{ url('admin/payments') }}/" + paymentId + "/refund", // Refund route using URL helper
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Reload the page to update the status
                } else {
                    alert('Failed to process refund: ' + response.message);
                }
                // Reset button and spinner
                refundSpinner.hide();
                refundButton.prop('disabled', false);
            },
            error: function(xhr) {
                var responseText = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.';
                alert('An error occurred: ' + responseText);
                // Reset button and spinner
                refundSpinner.hide();
                refundButton.prop('disabled', false);
            }
        });
    }

    // Function to decline refund via AJAX
    function declineRefund(paymentId, declineButton, declineSpinner) {
        $.ajax({
            url: "{{ url('admin/payments') }}/" + paymentId + "/refund/decline", // Route for declining refund
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Refund process declined successfully.');
                    location.reload(); // Reload the page to update the status
                } else {
                    alert('Failed to decline refund: ' + response.message);
                }
                // Reset button and spinner
                declineSpinner.hide();
                declineButton.prop('disabled', false);
            },
            error: function(xhr) {
                var responseText = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.';
                alert('An error occurred: ' + responseText);
                // Reset button and spinner
                declineSpinner.hide();
                declineButton.prop('disabled', false);
            }
        });
    }

    // Function to process cash refund via AJAX
    function refundCash(paymentId, refundCashButton, refundCashSpinner) {
        $.ajax({
            url: "{{ url('admin/payments') }}/" + paymentId + "/refund/cash", // Correct route for refunding cash payments
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Cash payment refunded successfully.');
                    location.reload(); // Reload the page to update the status
                } else {
                    alert('Failed to refund cash payment: ' + response.message);
                }
                // Reset button and spinner
                refundCashSpinner.hide();
                refundCashButton.prop('disabled', false);
            },
            error: function(xhr) {
                var responseText = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.';
                alert('An error occurred: ' + responseText);
                // Reset button and spinner
                refundCashSpinner.hide();
                refundCashButton.prop('disabled', false);
            }
        });
    }
});
</script>

@endpush
