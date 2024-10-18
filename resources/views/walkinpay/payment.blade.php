<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
          margin-top: 50px;
        }

        .lock-screen-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .image-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .car-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .lock-screen-container {
                flex-direction: column;
            }

            .image-container {
                margin-top: 20px;
            }
        }

        .spinner-border {
            display: none; /* Hide spinner initially */
        }
    </style>

</head>


<body>
    <div class="container lock-screen-container">
        <div class="row w-100">
            <!-- Left side: form -->
            <div class="col-md-6 form-container">
                <h2 class="text-center">Make Payment</h2>
                <p class="text-center">SAPTransport payment made easy</p>
                <form id="search-form">
                <div class="form-group mb-3">
                    <label for="booking-ref">Enter Booking Reference:</label>
                    <input type="text" name="booking_ref" id="booking-ref" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3" id="search-btn">
                    Search
                    <span class="spinner-border spinner-border-sm" id="search-spinner" style="display:none;" role="status" aria-hidden="true"></span>
                </button>
            </form>

                <!-- Google login button for social login users -->
            



            <!-- Display Error or Success Message -->
            <div id="message-area"></div>

                <!-- Display Booking Details if Found -->
                <div id="booking-details" class="d-none">
                    <h3>Booking Details</h3>
                    <p class="mb-3 text-danger"><strong>Note: </strong> Check your details to confirm before clicking on Pay Now button</p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr><th>Booking Reference</th><td id="booking-reference"></td></tr>
                            <tr><th>Booker's Name</th><td id="booker-name"></td></tr>
                            <tr><th>Booker's Phone</th><td id="booker-phone"></td></tr>
                            <tr><th>Booker's Email</th><td id="booker-email"></td></tr>
                            <tr><th>Service Type</th><td id="service-type"></td></tr>
                            <tr><th>Trip Type</th><td id="trip-type"></td></tr>
                            <tr><th>Pickup Date</th><td id="pickup-date"></td></tr>
                            <tr><th>Booking Status</th><td id="booking-status"></td></tr>
                            <tr><th>Invoice Number</th><td id="invoice-number"></td></tr>
                            <tr><th>Amount</th><td id="invoice-amount"></td></tr>
                            <tr><th>Invoice Status</th><td id="invoice-status"></td></tr>
                        </table>
                    </div>

                    <!-- Pay Now Button -->
                    <div id="pay-now-container" class="mt-4"></div>
                </div>
       


                <p class="text-center mt-3" style="font-weight: bolder;">Back to <a href="{{ url('/') }}">Home</a></p>
            </div>

            <!-- Right side: car image -->
            <div class="col-md-6 image-container">
                <img src="{{ asset('img/p22.png') }}" alt="Car Image" class="car-image">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Handle form submission with spinner for search
    $('#search-form').on('submit', function(event) {
        event.preventDefault();
        
        const bookingRef = $('#booking-ref').val();
        const searchButton = $('#search-btn');
        const searchSpinner = $('#search-spinner');
        const messageArea = $('#message-area');
        const bookingDetails = $('#booking-details');
        const payNowContainer = $('#pay-now-container');
        
        // Show spinner and disable the button
        searchSpinner.show();
        searchButton.prop('disabled', true);
        
        // Clear previous messages
        messageArea.html('');
        bookingDetails.addClass('d-none');
        payNowContainer.html(''); // Clear previous pay now button

        // Send AJAX request
        $.ajax({
            url: "{{ url('/pay') }}",
            method: 'GET',
            data: { booking_ref: bookingRef },
            success: function(response) {
                // Hide spinner and re-enable button
                searchSpinner.hide();
                searchButton.prop('disabled', false);
                
                if (response.status === 'error') {
                    // Display error message
                    messageArea.html('<div class="alert alert-danger">' + response.message + '</div>');
                } else {
                    // Fill the booking details table
                    $('#booking-reference').text(response.booking.booking_reference);
                    $('#booker-name').text(response.booking.user ? response.booking.user.name : 'N/A');
                    $('#booker-phone').text(response.booking.user ? response.booking.user.phone : 'N/A');
                    $('#booker-email').text(response.booking.user ? response.booking.user.email : 'N/A');
                    $('#service-type').text(response.booking.service_type);
                    $('#trip-type').text(response.booking.trip_type);
                    $('#pickup-date').text(response.booking.pickup_date);
                    $('#booking-status').text(response.booking.status);
                    $('#invoice-number').text(response.invoice.invoice_number);
                    $('#invoice-amount').text('₦' + parseFloat(response.invoice.amount).toFixed(2));
                    $('#invoice-status').text(response.invoice.status);

                    // Show booking details
                    bookingDetails.removeClass('d-none');

                    // Check invoice status and display appropriate button or message
                    if (response.invoice.status.toLowerCase() === 'unpaid') {
                        payNowContainer.html(`
                            <form method="POST" action="{{ url('/walkinpay/process') }}">
                                @csrf
                                <input type="hidden" name="invoice_id" value="${response.invoice.id}">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    Pay Now
                                    <span class="spinner-border spinner-border-sm" id="pay-spinner" style="display:none;" role="status" aria-hidden="true"></span>
                                </button>
                            </form>
                        `);

                        // Add spinner handling for pay now button
                        $('form').on('submit', function(event) {
                            const paySpinner = $('#pay-spinner');
                            const payButton = $(this).find('button[type="submit"]');

                            paySpinner.show(); // Show spinner
                            payButton.prop('disabled', true); // Disable button
                            payButton.html('Processing...'); // Update button text
                        });
                    } else {
                        payNowContainer.html(`
                            <div class="alert alert-success">
                                <strong>Payment Completed:</strong> The invoice has already been paid.
                            </div>
                        `);
                    }
                }
            },
            error: function(error) {
                searchSpinner.hide();
                searchButton.prop('disabled', false);
                messageArea.html('<div class="alert alert-danger">An error occurred. Please try again later.</div>');
            }
        });
    });
</script>

</body>
</html>


