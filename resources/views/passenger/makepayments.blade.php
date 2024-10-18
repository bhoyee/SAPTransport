@extends('layouts.passenger')

@section('title', 'Unpaid Invoices')

@section('content')
<div class="app-page-title">
    <h1>Unpaid Payments</h1>
</div>
@if(session('error'))
    <div class="alert alert-danger mt-4 alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

    </div>
@endif


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

                        <!-- Pay Now Button as a form -->
                        <form method="POST" action="{{ route('pay') }}" class="d-inline-block">
                            @csrf
                            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                            <input type="hidden" name="email" value="{{ $invoice->booking->user->email }}">
                            <input type="hidden" name="amount" value="{{ $invoice->amount * 100 }}"> {{-- Amount in kobo --}}
                            <input type="hidden" name="reference" value="{{ $invoice->invoice_number }}"> {{-- Invoice number as reference --}}
                            <button class="btn btn-success" type="submit" value="Pay Now!">
                                <i class="fa fa-plus-circle"></i> Pay Now!
                            </button>
                        </form>


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
