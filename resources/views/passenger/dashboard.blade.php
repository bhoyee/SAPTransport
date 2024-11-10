@extends('layouts.passenger')

@section('title', 'Passenger Dashboard')
@push('styles')

	<!-- Custom CSS for Mobile-Friendly Table -->
<style>
/* Sidebar positioning and overflow behavior */
/* Style for Recent Activity area */
#userActivityList .list-group-item {
    font-size: 14px; /* Default size for desktop */
}

/* General table styles */
.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

th, td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
    white-space: nowrap;
}

/* Button group styling */
.button-group {
    display: flex;
    justify-content: flex-start;
    gap: 5px;
}

.button-group .btn {
    padding: 5px 8px;
    font-size: 10px;
}

/* Mobile view for tables */
@media (max-width: 768px) {

    #userActivityList .list-group-item {
        font-size: 12px; /* Smaller font size for mobile */
    }
    table, thead, tbody, th, td, tr {
        display: block; /* Each element becomes block-level */
    }
    
    th {
        display: none; /* Hide table headers */
    }

    td {
        position: relative;
        padding-left: 50%;
        text-align: right; /* Align text to the right for a better mobile view */
    }

    /* Add labels using the data-label attribute */
    td[data-label]:before {
        content: attr(data-label); /* Show the data-label attribute as content */
        position: absolute;
        left: 10px;
        font-weight: bold;
        text-align: left;
    }

    td {
        display: block;
        font-size: 14px; /* Increase the font size slightly for readability */
    }
}

</style>

@endpush

@section('content')
 
			    <h1 class="app-page-title">Overview</h1>
			    
			    <div class="app-card alert alert-dismissible shadow-sm mb-4 border-left-decoration" role="alert">
				    <div class="inner">
					    <div class="app-card-body p-3 p-lg-4">
						    <h3 class="mb-3">Welcome, {{ Auth::user()->name }}!</h3>
						    <div class="row gx-5 gy-3">
						        <div class="col-12 col-lg-9">
							        
							        <div>Ready to plan your next trip? Book your transport easily and get on the road!</div>
							    </div><!--//col-->
							    <div class="col-12 col-lg-3">
								    <a class="btn btn-primary" href="{{ url('/') }}"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-file-earmark-arrow-down me-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path d="M4 0h5.5v1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h1V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/>
  <path d="M9.5 3V0L14 4.5h-3A1.5 1.5 0 0 1 9.5 3z"/>
  <path fill-rule="evenodd" d="M8 6a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 10.293V6.5A.5.5 0 0 1 8 6z"/>
</svg>Book Now</a>
							    </div><!--//col-->
						    </div><!--//row-->
						    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					    </div><!--//app-card-body-->
					    
				    </div><!--//inner-->
			    </div><!--//app-card-->

				<div class="small-cards mt-5 mb-4">
                    <div class="row">
                        <!-- Col sm 6, col md 6, col lg 3 -->
                        <div class="col-sm-6 col-md-6 col-lg-3 mt-3 mt-lg-0">
                            <!-- Card -->
                            <div class="card border-0 rounded-lg">
                                <!-- Card body -->
                                <div class="card-body">

                                    <div class="d-flex flex-row justify-content-center align-items-center">
                                        <!-- Icon -->
                                        <div class="small-card-icon">
									
                                            <i class="fa-solid fa-car card-icon-bg-primary fa-3x"></i>
                                        </div>
                                        <!-- Text -->
                                        <div class="small-card-text w-100 text-center">
                                            <p class="font-weight-normal m-0 text-muted">Total Trips</p>
                                            <h4 class="font-weight-normal m-0 text-primary" id="total-trips">0</h4>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Col sm 6, col md 6, col lg 3 -->
                        <div class="col-sm-6 col-md-6 col-lg-3 mt-3 mt-lg-0">
                            <!-- Card -->
                            <div class="card border-0 rounded-lg">
                                <!-- Card body -->
                                <div class="card-body">

                                    <div class="d-flex flex-row justify-content-center align-items-center">
                                        <!-- Icon -->
                                        <div class="small-card-icon">
										<i class="far fa-money-bill-alt card-icon-bg-primary fa-3x"></i>
                                           
                                        </div>
                                        <!-- Text -->
                                        <div class="small-card-text w-100 text-center">
                                            <p class="font-weight-normal m-0 text-muted">Total Payments</p>
                                            <h4 class="font-weight-normal m-0 text-primary">₦ <span id="total-payment"></span></h4>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Col sm 6, col md 6, col lg 3 -->
                        <div class="col-sm-6 col-md-6 col-lg-3 mt-3 mt-lg-0">
                            <!-- Card -->
                            <div class="card border-0 rounded-lg">
                                <!-- Card body -->
                                <div class="card-body">

                                    <div class="d-flex flex-row justify-content-center align-items-center">
                                        <!-- Icon -->
                                        <div class="small-card-icon">
                                            <i class="fa-solid fa-ban card-icon-bg-primary fa-3x"></i>
                                        </div>
                                        <!-- Text -->
									
                                        <div class="small-card-text w-100 text-center">
                                            <p class="font-weight-normal m-0 text-muted">Cancelled Trips</p>
                                            <h4 class="font-weight-normal m-0 text-primary" id="cancelled-trips">0</h4>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Col sm 6, col md 6, col lg 3 -->
                        <div class="col-sm-6 col-md-6 col-lg-3 mt-3 mt-lg-0">
                            <!-- Card -->
                            <div class="card border-0 rounded-lg">
                                <!-- Card body -->
                                <div class="card-body">

                                    <div class="d-flex flex-row justify-content-center align-items-center">
                                        <!-- Icon -->
                                        <div class="small-card-icon">
										<i class="fas fa-coins card-icon-bg-primary fa-3x"></i>
                                        </div>
                                        <!-- Text -->
                                        <div class="small-card-text w-100 text-center">
                                            <p class="font-weight-normal m-0 text-muted">UpComing Trips</p>
                                            <h4 class="font-weight-normal m-0 text-primary" id="upcoming-trips">0</h4>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>
                </div>

			    <div class="row g-4 mb-4">
			        <div class="col-12 col-lg-6">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">
							        <div class="col-auto">
						                <h4 class="app-card-title">Booking Chart</h4>
							        </div><!--//col-->
							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="charts.html"></a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->
						        </div><!--//row-->
					        </div><!--//app-card-header-->
					        <div class="app-card-body p-3 p-lg-4">
							    <div class="mb-3 d-flex">   
							        <select id="timeFilter" class="form-select form-select-sm ms-auto d-inline-flex w-auto">
								
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                        <option value="year">This Year</option>
									</select>
							    </div>
						        <div class="chart-container">
				                    <canvas id="completedBookingsChart" ></canvas>
						        </div>
					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->

    <div class="col-12 col-lg-6">
        <div class="app-card app-card-progress-list h-100 shadow-sm">
            <div class="app-card-header p-3">
                <h4 class="app-card-title">Recent Booking</h4>
            </div>
            <div class="table-responsive">
                  <table id="recent-bookings" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th id="booking-ref-header">Booking Ref</th>
                            <th id="booking-date-header">Booking Date</th>
                            <th id="service-type-header">Service Type</th>
                            <th id="status-header">Status</th>
                            <th id="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Booking data will be injected here -->
                    </tbody>
                </table>
            </div>

        </div>

    </div>



			        
			    </div><!--//row-->
			    

			    <div class="row g-4 mb-4">
				<!-- Responsive Recent Bookings Table -->


					<!-- Responsive Payment History Table -->
					<!-- Responsive Payment History Table -->
                    <div class="col-12 col-lg-6">
                        <div class="app-card app-card-stats-table h-100 shadow-sm">
                            <div class="app-card-header p-3">
                                <h4 class="app-card-title">Payment History</h4>
                            </div>
                            <div class="container">
                                <div class="table-responsive">
                                    <table id="payment-history" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Booking Ref</th>
                                                <th>Invoice Num</th>
                                                <th>Amount</th>
                                                <th>Invoice Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Payment data will be injected here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

					
					
								<!-- RRecent Activity -->
                        <!-- Responsive Recent Activity -->
                        <div class="col-12 col-lg-6">
                            <div class="app-card app-card-stats-table h-100 shadow-sm">
                                <div class="app-card-header p-3">
                                    <h4 class="app-card-title">Recent Activity</h4>
                                </div>
                                <div class="container">
                                    <ul id="userActivityList" class="list-group">
                                        <!-- User activity items will be appended here -->
                                    </ul>

                                    <!-- Pagination Buttons -->
                                    <div class="d-flex justify-content-between mt-3">
                                        <button id="previousPageBtn" class="btn btn-secondary btn-sm" disabled>Previous</button>
                                        <button id="nextPageBtn" class="btn btn-secondary btn-sm">Next</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                                            
					


			    </div><!--//row-->

			    <div class="row g-4 mb-4">

				    <div class="col-12 col-lg-4">

					    <div class="app-card app-card-basic d-flex flex-column align-items-start shadow-sm">

						    <div class="app-card-header p-3 border-bottom-0">

						        <div class="row align-items-center gx-3">

							        <div class="col-auto">

								        <div class="app-icon-holder">

										    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-receipt" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

  <path fill-rule="evenodd" d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zm.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51z"/>

  <path fill-rule="evenodd" d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>

</svg>

									    </div><!--//icon-holder-->

						                

							        </div><!--//col-->

							        <div class="col-auto">

								        <h4 class="app-card-title">Invoices</h4>

							        </div><!--//col-->

						        </div><!--//row-->

						    </div><!--//app-card-header-->

						    <div class="app-card-body px-4">

							    
							<div class="intro">View and manage your invoices to keep track of payments and billing history.</div>


						    </div><!--//app-card-body-->

						    <div class="app-card-footer p-4 mt-auto">

							   <a class="btn btn-secondary" href="#">View Invoices</a>

						    </div><!--//app-card-footer-->

						</div><!--//app-card-->

				    </div><!--//col-->

				    <div class="col-12 col-lg-4">

					    <div class="app-card app-card-basic d-flex flex-column align-items-start shadow-sm">

						    <div class="app-card-header p-3 border-bottom-0">

						        <div class="row align-items-center gx-3">

							        <div class="col-auto">

								        <div class="app-icon-holder">

										    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-code-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

  <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>

  <path fill-rule="evenodd" d="M6.854 4.646a.5.5 0 0 1 0 .708L4.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0zm2.292 0a.5.5 0 0 0 0 .708L11.793 8l-2.647 2.646a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708 0z"/>

</svg>

									    </div><!--//icon-holder-->

						                

							        </div><!--//col-->

							        <div class="col-auto">

								        <h4 class="app-card-title">Support</h4>

							        </div><!--//col-->

						        </div><!--//row-->

						    </div><!--//app-card-header-->

						    <div class="app-card-body px-4">

							    

							<div class="intro">Get help with any issues or questions. Reach out to our support team anytime.</div>

						    </div><!--//app-card-body-->

						    <div class="app-card-footer p-4 mt-auto">

							   <a class="btn btn-secondary" href="#">Contact Support</a>

						    </div><!--//app-card-footer-->

						</div><!--//app-card-->

				    </div><!--//col-->

				    <div class="col-12 col-lg-4">

					    <div class="app-card app-card-basic d-flex flex-column align-items-start shadow-sm">

						    <div class="app-card-header p-3 border-bottom-0">

						        <div class="row align-items-center gx-3">

							        <div class="col-auto">

								        <div class="app-icon-holder">

										    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-tools" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

  <path fill-rule="evenodd" d="M0 1l1-1 3.081 2.2a1 1 0 0 1 .419.815v.07a1 1 0 0 0 .293.708L10.5 9.5l.914-.305a1 1 0 0 1 1.023.242l3.356 3.356a1 1 0 0 1 0 1.414l-1.586 1.586a1 1 0 0 1-1.414 0l-3.356-3.356a1 1 0 0 1-.242-1.023L9.5 10.5 3.793 4.793a1 1 0 0 0-.707-.293h-.071a1 1 0 0 1-.814-.419L0 1zm11.354 9.646a.5.5 0 0 0-.708.708l3 3a.5.5 0 0 0 .708-.708l-3-3z"/>

  <path fill-rule="evenodd" d="M15.898 2.223a3.003 3.003 0 0 1-3.679 3.674L5.878 12.15a3 3 0 1 1-2.027-2.027l6.252-6.341A3 3 0 0 1 13.778.1l-2.142 2.142L12 4l1.757.364 2.141-2.141zm-13.37 9.019L3.001 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z"/>

</svg>

									    </div><!--//icon-holder-->

						                

							        </div><!--//col-->

							        <div class="col-auto">

								        <h4 class="app-card-title">Settings</h4>

							        </div><!--//col-->

						        </div><!--//row-->

						    </div><!--//app-card-header-->

						    <div class="app-card-body px-4">

							    

							<div class="intro">Customize your preferences and manage account settings for a personalized experience.</div>

						    </div><!--//app-card-body-->

						    <div class="app-card-footer p-4 mt-auto">

							   <a class="btn btn-secondary" href="#">Update Settings</a>

						    </div><!--//app-card-footer-->

						</div><!--//app-card-->

				    </div><!--//col-->

			    </div><!--//row-->
				<div>
	
				</div>


			<!-- Modal for Booking Cancellation -->
			<div class="modal fade" id="cancelBookingModal" tabindex="-1" role="dialog" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="cancelBookingModalLabel">Confirm Cancellation</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> <!-- Updated the close button -->
						</div>
						<div class="modal-body">
							Are you sure you want to cancel this booking?
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button> <!-- Updated the "No" button -->
							<button type="button" id="confirmCancelButton" class="btn btn-danger">Yes, Cancel Booking</button>
						</div>
					</div>
				</div>
			</div>


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let bookingIdToCancel = null; // Store the booking ID for cancellation
    const confirmCancelButton = document.getElementById('confirmCancelButton'); // "Yes, Cancel Booking" button
    const ctx = document.getElementById('completedBookingsChart').getContext('2d');
    let bookingChart = null;
    let currentPage = 1; // Track current page for activities

    // Initialize the DataTable for recent bookings
        new DataTable('#recent-bookings', {
        responsive: true,
        searching: false,
        paging: false,
        lengthChange: false,
        bInfo: false,
        fixedHeader: true
    });

    new DataTable('#payment-history', {
        responsive: true,
        searching: false,
        paging: false,
        lengthChange: false,
        bInfo: false,
        fixedHeader: true
    });


    // Function to update the bookings chart with real-time data
    function updateChart() {
        const filter = document.getElementById('timeFilter').value;

        fetch('{{ route('passenger.bookings.chartData') }}?filter=' + filter)
            .then(response => response.json())
            .then(data => {
                if (bookingChart) {
                    bookingChart.destroy(); // Destroy previous chart instance
                }

                bookingChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Completed Bookings',
                                data: data.completedBookings,
                                backgroundColor: 'rgba(0, 128, 0, 1)', // Deep green
                                borderColor: 'rgba(0, 128, 0, 1)', // Deep green border
                                borderWidth: 1
                            },
                            {
                                label: 'Cancelled Bookings',
                                data: data.cancelledBookings,
                                backgroundColor: 'rgba(255, 0, 0, 1)', // Deep red
                                borderColor: 'rgba(255, 0, 0, 1)', // Deep red border
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }

    // Function to fetch and display user activities with pagination
    function updateUserActivities(page = 1) {
        fetch(`{{ route('passenger.activities') }}?page=${page}`)
            .then(response => response.json())
            .then(data => {
                const activityList = document.getElementById('userActivityList');
                const previousPageBtn = document.getElementById('previousPageBtn');
                const nextPageBtn = document.getElementById('nextPageBtn');
                
                activityList.innerHTML = ''; // Clear previous activities

                if (data.activities.length > 0) {
                    data.activities.forEach(activity => {
                        const listItem = document.createElement('li');
                        listItem.className = 'list-group-item';
                        listItem.innerHTML = `<strong>${activity.description}</strong> <small class="text-muted">(${new Date(activity.created_at).toLocaleString()})</small>`;
                        activityList.appendChild(listItem);
                    });
                } else {
                    activityList.innerHTML = '<li class="list-group-item">No recent activity found.</li>';
                }

                // Handle pagination buttons
                previousPageBtn.disabled = (data.page === 1);
                nextPageBtn.disabled = !data.hasNextPage;
            })
            .catch(error => console.error('Error fetching user activities:', error));
    }

    // Function to fetch recent bookings
    function fetchRecentBookings() {
        fetch('/passenger/recent-bookings')
            .then(response => response.json())
            .then(data => {
                updateBookingsTable(data);
            })
            .catch(error => {
                console.error('Error fetching recent bookings:', error);
            });
    }

    function updateBookingsTable(bookings) {
    const tableBody = document.querySelector('#recent-bookings tbody');
    tableBody.innerHTML = '';  // Clear previous table rows

    if (bookings.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No recent bookings found.</td></tr>';
        return;
    }

    bookings.forEach(booking => {
        const bookingDate = new Date(booking.created_at).toLocaleDateString(); // Format the date
        const isEditable = booking.status.toLowerCase() === 'pending';
        const isCancelable = booking.status.toLowerCase() === 'pending';
        const isExpired = booking.status.toLowerCase() === 'expired';
        const isConfirmed = booking.status.toLowerCase() === 'confirmed';

        // Combine pickup date and time
        const pickupDateTime = new Date(`${booking.pickup_date}T${booking.pickup_time}`);
        const now = new Date();
        const within24Hours = (pickupDateTime - now) <= (24 * 60 * 60 * 1000); // Check if within 24 hours

        const bookingUrl = `/passenger/booking/${booking.id}/view`;
        const editUrl = `/passenger/booking/${booking.id}/edit`;

        // Generate row for each booking
        const row = `
            <tr>
                <td data-label="Booking Ref">${booking.booking_reference}</td>
                <td data-label="Booking Date">${bookingDate}</td>
                <td data-label="Service Type">${booking.service_type}</td>
                <td data-label="Status"><span class="badge ${getStatusClass(booking.status)}">${booking.status}</span></td>
                <td data-label="" class="button-group">
                    <a class="btn btn-warning btn-sm ${!isEditable ? 'disabled' : ''}" href="${editUrl}" ${!isEditable ? 'aria-disabled="true"' : ''}>Edit</a>
                    <button class="btn btn-danger btn-sm ${(!isCancelable && (!isConfirmed || within24Hours)) ? 'disabled' : ''}" data-id="${booking.id}" type="button">Cancel</button>
                    <a class="btn btn-primary btn-sm" href="${bookingUrl}">View</a>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });

    // Attach click event for cancel buttons
    document.querySelectorAll('.btn-danger').forEach(button => {
        button.addEventListener('click', function() {
            bookingIdToCancel = this.getAttribute('data-id');
            $('#cancelBookingModal').modal('show'); // Trigger modal on click
        });
    });
}


    // Function to handle booking cancellation with spinner
    confirmCancelButton.addEventListener('click', function() {
        if (bookingIdToCancel) {
            // Show spinner and disable button
            confirmCancelButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Cancelling...";
            confirmCancelButton.disabled = true;

            fetch(`/passenger/booking/cancel/${bookingIdToCancel}`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        $('#cancelBookingModal').modal('hide');
        fetchRecentBookings(); // Refresh bookings list after cancellation
    } else {
        alert('Failed to cancel booking. Please try again.');
    }
})
.catch(error => {
    console.error('Error cancelling booking:', error);
})
.finally(() => {
    // Reset button state after request completes
    confirmCancelButton.innerHTML = "Yes, Cancel Booking";
    confirmCancelButton.disabled = false;
});

        }
    });

    // Function to fetch payment history
    function fetchPaymentHistory() {
        fetch('/passenger/payment-history')
            .then(response => response.json())
            .then(data => {
                updatePaymentTable(data);
            })
            .catch(error => console.error('Error fetching payment history:', error));
    }

    // Function to update the payments table
    function updatePaymentTable(payments) {
        const tableBody = document.querySelector('#payment-history tbody');
        tableBody.innerHTML = '';  // Clear previous table rows

        if (payments.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No payment history found.</td></tr>';
            return;
        }

        payments.forEach(payment => {
    const invoiceDate = new Date(payment.invoice_date).toLocaleDateString(); // Format the invoice date

    // Determine the badge class based on the payment status
    let badgeClass = '';
    switch (payment.status.toLowerCase()) {
        case 'paid':
            badgeClass = 'bg-success'; // Green for paid
            break;
        case 'refunded':
            badgeClass = 'bg-info'; // Blue for refunded
            break;
        case 'unpaid':
            badgeClass = 'bg-danger'; // Red for unpaid
            break;
        case 'refund-pending':
            badgeClass = 'bg-warning'; // Yellow for refund pending
            break;
        default:
            badgeClass = 'bg-secondary'; // Default if status is unknown
    }

    const row = `
        <tr>
            <td data-label="Booking Ref">${payment.booking.booking_reference}</td>
            <td data-label="Invoice Number">${payment.invoice_number}</td>
            <td data-label="Amount">₦${payment.amount}</td>
            <td data-label="Invoice Date">${invoiceDate}</td>
            <td data-label="Payment Status"><span class="badge ${badgeClass}">${payment.status}</span></td>
        </tr>
    `;
    tableBody.innerHTML += row;
});

    }

    // Function to get the correct class for booking status
    function getStatusClass(status) {
        switch (status) {
            case 'paid':
                return 'bg-success';
            case 'unpaid':
                return 'bg-warning';
            case 'completed':
                return 'bg-success';
            case 'pending':
                return 'bg-warning';
                       case 'refunded':
                return 'bg-danger';
            case 'cancelled':
                return 'bg-danger';
            case 'confirmed':
                return 'bg-info';
            case 'expired':
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
    }

    // Function to fetch dashboard data
    function fetchDashboardData() {
        console.log('Fetching dashboard data...');
        fetch('{{ route('passenger.dashboard.data') }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-trips').innerText = data.totalTrips || 0;
                document.getElementById('cancelled-trips').innerText = data.cancelledTrips || 0;
                document.getElementById('upcoming-trips').innerText = data.upcomingTrips || 0;
                document.getElementById('total-payment').innerText = data.totalAmountPaid || 0;
            })
            .catch(error => console.error('Error fetching dashboard data:', error));
    }

    // Poll recent bookings, dashboard data, payment history, and chart data every 10 seconds
    setInterval(() => {
        fetchRecentBookings();
        fetchDashboardData();
        fetchPaymentHistory();
        updateChart();
        updateUserActivities(currentPage);
    }, 10000);  // Poll every 10 seconds

    // Event listener for time filter change for chart
    document.getElementById('timeFilter').addEventListener('change', updateChart);

    // Event listeners for pagination of user activities
    document.getElementById('previousPageBtn').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            updateUserActivities(currentPage);
        }
    });

    document.getElementById('nextPageBtn').addEventListener('click', () => {
        currentPage++;
        updateUserActivities(currentPage);
    });

    // Initial data fetch when the page loads
    fetchRecentBookings();
    fetchDashboardData();
    fetchPaymentHistory();
    updateChart();
    updateUserActivities(currentPage);
});
</script>

@endpush


@endsection

