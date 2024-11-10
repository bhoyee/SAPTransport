@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

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
                    <!-- DataTables will populate this dynamically -->
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
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.support-tickets.data') }}",
            type: "GET",
            error: function(xhr, status, error) {
                console.error('Error fetching support tickets data:', error);
            }
        },
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false }, // Serial number
            { data: 'department', name: 'department' },
            { data: 'subject', name: 'subject', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']], // Order by 'Last Updated'
        pageLength: 10,
        lengthMenu: [5, 10, 15, 20],
    });

    // Optional: Refresh data every 30 seconds
    setInterval(function() {
        $('#tickets-table').DataTable().ajax.reload(null, false);
    }, 30000);
});
</script>
@endpush
