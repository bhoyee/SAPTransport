@extends('layouts.passenger')

@section('title', 'My Support Tickets')

@section('content')
<h1 class="app-page-title">My Support Tickets</h1>
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <table id="tickets-table" class="table table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>S/N</th> <!-- Serial Number Column -->
                        <th>Department</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $index => $ticket)
                    <tr>
                        <td>{{ $index + 1 }}</td> <!-- Serial number based on index (starting from 1) -->
                        <td>{{ ucfirst($ticket->department) }}</td>
                        <td>
                            <strong>#{{ $ticket->ticket_num }}</strong><br>
                            <p>{{ $ticket->subject }}</p>
                        </td>
                        <td>
                            <span class="badge {{ $ticket->status == 'open' ? 'bg-danger' : 'bg-success' }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y H:i') }}</td> <!-- 24-hour format -->
                        <td>
                            <a href="{{ route('viewTicket', $ticket->id) }}" class="btn btn-primary btn-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with custom ordering by 'Last Updated'
        var table = $('#tickets-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
            order: [[4, 'desc']], // Order by 'Last Updated' (5th column, 0-indexed as 4)
            columnDefs: [
                { orderable: false, targets: 0 } // Disable ordering for the 'S/N' column
            ]
        });

        // Polling function to fetch tickets
        function fetchTickets() {
            $.ajax({
                url: "{{ route('tickets.fetch') }}", // This route should return the latest tickets data
                method: 'GET',
                success: function(response) {
                    // Clear the table data
                    table.clear();

                    // Add the new data
                    response.tickets.forEach(function(ticket, index) {
                        table.row.add([
                            index + 1, // S/N
                            ticket.department.charAt(0).toUpperCase() + ticket.department.slice(1), // Department
                            `<strong>#${ticket.ticket_num}</strong><br><p>${ticket.subject}</p>`, // Subject
                            `<span class="badge ${ticket.status === 'open' ? 'bg-danger' : 'bg-success'}">${ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1)}</span>`, // Status
                            moment(ticket.updated_at).format('DD MMM YYYY HH:mm'), // Last Updated
                            `<a href="/tickets/view/${ticket.id}" class="btn btn-primary btn-sm">View</a>` // Actions
                        ]);
                    });

                    // Redraw the table
                    table.draw();
                },
                error: function() {
                    console.error('Failed to fetch tickets.');
                }
            });
        }

        // Poll every 5 seconds
        setInterval(fetchTickets, 5000);
    });
</script>
@endpush
