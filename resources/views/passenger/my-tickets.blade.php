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
                        <th>S/N</th> <!-- Added S/N column -->
                        <th>Department</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $loop->iteration }}</td> <!-- Serial number based on loop iteration -->
                        <td>{{ ucfirst($ticket->department) }}</td>
                        <td>
                            <strong>#{{ $ticket->ticket_num }}</strong><br>
                            <p>{{ $ticket->subject }}<p>
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
        // Initialize DataTable with custom ordering by 'updated_at'
        $('#tickets-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
            order: [[4, 'desc']], // Order by 'Last Updated' (5th column, 0-indexed as 4)
        });
    });
</script>
@endpush
