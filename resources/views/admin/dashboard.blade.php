@extends('admin.layouts.admin-layout')

@section('content')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet">
@endpush


    <h2>Welcome to the Admin Dashboard</h2>
    <p>Admin-specific data and metrics go here.</p>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
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
                    <h3 id="totalSalesToday">{{ $totalSalesToday }}</h3>
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
     <!-- Charts Row -->
     <style>
.chart-container {
    position: relative;
    width: 100%;
    padding: 1rem;
    overflow: hidden;
    height: 100%;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

#bookingVolumeChart, #revenueDistributionChart {
    width: 100% !important;
    height: 350px !important; /* Ensures consistent height */
}
.calendar-container {
    height: 400px; /* Fixed height for the calendar container */
    overflow-y: auto;
}
.fc-daygrid-day-number {
    font-size: 0.8rem; /* Adjust as needed */
}

/* Reduce font size of event titles */
.fc-event-title, .fc-daygrid-event-dot {
    font-size: 0.75rem; /* Adjust as needed */
}

/* Reduce font size of header (e.g., month name) */
.fc-toolbar-title {
    font-size: 1rem; /* Adjust as needed */
}

/* Reduce font size of day names in the header */
.fc-col-header-cell {
    font-size: 0.8rem; /* Adjust as needed */
}

/* Reduce font size in the list view */
.fc-list-event-title, .fc-list-event-time {
    font-size: 0.75rem; /* Adjust as needed */
}

#calendar {
    transform: scale(0.8); /* Scale down to 80% of the original size */
    transform-origin: top left; /* Scale from the top left corner */
    width: 125%; /* Adjust width to fit the container properly after scaling */
    height: 100%; /* Ensure it fills the container’s height */
    overflow: hidden; /* Prevent scrollbars within the calendar */
}

/* Adjust the container to fit the scaled calendar */
#calendar-container {
    height: 400px; /* Set a fixed height that fits well on your page */
    overflow: hidden; /* Hide any overflow */
}

@media (max-width: 768px) {
    .chart-container {
        padding: 1rem 0.5rem; /* Adjust padding for mobile */
    }
    .chart-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .chart-header select {
        margin-top: 0.5rem;
    }
    #bookingVolumeChart, #revenueDistributionChart {
        height: 400px !important; /* Increase height for better visibility on mobile */
    }
    #calendar {
        transform: scale(0.75); /* Further scaling on smaller screens */
        width: 135%; /* Adjust width to fit properly after scaling */
    }
    
    /* Hide 'day' and 'week' options for compact mobile view */
    .fc-toolbar .fc-dayGridMonth-button,
    .fc-toolbar .fc-timeGridWeek-button,
    .fc-toolbar .fc-timeGridDay-button,
    .fc-toolbar .fc-listMonth-button {
        display: none;
    }

    /* Hide unused sections of toolbar on mobile */
    #calendar .fc-toolbar .fc-right {
        display: none;
    }

    /* Adjust font size and layout for the navigation buttons */
    #calendar .fc-toolbar h2 {
        font-size: 1rem; /* Adjust month title font size further */
    }
    #calendar .fc-toolbar .fc-left,
    #calendar .fc-toolbar .fc-center {
        font-size: 0.8rem;
    }
    
}

#calendar .fc-dayGridMonth .fc-dayNumber {
    font-size: 0.9rem; /* Reduce the day number font size */
}

</style>
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
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
        <div class="card">
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
        <div class="card">
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
    <div class="card">
        <div class="card-body chart-container calendar-container">
            <h5>Calendar</h5>
            <div id="calendar"></div> <!-- Calendar container -->
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
                document.getElementById('totalSalesToday').innerText = data.totalSalesToday;
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
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Bookings',
                data: [],
                borderColor: 'blue',
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,  // Allows charts to fill the container height
            scales: {
                x: { beginAtZero: true },
                y: { beginAtZero: true }
            }
        }
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
            maintainAspectRatio: false  // Allows charts to fill the container height
            }
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
            labels: ['Cancelled', 'Confirmed', 'Completed'], // Add 'Confirmed' to the labels
            datasets: [{
                label: 'Booking Status',
                data: [0, 0, 0], // Include an initial zero for 'Confirmed'
                backgroundColor: [ '#F44336', '#2196F3','#4CAF50'], // Different colors for each status
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Bookings'
                    }
                }
            }
        }
    });
}


    function fetchBookingCompletionRateData() {
    const timeFrame = document.getElementById('completionTimeFrame').value; // assuming there's a dropdown with id 'completionTimeFrame'
    fetch(`/admin/booking-completion-rate-data?timeFrame=${timeFrame}`)
        .then(response => response.json())
        .then(data => {
            bookingCompletionRateChart.data.datasets[0].data = [data.confirmed, data.cancelled, data.completed];
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
