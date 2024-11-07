@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('content')
<h1 class="app-page-title">Generate Payment Report</h1>

<!-- Dropdown for Report Timeframe -->
<div class="mb-4">
    <label for="report-timeframe" class="form-label">Select Statistics Timeframe</label>
    <select id="report-timeframe" class="form-control">
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
        <option value="yearly">Yearly</option>
    </select>
</div>
<p>Daily statistics are shown on the cards by default</p>

<!-- Cards for Payment Statistics -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-light">
            <div class="card-body">
                <h5 class="card-title">Total Payments</h5>
                <p class="card-text" id="total-payments" style="font-size: 24px;">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-info">
            <div class="card-body">
                <h5 class="card-title">Total Refunded</h5>
                <p class="card-text" id="total-refunded" style="font-size: 24px;">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center bg-warning">
            <div class="card-body">
                <h5 class="card-title">Total Unpaid</h5>
                <p class="card-text" id="total-unpaid" style="font-size: 24px;">0</p>
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
<!-- Error Message -->
@if (session('error'))
    <div id="error-message" class="alert alert-danger alert-dismissible fade show" role="alert">
        <span id="error-message-text">{{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif



    <!-- Success Message -->
    <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
        <span id="success-message-text">PDF generated successfully!</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <p>Generate a report based on payment selection</p>
    <form id="generate-report-form" method="POST" action="{{ route('admin.payments.report.pdf') }}">
        @csrf
        <!-- Payment Status Filter -->
        <div class="form-group mb-3">
            <label for="payment-status">Select Payment Status</label>
            <select id="payment-status" name="status" class="form-control">
                <option value="All">All</option>
                <option value="paid">Paid</option>
                <option value="unpaid">Unpaid</option>
                <option value="refunded">Refunded</option>
                <option value="refund-pending">Refund-Pending</option>
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

        // Hide previous messages
        document.getElementById('error-message').style.display = 'none';
        document.getElementById('success-message').style.display = 'none';

        // Show the spinner and hide the button
        document.getElementById('generate-pdf-btn').style.display = 'none';
        document.getElementById('generate-pdf-spinner').style.display = 'inline-block';

        // Log CSRF token to the console (correct placement)
        console.log('{{ csrf_token() }}');

        // Send AJAX request to generate the PDF
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: new FormData(this),
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => { throw new Error(data.error || 'An error occurred'); });
            }

            // Get the filename and blob if successful
            const contentDisposition = response.headers.get('Content-Disposition');
            let filename = 'payment_report.pdf';
            if (contentDisposition) {
                const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(contentDisposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }

            return response.blob().then(blob => {
                document.getElementById('generate-pdf-spinner').style.display = 'none';
                document.getElementById('generate-pdf-btn').style.display = 'inline-block';

                document.getElementById('success-message').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('success-message').style.display = 'none';
                }, 5000);

                const downloadLink = document.createElement('a');
                downloadLink.href = URL.createObjectURL(blob);
                downloadLink.download = filename;
                downloadLink.click();
            });
        })
        .catch(error => {
            document.getElementById('error-message-text').innerText = error.message;
            document.getElementById('error-message').style.display = 'block';
            setTimeout(() => {
                document.getElementById('error-message').style.display = 'none';
            }, 5000);

            document.getElementById('generate-pdf-spinner').style.display = 'none';
            document.getElementById('generate-pdf-btn').style.display = 'inline-block';
        });
    });

    // Handle report timeframe dropdown
    document.getElementById('report-timeframe').addEventListener('change', function() {
        const range = this.value;
        fetch(`/admin/payments/report-data/${range}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-payments').innerText = data.totalPayments;
                document.getElementById('total-refunded').innerText = data.totalRefunded;
                document.getElementById('total-unpaid').innerText = data.totalUnpaid;
            })
            .catch(error => console.error('Error fetching report data:', error));
    });

    // Trigger change event to load today's data by default
    document.getElementById('report-timeframe').dispatchEvent(new Event('change'));
</script>
@endpush
