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
            <th>S/N</th> <!-- Add S/N column -->
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
        <!-- Body will be filled dynamically via AJAX -->
    </tbody>
</table>

    </div>
</div>

@endsection

@push('scripts')
<!-- Include Moment.js for date formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize the DataTable with AJAX source
        let table = $('#invoices-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,  // Disable ordering because we will sort manually by updated_at
            lengthChange: true,
            ajax: {
                url: "{{ route('invoices.index') }}", // URL to fetch the data
                type: 'GET',
                dataSrc: function(json) {
                    // Sort the data by updated_at in descending order (most recent first)
                    return json.data.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
                }
            },
            columns: [
                { 
                    data: null, // S/N column
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Serial number starts from 1 and increments by row index
                    }
                },
                { data: 'invoice_number' }, // Invoice Number
                { data: 'booking.booking_reference' }, // Booking Reference
                { data: 'booking.created_at', render: function(data) {
                    return moment(data).format('D MMM, YYYY'); // Format the booking date using moment.js
                }},
                { data: 'booking.service_type' }, // Service Type
                { data: 'amount', render: function(data) {
                    return '₦' + parseFloat(data).toLocaleString(); // Format the amount as currency
                }},
                { 
                    data: 'status', 
                    render: function(data) { 
                        let badgeClass;

                        // Determine the badge class based on the status
                        switch (data) {
                            case 'Paid':
                                badgeClass = 'success';  // Green for paid
                                break;
                            case 'Unpaid':
                                badgeClass = 'danger';   // Red for unpaid
                                break;
                            case 'Refunded':
                                badgeClass = 'info';     // Blue for refunded
                                break;
                            default:
                                badgeClass = 'warning';  // Yellow for other statuses (if any)
                                break;
                        }

                        // Return the badge HTML with the appropriate color and status text
                        return `<span class="badge bg-${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                    }
                },
                { 
                    data: null, 
                    render: function(data, type, row) {
                        let actions = `<a href="/passenger/invoice/${row.id}" class="btn btn-info btn-sm">View Invoice</a>`;

                        if (row.status === 'Unpaid') {
                            actions += `
                                <form method="POST" action="{{ route('invoice.pay') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="invoice_id" value="${row.id}">
                                    <button type="submit" class="btn btn-success btn-sm">Pay Now</button>
                                </form>`;
                        }
                        return actions;
                    }
                }
            ]
        });

        // Optional: Poll the table to reload every 30 seconds to ensure real-time updates
        setInterval(function() {
            table.ajax.reload(null, false); // false to keep current paging
        }, 30000); // Reload every 30 seconds
    });
</script>
@endpush
