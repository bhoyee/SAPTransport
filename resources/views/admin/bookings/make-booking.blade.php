@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('content')

<section class="container py-5">
    <div class="row">
        <div class="col-md-7 form-container">
            <h3>Booking for Walk-in</h3>

            <div class="app-card app-card-details shadow-sm mb-4" style="background-color: #f9f9f9; padding: 20px; border-radius: 10px;">
            <div class="app-card-body p-4">
                    <p>Making for passenger (Walkin or existing passenger)</p>

                    <!-- Email search section -->
                    <div id="email-search-section">
                        <form id="email-search-form">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="email">Enter Passenger Email (check if they exist on system or not.)</label>
                                <input type="email" class="form-control mt-2" id="search-email" name="email" placeholder="Enter email">
                            </div>
                            <button type="button" class="btn btn-primary mt-3" id="search-email-btn">Search</button>
                        </form>
                    </div>

                    <!-- Booking Form -->
                    <form id="admin-booking-form" style="display:none;">
                        @csrf
                        <input type="hidden" id="user-id" name="user_id" value="">

                        <!-- Personal Details Form (shown if user does not exist) -->
                        <div id="personal-details-section" style="display:none;">
                            <h4>Personal Details</h4>
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="personal-email" name="email" placeholder="Enter email" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="fullname">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="name" placeholder="Enter full name">
                            </div>
                            <div class="form-group mb-3">
                                <label for="phone">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number">
                            </div>
                            <div class="form-group mb-3">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Service Type -->
                        <div class="form-group mb-3">
                            <label for="service_type">Service Type</label>
                            <select id="service_type" name="service_type" class="form-control" required>
                                <option value="">Select Service Type</option>
                                <option value="AirportTransfer">Airport Transfer</option>
                                <option value="Charter">Charter</option>
                            </select>
                        </div>

                        <!-- Trip Type -->
                        <div class="form-group mb-3" id="trip-type-container">
                            <label for="trip_type">Trip Type</label>
                            <select id="trip_type" name="trip_type" class="form-control">
                                <!-- Options will change dynamically based on the service type -->
                            </select>
                        </div>

                        <!-- Select Airport (Shown for Airport Transfer) -->
                        <div class="form-group mb-3" id="airport-select-field" style="display:none;">
                            <label for="select-airport">Select Airport</label>
                            <select id="select-airport" name="airport_name" class="form-control">
                                <option value="">Select Airport</option>
                                <option value="murtala-intl">Murtala Muhammed International Airport</option>
                                <option value="murtala-domestic-mm1">Murtala Muhammed Domestic MM1</option>
                                <option value="murtala-domestic-mm2">Murtala Muhammed Domestic MM2</option>
                            </select>
                        </div>
            <!-- Vehicle Type -->
            <div class="form-group mb-3">
                            <label for="vehicle_type">Vehicle Type</label>
                            <select id="vehicle_type" name="vehicle_type" class="form-control vehicle-type" required>
                                <option value="">Select Vehicle Type</option>
                                <option value="car">SAP Car</option>
                                <option value="hilux">SAP Hilux (4 Seats)</option>
                                <option value="hiace">SAP Hiace (14 Seats)</option>
                                <option value="coaster">SAP Coaster (32 Seats)</option>
                            </select>
                        </div>

                        <!-- Common Fields for Pickup/Dropoff, Adults/Children -->
                        <div class="form-group mb-3">
                            <label for="pickup_address" id="pickup-label">Pickup Address</label>
                            <input type="text" id="pickup_address" name="pickup_address" class="form-control" placeholder="Enter pickup address or point">
                        </div>

                        <div class="form-group mb-3">
                            <label for="dropoff_address" id="dropoff-label">Drop-Off Address</label>
                            <input type="text" id="dropoff_address" name="dropoff_address" class="form-control" placeholder="Enter drop-off address or point">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="pickup_date">Pickup Date</label>
                                <input type="date" id="pickup_date" name="pickup_date" class="form-control">
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="pickup_time">Pickup Time</label>
                                <input type="time" id="pickup_time" name="pickup_time" class="form-control">
                            </div>
                        </div>
       
                        <div class="form-group mb-3">
                            <label for="number_adults">Number of Adults</label>
                            <select id="number_adults" name="number_adults" class="form-control adults-select" placeholder="Enter number of adults" min="1" required></select>

                            <!-- <input type="number" id="number_adults" name="number_adults" class="form-control" placeholder="Enter number of adults" min="1" required> -->
                        </div>

                        <div class="form-group mb-3">
                            <label for="number_children">Number of Children</label>
                            <select id="number_children" name="number_children" class="form-control children-select" placeholder="Enter number of children"></select>

                            <!-- <input type="number" id="number_children" name="number_children" class="form-control" placeholder="Enter number of children"> -->
                        </div>

                        <!-- Return Trip Fields for Roundtrip (Charter only) -->
                        <div id="roundtrip-fields" style="display:none;">
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-3">
                                    <label for="return_pickup_date">Return Pickup Date</label>
                                    <input type="date" id="return_pickup_date" name="return_pickup_date" class="form-control">
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label for="return_pickup_time">Return Pickup Time</label>
                                    <input type="time" id="return_pickup_time" name="return_pickup_time" class="form-control">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4" id="submit-booking-btn">
                            <span class="spinner-border spinner-border-sm" id="submit-spinner" style="display:none;"></span>
                            Proceed
                        </button>

                    </form>
        

                        <div id="alert-message" class="alert alert-dismissible fade show mt-2" style="display: none;" role="alert">
                            <span id="alert-message-content"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                        </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/scripts.js') }}"></script>
<script>
    $(document).ready(function() {
        console.log('Page loaded successfully');

        // Initially hide all form fields except the service type
        $('#admin-booking-form').hide(); // Hide the form
        $('#service_type').closest('.form-group').hide(); // Hide service type dropdown
        $('#trip_type').closest('.form-group').hide();    // Hide trip type dropdown
        $('#airport-select-field').hide();                // Hide airport field
        $('#pickup_address').closest('.form-group').hide(); // Hide pickup address
        $('#dropoff_address').closest('.form-group').hide(); // Hide dropoff address
        $('#number_adults').closest('.form-group').hide(); // Hide number of adults
        $('#number_children').closest('.form-group').hide(); // Hide number of children
        $('#vehicle_type').closest('.form-group').hide();  // Hide vehicle type
        $('#roundtrip-fields').hide();                    // Hide round trip fields for Charter
        $('#pickup_date').closest('.form-group').hide();   // Hide pickup date
        $('#pickup_time').closest('.form-group').hide();   // Hide pickup time

        // Handle email search button click
        $('#search-email-btn').on('click', function() {
            const email = $('#search-email').val();
            console.log('Searching for email:', email);
            $('#alert-message').removeClass('alert-success alert-danger').hide();
            $('#submit-spinner').show();

            $.ajax({
                url: '/admin/check-user',
                method: 'POST',
                data: { email: email, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    console.log('Email check response:', response);
                    $('#submit-spinner').hide();

                    if (response.exists) {
                        console.log('User exists, showing service type dropdown');
                        $('#user-id').val(response.user_id); // Set user ID in hidden field
                        $('#email-search-section').hide();  // Hide the email search

                        // Show the form and only the service type dropdown
                        $('#admin-booking-form').show(); 
                        $('#service_type').closest('.form-group').show(); // Show service type dropdown
                    } else {
                        console.log('User does not exist, showing error message');
                        $('#personal-email').val(email);  // Set the email in the personal details form
                        $('#email-search-section').hide();  // Hide the email search
                        $('#personal-details-section').show();  // Show personal details form
                        $('#admin-booking-form').show(); // Show the form
                        $('#service_type').closest('.form-group').show(); 

                    }
                },
                error: function() {
                    console.log('Error occurred while checking the email');
                    $('#submit-spinner').hide();
                    $('#alert-message').addClass('alert-danger').text('Error checking user. Please try again.').show();
                }
            });
        });

        // Show trip type dropdown after selecting service type
        $('#service_type').on('change', function() {
            const serviceType = $(this).val();
            console.log('Service type selected:', serviceType);

            // Reset all fields and hide the unnecessary ones
            $('#trip_type').empty();
            $('#trip_type').closest('.form-group').hide(); // Hide trip type dropdown initially
            $('#airport-select-field').hide();
            $('#pickup_address').closest('.form-group').hide();
            $('#dropoff_address').closest('.form-group').hide();
            $('#number_adults').closest('.form-group').hide();
            $('#number_children').closest('.form-group').hide();
            $('#vehicle_type').closest('.form-group').hide();
            $('#roundtrip-fields').hide();
            $('#pickup_date').closest('.form-group').hide();
            $('#pickup_time').closest('.form-group').hide();

            if (serviceType === 'AirportTransfer') {
                console.log('Populating trip type for Airport Transfer');
                $('#trip_type').append('<option value="airport_pickup">Select Trip Type</option>');
                $('#trip_type').append('<option value="airport_pickup">Airport Pickup</option>');
                $('#trip_type').append('<option value="airport_dropoff">Airport Drop-Off</option>');
                $('#trip_type').closest('.form-group').show(); // Show trip type dropdown after selecting service type
            } else if (serviceType === 'Charter') {
                console.log('Populating trip type for Charter');
                $('#trip_type').append('<option value="oneway">Select Trip Type</option>');
                $('#trip_type').append('<option value="oneway">Oneway</option>');
                $('#trip_type').append('<option value="round_trip">Roundtrip</option>');
                $('#trip_type').closest('.form-group').show(); // Show trip type dropdown after selecting service type
            }
        });

        // Show fields based on trip type selection
        $('#trip_type').on('change', function() {
            const tripType = $(this).val();
            const serviceType = $('#service_type').val();
            console.log('Trip type selected:', tripType);
            console.log('Service type is:', serviceType);

            // Reset and hide all unnecessary fields first
            $('#airport-select-field').hide();
            $('#pickup_address').closest('.form-group').hide();
            $('#dropoff_address').closest('.form-group').hide();
            $('#number_adults').closest('.form-group').hide();
            $('#number_children').closest('.form-group').hide();
            $('#vehicle_type').closest('.form-group').hide();
            $('#roundtrip-fields').hide();
            $('#pickup_date').closest('.form-group').hide();
            $('#pickup_time').closest('.form-group').hide();

            if (serviceType === 'AirportTransfer') {
                console.log('Handling Airport Transfer fields');
                $('#airport-select-field').show(); // Show airport select
                $('#vehicle_type').closest('.form-group').show(); // Show vehicle type dropdown
                $('#number_adults').closest('.form-group').show(); // Show number of adults
                $('#number_children').closest('.form-group').show(); // Show number of children
                $('#pickup_date').closest('.form-group').show();   // Show pickup date
                $('#pickup_time').closest('.form-group').show();   // Show pickup time

                if (tripType === 'airport_pickup') {
                    $('#pickup_address').closest('.form-group').hide(); // Hide pickup address
                    $('#dropoff_address').closest('.form-group').show(); // Show drop-off address
                } else if (tripType === 'airport_dropoff') {
                    $('#pickup_address').closest('.form-group').show(); // Show pickup address
                    $('#dropoff_address').closest('.form-group').hide(); // Hide drop-off address
                }
            } else if (serviceType === 'Charter') {
                console.log('Handling Charter fields');
                $('#vehicle_type').closest('.form-group').show(); // Show vehicle type dropdown
                $('#pickup_address').closest('.form-group').show(); // Show pickup address
                $('#dropoff_address').closest('.form-group').show(); // Show drop-off address
                $('#number_adults').closest('.form-group').show(); // Show number of adults
                $('#number_children').closest('.form-group').show(); // Show number of children
                $('#pickup_date').closest('.form-group').show();   // Show pickup date
                $('#pickup_time').closest('.form-group').show();   // Show pickup time

                if (tripType === 'oneway') {
                    $('#roundtrip-fields').hide(); // Hide roundtrip fields for Oneway
                } else if (tripType === 'round_trip') {
                    $('#roundtrip-fields').show(); // Show roundtrip fields for Roundtrip
                }
            }
        });



        // Disable past dates and restrict past times
        const today = new Date().toISOString().split('T')[0];
        const pickupDateInput = document.getElementById('pickup_date');
        const pickupTimeInput = document.getElementById('pickup_time');

        const returnPickupDateInput = document.getElementById('return_pickup_date');
        const returnPickupTimeInput = document.getElementById('return_pickup_time');

        // Disable past dates for pickup and return
        if (pickupDateInput) {
            pickupDateInput.setAttribute('min', today);
        }

        if (returnPickupDateInput) {
            returnPickupDateInput.setAttribute('min', today);
        }

        function restrictTime(dateInput, timeInput) {
            const selectedDate = new Date(dateInput.value);
            const now = new Date();
            if (selectedDate.toDateString() === now.toDateString()) {
                const currentTime = now.toTimeString().slice(0, 5); // Get current time in 'HH:mm' format
                timeInput.setAttribute('min', currentTime);
            } else {
                timeInput.removeAttribute('min');
            }
        }

        if (pickupDateInput) {
            pickupDateInput.addEventListener('change', function() {
                restrictTime(pickupDateInput, pickupTimeInput);
            });
        }

        if (returnPickupDateInput) {
            returnPickupDateInput.addEventListener('change', function() {
                restrictTime(returnPickupDateInput, returnPickupTimeInput);
            });
        }



        // Form submission logic
        $('#admin-booking-form').on('submit', function(e) {
            e.preventDefault();

            const pickupDate = $('#pickup_date').val();
            const currentDate = new Date().toISOString().split('T')[0];

            if (pickupDate < currentDate) {
                showError('You cannot book for past dates.');
                return;
            }

            $('#submit-spinner').show();
            const formData = $(this).serialize();
            console.log('Submitting form data:', formData);

            $.ajax({
                url: '/admin/make-booking',
                method: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Booking successful:', response);
                    $('#submit-spinner').hide();
                    $('#alert-message').addClass('alert-success').text(`Booking successful! Reference: ${response.booking_reference}`).show();
                    $('#admin-booking-form')[0].reset();
                },
                error: function() {
                    console.log('Error submitting the booking');
                    $('#submit-spinner').hide();
                    $('#alert-message').addClass('alert-danger').text('Failed to complete booking. Please try again.').show();
                }
            });
        });

        // function showError(message) {
        //     console.log('Error:', message);
        //     $('#alert-message').removeClass('alert-success').addClass('alert-danger').text(message).show();
        // }

        // Function to show error messages with a fade out after 5 seconds
        function showError(message) {
            $('#submit-spinner').hide();
            $('#alert-message')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .html(message)
                .show();
            console.log('Error message shown:', message); // Log when the message is shown
            hideAlertWithTimer(); // Trigger timer to hide alert
        }

        // Function to show success messages with a fade out after 5 seconds
        function showSuccess(message) {
            $('#submit-spinner').hide();
            $('#alert-message')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .html(message)
                .show();
            console.log('Success message shown:', message); // Log when the success message is shown
            hideAlertWithTimer(); // Trigger timer to hide alert
        }

        // Timer function to hide the alert after 5 seconds
        function hideAlertWithTimer() {
            console.log('Setting timer to hide alert');
            setTimeout(function () {
                $('#alert-message').fadeOut(1000, function () {
                    $(this).hide(); // Ensure the element is hidden after fading out
                    console.log('Alert message hidden');
                });
            }, 5000); // 5 seconds (5,000 milliseconds)
        }

        // Example to trigger the success message for testing
        // showSuccess('This is a success message!');


    });
</script>
@endpush
