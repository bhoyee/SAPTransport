@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">Generate Booking Report</h1>

<!-- Dropdown for Report Timeframe -->
<div class="mb-4">
    <label for="report-range" class="form-label">Select Report Timeframe</label>
    <select id="report-timeframe" class="form-control">
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
        <option value="yearly">Yearly</option>
    </select>
</div>
<p>Daily statistic show on the cards by default</p>
<!-- Cards for Booking Statistics -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-light">
            <div class="card-body">
                <h5 class="card-title">Total Bookings</h5>
                <p class="card-text" id="total-bookings" style="font-size: 24px;">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-warning">
            <div class="card-body">
                <h5 class="card-title">Pending Bookings</h5>
                <p class="card-text" id="total-pending" style="font-size: 24px;">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-info">
            <div class="card-body">
                <h5 class="card-title">Confirmed Bookings</h5>
                <p class="card-text" id="total-confirmed" style="font-size: 24px;">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-danger">
            <div class="card-body">
                <h5 class="card-title">Cancelled Bookings</h5>
                <p class="card-text" id="total-cancelled" style="font-size: 24px;">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-success">
            <div class="card-body">
                <h5 class="card-title">Completed Bookings</h5>
                <p class="card-text" id="total-completed" style="font-size: 24px;">0</p>
            </div>
        </div>
    </div>
</div>

<!-- Form for Detailed Report and PDF Generation -->
<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">

<!-- Error Message -->
<div id="error-message" class="alert alert-danger alert-dismissible fade show" role="alert" style="display:none;">
    <span id="error-message-text"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Success Message -->
<div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <span id="success-message-text">PDF generated successfully!</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>



<p>Generate report base on booking selection</p>
        <form id="generate-report-form" method="POST" action="{{ route('admin.bookings.report.pdf') }}">
            @csrf
            <!-- Booking Status Filter -->
          <!-- Booking Status Filter -->
<div class="form-group mb-3">
    <label for="booking-status">Select Booking Status</label>
    <select id="booking-status" name="status" class="form-control">
        <option value="All">All</option>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="cancelled">Cancelled</option>
        <option value="completed">Completed</option>
    </select>
</div>

<!-- Service Type Filter -->
<div class="form-group mb-3">
    <label for="service-type">Select Service Type</label>
    <select id="service-type" name="service_type" class="form-control">
        <option value="All">All</option>
        <option value="airport_transfer">Airport Transfer</option>
        <option value="charter">Charter</option>
    </select>
</div>


            <!-- Date Range Filter -->
            <div class="form-group mb-3">
                <label for="date-from">From Date</label>
                <input type="date" id="date-from" name="date_from" class="form-control">
            </div>
            <div class="form-group mb-3">
                <label for="date-to">To Date</label>
                <input type="date" id="date-to" name="date_to" class="form-control">
            </div>

            <!-- Button and Spinner for PDF Generation -->
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
    // Handle the PDF generation form submission
    document.getElementById('generate-report-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        console.log('Form submitted'); // Log form submission

        // Hide any previous messages
        document.getElementById('error-message').style.display = 'none';
        document.getElementById('success-message').style.display = 'none';

        // Show the spinner and hide the button
        document.getElementById('generate-pdf-btn').style.display = 'none';
        document.getElementById('generate-pdf-spinner').style.display = 'inline-block';

        // Send AJAX request to generate the PDF
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'  // Expecting a JSON response for errors
            },
            body: new FormData(this),
        })
        .then(response => {
            console.log('Response status:', response.status); // Log the response status

            if (!response.ok) {
                return response.json().then(data => { throw new Error(data.error || 'An error occurred'); });
            }

            // Get the filename from the Content-Disposition header
            const contentDisposition = response.headers.get('Content-Disposition');
            let filename = 'booking_report.pdf';  // Default filename

            if (contentDisposition) {
                const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(contentDisposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, ''); // Remove quotes if present
                }
            }

            // Process the blob for download
            return response.blob().then(blob => {
                console.log('PDF generation successful'); // Log success
                // Reset the spinner and show the button
                document.getElementById('generate-pdf-spinner').style.display = 'none';
                document.getElementById('generate-pdf-btn').style.display = 'inline-block';

                // Show success message with auto-hide after 5 seconds
                document.getElementById('success-message').innerText = 'PDF generated successfully!';
                document.getElementById('success-message').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('success-message').style.display = 'none';
                }, 5000); // Hide after 5 seconds

                // Create a download link for the PDF
                const downloadLink = document.createElement('a');
                downloadLink.href = URL.createObjectURL(blob);
                downloadLink.download = filename;  // Use dynamic filename from response
                downloadLink.click();
            });
        })
        .catch(error => {
            console.error('Error:', error.message); // Log any error

            // Show error message with auto-hide after 5 seconds
            document.getElementById('error-message').innerText = error.message;
            document.getElementById('error-message').style.display = 'block';
            setTimeout(() => {
                document.getElementById('error-message').style.display = 'none';
            }, 5000); // Hide after 5 seconds

            document.getElementById('generate-pdf-spinner').style.display = 'none';
            document.getElementById('generate-pdf-btn').style.display = 'inline-block';
        });
    });

    // Event listener for the report range dropdown (daily, weekly, monthly, yearly)
    document.getElementById('report-timeframe').addEventListener('change', function() {
    const range = this.value;
    console.log('Selected range:', range); // Log the selected range

    // Send AJAX request to fetch data based on the selected time range
    fetch(`/admin/bookings/report-data/${range}`)
        .then(response => response.json())
        .then(data => {
            console.log('Received data:', data); // Log the received data

            document.getElementById('total-bookings').innerText = data.totalBookings;
            document.getElementById('total-pending').innerText = data.totalPending;
            document.getElementById('total-confirmed').innerText = data.totalConfirmed;
            document.getElementById('total-cancelled').innerText = data.totalCancelled;
            document.getElementById('total-completed').innerText = data.totalCompleted;
        })
        .catch(error => {
            console.error('Error fetching data:', error); // Log any error
        });
});

// Trigger change event to load today's data on page load
document.getElementById('report-timeframe').dispatchEvent(new Event('change'));

</script>

@endpush
