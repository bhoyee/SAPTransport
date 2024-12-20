@extends('layouts.passenger')

@section('title', 'Edit Booking')

@section('content')


<!-- Notification Area -->
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <a href="{{ route('passenger.dashboard') }}" class="btn btn-sm btn-primary">Go to Dashboard</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif


<div class="position-relative mb-3">
    <div class="row g-3 justify-content-between">
        <div class="col-auto">
            <h1 class="app-page-title mb-0">Edit Booking - {{ $booking->booking_reference }}</h1>
        </div>
    </div>
</div>

<div class="app-card app-card-notification shadow-sm mb-4">
    <div class="app-card-body p-4">
        <div class="notification-content">
            <form id="edit-Form" action="{{ route('booking.update', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Service Type Dropdown -->
                <div class="form-group">
                    <label for="service-type">Service Type</label>
                    <select id="service-type" name="service_type" class="form-control" required>
                        <option value="AirportTransfer" {{ old('service_type', $booking->service_type) == 'AirportTransfer' ? 'selected' : '' }}>Airport Transfer</option>
                        <option value="Charter" {{ old('service_type', $booking->service_type) == 'Charter' ? 'selected' : '' }}>Charter</option>
                    </select>
                </div>

                <!-- Trip Type Dropdown -->
                <div class="form-group">
                    <label for="trip-type">Trip Type</label>
                    <select id="trip-type" name="trip_type" class="form-control" required>
                        <!-- Options will be dynamically added by JS based on service type -->
                    </select>
                </div>

                <!-- Airport Dropdown (if Airport Transfer is selected) -->
                <div class="form-group" id="airport-select">
                    <label for="select-airport">Select Airport</label>
                    <select id="select-airport" name="airport_name" class="form-control" required>
                        <option value="murtala-intl">Murtala Muhammed International Airport</option>
                        <option value="murtala-domestic-mm1">Murtala Muhammed Domestic MM1</option>
                        <option value="murtala-domestic-mm2">Murtala Muhammed Domestic MM2</option>
                    </select>
                </div>

                <!-- Vehicle Type Dropdown -->
                <div class="form-group">
                    <label for="vehicle-type">Vehicle Type</label>
                    <select id="vehicle-type" name="vehicle_type" class="form-control" required>
                        <option value="car">SAP Car</option>
                        <option value="hilux">SAP Hilux (4 Seats)</option>
                        <option value="hiace">SAP Hiace (14 Seats)</option>
                        <option value="coaster">SAP Coaster (32 Seats)</option>
                    </select>
                </div>

                <!-- Pickup/Dropoff Address Fields -->
                <div class="form-group" id="pickup-address-group" style="{{ old('trip_type', $booking->trip_type) == 'airport_pickup' ? 'display:none;' : '' }}">
                    <label for="pickup-address">Pickup Address</label>
                    <input type="text" id="pickup-address" name="pickup_address" class="form-control" value="{{ old('pickup_address', $booking->pickup_address) }}">
                </div>

                <div class="form-group" id="dropoff-address-group" style="{{ old('trip_type', $booking->trip_type) == 'airport_dropoff' ? 'display:none;' : '' }}">
                    <label for="dropoff-address">Dropoff Address</label>
                    <input type="text" id="dropoff-address" name="dropoff_address" class="form-control" value="{{ old('dropoff_address', $booking->dropoff_address) }}">
                </div>

                <!-- Pickup Date -->
                <div class="form-group">
                    <label for="pickup-date">Pickup Date</label>
                    <input type="date" id="pickup-date" name="pickup_date" class="form-control" value="{{ old('pickup_date', $booking->pickup_date) }}" required>
                </div>

                <!-- Pickup Time -->
                <div class="form-group">
                    <label for="pickup-time">Pickup Time</label>
                    <input type="time" id="pickup-time" name="pickup_time" class="form-control" value="{{ old('pickup_time', $booking->pickup_time) }}" required>
                </div>

                <!-- Number of Adults -->
                <div class="form-group">
                    <label for="number-adults">Number of Adults</label>
                    <input type="number" id="number-adults" name="number_adults" class="form-control" value="{{ old('number_adults', $booking->number_adults) }}" required>
                </div>

                <!-- Number of Children -->
                <div class="form-group">
                    <label for="number-children">Number of Children</label>
                    <input type="number" id="number-children" name="number_children" class="form-control" value="{{ old('number_children', $booking->number_children) }}">
                </div>

                <!-- Security Coverage -->
                <div class="form-group">
                    <label for="security-coverage">Do you need security coverage?</label>
                    <select id="security-coverage" name="security_coverage" class="form-control" required>
                        <option value="no" {{ old('security_coverage', $booking->security_coverage) == 'no' ? 'selected' : '' }}>No</option>
                        <option value="yes" {{ old('security_coverage', $booking->security_coverage) == 'yes' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>

                <!-- Mobile Police Count (only visible if security coverage is 'yes') -->
                <div class="form-group" id="mobile-police-group" style="{{ old('security_coverage', $booking->security_coverage) == 'yes' ? '' : 'display:none;' }}">
                    <label for="mobile-police">Select number of mobile police (minimum 2)</label>
                    <select id="mobile-police" name="mobile_police_count" class="form-control" required>
                        @for ($i = 2; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('mobile_police_count', $booking->mobile_police_count) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- With Van or Without Van (only visible if security coverage is 'yes') -->
                <div class="form-group" id="with-van-group" style="{{ old('security_coverage', $booking->security_coverage) == 'yes' ? '' : 'display:none;' }}">
                    <label>With Van or Without Van</label><br>
                    <input type="checkbox" id="with-van" name="with_van" value="yes" {{ old('with_van', $booking->with_van) == 'yes' ? 'checked' : '' }}> With Van
                    <input type="checkbox" id="without-van" name="with_van" value="no" {{ old('with_van', $booking->with_van) == 'no' ? 'checked' : '' }}> Without Van
                </div>

                <!-- Return Trip Fields (if Round Trip is selected) -->
                <div class="form-group" id="return-group" style="{{ old('trip_type', $booking->trip_type) === 'round_trip' ? '' : 'display:none;' }}">
                    <label for="return-pickup-date">Return Pickup Date</label>
                    <input type="date" id="return-pickup-date" name="return_pickup_date" class="form-control" value="{{ old('return_pickup_date', $booking->return_pickup_date) }}">
                </div>

                <div class="form-group" id="return-group-time" style="{{ old('trip_type', $booking->trip_type) === 'round_trip' ? '' : 'display:none;' }}">
                    <label for="return-pickup-time">Return Pickup Time</label>
                    <input type="time" id="return-pickup-time" name="return_pickup_time" class="form-control" value="{{ old('return_pickup_time', $booking->return_pickup_time) }}">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" id="update-button">
                    <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span id="button-text">Update Booking</span>
                </button>
                <a href="{{ route('passenger.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<style>
    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
    }

    .btn {
        margin-top: 20px;
        margin-right: 10px;
    }

    .app-card {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
    }

    .app-card-body {
        padding: 20px;
    }

    .form-control {
        padding: 10px;
        font-size: 14px;
    }

    .btn-secondary {
        margin-left: 10px;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const securityCoverageSelect = document.getElementById('security-coverage');
    const mobilePoliceGroup = document.getElementById('mobile-police-group');
    const withVanGroup = document.getElementById('with-van-group');

    // Toggle visibility of Mobile Police and Van options based on security coverage
    securityCoverageSelect.addEventListener('change', function () {
        if (this.value === 'yes') {
            mobilePoliceGroup.style.display = '';
            withVanGroup.style.display = '';
        } else {
            mobilePoliceGroup.style.display = 'none';
            withVanGroup.style.display = 'none';
        }
    });

    // Initialize the visibility based on the current value of security coverage
    if (securityCoverageSelect.value === 'yes') {
        mobilePoliceGroup.style.display = '';
        withVanGroup.style.display = '';
    } else {
        mobilePoliceGroup.style.display = 'none';
        withVanGroup.style.display = 'none';
    }

    // Handle checkbox validation: only one checkbox (With Van or Without Van) should be selected at a time
    const withVanCheckbox = document.getElementById('with-van');
    const withoutVanCheckbox = document.getElementById('without-van');

    withVanCheckbox.addEventListener('change', function () {
        if (withVanCheckbox.checked) {
            withoutVanCheckbox.checked = false;
        }
    });

    withoutVanCheckbox.addEventListener('change', function () {
        if (withoutVanCheckbox.checked) {
            withVanCheckbox.checked = false;
        }
    });
});
</script>
<script>


document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById('edit-Form');
    const submitButton = document.getElementById('update-button');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');

    form.addEventListener('submit', function(event) {
        // Show the spinner and disable the button
        spinner.classList.remove('d-none');
        buttonText.classList.add('d-none');  // Hide the button text while spinning
        submitButton.disabled = true;  // Disable the button to prevent multiple clicks
    });
    
    const bookingData = @json($booking);
    console.log("Booking data from the server:", bookingData);

    // Populate the form fields with the booking data from the server
    document.getElementById('service-type').value = normalizeServiceType(bookingData.service_type);
    document.getElementById('vehicle-type').value = normalizeVehicleType(bookingData.vehicle_type);
    document.getElementById('pickup-date').value = bookingData.pickup_date;
    document.getElementById('pickup-time').value = bookingData.pickup_time;
    document.getElementById('number-adults').value = bookingData.number_adults;
    document.getElementById('number-children').value = bookingData.number_children;

    // Populate the airport select field if service type is "Airport Transfer"
    if (bookingData.service_type === 'Airport Transfer') {
        document.getElementById('select-airport').value = normalizeAirportName(bookingData.airport_name);
    }

    // Initialize the trip type options based on the service type
    updateTripTypeOptions(normalizeServiceType(bookingData.service_type), normalizeTripType(bookingData.trip_type));

    // Event listeners for service type and trip type
    document.getElementById('service-type').addEventListener('change', function() {
        const serviceType = this.value;
        const tripType = document.getElementById('trip-type').value;
        updateTripTypeOptions(serviceType, tripType);
        toggleFields(serviceType, tripType);
    });

    document.getElementById('trip-type').addEventListener('change', function() {
        const serviceType = document.getElementById('service-type').value;
        const tripType = this.value;
        toggleFields(serviceType, tripType);
    });

    // Function to dynamically update trip type options based on service type
    function updateTripTypeOptions(serviceType, tripType) {
        const tripTypeSelect = document.getElementById('trip-type');
        tripTypeSelect.innerHTML = '';  // Clear existing options

        if (serviceType === 'AirportTransfer') {
            tripTypeSelect.innerHTML += '<option value="airport_pickup">Airport Pickup</option>';
            tripTypeSelect.innerHTML += '<option value="airport_dropoff">Airport Drop-Off</option>';
            document.getElementById('airport-select').style.display = ''; // Show airport select
            document.getElementById('select-airport').value = normalizeAirportName(bookingData.airport_name);  // Set airport value
        } else if (serviceType === 'Charter') {
            tripTypeSelect.innerHTML += '<option value="oneway">One Way</option>';
            tripTypeSelect.innerHTML += '<option value="round_trip">Round Trip</option>';
            document.getElementById('airport-select').style.display = 'none';  // Hide airport select
        }

        // Set the trip type value based on the booking data
        tripTypeSelect.value = tripType;
        toggleFields(serviceType, tripType);
    }

    // Function to toggle visibility of fields based on trip type
    function toggleFields(serviceType, tripType) {
        if (serviceType === 'AirportTransfer') {
            if (tripType === 'airport_pickup') {
                document.getElementById('pickup-address-group').style.display = 'none';
                document.getElementById('dropoff-address-group').style.display = '';
            } else if (tripType === 'airport_dropoff') {
                document.getElementById('pickup-address-group').style.display = '';
                document.getElementById('dropoff-address-group').style.display = 'none';
            }
            document.getElementById('return-group').style.display = 'none';
            document.getElementById('return-group-time').style.display = 'none';
        } else if (serviceType === 'Charter') {
            document.getElementById('pickup-address-group').style.display = '';
            document.getElementById('dropoff-address-group').style.display = '';
            if (tripType === 'oneway') {
                document.getElementById('return-group').style.display = 'none';
                document.getElementById('return-group-time').style.display = 'none';
            } else if (tripType === 'round_trip') {
                document.getElementById('return-group').style.display = '';
                document.getElementById('return-group-time').style.display = '';
            }
        }
    }

    // Helper function to normalize service type
    function normalizeServiceType(serviceType) {
        if (serviceType === 'Airport Transfer' || serviceType === 'AirportTransfer') {
            return 'AirportTransfer';
        } else if (serviceType === 'Charter Service' || serviceType === 'Charter') {
            return 'Charter';
        }
        return '';
    }

    // Helper function to normalize trip type
    function normalizeTripType(tripType) {
        if (tripType === 'Airport Pickup' || tripType === 'airport_pickup') {
            return 'airport_pickup';
        } else if (tripType === 'Airport Drop-Off' || tripType === 'airport_dropoff') {
            return 'airport_dropoff';
        } else if (tripType === 'One Way' || tripType === 'oneway') {
            return 'oneway';
        } else if (tripType === 'Round Trip' || tripType === 'round_trip') {
            return 'round_trip';
        }
        return '';
    }

    // Helper function to normalize vehicle type
    function normalizeVehicleType(vehicleType) {
        if (vehicleType === 'SAP Car' || vehicleType === 'car') {
            return 'car';
        } else if (vehicleType === 'SAP Hilux (4 Seats)' || vehicleType === 'hilux') {
            return 'hilux';
        } else if (vehicleType === 'SAP Hiace (14 Seats)' || vehicleType === 'hiace') {
            return 'hiace';
        } else if (vehicleType === 'SAP Coaster (32 Seats)' || vehicleType === 'coaster') {
            return 'coaster';
        }
        return '';
    }

    // Helper function to normalize airport name
    function normalizeAirportName(airportName) {
        if (airportName === 'Murtala Muhammed International Airport' || airportName === 'murtala-intl') {
            return 'murtala-intl';
        } else if (airportName === 'Murtala Muhammed Domestic MM1' || airportName === 'murtala-domestic-mm1') {
            return 'murtala-domestic-mm1';
        } else if (airportName === 'Murtala Muhammed Domestic MM2' || airportName === 'murtala-domestic-mm2') {
            return 'murtala-domestic-mm2';
        }
        return '';
    }

    // Initialize the fields based on the booking data
    toggleFields(normalizeServiceType(bookingData.service_type), normalizeTripType(bookingData.trip_type));
});

</script>


@endsection
