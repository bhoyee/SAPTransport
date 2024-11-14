@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)
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
                    <th>Payment Date</th>

                    <th>Status</th>
                    <th>Payment Mtd</th>
                    <th>Payment Ref</th>
                    <th>Updated At</th>
                    <th>Action</th>
                   
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

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
    let table = $('#payments-table').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        order: [[7, 'desc']], // Orders by updated_at (descending)
        ajax: {
            url: "{{ route('admin.payments.fetch') }}",
            method: 'GET',
            dataSrc: 'data',
            error: function(xhr, error, thrown) {
                console.error('Error fetching payments:', error, thrown);
            }
        },
        columns: [
            { data: null },
            { data: 'booking_reference' },
            { data: 'amount', render: function(data) { return `₦${parseFloat(data).toFixed(2)}`; }},
            { data: 'payment_date', render: function(data) { return new Date(data).toLocaleDateString(); }},
            { data: 'status', render: function(data) {
                if (data === 'paid') return '<span class="badge bg-success">Paid</span>';
                else if (data === 'unpaid') return '<span class="badge bg-danger">Unpaid</span>';
                else if (data === 'refund-pending') return '<span class="badge bg-warning">Refund Pending</span>';
                else if (data === 'refunded') return '<span class="badge bg-info">Refunded</span>';
            }},
            { data: 'payment_method', render: function(data) { return data.charAt(0).toUpperCase() + data.slice(1); }},
            { data: 'payment_reference' },
            { data: 'updated_at', visible: false },  // Updated At column
            { data: null, render: function(data) {
                if (data.status === 'refund-pending') {
                    return `<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#refundModal" data-id="${data.id}">Process Refund</button>`;
                } else if (data.status === 'refunded') {
                    return `<button class="btn btn-info btn-sm" disabled>Refunded</button>`;
                } else {
                    return '<span>No Actions</span>';
                }
            }}
        ],
        drawCallback: function(settings) {
            let api = this.api();
            let startIndex = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
        }
    });



    // Set interval to refresh the DataTable every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false); // Reload DataTable without resetting pagination
    }, 30000); // 30 seconds
    
    // Modal trigger
    $('#refundModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var paymentId = button.data('id');
        $('#refund-payment-id').val(paymentId);
    });

    // Process Refund AJAX
    $('#confirm-refund').on('click', function() {
        let paymentId = $('#refund-payment-id').val();
        let refundButton = $(this);
        let refundSpinner = $('#confirm-refund-spinner');
        refundSpinner.show();
        refundButton.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/payments') }}/" + paymentId + "/refund",
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                refundSpinner.hide();
                refundButton.prop('disabled', false);
                if (response.success) {
                    alert('Refund processed successfully.');
                    table.ajax.reload(); // Reload DataTable after success
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                refundSpinner.hide();
                refundButton.prop('disabled', false);
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Decline Refund AJAX
    $('#decline-refund').on('click', function() {
        let paymentId = $('#refund-payment-id').val();
        let declineButton = $(this);
        let declineSpinner = $('#decline-refund-spinner');
        declineSpinner.show();
        declineButton.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/payments') }}/" + paymentId + "/refund/decline",
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                declineSpinner.hide();
                declineButton.prop('disabled', false);
                if (response.success) {
                    alert('Refund declined successfully.');
                    table.ajax.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                declineSpinner.hide();
                declineButton.prop('disabled', false);
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Refund Cash AJAX
    $('#refund-cash').on('click', function() {
        let paymentId = $('#refund-payment-id').val();
        let refundCashButton = $(this);
        let refundCashSpinner = $('#refund-cash-spinner');
        refundCashSpinner.show();
        refundCashButton.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/payments') }}/" + paymentId + "/refund/cash",
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                refundCashSpinner.hide();
                refundCashButton.prop('disabled', false);
                if (response.success) {
                    alert('Cash refunded successfully.');
                    table.ajax.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                refundCashSpinner.hide();
                refundCashButton.prop('disabled', false);
                alert('An error occurred. Please try again.');
            }
        });
    });
});

</script>
@endpush
