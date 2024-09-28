@extends('layouts.passenger')

@section('title', 'Passenger Dashboard')

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
				<!-- Responsive Recent Bookings Table -->

				<div class="col-12 col-lg-7">
						<div class="app-card app-card-progress-list h-100 shadow-sm">
							<div class="app-card-header p-3">
								<h4 class="app-card-title">Recent Booking</h4>
							</div>
							<div class="container">
								<table id="recent-bookings" class="table table-striped table-bordered table-responsive">
									<thead>
										<tr>
											<th>Booking Ref</th>
											<th>Booking Date</th>
											<th>Service Type</th>
											<th>Status</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<!-- Booking data will be injected here -->
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- Responsive Payment History Table -->
					<div class="col-12 col-lg-5">
						<div class="app-card app-card-stats-table h-100 shadow-sm">
							<div class="app-card-header p-3">
								<h4 class="app-card-title">Payment History</h4>
							</div>
							<div class="container">
								<table id="payment-history" class="table table-striped table-bordered table-responsive">
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



	<!-- Custom CSS for Mobile-Friendly Table -->
<style>
	/* Default table style */

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
    white-space: nowrap; /* Prevent wrapping */
}

/* Adjust the width of the "Actions" column and "Service Type" column for desktop */
td.button-group {
    width: 200px; /* Set a fixed width for the Actions column */
    text-align: left; /* Align buttons to the left */
}

td[data-label="Service Type"], td[data-label="Booking Date"] {
    width: 150px; /* Set fixed width for Service Type and Booking Date columns */
}

/* Button group adjustments */
.button-group {
    display: flex;
    justify-content: flex-start;
    gap: 5px; /* Reduce the gap between the buttons */
}

/* Smaller button styling */
.button-group .btn {
    padding: 5px 8px;
    font-size: 12px;
}

/* Mobile view styling */
@media (max-width: 768px) {
    table thead {
        display: none; /* Hide the table headers */
    }

    table tbody tr {
        display: block;
        margin-bottom: 15px;
        border-bottom: 2px solid #ddd;
        padding-bottom: 15px;
    }

    table tbody tr td {
        display: flex;
        justify-content: space-between; /* Space between label and value */
        align-items: center; /* Align vertically */
        padding: 8px 10px;
        border-bottom: 1px solid #ddd;
        position: relative;
    }

    table tbody tr td:before {
        content: attr(data-label);
        flex-basis: 40%; /* Label takes 40% of the row */
        font-weight: bold;
        white-space: nowrap; /* Prevent wrapping of the labels */
    }

    table tbody tr td:last-child {
        border-bottom: 0;
    }

    /* Ensure Booking Date displays fully on mobile */
    td[data-label="Booking Date"] {
        text-align: right; /* Align the date value to the right */
        padding-right: 10px; /* Add space between text and the edge */
        white-space: nowrap; /* Prevent wrapping */
    }

    /* Adjust buttons for mobile */
    .button-group {
        justify-content: space-between;
        gap: 5px;
    }

    .button-group .btn {
        flex: 1 1 45%;
        margin-bottom: 5px;
    }

    /* Mobile does not use fixed width for Actions or Service Type */
    td.button-group, td[data-label="Service Type"], td[data-label="Booking Date"] {
        width: auto; /* Reset width for mobile */
    }
}



</style>





    <script>
        // Initialize the DataTable without search, pagination, and length change
		new DataTable('#recent-bookings', {
        responsive: true,
        searching: false,
        paging: false,
        lengthChange: false,
        bInfo: false,
        fixedHeader: true
    });

    // Initialize DataTable for payment history
    new DataTable('#payment-history', {
        responsive: true,
        searching: false,
        paging: false,
        lengthChange: false,
        bInfo: false,
        fixedHeader: true
    });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let bookingIdToCancel = null; // Store the booking ID for cancellation
    const confirmCancelButton = document.getElementById('confirmCancelButton'); // "Yes, Cancel Booking" button

    // Function to fetch recent bookings
    function fetchRecentBookings() {
        console.log('Fetching recent bookings...'); // Log when fetching starts
        
        // Ensure the CSRF token is available
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenMeta) {
            console.error('CSRF token not found in the page.');
            return;
        }

        const csrfToken = csrfTokenMeta.getAttribute('content');
        console.log('CSRF token:', csrfToken); // Log the CSRF token
        
        fetch('/passenger/recent-bookings', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Ensure CSRF token is included
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Data received:', data);
            updateBookingsTable(data);
        })
        .catch(error => {
            console.error('Error fetching recent bookings:', error);
        });
    }

    // Function to update the bookings table
    function updateBookingsTable(bookings) {
        const tableBody = document.querySelector('#recent-bookings tbody');
        if (!tableBody) {
            console.log('Table body not found in DOM.');
            return;
        }

        console.log('Updating bookings table with data:', bookings);
        tableBody.innerHTML = '';  // Clear previous table rows

        if (bookings.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No recent bookings found.</td></tr>';
            return;
        }

        bookings.forEach(booking => {
            const bookingDate = new Date(booking.created_at).toLocaleDateString(); // Format the date

            // Check if the Edit button should be disabled based on the status
            const isEditable = booking.status.toLowerCase() === 'pending';
            const isCancelable = booking.status.toLowerCase() === 'pending';

            const isExpired = booking.status.toLowerCase() === 'expired';

			const bookingUrl = `/booking/${booking.id}/view`;
			const editUrl = `/booking/${booking.id}/edit`;

			const row = `
				<tr>
					<td data-label="Booking Ref">${booking.booking_reference}</td>
					<td data-label="Booking Date">${bookingDate}</td>
					<td data-label="Service Type">${booking.service_type}</td>
					<td data-label="Status"><span class="badge ${getStatusClass(booking.status)}">${booking.status}</span></td>
					<td data-label="Actions" class="button-group">
						<a class="btn btn-warning btn-sm ${!isEditable ? 'disabled' : ''}" href="${editUrl}" ${!isEditable ? 'aria-disabled="true"' : ''}>Edit</a>
						<button class="btn btn-danger btn-sm ${(!isCancelable || isExpired) ? 'disabled' : ''}" data-id="${booking.id}" type="button">Cancel</button>
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

            fetch(`/booking/cancel/${bookingIdToCancel}`, {
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

    // Function to fetch payment history
    function fetchPaymentHistory() {
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
        
        console.log('Fetching payment history...');

        fetch('/passenger/payment-history', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Payment history received:', data);
            updatePaymentTable(data);
        })
        .catch(error => {
            console.error('Error fetching payment history:', error);
        });
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

        // Determine badge class based on status
        const badgeClass = payment.status.toLowerCase() === 'paid' ? 'bg-success' : 'bg-danger';

        const row = `
            <tr>
                <td data-label="Booking Ref">${payment.booking.booking_reference}</td>
                <td data-label="Invoice Number">${payment.invoice_number}</td>
                <td data-label="Amount">₦${payment.amount}</td>
                <td data-label="Invoice Date">${invoiceDate}</td>
                <td data-label="Payment Status">
                    <span class="badge ${badgeClass}">${payment.status}</span>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

    // Function to get the correct class for payment status
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
            default:
                return 'bg-secondary';
        }
    }

    // Poll the recent bookings, dashboard data, and payment history every 10 seconds
    setInterval(() => {
        fetchRecentBookings();
        fetchDashboardData();
        fetchPaymentHistory();
    }, 10000);  // 10 seconds polling interval

    // Fetch the data immediately when the page loads
    fetchRecentBookings();
    fetchDashboardData();
    fetchPaymentHistory();

});
</script>


@endsection

