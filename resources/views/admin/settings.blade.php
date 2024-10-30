@extends('admin.layouts.admin-layout')

@section('title', 'Admin Settings')

@section('content')
<h1 class="app-page-title">Admin Settings</h1>

<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
      

        <div class="container mt-5">
            <div class="row">
                <!-- Password Change Section -->
                <div class="col-md-12 mb-5">
                    <h3>Change Password</h3>
                    <form id="password-change-form" action="{{ route('admin.settings.change-password') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="current-password">Current Password</label>
                            <input type="password" id="current-password" name="current_password" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" name="new_password" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm-new-password">Confirm New Password</label>
                            <input type="password" id="confirm-new-password" name="confirm_new_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm" id="password-spinner" style="display: none;"></span>
                            Change Password
                        </button>
                    </form>
                    <div id="password-feedback" class="mt-3"></div>
                </div>
            </div>
        </div>

    </div>
</div>

        <!-- Real-Time Activity Log Table -->

        <div class="app-card app-card-details shadow-sm mb-4 mt-3">
            <div class="app-card-body p-4">
      
                <div class="col-md-12">
                    <h3>Activity Log</h3>
                    <table id="activity-log-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>description</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>


    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script>
$(document).ready(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize DataTable
    const activityTable = $('#activity-log-table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        order: [[5, 'desc']], // Order by the "Timestamp" column (index 5) in descending order

        ajax: {
            url: "{{ route('admin.settings.activity-log') }}",
            dataSrc: ''
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 }, // Serial number based on row index

            { data: 'user_name' },
            { data: 'action' },
            { data: 'description' },
            { data: 'ip_address' },
            { data: 'timestamp' }
        ]
    });

    // Refresh the table every 10 seconds
    setInterval(function () {
        activityTable.ajax.reload(null, false);
    }, 10000);

    // Password change form submission with AJAX
    document.getElementById('password-change-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-new-password').value;

        // Check if new password and confirm password match
        if (newPassword !== confirmPassword) {
            const feedback = document.getElementById('password-feedback');
            feedback.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    New password and confirm password do not match.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            return; // Exit function to prevent form submission
        }

    document.getElementById('password-spinner').style.display = 'inline-block';

    fetch("{{ route('admin.settings.change-password') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
                current_password: document.getElementById('current-password').value,
                new_password: newPassword,
                new_password_confirmation: confirmPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('password-spinner').style.display = 'none';
        const feedback = document.getElementById('password-feedback');
        feedback.innerHTML = `
                <div class="alert ${data.success ? 'alert-success' : 'alert-danger'} alert-dismissible fade show" role="alert">
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;

    })
    .catch(error => {
        console.error('Password change failed:', error);
        document.getElementById('password-spinner').style.display = 'none';
    });
});

});
</script>
@endpush
