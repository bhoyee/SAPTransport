<section class="container py-5">
        <div class="row">
            <div class="col-md-7 form-container">
                <h3>Want us to contact you</h3>
                <p>Leave a message</p>
                <form id="contact-form" action="contact_process.php" method="POST">
                    <div class="form-group">
                        <label for="categories">Categories</label>
                        <select class="form-control" id="categories" name="categories">
                            <option>Select Categories</option>
                            <option value="enquiry">Enquiry</option>
                            <option value="emergency">Emergency</option>
                            <option value="feedback">Feedback</option>
                            <option value="query">Query</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Enter your message"></textarea>
                    </div>
                
                    <!-- Hidden input to store reCAPTCHA token -->
                    <input type="hidden" id="recaptcha-token" name="recaptcha-token">
                
                    <button type="submit" class="btn btn-submit" onclick="onSubmit()">SEND</button>
                </form>
                
                <script>
                  function onSubmit() {
                    event.preventDefault();
                    grecaptcha.enterprise.ready(function() {
                      grecaptcha.enterprise.execute('6LfZtDQqAAAAAN1N3RiSZVGdHGBpV27rD9tRAguI', {action: 'submit'}).then(function(token) {
                          document.getElementById('recaptcha-token').value = token;
                          document.getElementById('contact-form').submit();
                      });
                    });
                  }
                </script>
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