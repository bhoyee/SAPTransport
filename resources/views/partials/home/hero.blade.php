<style>

    /* Customize modal content */

    .modal-content {

        background-color: #f8f9fa; /* Light background */

        color: #333; /* Dark text */

    }

    

    /* Customize modal header */

    .modal-header {

        background-color: #007bff; /* Primary background for header */

        color: #fff; /* White text for header */

    }

    

    /* Customize modal footer */

    .modal-footer {

        background-color: #f1f1f1; /* Light gray background for footer */

    }



</style>



    <!-- resources/views/partials/home/hero.blade.php -->

        <section class="hero">

            <div class="container">

                <div class="row justify-content-center">

                    <!-- Left Column: Intro Text -->

                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-left text-left">

                        <h1>The modern way to commute across cities</h1>

                        <p>Experience seamless and reliable transportation with SAPTransport.</p>

                    </div>

        

                    <!-- Right Column: Tabs and Forms -->

                    <div class="col-md-6">

                         @if (session('error'))

            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">

                <strong>Error!</strong> {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

            </div>

        @endif

        

        @if (session('success'))

            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">

                <strong>Success!</strong> {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

            </div>

        @endif

        

        @if ($errors->any())

            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-list">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

            </div>

        @endif

                        <ul class="nav nav-tabs" id="myTab" role="tablist">

                            <li class="nav-item">

                                <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Airport Transfer</a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Charter</a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Booking Status</a>

                            </li>

                        </ul>

                        <div class="tab-content" id="myTabContent">

                            <!-- Airport Transfer Tab -->

                            <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">

                               



                        <!-- Toggle Buttons -->

                        <div class="btn-group mt-3" role="group">

                            <button type="button" class="btn btn-outline-info mr-3 text-light btn-sm p-2 active" name="pickup-btn" id="pickup-btn">Airport Pickup</button>

                            <button type="button" class="btn btn-outline-info text-light btn-sm p-2" name="dropoff-btn" id="dropoff-btn">Airport Drop-Off</button>

                        </div>



                        <!-- Airport Transfer Form -->

                        <form id="airport-transfer-form" class="booking-form mt-3" action="{{ route('booking.store') }}" method="POST">

                            @csrf

                            <input type="hidden" name="service_type" value="AirportTransfer">

                            <!-- Hidden input to store the trip type -->

                            <input type="hidden" name="trip_type" id="trip_type" value="airport_pickup"> <!-- Default is 'airport_pickup' -->





                            <div class="form-group">

                                <label for="select-airport">Select Airport</label>

                                <select id="select-airport" name="airport_name" class="form-control" required>

                                    <option value="">Select Airport</option>

                                    <option value="murtala-intl">Murtala Muhammed International Airport</option>

                                    <option value="murtala-domestic-mm1">Murtala Muhammed Domestic Airport MM1</option>

                                    <option value="murtala-domestic-mm2">Murtala Muhammed Domestic Airport MM2</option>

                                </select>

                            </div>

                            <div class="form-group">

                                <label for="vehicle-type-tab1">Select Vehicle Type</label>

                                <select id="vehicle-type-tab1" name="vehicle_type" class="form-control vehicle-type" required>

                                    <option value="">Select Vehicle Type</option>

                                    <option value="car">SAP Car</option>

                                    <option value="hilux">SAP Hilux (4 Seats)</option>

                                    <option value="hiace">SAP Hiace (14 Seats)</option>

                                    <option value="coaster">SAP Coaster (32 Seats)</option>

                                </select>

                            </div>

                            <div class="form-group" id="pickup-address-group" style="display: none;">

                                <label for="pickup-address">Pickup Address</label>

                                <input type="text" id="pickup-address" name="pickup_address" class="form-control" placeholder="Enter pickup address">

                            </div>

                            <div class="form-group" id="dropoff-address-group">

                                <label for="dropoff-address">Drop Off Address</label>

                                <input type="text" id="dropoff-address" name="dropoff_address" class="form-control" placeholder="Enter drop off address">

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6">

                                    <label for="pickup-date">Pickup Date</label>

                                    <input type="date" id="pickup-date" name="pickup_date" class="form-control" required>

                                </div>

                                <div class="form-group col-md-6">

                                    <label for="pickup-time">Pickup Time</label>

                                    <input type="time" id="pickup-time" name="pickup_time" class="form-control" required>

                                </div>

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6">

                                    <label for="adults-tab1">Number of Adults</label>

                                    <select id="adults-tab1" name="number_adults" class="form-control adults-select" required></select>

                                </div>

                                <div class="form-group col-md-6">

                                    <label for="children-tab1">Number of Children</label>

                                    <select id="children-tab1" name="number_children" class="form-control children-select"></select>

                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary" id="submit-btn">Proceed</button>

                        </form>

                    </div>



                    <!-- Charter Tab -->

                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">

                        <!-- Charter form will be added here -->

                        <div class="btn-group mt-3" role="group">

                            <button type="button" class="btn btn-outline-info mr-3 text-light btn-sm p-2 active" id="one-way">One Way</button>

                            <button type="button" class="btn btn-outline-info text-light btn-sm p-2" id="round-trip">Round Trip</button>

                        </div>

                        <form id="charter-form" class="booking-form mt-3" action="{{ route('booking.store') }}" method="POST">

                            @csrf

                            <input type="hidden" name="service_type" value="Charter">

        <input type="hidden" name="trip_type" id="charter_trip_type" value="oneway"> <!-- Hidden input for trip_type -->





                            <div class="form-group">

                                <label for="vehicle-type-tab2">Select Vehicle Type</label>

                                <select id="vehicle-type-tab2" name="vehicle_type" class="form-control vehicle-type" required>

                                    <option value="">Select Vehicle Type</option>

                                    <option value="car">SAP Car</option>

                                    <option value="hilux">SAP Hilux (4 Seats)</option>

                                    <option value="hiace">SAP Hiace (14 Seats)</option>

                                    <option value="coaster">SAP Coaster (32 Seats)</option>

                                </select>

                            </div>

                            <div class="form-group">

                                <label for="pickup-point">Pickup Point</label>

                                <input type="text" id="pickup-point" name="pickup_address" placeholder="Enter pickup location" class="form-control" required>

                            </div>

                            <div class="form-group">

                                <label for="dropoff-point">Drop-off Point</label>

                                <input type="text" id="dropoff-point" name="dropoff_address" placeholder="Enter drop-off location" class="form-control" required>

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6">

                                    <label for="pickup-date-tab2">Pickup Date</label>

                                    <input type="date" id="pickup-date-tab2" name="pickup_date" class="form-control" required>

                                </div>

                                <div class="form-group col-md-6">

                                    <label for="pickup-time-tab2">Pickup Time</label>

                                    <input type="time" id="pickup-time-tab2" name="pickup_time" class="form-control" required>

                                </div>

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6">

                                    <label for="adults-tab2">Number of Adults</label>

                                    <select id="adults-tab2" name="number_adults" class="form-control adults-select" required></select>

                                </div>

                                <div class="form-group col-md-6">

                                    <label for="children-tab2">Number of Children</label>

                                    <select id="children-tab2" name="number_children" class="form-control children-select"></select>

                                </div>

                            </div>

                            <div class="form-row" id="return-fields" style="display: none;">

                                <div class="form-group col-md-6">

                                    <label for="return-pickup-date">Return Pickup Date</label>

                                    <input type="date" id="return-pickup-date" name="return_pickup_date" class="form-control">

                                </div>

                                <div class="form-group col-md-6">

                                    <label for="return-pickup-time">Return Pickup Time</label>

                                    <input type="time" id="return-pickup-time" name="return_pickup_time" class="form-control">

                                </div>

                            </div>

                            <button type="submit" id="ch-submit-btn" class="btn btn-primary">Proceed</button>

                        </form>

                    </div>



                    <!-- Booking Status Tab -->

                    <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">

                        <form id="booking-status-form" class="booking-status-form mt-3">

                            @csrf

                            <div class="form-group">

                                <label for="booking-reference">Booking Reference Number</label>

                            <input type="text" name="booking_reference" class="form-control" id="booking-reference" placeholder="Enter booking reference number" required>



                            </div>

                            <button type="submit" id="bk-submit-btn" class="btn btn-primary">Check Status</button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>



        <!-- Booking Success Modal -->



    <div class="modal fade" id="bookingSuccessModal" tabindex="-1" role="dialog" aria-labelledby="bookingSuccessModalLabel" aria-hidden="true">

      <div class="modal-dialog" role="document">

        <div class="modal-content" style="background-color: #f8f9fa; color: #333;">

          <div class="modal-header">

            <h5 class="modal-title" id="bookingSuccessModalLabel">Booking Successful!</h5>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

          </div>

          <div class="modal-body">

            <p>Your booking has been successfully completed.</p>

            <p><strong>Booking Reference:</strong> {{ session('booking_reference') }}</p>

          </div>

          <div class="modal-footer">

            <!-- Red Close Button -->

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>

            

            <!-- Conditionally show the dashboard button if the user is authenticated -->

            @if(Auth::check())

                <a href="{{ route('passenger.dashboard') }}" class="btn btn-primary">View Dashboard</a>

            @endif

          </div>

        </div>

      </div>

    </div>



<!-- Booking Status Result Modal -->

<div class="modal fade" id="bookingStatusModal" tabindex="-1" role="dialog" aria-labelledby="bookingStatusModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content" style="background-color: #f8f9fa; color: #333;">

            <div class="modal-header">

                <h5 class="modal-title" id="bookingStatusModalLabel">Booking Status</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body" id="booking-status-details">

                <!-- Booking details will be inserted here via JavaScript -->

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>





</section>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>





<script>
document.addEventListener('DOMContentLoaded', function () {

// Check for booking reference in the session and show success modal
const bookingReference = "{{ session('booking_reference') }}";
console.log('Booking reference from session:', bookingReference); // Debugging log
if (bookingReference) {
    $('#bookingSuccessModal').modal('show');
}

// Common date and time validation logic
const airportForm = document.getElementById('airport-transfer-form');
const charterForm = document.getElementById('charter-form');
const bookStatusForm = document.getElementById('booking-status-form');
const airportSubmitBtn = document.getElementById('submit-btn');
const charterSubmitBtn = document.getElementById('ch-submit-btn');
const bookSubmitBtn = document.getElementById('bk-submit-btn');
const pickupDateInput = document.getElementById('pickup-date');
const pickupTimeInput = document.getElementById('pickup-time');
const ChpickupDateInput = document.getElementById('pickup-date-tab2');
const ChpickupTimeInput = document.getElementById('pickup-time-tab2');
const returnPickupDateInput = document.getElementById('return-pickup-date');
const returnPickupTimeInput = document.getElementById('return-pickup-time');
const today = new Date().toISOString().split('T')[0];

// Disable past dates for pickup and return
if (pickupDateInput) {
    pickupDateInput.setAttribute('min', today);
}
if (returnPickupDateInput) {
    returnPickupDateInput.setAttribute('min', today);
}

if (ChpickupDateInput) {
    ChpickupDateInput.setAttribute('min', today);
}
// Restrict past times on the same day
function restrictTime(dateInput, timeInput) {
    const selectedDate = new Date(dateInput.value);
    const now = new Date();
    if (selectedDate.toDateString() === now.toDateString()) {
        const currentTime = now.toTimeString().slice(0, 5);
        timeInput.setAttribute('min', currentTime);
    } else {
        timeInput.removeAttribute('min');
    }
}

// Apply time restrictions based on date changes
if (pickupDateInput) {
    pickupDateInput.addEventListener('change', function () {
        restrictTime(pickupDateInput, pickupTimeInput);
    });
}
if (returnPickupDateInput) {
    returnPickupDateInput.addEventListener('change', function () {
        restrictTime(returnPickupDateInput, returnPickupTimeInput);
    });
}

// Spinner on airport
if (airportForm) {
    airportForm.addEventListener('submit', function (event) {
        airportSubmitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Checking...";
        airportSubmitBtn.disabled = true;
    });
}

// Spinner on Charter
if (charterForm) {
    charterForm.addEventListener('submit', function (event) {
        charterSubmitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Processing...";
        charterSubmitBtn.disabled = true;
    });
}

// Spinner on book status
if (bookStatusForm) {
    bookStatusForm.addEventListener('submit', function (event) {
        bookSubmitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Processing...";
        bookSubmitBtn.disabled = true;
    });
}

// When the booking status result modal is closed
$('#bookingStatusModal').on('hidden.bs.modal', function () {
    bookSubmitBtn.disabled = false;
    bookSubmitBtn.innerHTML = "Check Status"; // Replace "Check Status" with the original button text
    document.getElementById('booking-reference').value = ""; // Clear the booking reference input
});

// Logic for Airport Pickup and Drop-Off
const pickupBtn = document.getElementById('pickup-btn');
const dropoffBtn = document.getElementById('dropoff-btn');
const tripTypeInput = document.getElementById('trip_type');
const pickupAddressInput = document.getElementById('pickup-address');
const dropoffAddressInput = document.getElementById('dropoff-address');
const pickupAddressGroup = document.getElementById('pickup-address-group');
const dropoffAddressGroup = document.getElementById('dropoff-address-group');

if (pickupBtn && dropoffBtn) {
    pickupBtn.addEventListener('click', function () {
        dropoffAddressGroup.style.display = 'block'; // Show Dropoff Address field
        pickupAddressGroup.style.display = 'none'; // Hide Pickup Address field

        this.classList.add('active');
        dropoffBtn.classList.remove('active');

        tripTypeInput.value = 'airport_pickup';
        dropoffAddressInput.setAttribute('required', 'required');
        pickupAddressInput.removeAttribute('required');
    });

    dropoffBtn.addEventListener('click', function () {
        dropoffAddressGroup.style.display = 'none'; // Hide Dropoff Address field
        pickupAddressGroup.style.display = 'block'; // Show Pickup Address field

        this.classList.add('active');
        pickupBtn.classList.remove('active');

        tripTypeInput.value = 'airport_dropoff';
        pickupAddressInput.setAttribute('required', 'required');
        dropoffAddressInput.removeAttribute('required');
    });
}

// Logic for Charter One-Way and Round-Trip
const oneWayBtn = document.getElementById('one-way');
const roundTripBtn = document.getElementById('round-trip');
const charterTripTypeInput = document.getElementById('charter_trip_type');
const returnFields = document.getElementById('return-fields');

if (oneWayBtn && roundTripBtn) {
    oneWayBtn.addEventListener('click', function () {
        returnFields.style.display = 'none'; // Hide return fields for one-way
        charterTripTypeInput.value = 'oneway';
        this.classList.add('active');
        roundTripBtn.classList.remove('active');
    });

    roundTripBtn.addEventListener('click', function () {
        returnFields.style.display = 'block'; // Show return fields for round trip
        charterTripTypeInput.value = 'round_trip';
        this.classList.add('active');
        oneWayBtn.classList.remove('active');
    });
}

// Retrieve active tab from local storage
const activeTab = localStorage.getItem('activeTab');
if (activeTab) {
    $(`#${activeTab}-tab`).tab('show'); // Show the previously active tab
}

// Store active tab in local storage when user clicks on a tab
$('a[data-toggle="tab"]').on('click', function (e) {
    localStorage.setItem('activeTab', $(e.target).attr('href').substring(1));
});

// AJAX Logic to handle Booking Status form submission
if (bookStatusForm) {
    bookStatusForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent traditional form submission
        const bookingReference = document.getElementById('booking-reference').value;
        console.log('Submitting booking status form');
        console.log('Booking reference entered:', bookingReference);
        const url = '/check-booking-status';
        console.log('Sending POST request to:', url);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ booking_reference: bookingReference })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Data received:', data);
            if (data.booking_reference) {
                const bookingDetails = `
                    <p><strong>Booking Reference:</strong> ${data.booking_reference}</p>
                    <p><strong>Booking Status:</strong> ${data.status}</p>
                    <p><strong>Service Type:</strong> ${data.service_type}</p>
                    <p><strong>Pickup/Drop-off Date:</strong> ${data.date}</p>
                    <p><strong>Vehicle Type:</strong> ${data.vehicle_type}</p>
                `;
                document.getElementById('booking-status-details').innerHTML = bookingDetails;
            } else {
                document.getElementById('booking-status-details').innerHTML = '<p>No such booking reference number found.</p>';
            }

            // Show the Booking Status Modal
            $('#bookingStatusModal').modal('show');
        })
        .catch(error => {
            console.error('Error during fetch request:', error);
        });
    });
}

});


</script>


