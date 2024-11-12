@extends('admin.layouts.admin-layout')

@section('content')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet">
@endpush

<h2>Welcome to the Admin Dashboard</h2>
<p>Admin-specific data and metrics go here.</p>

<!-- Summary Cards -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>Total Active Passengers</h5>
                <h3 id="totalActivePassengers">{{ $totalActivePassengers }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Total Bookings Today</h5>
                <h3 id="totalBookingsToday">{{ $totalBookingsToday }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>Total Sales Today</h5>
                <h3 id="totalSalesToday">₦{{ number_format($totalSalesToday, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5>Open Support Tickets</h5>
                <h3 id="openTickets">{{ $openTickets }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- CSS for consistent height -->
<style>
.chart-card-height {
    height: 400px; /* Adjust the height as needed */
}

.chart-container {
        position: relative;
        padding: 20px; /* Add padding around the chart for axes visibility */
    }



#bookingVolumeChart, #revenueDistributionChart,  #calendar {
    width: 90% !important;
    height: 95% !important;
}
#bookingCompletionRateChart {
    width: 100% !important;
    height: 100% !important;
}

.chart-container {
    height: 400px; /* Increase height for more space */
    padding-bottom: 30px; /* Extra padding for bottom axis */
}

#calendar{
    width: 100% !important;
    height: 85% !important;
    font-size: smaller;
}
</style>

<!-- Charts Row -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card chart-card-height">
            <div class="card-body chart-container">
                <h5>Booking Volume Over Time</h5>
                <select id="timeFrame" class="form-select mb-3" onchange="fetchBookingVolumeData()">
                    <option value="today">Today</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
                <canvas id="bookingVolumeChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card chart-card-height">
            <div class="card-body chart-container">
                <h5>Revenue Distribution by Service Type</h5>
                <canvas id="revenueDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Booking Completion Rate & Calendar Row -->
<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card chart-card-height">
            <div class="card-body chart-container">
                <h5>Booking Completion Rate</h5>
                <select id="completionTimeFrame" class="form-select mb-3">
                    <option value="today" selected>Today</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
                <canvas id="bookingCompletionRateChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card chart-card-height">
            <div class="card-body chart-container calendar-container">
                <h5>Calendar</h5>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings and Recent Payments -->
<div class="row mt-4">
    <!-- Recent Bookings Table -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Recent Bookings</h5>
            </div>
            <div class="card-body">
                <table id="recentBookingsTable" class="table table-hover table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Booking Reference</th>
                            <th>Booking Date</th>
                            <th>Service Type</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTable -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Payments Table -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Recent Payments</h5>
            </div>
            <div class="card-body">
                <table id="recentPaymentsTable" class="table table-hover table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Booking Reference</th>
                            <th>Invoice Number</th>
                            <th>Amount</th>
                            <th>Invoice Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTable -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<!-- DataTables CSS and JS -->
<link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
    // Function to fetch and update dashboard data in real-time
    function fetchDashboardData() {
    console.log('Fetching real-time dashboard data');
    fetch('/admin/dashboard-data')
        .then(response => response.json())
        .then(data => {
            // Update each card with the real-time data
            document.getElementById('totalActivePassengers').innerText = data.totalActivePassengers;
            document.getElementById('totalBookingsToday').innerText = data.totalBookingsToday;
            document.getElementById('totalSalesToday').innerText = `₦${parseFloat(data.totalSalesToday).toFixed(2)}`;
            document.getElementById('openTickets').innerText = data.openTickets;

            console.log('Dashboard data updated:', data);
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
        });
}


    // Poll every 10 seconds for real-time updates
    setInterval(fetchDashboardData, 10000);

    // Initial fetch to load data as soon as the page loads
    fetchDashboardData();

    let bookingVolumeChart, revenueDistributionChart;

    function initializeCharts() {
    const bookingVolumeCtx = document.getElementById('bookingVolumeChart').getContext('2d');
    bookingVolumeChart = new Chart(bookingVolumeCtx, {
        type: 'line', // Keep as 'line' but add fill for area effect
        data: {
            labels: [], // Update with actual data later
            datasets: [{
                label: 'Bookings',
                data: [], // Update with actual data later
                borderColor: 'blue',
                backgroundColor: 'rgba(0, 123, 255, 0.2)', // Set a semi-transparent fill color
                fill: true, // Enables the area below the line
                tension: 0.3 // Optional: adds a slight curve for smoothness
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    display: true, // Always display labels
                    color: 'black',
                    align: 'top',
                    font: {
                        weight: 'bold'
                    },
                    formatter: function(value, context) {
                        return value; // Show the value directly on each point
                    }
                }
            },
            tooltips: {
                enabled: true, // Enable tooltips on hover
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += tooltipItem.yLabel;
                        return label;
                    }
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                x: { beginAtZero: true },
                y: { beginAtZero: true }
            }
        },
        plugins: [ChartDataLabels]
    });

    const revenueCtx = document.getElementById('revenueDistributionChart').getContext('2d');
    revenueDistributionChart = new Chart(revenueCtx, {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    display: true,
                    color: 'white',
                    font: {
                        weight: 'bold'
                    },
                    formatter: function(value, context) {
                        return value; // Display value directly on pie slices
                    }
                }
            },
            tooltips: {
                enabled: true,
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.labels[tooltipItem.index] || '';
                        if (label) {
                            label += ': ';
                        }
                        label += data.datasets[0].data[tooltipItem.index];
                        return label;
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

        // Function to fetch chart data in real-time
        function fetchBookingVolumeData() {
        const timeFrame = document.getElementById('timeFrame').value;
        console.log('Fetching booking volume data for time frame:', timeFrame);

        fetch(`/admin/booking-volume-data?timeFrame=${timeFrame}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Booking volume data received:', data);

                bookingVolumeChart.data.labels = data.labels;
                bookingVolumeChart.data.datasets[0].data = data.data;
                bookingVolumeChart.update();
            })
                .catch(error => console.error('Error fetching booking volume data:', error));
        }

        function fetchRevenueDistributionData() {
        console.log('Fetching revenue distribution data');

        fetch('/admin/revenue-distribution-data')
            .then(response => response.json())
            .then(data => {
                console.log('Revenue distribution data received:', data);

                // Combine booking count and revenue for display
                const combinedLabels = data.labels.map((label, index) => {
                    return `${label}: ${data.bookingCounts[index]} bookings, ${data.totalRevenue[index]}`;
                });

                // Update Revenue Distribution Chart
                revenueDistributionChart.data.labels = combinedLabels;
                revenueDistributionChart.data.datasets[0].data = data.bookingCounts; // Use booking counts for chart values
                revenueDistributionChart.update();
            })
            .catch(error => console.error('Error fetching revenue distribution data:', error));
    }

    let bookingCompletionRateChart;

    
// Initialize the Booking Completion Rate Chart with dropdown
function initializeCompletionRateChart() {
    const ctx = document.getElementById('bookingCompletionRateChart').getContext('2d');
    bookingCompletionRateChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Cancelled', 'Confirmed', 'Completed'],
            datasets: [{
                label: 'Booking Status',
                data: [0, 0, 0],
                backgroundColor: ['#F44336', '#2196F3', '#4CAF50']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allow chart to adjust to container
            layout: {
                padding: {
                    top: 20,
                    right: 20,
                    bottom: 40, // Extra padding for bottom axis
                    left: 20
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Booking Status',
                        font: {
                            size: 14
                        }
                    },
                    ticks: {
                        maxRotation: 0,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Bookings',
                        font: {
                            size: 14
                        }
                    },
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Hide legend if it overlaps with the chart
                }
            }
        }
    });
}



function fetchBookingCompletionRateData() {
    const timeFrame = document.getElementById('completionTimeFrame').value;
    fetch(`/admin/booking-completion-rate-data?timeFrame=${timeFrame}`)
        .then(response => response.json())
        .then(data => {
            // Update the chart data in the correct order
            bookingCompletionRateChart.data.datasets[0].data = [data.cancelled, data.confirmed, data.completed];
            bookingCompletionRateChart.update();
        })
        .catch(error => console.error('Error fetching booking completion rate data:', error));
}



// Event listener to update chart when dropdown changes
document.getElementById('completionTimeFrame').addEventListener('change', fetchBookingCompletionRateData);



        // Update initial load and interval
        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
            initializeCompletionRateChart();
            fetchBookingVolumeData();
            fetchRevenueDistributionData();
            fetchBookingCompletionRateData();
            setInterval(fetchBookingVolumeData, 10000);
            setInterval(fetchRevenueDistributionData, 10000);
            setInterval(fetchBookingCompletionRateData, 10000);


              // Calendar initialization
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', // Default to month view
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: '/admin/calendar-events', // Route to fetch calendar events
            editable: true,
            selectable: true,
            eventClick: function (info) {
                alert('Event: ' + info.event.title);
            }
        });

        calendar.render();


        });



</script>
<script>

$(document).ready(function() {
    // Recent Bookings Table
 // Recent Bookings Table
 $('#recentBookingsTable').DataTable({
        responsive: true,
        paging: false,        // Disable pagination
        searching: false,     // Disable search
        info: false,          // Disable table info
        lengthMenu: [6],      // Show only 6 rows
        ajax: {
            url: '/admin/recent-bookings-data',
            dataSrc: '',
            error: function(xhr, status, error) {
                console.error('Error fetching recent bookings data:', error);
                console.log('Response:', xhr.responseText);
            }
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;  // Generate S/N starting from 1
                },
                title: "S/N" 
            },
            { data: 'booking_reference', title: "Booking Reference" },
            { data: 'booking_date', title: "Booking Date" },
            
            { data: 'service_type', title: "Service Type" },
            { 
                data: 'status', 
                title: "Status",
                render: function(data) {
                    let badgeClass = '';
                    switch (data) {
                        case 'completed':
                            badgeClass = 'success';
                            break;
                        case 'expired':
                            badgeClass = 'secondary';
                            break;
                        case 'pending':
                            badgeClass = 'warning';
                            break;
                        case 'cancelled':
                            badgeClass = 'danger';
                            break;
                        default:
                            badgeClass = 'info';
                    }
                    return `<span class="badge bg-${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { data: 'created_by', title: "Created By" },
            {
                data: null,
                title: "Actions",
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-primary btn-sm" onclick="editBooking(${data.id})">Edit</button>
                        <a href="/admin/bookings/${data.id}/view" class="btn btn-sm btn-info">View</a>
                    `;
                }
            }
        ]
    });

    // Recent Payments Table
    $('#recentPaymentsTable').DataTable({
        responsive: true,
        paging: false,        // Disable pagination
        searching: false,     // Disable search
        info: false,          // Disable table info
        lengthMenu: [6],      // Show only the top 6 records
        ajax: {
            url: '/admin/recent-payments-data',
            dataSrc: '',
            error: function(xhr, status, error) {
                console.error('Error fetching recent payments data:', error);
                console.log('Response:', xhr.responseText);
            }
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;  // Generate S/N starting from 1
                },
                title: "S/N" 
            },
            { data: 'booking_reference', title: "Booking Reference" },
            { data: 'invoice_number', title: "Invoice Number" },
            { data: 'amount', title: "Amount" },
            { data: 'invoice_date', title: "Invoice Date" },
            { 
                data: 'status', 
                title: "Status",
                render: function(data) {
                    let badgeClass = '';
                    switch (data) {
                        case 'paid':
                            badgeClass = 'success';
                            break;
                        case 'unpaid':
                            badgeClass = 'danger';
                            break;
                        case 'refunded':
                            badgeClass = 'info';
                            break;
                        case 'refund-pending':
                            badgeClass = 'warning';
                            break;
                        default:
                            badgeClass = 'secondary'; // Default badge for any other statuses
                    }
                    return `<span class="badge bg-${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            }
        ]
    });
});


// Action functions for recent bookings
function editBooking(id) {
    console.log(`Editing booking with ID: ${id}`);
    window.location.href = `/admin/bookings/${id}/edit`;
}


function viewBooking(id) {
    console.log(`Viewing booking with ID: ${id}`);
    window.location.href = `/admin/bookings/${id}/view`;
}
</script>

@endpush
