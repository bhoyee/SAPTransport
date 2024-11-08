@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';

    // Set the title based on the user's role
    $pageTitle = auth()->user()->hasRole('admin') ? 'Admin Settings' : 'Staff Settings';
@endphp

@extends($layout)

@section('title', $pageTitle)

@section('content')
<h1 class="app-page-title">{{ $pageTitle }}</h1>

<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
        <div class="container mt-5">
            <div class="row">
                <!-- Password Change Section -->
                <div class="col-md-6 mb-5">
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

                <!-- Conditionally show Enable / Disable Bookings Section for non-consultants -->
                @if(!auth()->user()->hasRole('consultant'))
                <div class="col-md-6 mb-5">
                    <h2>Enable / Disable Bookings</h2>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Setting Key</th>
                                <th>Setting Value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settings as $setting)
                                <tr>
                                    <td>{{ $setting->key }}</td>
                                    <td>
                                        @if($setting->key === 'booking_status')
                                            <span class="badge bg-{{ $setting->value === 'closed' ? 'danger' : 'success' }}">
                                                {{ ucfirst($setting->value) }}
                                            </span>
                                        @else
                                            {{ $setting->value }}
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.settings.update', $setting->id) }}" method="POST" onsubmit="showSpinner(this)">
                                            @csrf
                                            @method('PUT')

                                            @if($setting->key === 'booking_status')
                                                <select name="value" class="form-control">
                                                    <option value="open" {{ $setting->value === 'open' ? 'selected' : '' }}>Enable Bookings</option>
                                                    <option value="closed" {{ $setting->value === 'closed' ? 'selected' : '' }}>Disable Bookings</option>
                                                </select>
                                            @else
                                                <input type="text" name="value" value="{{ $setting->value }}" class="form-control">
                                            @endif

                                            <button type="submit" class="btn btn-primary mt-2">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                                <span class="button-text">Update</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Conditionally show Activity Log for non-consultants -->
@if(!auth()->user()->hasRole('consultant'))
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
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endif
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

//booking buton status spinner 
const forms = document.querySelectorAll('form');

forms.forEach(form => {
    form.addEventListener('submit', function (event) {
        try {
            showSpinner(form); // Call the showSpinner function on form submission
        } catch (error) {
            console.error('Error on form submit:', error);
        }
    });
});

    function showSpinner(form) {
        console.log('Form submission initiated...');
        const button = form.querySelector('button[type="submit"]');
        if (!button) {
            console.error('Submit button not found.');
            return;
        }

        // Add a spinner element if not present
        let spinner = button.querySelector('.spinner-border');
        if (!spinner) {
            spinner = document.createElement('span');
            spinner.className = 'spinner-border spinner-border-sm';
            spinner.setAttribute('role', 'status');
            spinner.setAttribute('aria-hidden', 'true');
            button.appendChild(spinner);
        }

        // Update the button text and disable it
        button.textContent = 'Updating...';
        button.appendChild(spinner); // Ensure spinner is visible
        spinner.style.display = 'inline-block';
        button.disabled = true;
        
        console.log('Spinner is shown, button text changed, and button disabled');
    }

});
</script>
@endpush
