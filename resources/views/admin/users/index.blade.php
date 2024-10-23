@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">User Management</h1>

<!-- Statistics Cards -->
<div class="row mb-4" id="user-stats">
    <!-- Active Passengers (Success) -->
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h5 class="card-title">Active Passengers</h5>
                <h2 id="activePassengers">{{ $activePassengers }}</h2>
                <small class="text-white">Active</small>
            </div>
        </div>
    </div>

    <!-- Active Staff (Info) -->
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h5 class="card-title">Active Staffs</h5>
                <h2 id="activeConsultants">{{ $activeConsultants }}</h2>
                <small class="text-white">Active</small>
            </div>
        </div>
    </div>

    <!-- Suspended Users (Danger) -->
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
        order: [[4, "desc"]], // Order by Created At column (descending)
        ajax: {
            url: "{{ route('admin.users.index') }}", // The route to get user data via AJAX
            dataSrc: 'data', // Look for the 'data' array in the JSON response
            error: function(xhr, error, thrown) {
                console.error('Error fetching users:', error, thrown); // Log any errors to the console
            }
        },
        columns: [
            { 
                data: null, // S/N column
                orderable: true, // Make S/N column sortable
                searchable: false, // Disable searching for S/N
                render: function (data, type, row, meta) {
                    // S/N based on the order of the created_at field
                    return meta.row + 1;
                }
            },
            { data: 'name' },
            { data: 'email' },
            { data: 'status', render: function(data) {
                let badgeClass = data === 'active' ? 'bg-success' : data === 'suspend' ? 'bg-danger' : 'bg-warning';
                return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
            }},
            { data: 'created_at' },
            { data: 'actions', orderable: false, searchable: false } // Action buttons
        ],
        drawCallback: function(settings) {
            // Renumber the S/N column based on the current page
            let api = this.api();
            let startIndex = api.page.info().start;

            // Reassign S/N in case of sorting, pagination, etc.
            api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
        }
    });

    // Periodically reload the DataTable every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false); // Reload DataTable without resetting pagination
    }, 30000); // 30 seconds

    // Modal logic for delete button
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var userId = button.data('user-id'); // Extract info from data-* attributes
        $('#delete-user-id').val(userId); // Set the user ID in the form
    });

    // Handle Delete Form submission via AJAX
    $('#deleteUserForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        let form = $(this);
        let userId = $('#delete-user-id').val();

        $.ajax({
            url: "{{ route('admin.users.delete') }}", // The route to delete the user
            method: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                $('#delete-user-btn').hide();
                $('#delete-spinner').show();
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload(null, false); // Reload DataTable without resetting pagination
                } else {
                    alert('Failed to delete user: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error deleting user:', error);
            },
            complete: function() {
                $('#delete-user-btn').show();
                $('#delete-spinner').hide();
            }
        });
    });
});
</script>
@endpush
