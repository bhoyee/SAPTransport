@extends('layouts.passenger')

@section('title', 'Invoices / Receipts')

@section('content')
<div class="app-page-title">
    <h1>Invoices / Receipts</h1>
</div>

<div class="app-card shadow-sm mb-4">
    <div class="app-card-body">
    <table id="invoices-table" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Invoice Number</th>
            <th>Booking Reference</th>
            <th>Booking Date</th>
            <th>Service Type</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->booking->booking_reference }}</td>
            <td>{{ \Carbon\Carbon::parse($invoice->booking->created_at)->format('d M, Y') }}</td>
            <td>{{ $invoice->booking->service_type }}</td>
            <td>₦{{ number_format($invoice->amount, 2) }}</td>
            <td>
                <span class="badge bg-{{ $invoice->status == 'Paid' ? 'success' : 'warning' }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </td>
            <td>
                <!-- View Invoice Button -->
                <a href="{{ route('invoice.view', ['id' => $invoice->id]) }}" class="btn btn-info btn-sm">View Invoice</a>


                <!-- Pay Now Button (Only if unpaid) -->
                @if($invoice->status == 'Unpaid')
                    <form method="POST" action="{{ route('invoice.pay') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                        <button type="submit" class="btn btn-success btn-sm">Pay Now</button>
                    </form>
                @endif
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
        // Initialize the DataTable with responsive design and features
        $('#invoices-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
        });
    });
</script>
@endpush
