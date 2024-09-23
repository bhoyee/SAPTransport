document.addEventListener('DOMContentLoaded', function() {
    // Toggle the visibility of the Pickup and Dropoff fields based on button clicks.
    console.log('DOM is fully loaded and script is running');

    const pickupBtn = document.getElementById('pickup-btn');
    const dropoffBtn = document.getElementById('dropoff-btn');
    const tripTypeInput = document.getElementById('trip_type');

    if (pickupBtn && dropoffBtn) {
        console.log('Pickup and Drop-off buttons found'); // Debugging check

        pickupBtn.addEventListener('click', function () {
            document.getElementById('dropoff-address-group').style.display = 'block';
            document.getElementById('pickup-address-group').style.display = 'none';

            this.classList.add('active');
            dropoffBtn.classList.remove('active');

            // Update trip_type value
            tripTypeInput.value = 'airport_pickup';
            console.log('Button clicked: Airport Pickup');
            console.log('Trip Type set to:', tripTypeInput.value); // Debugging log to verify change
        });

        dropoffBtn.addEventListener('click', function () {
            document.getElementById('dropoff-address-group').style.display = 'none';
            document.getElementById('pickup-address-group').style.display = 'block';

            this.classList.add('active');
            pickupBtn.classList.remove('active');

            // Update trip_type value
            tripTypeInput.value = 'airport_dropoff';
            console.log('Button clicked: Airport Drop-Off');
            console.log('Trip Type set to:', tripTypeInput.value); // Debugging log to verify change
        });
    } else {
        console.log('Pickup or Drop-off button not found in DOM');
    }
    // Toggle the visibility of the “Return Pickup Date” and “Return Pickup Time” fields based on which button is clicked
    const oneWayBtn = document.getElementById('one-way');
    const roundTripBtn = document.getElementById('round-trip');

    if (oneWayBtn && roundTripBtn) {
        oneWayBtn.addEventListener('click', function() {
            document.getElementById('return-fields').style.display = 'none';
            this.classList.add('active');
            roundTripBtn.classList.remove('active');
        });

        roundTripBtn.addEventListener('click', function() {
            document.getElementById('return-fields').style.display = 'flex';
            this.classList.add('active');
            oneWayBtn.classList.remove('active');
        });
    }

    // Code to dynamically populate the dropdowns based on the selected vehicle type
    document.querySelectorAll('.vehicle-type').forEach(selectElement => {
        selectElement.addEventListener('change', function() {
            const vehicleType = this.value;
            const parentForm = this.closest('form');
            const adultsSelect = parentForm.querySelector('.adults-select');
            const childrenSelect = parentForm.querySelector('.children-select');

            let adultsOptions = [];
            let childrenOptions = [];

            if (vehicleType === 'car') {
                adultsOptions = generateOptions(1, 4);
                childrenOptions = generateOptions(0, 4);
            } else if (vehicleType === 'hilux') {
                adultsOptions = generateOptions(1, 4);
                childrenOptions = generateOptions(0, 4);
            } else if (vehicleType === 'hiace') {
                adultsOptions = generateOptions(1, 14);
                childrenOptions = generateOptions(0, 13);
            } else if (vehicleType === 'coaster') {
                adultsOptions = generateOptions(1, 32);
                childrenOptions = generateOptions(0, 31);
            }

            populateSelect(adultsSelect, adultsOptions);
            populateSelect(childrenSelect, childrenOptions);
        });
    });

    function generateOptions(start, end) {
        let options = [];
        for (let i = start; i <= end; i++) {
            options.push(i);
        }
        return options;
    }

    function populateSelect(selectElement, options) {
        selectElement.innerHTML = '';
        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option;
            opt.textContent = option;
            selectElement.appendChild(opt);
        });
        selectElement.value = options[0];
    }

    // Auto slide for WhatsApp Chat
    setInterval(() => {
        moveToNextSlide();
    }, 5000); // Auto slide every 5 seconds

    const whatsappLauncher = document.querySelector('.whatsapp-launcher');
    const closeWidget = document.querySelector('.close-widget');

    if (whatsappLauncher) {
        whatsappLauncher.addEventListener('click', function() {
            document.querySelector('.whatsapp-widget').style.display = 'block';
            this.style.display = 'none';
        });
    }

    if (closeWidget) {
        closeWidget.addEventListener('click', function() {
            document.querySelector('.whatsapp-widget').style.display = 'none';
            document.querySelector('.whatsapp-launcher').style.display = 'flex';
        });
    }

    // FAQ sections
    $(document).ready(function() {
        // Hide all sections by default
        $(".faq-section .collapse").collapse('hide');

        // Toggle sections on click
        $(".faq-toggle").click(function(event) {
            event.preventDefault(); // Prevent default anchor click behavior
            var target = $(this).attr("data-target");

            // Toggle the clicked section
            $(target).collapse('toggle');

            // Hide all other sections
            $(".faq-section .collapse").not($(target)).collapse('hide');
        });

        // Ensure only one accordion item is open at a time within each section
        $('.collapse').on('show.bs.collapse', function () {
            $(this).siblings('.collapse.show').collapse('hide');
        });
    });

});
