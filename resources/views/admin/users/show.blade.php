@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">User Details</h1>

<div class="row mb-4">
    <!-- User Details Column -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $userDetails->name }}'s Details</h5>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Name:</strong> {{ $userDetails->name }}</li>
                    <li class="list-group-item"><strong>Email:</strong> {{ $userDetails->email }}</li>
                    <li class="list-group-item"><strong>Email Verified At:</strong> {{ $userDetails->email_verified_at ?? 'Not Verified' }}</li>
                    <li class="list-group-item"><strong>Phone:</strong> {{ $userDetails->phone }}</li>
                    <li class="list-group-item"><strong>Gender:</strong> {{ ucfirst($userDetails->gender) }}</li>
                    <li class="list-group-item"><strong>Role:</strong> {{ $userDetails->role === 'consultant' ? 'Staff' : ucfirst($userDetails->role) }}</li>
                    <li class="list-group-item"><strong>Status:</strong> 
                        @if($userDetails->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif($userDetails->status === 'inactive')
                            <span class="badge bg-warning">Inactive</span>
                        @else
                            <span class="badge bg-danger">Suspended</span>
                        @endif
                    </li>
                    <li class="list-group-item"><strong>Created By:</strong> {{ $userDetails->created_by }}</li>
                    <li class="list-group-item"><strong>Created At:</strong> {{ $userDetails->created_at->format('Y-m-d H:i:s') }}</li>
                    <li class="list-group-item"><strong>Updated At:</strong> {{ $userDetails->updated_at->format('Y-m-d H:i:s') }}</li>
                </ul>

                <!-- Buttons -->
                <div class="mt-3">
                    <a href="{{ route('admin.users.edit', $userDetails->id) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Close</a>
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity Logs Column -->
    <div class="col-md-12 mt-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Activity Logs</h5>
                <table id="activity-logs-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userActivities as $activity)
                            <tr>
                                <td>{{ $activity->action }}</td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->ip_address }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('Y-m-d H:i:s') }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->updated_at)->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection



@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
    <!-- Include jQuery, Bootstrap, DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#activity-logs-table').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            order: [[ 3, "desc" ]],
        });
    });
</script>
@endpush
