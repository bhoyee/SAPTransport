@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('title', 'Sales Report')

@section('content')
<div class="container mt-5">
    <h4 class="fw-bold">Sales Report</h4>



    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Today's Total Sales</h5>
                    <p class="card-text" id="today-sales">₦0.00</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">This Week's Total Sales</h5>
                    <p class="card-text" id="week-sales">₦0.00</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">This Month's Total Sales</h5>
                    <p class="card-text" id="month-sales">₦0.00</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">This Year's Total Sales</h5>
                    <p class="card-text" id="year-sales">₦0.00</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Report PDF Download Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="fw-bold">Generate Sales Report</h5>

                <!-- Display Alert Messages -->
    <div id="alert-container" class="mb-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
            <div class="row">
                <div class="col-md-5">
                    <label for="from-date" class="form-label">From Date</label>
                    <input type="date" id="from-date" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <label for="to-date" class="form-label">To Date</label>
                    <input type="date" id="to-date" class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="download-report-btn" class="btn btn-primary w-100">
                        <span id="download-text">Download Sales Report</span>
                        <span id="download-spinner" class="spinner-border spinner-border-sm" style="display: none;" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Function to load sales data
    function loadSalesData() {
        $.ajax({
            url: "{{ route('admin.salesReport.fetch') }}",
            method: 'GET',
            success: function(response) {
                $('#today-sales').text(`₦${parseFloat(response.today).toFixed(2)}`);
                $('#week-sales').text(`₦${parseFloat(response.week).toFixed(2)}`);
                $('#month-sales').text(`₦${parseFloat(response.month).toFixed(2)}`);
                $('#year-sales').text(`₦${parseFloat(response.year).toFixed(2)}`);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching sales data:', error);
            }
        });
    }

    // Initial load
    loadSalesData();

    // Refresh data every 30 seconds
    setInterval(loadSalesData, 30000);

    // Download Sales Report
    $('#download-report-btn').on('click', function() {
        const fromDate = $('#from-date').val();
        const toDate = $('#to-date').val();
        const btnText = $('#download-text');
        const btnSpinner = $('#download-spinner');

        // Validate date inputs
        if (!fromDate || !toDate) {
            showAlert('Please select both from and to dates.', 'danger');
            return;
        }

        // Show spinner and hide the button text
        btnText.hide();
        btnSpinner.show();

        // Make AJAX request to download the sales report
        $.ajax({
            url: "{{ route('admin.salesReport.download') }}",
            method: 'GET',
            data: { from: fromDate, to: toDate },
            xhrFields: { responseType: 'blob' },
            success: function(response, status, xhr) {
                const disposition = xhr.getResponseHeader('content-disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    const downloadUrl = window.URL.createObjectURL(new Blob([response]));
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = `sales_report_${fromDate}_to_${toDate}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    showAlert('Sales report downloaded successfully.', 'success');
                } else {
                    showAlert('No data found for the specified date range.', 'danger');
                }
            },
            error: function(xhr) {
                showAlert('Failed to generate the sales report.', 'danger');
            },
            complete: function() {
                btnText.show();
                btnSpinner.hide();
            }
        });
    });

    // Function to display alert messages
    function showAlert(message, type) {
        const alertDiv = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert"></div>')
            .text(message)
            .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');

        $('#alert-container').html(alertDiv);
    }
});
</script>
@endpush
