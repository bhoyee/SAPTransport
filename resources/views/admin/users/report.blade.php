@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">Generate User Report</h1>
<p style="font-size: small; font-weight: bold;">Note: Total Users include (admin) while the rest doesn't .</p>
<!-- Cards for Statistics -->
<div class="row mb-4">
    <!-- Total Users Card -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-light">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="card-text" style="font-size: 24px;">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>

    <!-- Total Passengers Card -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-light">
            <div class="card-body">
                <h5 class="card-title">Total Passengers</h5>
                <p class="card-text" style="font-size: 24px;">{{ $totalPassengers }}</p>
            </div>
        </div>
    </div>

    <!-- Total Staff (Consultants) Card -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-light">
            <div class="card-body">
                <h5 class="card-title">Total Staff</h5>
                <p class="card-text" style="font-size: 24px;">{{ $totalStaff }}</p>
            </div>
        </div>
    </div>

    <!-- Total Unverified Users Card -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-light">
            <div class="card-body">
                <h5 class="card-title">Unverified Users</h5>
                <p class="card-text" style="font-size: 24px;">{{ $unverifiedUsers }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Form for generating PDF report -->
<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">

        <!-- Success Message -->
        <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Error Message -->
        <div id="error-message" class="alert alert-danger alert-dismissible fade show" role="alert" style="display:none;">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <form method="POST" action="{{ route('admin.users.report.pdf') }}" id="generate-pdf-form">
            @csrf

            <!-- Role Filter -->
            <div class="form-group mb-3">
                <label for="role">Select Role</label>
                <select id="role" name="role" class="form-control">
                    <option value="">All</option>
                    <option value="passenger">Passenger</option>
                    <option value="consultant">Staff</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="form-group mb-3">
                <label for="date_from">From Date</label>
                <input type="date" id="date_from" name="date_from" class="form-control">
            </div>
            <div class="form-group mb-3">
                <label for="date_to">To Date</label>
                <input type="date" id="date_to" name="date_to" class="form-control">
            </div>

            <!-- Email Verification Filter -->
            <div class="form-group mb-3">
                <label for="email_verified">Email Verified</label>
                <select id="email_verified" name="email_verified" class="form-control">
                    <option value="">All</option>
                    <option value="verified">Verified</option>
                    <option value="not_verified">Not Verified</option>
                </select>
            </div>

            <!-- Button and Spinner -->
            <button type="submit" class="btn btn-primary mt-3" id="generate-pdf-btn">
                Generate PDF
            </button>
            <button class="btn btn-primary mt-3" type="button" id="generate-pdf-spinner" disabled style="display: none;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Generating PDF...
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('generate-pdf-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Hide any previous messages
        document.getElementById('success-message').style.display = 'none';
        document.getElementById('error-message').style.display = 'none';

        // Show the spinner and hide the button
        document.getElementById('generate-pdf-btn').style.display = 'none';
        document.getElementById('generate-pdf-spinner').style.display = 'inline-block';

        // Prepare form data
        let formData = new FormData(this);

        // Send AJAX request
        fetch('{{ route('admin.users.report.pdf') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('No records found for the selected criteria.');
            }

            // Get the filename from the response headers
            const contentDisposition = response.headers.get('Content-Disposition');
            let fileName = 'user_report_' + new Date().toISOString().replace(/[:.]/g, '-') + '.pdf';

            if (contentDisposition) {
                const fileNameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (fileNameMatch && fileNameMatch.length > 1) {
                    fileName = fileNameMatch[1].replace(/['"]/g, '');
                }
            }

            return response.blob().then(blob => ({ blob, fileName }));
        })
        .then(obj => {
            // Reset the spinner and show the button
            document.getElementById('generate-pdf-spinner').style.display = 'none';
            document.getElementById('generate-pdf-btn').style.display = 'inline-block';

            // Create a download link for the PDF
            let downloadLink = document.createElement('a');
            downloadLink.href = window.URL.createObjectURL(obj.blob);
            downloadLink.download = obj.fileName;
            downloadLink.click();

            // Show success message
            document.getElementById('success-message').innerHTML = 'PDF generated successfully!';
            document.getElementById('success-message').style.display = 'block';

            // Hide success message after 5 seconds
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 5000);
        })
        .catch(error => {
            // Handle any errors
            document.getElementById('generate-pdf-spinner').style.display = 'none';
            document.getElementById('generate-pdf-btn').style.display = 'inline-block';

            document.getElementById('error-message').innerHTML = error.message;
            document.getElementById('error-message').style.display = 'block';

            // Hide error message after 5 seconds
            setTimeout(function() {
                document.getElementById('error-message').style.display = 'none';
            }, 5000);
        });
    });
</script>
@endpush
