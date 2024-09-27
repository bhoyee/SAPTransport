@extends('layouts.passenger')

@section('title', 'Unpaid Bookings')

@section('content')
<div class="app-page-title">
    <h1>Unpaid Bookings</h1>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-body">
        <table id="unpaid-payments" class="table table-striped table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Booking Ref</th>
                    <th>Booking Date</th>
                    <th>Service Type</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unpaidPayments as $payment)
                <tr>
                    <td>{{ $payment->booking->booking_reference }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->booking->created_at)->format('d M, Y') }}</td>
                    <td>{{ $payment->booking->service_type }}</td>
                    <td>₦{{ number_format($payment->amount, 2) }}</td>
                    <td><span class="badge bg-warning">{{ ucfirst($payment->status) }}</span></td>
                    <td>
                        <a href="{{ route('booking.view', $payment->booking->id) }}" class="btn btn-primary btn-sm">View</a>
                        <a href="{{ route('payment.pay', $payment->id) }}" class="btn btn-success btn-sm">Pay Now</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#unpaid-payments').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
        });
    });
</script>
@endpush
