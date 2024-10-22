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
                    <th>S/N</th> <!-- Added S/N column -->
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
                <!-- Data will be loaded dynamically by DataTables -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script> <!-- Add this line -->

<script>
    $(document).ready(function() {
        let table = $('#unpaid-invoices').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: false, // Disable ordering because we'll manually sort the data
            lengthChange: true,
            ajax: {
                url: "{{ route('passenger.makepayments') }}",
                type: 'GET',
                dataSrc: function(json) {
                    // Sort by updated_at to show the most recent at the top
                    return json.data.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
                },
                error: function(xhr, error, thrown) {
                    console.error('Error loading data:', error, thrown);
                }
            },
            columns: [
                { 
                    data: null, // S/N column
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Serial number starts from 1
                    }
                },
                { data: 'booking_reference' }, // Booking Reference
                { data: 'invoice_number' }, // Invoice Number
                { data: 'created_at', render: function(data) { 
                    return moment(data).format('D MMM, YYYY'); // Format date using moment.js
                }},
                { data: 'service_type' }, // Service Type
                { data: 'amount', render: function(data) {
                    return '₦' + parseFloat(data).toLocaleString(); // Format amount with currency symbol
                }},
                { data: 'status', render: function(data) {
                    return `<span class="badge bg-danger">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`; // Format status
                }},
                { 
                    data: null, 
                    render: function(data, type, row) {
                        let viewUrl = "{{ route('booking.view', ['id' => ':id', 'from' => 'makepayments']) }}".replace(':id', row.booking_id);
                        return `
                            <a href="${viewUrl}" class="btn btn-primary btn-sm">View</a>
                            <form method="POST" action="{{ route('pay') }}" class="d-inline-block">
                                @csrf
                                <input type="hidden" name="invoice_id" value="${row.id}">
                                <input type="hidden" name="email" value="${row.user_email}">
                                <input type="hidden" name="amount" value="${row.amount * 100}">
                                <input type="hidden" name="reference" value="${row.invoice_number}">
                                <button class="btn btn-success" type="submit" value="Pay Now!">
                                    <i class="fa fa-plus-circle"></i> Pay Now!
                                </button>
                            </form>`;
                    }
                }
            ]
        });

        // Polling every 30 seconds for updates
        setInterval(function() {
            table.ajax.reload(null, false); // false to keep current paging
        }, 30000); // Reload data every 30 seconds
    });
</script>
@endpush
