@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">Deleted Users</h1>

<!-- Card for Total Deleted Users -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h5 class="card-title">Total Deleted Users</h5>
                <h2 id="totalDeletedUsers">{{ $totalDeletedUsers }}</h2>
                <small class="text-white">Temporarily Deleted</small>
            </div>
        </div>
    </div>
</div>

<!-- DataTable for Deleted Users -->
<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
        <table id="deleted-users-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Created By</th>
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
                <h5 class="modal-title" id="deleteModalLabel">Confirm Permanent Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Do you really want to permanently delete this user?</p>
                <p><strong>Note:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="permanentDeleteUserForm">
                    @csrf
                    <input type="hidden" name="user_id" id="delete-user-id" value="">
                    <button type="submit" class="btn btn-danger" id="delete-user-btn">Yes, Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>

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
    // Function to fetch the latest stats for the total number of deleted users
    function fetchTotalDeletedUsers() {
        $.ajax({
            url: "{{ route('admin.users.fetch-deleted-stats') }}", // Route to get the latest deleted stats
            method: 'GET',
            success: function(response) {
                $('#totalDeletedUsers').text(response.totalDeletedUsers);
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch stats:', error);
            }
        });
    }

    // Fetch the latest stats when the page loads
    fetchTotalDeletedUsers();

    // DataTable for Deleted Users
    let table = $('#deleted-users-table').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[5, "desc"]], // Order by updated_at column (descending)
        ajax: {
            url: "{{ route('admin.users.deleted-list') }}", // Route to get deleted users data via AJAX
            dataSrc: 'data', // The 'data' array in the JSON response
            error: function(xhr, error, thrown) {
                console.error('Error fetching deleted users:', error, thrown); // Log errors
            }
        },
        columns: [
            { 
                data: null, // S/N column
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + 1; // S/N numbering starting from 1
                }
            },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { 
                data: 'status',
                render: function(data) {
                    if (data === 'Deleted') {
                        return `<span class="badge bg-danger">${data}</span>`;
                    }
                    return `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            { data: 'created_by' },
            { 
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return `<button class="btn btn-danger btn-sm" data-user-id="${row.id}" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>`;
                }
            }
        ],
        drawCallback: function(settings) {
            let api = this.api();
            let startIndex = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
        }
    });

    // Periodically reload the DataTable every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false); // Reload DataTable without resetting pagination
        fetchTotalDeletedUsers(); // Update the card with the latest total deleted users count
    }, 30000); // 30 seconds

    // Capture the user ID when the modal is opened
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        $('#delete-user-id').val(userId);
    });

    // Handle Permanent Delete Form submission via AJAX
    $('#permanentDeleteUserForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        let form = $(this);
        let userId = $('#delete-user-id').val();

        $.ajax({
            url: "{{ route('admin.users.permanent-delete') }}", // Route to permanently delete the user
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
                    fetchTotalDeletedUsers(); // Update the total deleted users count
                } else {
                    alert('Failed to delete user: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error permanently deleting user:', error);
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
