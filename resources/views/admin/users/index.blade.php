@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">User Management</h1>
<p>Manage all registered users here.</p>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Active Passengers</h5>
                <h2>{{ $activePassengers }}</h2>
                <small class="text-success">Active</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Active Staffs</h5>
                <h2>{{ $activeConsultants }}</h2>
                <small class="text-success">Active</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Suspended Users</h5>
                <h2>{{ $suspendedUsers }}</h2>
                <small class="text-danger">Suspended</small>
            </div>
        </div>
    </div>
</div>

<!-- Display Success Message -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Display Error Message -->
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- DataTable -->
<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
        <table id="users-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @if($user->getRoleNames()->isNotEmpty())
                    {{ $user->getRoleNames()->first() === 'consultant' ? 'Staff' : ucfirst($user->getRoleNames()->first()) }}
                @else
                    No Role Assigned
                @endif
            </td>
            <td>
                @if($user->status === 'active')
                    <span class="badge bg-success">Active</span>
                @elseif($user->status === 'inactive')
                    <span class="badge bg-warning">Inactive</span>
                @elseif($user->status === 'suspend')
                    <span class="badge bg-danger">Suspended</span>
                @else
                    <span class="badge bg-secondary">Deleted</span>
                @endif
            </td>
            <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
            <td>
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary">View</a>
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-user-id="{{ $user->id }}">Delete</button>

                <!-- Suspend/Activate Form with Spinner -->
                <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" class="suspend-form" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $user->status === 'suspend' ? 'btn-secondary' : 'btn-warning' }} suspend-btn">
                    {{ $user->status === 'suspend' ? 'Activate' : 'Suspend' }}
                    </button>
                    <button class="btn btn-sm btn-warning suspend-spinner" type="button" disabled style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Processing...
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>

        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Do you really want to delete this user?</p>
                <p><strong>Note:</strong> Information deleted cannot be recovered.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="{{ route('admin.users.delete') }}" id="deleteUserForm">
                    @csrf
                    <input type="hidden" name="user_id" id="delete-user-id" value="">
                    <button type="submit" class="btn btn-danger" id="delete-user-btn">Yes, Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <!-- Spinner -->
                    <button class="btn btn-danger" type="button" id="delete-spinner" disabled style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Deleting...
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                order: [[ 4, "desc" ]], // Order by Created At column (descending)
            });

            // Capture the user ID when the modal is opened
            $('#deleteModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var userId = button.data('user-id'); // Extract info from data-* attributes
                var modal = $(this);
                modal.find('#delete-user-id').val(userId); // Set the user ID in the form
            });

            // Show spinner on delete form submit
            $('#deleteUserForm').on('submit', function() {
                $('#delete-user-btn').hide();
                $('#delete-spinner').show();
            });

            // Show spinner on suspend button click
            $('.suspend-form').on('submit', function() {
                $(this).find('.suspend-btn').hide();
                $(this).find('.suspend-spinner').show();
            });
        });
    </script>
@endpush
