@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('content')
<h1 class="app-page-title">User Management</h1>

<!-- Statistics Cards -->

<div class="row mb-4" id="user-stats">
<div class="col-md-4">
    <div class="card text-white bg-success">
        <div class="card-body text-center">
            <h5 class="card-title">Active Passengers</h5>
            <h2 id="activePassengers">{{ $totalPassengers }}</h2>
            <small class="text-white">Active</small>
        </div>
    </div>
</div>

    @if(auth()->user()->hasRole('consultant'))
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h5 class="card-title">Inactive Passengers</h5>
                    <h2 id="inactivePassengers">{{ $inactivePassengers }}</h2>
                    <small class="text-white">Inactive</small>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h5 class="card-title">Active Staff</h5>
                    <h2 id="activeConsultants">{{ $totalStaff }}</h2>
                    <small class="text-white">Active</small>
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <h5 class="card-title">Suspended Users</h5>
                <h2 id="suspendedUsers">{{ $suspendedUsers }}</h2>
                <small class="text-white">Suspended</small>
            </div>
        </div>
    </div>
</div>
<!-- DataTable -->
<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
        <table id="users-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody> <!-- Table body will be populated via AJAX -->
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
                <form method="POST" id="deleteUserForm">
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

@push('scripts')
<script>
$(document).ready(function() {
    let table = $('#users-table').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[4, "desc"]],
        ajax: {
            url: "{{ route('admin.users.index') }}",
            dataSrc: 'data',
            error: function(xhr, error, thrown) {
                console.error('Error fetching users:', error, thrown);
            }
        },
        columns: [
    { 
        data: null,
        orderable: true,
        searchable: false,
        render: function (data, type, row, meta) {
            return meta.row + 1;
        }
    },
    { data: 'name' },
    { data: 'email' },
    { 
        data: 'roles',  // Now we have the roles field in the response
        render: function(data, type, row) {
            // Check if the user is a consultant and return 'Staff' instead of 'Consultant'
            if (data.includes('consultant')) {
                return 'Staff';
            }
            // If not consultant, display the role(s)
            return data.charAt(0).toUpperCase() + data.slice(1); // Capitalize the role
        }
    },
    { data: 'status', render: function(data) {
        let badgeClass = data === 'active' ? 'bg-success' : data === 'suspend' ? 'bg-danger' : 'bg-warning';
        return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
    }},
    { data: 'created_at' },
    { data: 'actions', orderable: false, searchable: false }
],

        drawCallback: function(settings) {
            let api = this.api();
            let startIndex = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
        }
    });

    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);

    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        $('#delete-user-id').val(userId);
        console.log('Preparing to delete user with ID:', userId); 
    });

    $('#deleteUserForm').on('submit', function(e) {
        e.preventDefault(); 

        let form = $(this);
        let userId = $('#delete-user-id').val();
        let formData = form.serialize();

        console.log('Form data before sending:', formData); 
        console.log("Attempting AJAX POST to:", "{{ route('admin.users.delete') }}");

        $.ajax({
            url: "{{ route('admin.users.delete') }}",
            method: 'POST',
            data: formData,
            cache: false,
            beforeSend: function() {
                $('#delete-user-btn').hide();
                $('#delete-spinner').show();
            },
            success: function(response) {
                console.log('Delete response from server:', response);
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload(null, false);
                    alert('User deleted successfully.');
                } else {
                    alert('Failed to delete user: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error deleting user:', error);
                console.log('Status:', status);
                console.log('Response body:', xhr.responseText);
                alert('Failed to delete user. Check console for details.');
            },
            complete: function() {
                $('#delete-user-btn').show();
                $('#delete-spinner').hide();
            }
        });
    });

    function fetchStatistics() {
        $.ajax({
            url: "{{ route('admin.users.fetch-stats') }}",
            method: "GET",
            success: function(response) {
                $('#activePassengers').text(response.totalPassengers);
                $('#inactivePassengers').text(response.inactivePassengers);
                $('#activeConsultants').text(response.totalStaff);
                $('#suspendedUsers').text(response.suspendedUsers);
            },
            error: function(error) {
                console.error('Error fetching statistics:', error);
            }
        });
    }

    setInterval(fetchStatistics, 30000);
});
</script>
@endpush

