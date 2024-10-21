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
@else
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message" style="display:none;">
        <strong>Error!</strong>
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
    // Show booking success modal if booking reference exists in session
    const bookingReference = "{{ session('booking_reference') }}";
    if (bookingReference) {
        $('#bookingSuccessModal').modal('show');
    }

        // Date and time restrictions logic
    const setupDateRestrictions = (dateInput, timeInput) => {
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);

            dateInput.addEventListener('change', function () {
                const selectedDate = new Date(dateInput.value);
                const now = new Date();
                if (selectedDate.toDateString() === now.toDateString()) {
                    timeInput.setAttribute('min', now.toTimeString().slice(0, 5));
                } else {
                    timeInput.removeAttribute('min');
                }
            });
        }
    };

    setupDateRestrictions(document.getElementById('pickup-date'), document.getElementById('pickup-time'));
    setupDateRestrictions(document.getElementById('return-pickup-date'), document.getElementById('return-pickup-time'));
    setupDateRestrictions(document.getElementById('pickup-date-tab2'), document.getElementById('pickup-time-tab2'));

    // Generic AJAX submission handler for forms
    // Generic AJAX submission handler for forms
    const handleFormSubmission = (form, submitBtn, route) => {
        if (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Processing...";
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch(route, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

                          // Reset the form after successful submission
                        form.reset();

                        document.getElementById('bookingSuccessModalLabel').innerHTML = 'Booking Successful!';
                        document.querySelector('.modal-body').innerHTML = `
                            <p>Your booking has been successfully completed.</p>
                            <p><strong>Booking Reference:</strong> ${data.booking_reference}</p>`;
                        $('#bookingSuccessModal').modal('show');
                    } else if (data.error) {
                        // Inject the error message into the alert container
                        const errorContainer = document.getElementById('error-message');
                        errorContainer.innerHTML = `
                            <strong>Error!</strong> ${data.error}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                        errorContainer.style.display = 'block';
                    } else {
                        alert('An error occurred while processing your booking.');
                    }
                    submitBtn.innerHTML = "Proceed";
                    submitBtn.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.innerHTML = "Proceed";
                    submitBtn.disabled = false;
                });
            });
        }
    };



    // Setup form submissions for Airport Transfer and Charter
    handleFormSubmission(document.getElementById('airport-transfer-form'), document.getElementById('submit-btn'), "{{ route('booking.store') }}");
    handleFormSubmission(document.getElementById('charter-form'), document.getElementById('ch-submit-btn'), "{{ route('booking.store') }}");

    // Booking Status AJAX submission
    const bookStatusForm = document.getElementById('booking-status-form');
    const bookSubmitBtn = document.getElementById('bk-submit-btn');

    if (bookStatusForm) {
        bookStatusForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Reset previous modal content
            document.getElementById('booking-status-details').innerHTML = "";

            const bookingReference = document.getElementById('booking-reference').value;

            console.log('Submitting booking reference:', bookingReference);

            bookSubmitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Processing...";
            bookSubmitBtn.disabled = true;

            fetch('/check-booking-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ booking_reference: bookingReference }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response received: ', data);

                // Response status is "success"
                if (data.status === 'success' || data.status === 'pending') {
                    // Populate the modal with the actual booking details
                    const bookingDetails = `
                        <p><strong>Booking Reference:</strong> ${data.booking_reference}</p>
                        <p><strong>Booking Status:</strong> ${data.status}</p> <!-- Now using data.status to show booking status -->
                        <p><strong>Service Type:</strong> ${data.service_type}</p>
                        <p><strong>Pickup/Drop-off Date:</strong> ${data.date}</p>
                        <p><strong>Vehicle Type:</strong> ${data.vehicle_type}</p>
                    `;
                    document.getElementById('booking-status-details').innerHTML = bookingDetails;

                    // Show the modal using jQuery
                    $('#bookingStatusModal').modal('show');

                } else {
                    // Handle error: No such booking reference number found
                    document.getElementById('booking-status-details').innerHTML = '<p>No such booking reference number found.</p>';
                    $('#bookingStatusModal').modal('show');
                }

                bookSubmitBtn.innerHTML = "Check Status";
                bookSubmitBtn.disabled = false;
            })
            .catch(error => {
                console.error('Error during fetch request:', error);
                bookSubmitBtn.innerHTML = "Check Status";
                bookSubmitBtn.disabled = false;
            });
        });
    }


    // Airport Pickup and Drop-off toggle logic
    const toggleTripType = (btn1, btn2, showElement, hideElement, tripTypeValue) => {
        btn1.addEventListener('click', function () {
            showElement.style.display = 'block';
            hideElement.style.display = 'none';
            btn1.classList.add('active');
            btn2.classList.remove('active');
            document.getElementById('trip_type').value = tripTypeValue;
        });
    };

    toggleTripType(
        document.getElementById('pickup-btn'),
        document.getElementById('dropoff-btn'),
        document.getElementById('dropoff-address-group'),
        document.getElementById('pickup-address-group'),
        'airport_pickup'
    );

    toggleTripType(
        document.getElementById('dropoff-btn'),
        document.getElementById('pickup-btn'),
        document.getElementById('pickup-address-group'),
        document.getElementById('dropoff-address-group'),
        'airport_dropoff'
    );

    // Charter One-Way and Round-Trip logic
    const oneWayBtn = document.getElementById('one-way');
    const roundTripBtn = document.getElementById('round-trip');
    const returnFields = document.getElementById('return-fields');
    if (oneWayBtn && roundTripBtn) {
        oneWayBtn.addEventListener('click', function () {
            returnFields.style.display = 'none';
            document.getElementById('charter_trip_type').value = 'oneway';
            oneWayBtn.classList.add('active');
            roundTripBtn.classList.remove('active');
        });

        roundTripBtn.addEventListener('click', function () {
            returnFields.style.display = 'block';
            document.getElementById('charter_trip_type').value = 'round_trip';
            roundTripBtn.classList.add('active');
            oneWayBtn.classList.remove('active');
        });
    }

    // Save active tab in localStorage and retrieve it on reload
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $(`#${activeTab}-tab`).tab('show');
    }

    $('a[data-toggle="tab"]').on('click', function (e) {
        localStorage.setItem('activeTab', $(e.target).attr('href').substring(1));
    });
});

</script>



