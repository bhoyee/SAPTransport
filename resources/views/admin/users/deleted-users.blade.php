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
                    <th>Deleted By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
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
            url: "{{ route('admin.users.fetch-deleted-stats') }}",
            method: 'GET',
            success: function(response) {
                $('#totalDeletedUsers').text(response.totalDeletedUsers);
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch stats:', error);
            }
        });
    }

    // Fetch stats on page load
    fetchTotalDeletedUsers();

    // Initialize DataTable for Deleted Users
    let table = $('#deleted-users-table').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[5, "desc"]],
        ajax: {
            url: "{{ route('admin.users.deleted-list') }}",
            dataSrc: 'data',
            error: function(xhr, error, thrown) {
                console.error('Error fetching deleted users:', error, thrown);
            }
        },
        columns: [
            { data: null, orderable: false, searchable: false, render: function (data, type, row, meta) { return meta.row + 1; }},
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'status', render: function(data) { return `<span class="badge bg-danger">${data}</span>`; }},
            { data: 'created_by' },
            { data: 'deleted_by' },
            { data: null, orderable: false, searchable: false, render: function (data, type, row, meta) {
                    return `<button class="btn btn-success btn-sm restore-user-btn" data-user-id="${row.id}">Restore</button>
                            <button class="btn btn-danger btn-sm" data-user-id="${row.id}" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>`;
                }
            }
        ],
        drawCallback: function(settings) {
            let api = this.api();
            let startIndex = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function(cell, i) { cell.innerHTML = startIndex + i + 1; });
        }
    });

    // Periodically reload the DataTable and stats every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
        fetchTotalDeletedUsers();
    }, 30000);

    // Handle Permanent Delete
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        $('#delete-user-id').val(userId);
    });

    $('#permanentDeleteUserForm').on('submit', function(e) {
        e.preventDefault();
        let userId = $('#delete-user-id').val();
        $.ajax({
            url: "{{ route('admin.users.permanent-delete') }}",
            method: 'POST',
            data: $(this).serialize(),
            beforeSend: function() { $('#delete-user-btn').hide(); $('#delete-spinner').show(); },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload(null, false);
                    fetchTotalDeletedUsers();
                } else { alert('Failed to delete user: ' + response.message); }
            },
            error: function(xhr, status, error) { console.error('Error permanently deleting user:', error); },
            complete: function() { $('#delete-user-btn').show(); $('#delete-spinner').hide(); }
        });
    });

    // Restore user
    $('#deleted-users-table').on('click', '.restore-user-btn', function() {
        let userId = $(this).data('user-id');
        let button = $(this);
        button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Restoring...').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.users.restore') }}",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', user_id: userId },
            success: function(response) {
                if (response.success) {
                    table.ajax.reload(null, false);
                    fetchTotalDeletedUsers();
                } else { alert('Failed to restore user: ' + response.message); }
            },
            error: function(xhr, status, error) { console.error('Error restoring user:', error); alert('An error occurred while restoring the user.'); },
            complete: function() { button.html('Restore').prop('disabled', false); }
        });
    });
});
</script>
@endpush
