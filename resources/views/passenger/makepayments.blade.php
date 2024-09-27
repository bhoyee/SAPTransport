@extends('layouts.passenger')

@section('title', 'Unpaid Invoices')

@section('content')
<div class="app-page-title">
    <h1>Unpaid Payments</h1>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-body">
        <table id="unpaid-invoices" class="table table-striped table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Booking Ref</th>
                    <th>Invoice Num</th>
                    <th>Booking Date</th>
                    <th>Service Type</th>
                    <th>Amount</th>
                    <th>Invoice Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unpaidInvoices as $invoice)
                <tr>
                    <td>{{ $invoice->booking->booking_reference }}</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->booking->created_at)->format('d M, Y') }}</td>
                    <td>{{ $invoice->booking->service_type }}</td>
                    <td>₦{{ number_format($invoice->amount, 2) }}</td>
                    <td><span class="badge bg-danger">{{ ucfirst($invoice->status) }}</span></td>
                    <td>
                        <a href="{{ route('booking.view', ['id' => $invoice->booking->id, 'from' => 'makepayments']) }}" class="btn btn-primary btn-sm">View</a>
                        <a href="{{ route('payment.pay', $invoice->id) }}" class="btn btn-success btn-sm">Pay Now</a>
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
        $('#unpaid-invoices').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
        });
    });
</script>
@endpush
