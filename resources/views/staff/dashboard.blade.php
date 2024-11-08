@extends('staff.layouts.staff-layout')

@section('title', 'Consultant Dashboard')

@section('content')


<div class="container mt-5">
    <h1 class="app-page-title">Consultant Dashboard</h1>

        <!-- Summary Cards Row -->
        <div class="row mb-4">
        <!-- Today's Total Bookings Card -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Bookings Today</h5>
                    <p class="card-text" id="totalBookingsToday">0</p>
                </div>
            </div>
        </div>

        <!-- Total Open Tickets Today Card -->

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Total Open Tickets</h5>
                    <p class="card-text" id="totalOpenTickets">0</p> <!-- Updated ID -->
                </div>
            </div>
        </div>
        
        <!-- Total Canceled Bookings Today Card -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Total Canceled Today</h5>
                    <p class="card-text" id="totalCanceledToday">0</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Customer Interactions Table -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Recent Customer Interactions</h3>
                </div>
                <div class="card-body">
                    <table id="recent-interactions-table" class="table table-striped table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Customer Name</th>
                                <th>Last Interaction Date</th>
                                <th>Status of Last Interaction</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Booking Status Distribution Chart -->
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Booking Status Distribution</h4>
                    <select id="bookingStatusFilter" class="form-select w-auto">
                        <option value="today" selected>Today</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="bookingStatusChart" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Active Bookings Table -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Active Bookings</h3>
                </div>
                <div class="card-body">
                    <table id="active-bookings-table" class="table table-striped table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Booking Reference</th>
                                <th>Service Type</th>
                                <th>Pickup Date</th>
                                <th>Status</th>
                                <th>Last Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Service Type Usage Chart -->
     <!-- Service Type Usage Chart -->
 <!-- Service Type Usage Chart -->
<div class="col-lg-4 col-md-12">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Service Type Usage</h4>
            <select id="serviceTypeFilter" class="form-select w-auto">
                <option value="today" selected>Today</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
        <div class="card-body" style="height: 480px;"> <!-- Adjust to match the table height -->
            <canvas id="serviceTypeChart" class="chart-canvas" style="height: 100%; width: 100%;"></canvas>
        </div>
    </div>
</div>

    </div>
</div>
@endsection


@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>


<script>
    // Function to fetch and update recent interactions
    $(document).ready(function() {
    // Initialize DataTables without pagination, search, or ordering
    var recentInteractionsTable = $('#recent-interactions-table').DataTable({
        responsive: true,
        paging: false,
        searching: false,
        ordering: false,
        lengthChange: false,
        info: false,
        pageLength: 6
    });

    var activeBookingsTable = $('#active-bookings-table').DataTable({
        responsive: true,
        paging: false,
        searching: false,
        ordering: false,
        lengthChange: false,
        info: false,
        pageLength: 6
    });
    // Function to fetch and update recent interactions
   // Fetch recent interactions and update table
   function fetchRecentInteractions() {
        $.ajax({
            url: "{{ route('staff.fetch.recentInteractions') }}",
            method: 'GET',
            success: function(data) {
                recentInteractionsTable.clear();
                data.slice(0, 6).forEach(function(interaction, index) {
                    recentInteractionsTable.row.add([
                        index + 1,
                        interaction.customer_name,
                        new Date(interaction.last_interaction_date).toLocaleString(),
                        `<span class="badge ${interaction.status === 'open' ? 'bg-warning' : 'bg-success'}">
                            ${interaction.status.charAt(0).toUpperCase() + interaction.status.slice(1)}
                        </span>`,
                        `<a href="/admin/support-tickets/${interaction.id}/view" class="btn btn-sm btn-info">View</a>`
                    ]);
                });
                recentInteractionsTable.draw();
            }
        });
    }


    // Function to fetch and update active bookings
    function fetchActiveBookings() {
        $.ajax({
            url: "{{ route('staff.fetch.activeBookings') }}",
            method: 'GET',
            success: function(data) {
                activeBookingsTable.clear();
                data.slice(0, 6).forEach(function(booking, index) {
                    activeBookingsTable.row.add([
                        index + 1,
                        booking.booking_reference,
                        booking.service_type,
                        new Date(booking.pickup_date).toLocaleDateString(),
                        `<span class="badge ${booking.status === 'pending' ? 'bg-danger' : 'bg-success'}">
                            ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                        </span>`,
                        new Date(booking.last_updated_by).toLocaleString() || 'N/A',
                        `<a href="/admin/bookings/${booking.id}/view" class="btn btn-sm btn-info">View</a>`
                    ]);
                });
                activeBookingsTable.draw();
            }
        });
    }
    // Poll every 5 seconds to update tables
    setInterval(fetchRecentInteractions, 5000);
    setInterval(fetchActiveBookings, 5000);

    // Initial load
    fetchRecentInteractions();
    fetchActiveBookings();

 //charts
     // Confirm Chart.js is loaded
     let bookingStatusChart, serviceTypeChart;

// Function to fetch and render Booking Status Distribution
function fetchBookingStatusDistribution(filter = 'today') {
    $.ajax({
        url: "{{ route('staff.dashboard.bookingStatusDistribution') }}",
        method: 'GET',
        data: { timeframe: filter },
        success: function(data) {
            const labels = Object.keys(data);
            const values = Object.values(data);
            const backgroundColors = {
                'completed': '#28a745', // Green
                'pending': '#ffc107',   // Yellow
                'cancelled': '#dc3545', // Red
                'expired': '#6c757d',   // Grey
                'confirmed': '#17a2b8'  // Sky blue
            };
            const chartColors = labels.map(label => backgroundColors[label.toLowerCase()] || '#cccccc');

            if (bookingStatusChart) {
                bookingStatusChart.data.labels = labels;
                bookingStatusChart.data.datasets[0].data = values;
                bookingStatusChart.data.datasets[0].backgroundColor = chartColors;
                bookingStatusChart.update();
            } else {
                bookingStatusChart = new Chart(document.getElementById('bookingStatusChart').getContext('2d'), {
                    type: 'pie',
                    data: { labels: labels, datasets: [{ data: values, backgroundColor: chartColors }] },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        },
        error: function() {
            console.error('Failed to fetch booking status distribution data.');
        }
    });
}

// Function to fetch and render Service Type Usage
function fetchServiceTypeUsage(filter = 'today') {
    $.ajax({
        url: "{{ route('staff.dashboard.serviceTypeUsage') }}",
        method: 'GET',
        data: { timeframe: filter },
        success: function(data) {
            const labels = Object.keys(data);
            const values = Object.values(data);
            const backgroundColors = ['#4BC0C0', '#FF6384'];

            if (serviceTypeChart) {
                serviceTypeChart.data.labels = labels;
                serviceTypeChart.data.datasets[0].data = values;
                serviceTypeChart.update();
            } else {
                serviceTypeChart = new Chart(document.getElementById('serviceTypeChart').getContext('2d'), {
                    type: 'bar',
                    data: { labels: labels, datasets: [{ label: 'Number of Bookings', data: values, backgroundColor: backgroundColors }] },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Bookings' } },
                            x: { title: { display: true, text: 'Service Type' } }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            }
        },
        error: function() {
            console.error('Failed to fetch service type usage data.');
        }
    });
}

// Initial load with "Today" as default
fetchBookingStatusDistribution();
fetchServiceTypeUsage();

// Event listeners for dropdowns to update charts based on selected timeframe
$('#bookingStatusFilter').on('change', function() {
    fetchBookingStatusDistribution(this.value);
});

$('#serviceTypeFilter').on('change', function() {
    fetchServiceTypeUsage(this.value);
});

// Poll every 10 seconds for real-time updates
setInterval(() => fetchBookingStatusDistribution($('#bookingStatusFilter').val()), 10000);
setInterval(() => fetchServiceTypeUsage($('#serviceTypeFilter').val()), 10000);

   // Function to fetch and update today's dashboard stats
      // Function to fetch and update dashboard stats
      function fetchTodayStats() {
        $.ajax({
            url: "{{ route('staff.dashboard.today-stats') }}",
            method: 'GET',
            success: function(data) {
                $('#totalBookingsToday').text(data.totalBookingsToday);
                $('#totalOpenTickets').text(data.totalOpenTickets);  // Updated ID
                $('#totalCanceledToday').text(data.totalCanceledToday);
            },
            error: function() {
                console.error('Failed to fetch dashboard stats.');
            }
        });
    }

    // Initial load
    fetchTodayStats();

    // Poll every 5 seconds for real-time updates
    setInterval(fetchTodayStats, 5000);

});


</script>
@endpush
