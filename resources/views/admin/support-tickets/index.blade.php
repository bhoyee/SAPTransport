@extends('admin.layouts.admin-layout')

@section('title', 'Manage Support Tickets')

@section('content')
<h1 class="app-page-title">Manage Support Tickets</h1>
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <table id="tickets-table" class="table table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>S/N</th> <!-- Serial number -->
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
                            <p>{{ $ticket->subject ?? ucfirst($ticket->category) }}</p> <!-- Display subject or category -->
                            </td>
                        <td>
                            <span class="badge {{ $ticket->status == 'open' ? 'bg-danger' : 'bg-success' }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y H:i') }}</td>
                        <td>
    <a href="{{ route('admin.support-tickets.view', $ticket->id) }}" class="btn btn-primary btn-sm">View</a>
    <form action="{{ route('admin.support-tickets.delete', $ticket->id) }}" method="POST" class="d-inline-block">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this ticket?')">Delete</button>
    </form>
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
        $('#tickets-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
            order: [[4, 'desc']], // Order by 'Last Updated'
        });
    });
</script>
@endpush