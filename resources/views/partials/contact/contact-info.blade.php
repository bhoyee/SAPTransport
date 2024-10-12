<section class="container py-5">
    <div class="row">
        <div class="col-md-7 form-container">
            <h3>Want us to contact you</h3>
            <p>Leave a message</p>
            
            <!-- Display success or error messages with close icon -->
            @if(session('success'))
                <div id="alert-message" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif(session('error'))
                <div id="alert-message" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="contact-form" action="{{ route('contact.submit') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="categories">Categories</label>
                    <select class="form-control" id="categories" name="categories" required>
                        <option>Select Categories</option>
                        <option value="enquiry">Enquiry</option>
                        <option value="emergency">Emergency</option>
                        <option value="feedback">Feedback</option>
                        <option value="query">Query</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="fullname" 
                        name="fullname" 
                        value="{{ auth()->check() ? auth()->user()->name : '' }}" 
                        placeholder="Enter your full name"
                        {{ auth()->check() ? 'readonly' : '' }}
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input 
                        type="tel" 
                        class="form-control" 
                        id="phone" 
                        name="phone" 
                        value="{{ auth()->check() ? auth()->user()->phone : '' }}" 
                        placeholder="Enter your phone number"
                        {{ auth()->check() ? 'readonly' : '' }}
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        value="{{ auth()->check() ? auth()->user()->email : '' }}" 
                        placeholder="Enter your email address"
                        {{ auth()->check() ? 'readonly' : '' }}
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Enter your message" required></textarea>
                </div>

                <!-- Hidden input for reCAPTCHA token -->
                <input type="hidden" id="recaptcha-token" name="recaptcha-token">
                
                <button type="submit" class="btn btn-submit">
                    <span class="spinner-border spinner-border-sm" id="submit-spinner" style="display: none;"></span>
                    SEND
                </button>
            </form>
        </div>
        <div class="col-md-5 contact-us">
            <h3>Contact Us</h3>
            <div class="contact-info pl-4 ml-1">
                <p><i class="fas fa-map-marker-alt"></i> 10 Awolowo Road Ikoyi, Lagos, Nigeria.</p>
                <p><i class="fas fa-envelope"></i> support@saptransportationandlogistics.ng</p>
                <p><i class="fas fa-phone"></i> +234 807 041 9826</p>
            </div>
        </div>
    </div>
</section>

<!-- Show spinner on form submit -->
<script>
    document.getElementById('contact-form').addEventListener('submit', function() {
        document.getElementById('submit-spinner').style.display = 'inline-block';
    });
</script>
