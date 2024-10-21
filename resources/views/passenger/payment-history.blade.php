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
                @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->booking->booking_reference }}</td>
                    <td>{{ $payment->booking->invoice->invoice_number ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->booking->created_at)->format('d M, Y') }}</td>
                    <td>{{ $payment->booking->service_type }}</td>
                    <td>₦{{ number_format($payment->amount, 2) }}</td> <!-- Render the Amount -->

                    <td>
                        <span class="badge bg-{{ 
                            $payment->status == 'paid' ? 'success' : 
                            ($payment->status == 'refund-pending' ? 'warning' : 
                            ($payment->status == 'refunded' ? 'info' : 'danger')) 
                        }}">
                            {{ ucfirst(str_replace('-', ' ', $payment->status)) }}
                        </span>
                    </td>


                    <td>
                        <!-- View Button -->
                        <a href="{{ route('booking.view', ['id' => $payment->booking->id]) }}" class="btn btn-primary btn-sm">
                            View
                        </a>

                        <!-- Refund Button -->
                        @if($payment->status == 'paid')
                            <button class="btn btn-danger btn-sm" onclick="showRefundModal('{{ $payment->id }}')">
                                Request Refund
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Refund Modal -->

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
<script>
    $(document).ready(function() {
        $('#payment-history-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
        });
    });

    function showRefundModal(paymentId) {
        $('#refundPaymentId').val(paymentId);
        $('#refundModal').modal('show');
    }

            // Show spinner and disable the Confirm Refund button on form submit
            $('#refundForm').on('submit', function() {
            $('#confirmRefundButton').prop('disabled', true); // Disable the button
            $('#refundSpinner').removeClass('d-none'); // Show the spinner
        });
</script>
@endpush


