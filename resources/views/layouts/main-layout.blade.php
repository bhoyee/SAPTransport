<!-- resources/views/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SAP Transportation and Logistics | Reliable Transport Services')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-96x96.png') }}">
    <link rel="manifest" href="{{ asset('img/manifest.json') }}">

    <!-- Meta Tags for SEO -->
    <meta name="description" content="@yield('meta_description', 'SAP Transportation and Logistics offers reliable and safe transportation services across the country. Airport Transfer, Charter Services. We available 24/7 support.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Transportation, Logistics, Safe Transport, Reliable Transport, SAP Transportation, Transportation Services, Logistics Services, Airport Transfer, Point to Point, Charter')">
    <meta name="author" content="SAP Transportation and Logistics">
    <meta name="robots" content="index, follow">
    <meta name="google-site-verification" content="your-google-site-verification-code-here" /> <!-- Replace with actual code if you have one -->

    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'SAP Transportation and Logistics | Reliable Transport Services')">
    <meta property="og:description" content="@yield('og_description', 'SAP Transportation and Logistics offers reliable and safe transportation services across the country. Contact us for personalized solutions and 24/7 support.')">
    <meta property="og:image" content="{{ asset('img/logo.png') }}"> <!-- Replace with actual image URL -->
    <meta property="og:url" content="@yield('og_url', 'http://saptransportationandlogistics.ng/')">
    <meta property="og:type" content="website">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'SAP Transportation and Logistics | Reliable Transport Services')">
    <meta name="twitter:description" content="@yield('twitter_description', 'SAP Transportation and Logistics offers reliable and safe transportation services across the country. Contact us for personalized solutions and 24/7 support.')">
    <meta name="twitter:image" content="{{ asset('img/logo.png') }}"> <!-- Replace with actual image URL -->

    <!-- Canonical Link -->
    <link rel="canonical" href="@yield('canonical_url', 'http://saptransportationandlogistics.ng')">

    <!-- Stylesheets -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
       
       
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LfZtDQqAAAAAN1N3RiSZVGdHGBpV27rD9tRAguI"></script>


    @stack('head')
</head>
<body>
<header class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" alt="SapTransport Logo" class="logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/faq') }}">FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">Contact</a></li>
                <li class="nav-item">
                <li class="nav-item">
                @if(Auth::check())
                    @if(Auth::user()->hasRole('passenger') && Auth::user()->email_verified_at === null)
                        <a href="{{ route('verification.notice') }}" id="auth-button" class="btn btn-warning ms-2">
                            Verify Your Email
                        </a>
                    @else
                        <a href="{{ route(
                            Auth::user()->hasRole('admin') ? 'admin.dashboard' : 
                            (Auth::user()->hasRole('consultant') ? 'consultant.dashboard' : 'passenger.dashboard')
                        ) }}" id="auth-button" class="btn btn-primary ms-2">Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" id="auth-button" class="btn btn-primary ms-2">Sign In / Sign Up</a>
                @endif
            </li>




       


            </ul>
        </div>
    </div>
</header>


    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer Section -->
    <footer class="footer-section text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h3>SapTransport</h3>
                    <p>
                        At SAP Transportation, we deliver safe, reliable, 
                        and customer-focused services, ensuring excellence whether you're traveling locally or nationwide.
                    </p>
                    <div class="social-icons">
                        <a href="#" class="text-white mr-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-pinterest"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h3>Explore Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Privacy Policy</a></li>
                        <li><a href="#" class="text-white">Sitemap</a></li>
                        <li><a href="#" class="text-white">Cancellation</a></li>
                        <li><a href="#" class="text-white">Terms Of Service</a></li>
                        <li><a href="#" class="text-white">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3>Quick Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">About</a></li>
                        <li><a href="#" class="text-white">My Booking</a></li>
                        <li><a href="{{ route('payment.search') }}" class="text-white">Make Payment</a></li>
                        <li><a href="#" class="text-white">FAQ</a></li>
                        <li><a href="#" class="text-white">Login / Register</a></li>
                        
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3>Contact Info</h3>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt"></i> 10 Awolowo Road Ikoyi, Lagos, Nigeria.</li>
                        <li><i class="fas fa-envelope"></i> support@saptransportationandlogistics.ng</li>
                        <li><i class="fas fa-phone"></i> +234 807 041 9826</li>
                        <li><i class="fas fa-globe"></i> http://www.saptransportationandlogistics.ng</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center mt-4">
    <div class="d-flex justify-content-center align-items-center flex-wrap">
        <p class="mb-0">
            Designed with 
            <span style="color: red;">&#10084;&#65039;</span> 
            by <a href="https://giddyhost.com/" class="text-white" target="_blank">Giddy Host</a>
        </p>
        <p class="mb-0 ml-3">
            © Copyrights 2024 
            <a href="#" class="text-white">SAP Transportation and Logistics</a>. All Rights Reserved.
        </p>
    </div>
    <div class="payment-icons mt-3">
        <i class="fab fa-cc-visa mr-2"></i>
        <i class="fab fa-cc-mastercard mr-2"></i>
        <i class="fab fa-cc-paypal mr-2"></i>
        <i class="fab fa-cc-discover mr-2"></i>
        <i class="fab fa-cc-jcb"></i>
    </div>
</div>

        </div>
    </footer>

    <!-- WhatsApp Widget -->
    <div class="whatsapp-widget">
        <div class="whatsapp-header">
            <img src="{{ asset('img/sup.png') }}" alt="User Image" class="user-img">
            <div class="user-info">
                <h4>Bob</h4>
                <p>Manager</p>
            </div>
            <span class="close-widget">&times;</span>
        </div>
        <div class="whatsapp-body">
            <p>Hello 👋 How may we help you? Just send us a message now to get assistance.</p>
        </div>
        <div class="whatsapp-footer">
            <a href="https://wa.me/2348070419826" target="_blank" class="whatsapp-button">WhatsApp</a>
        </div>
    </div>
    <div class="whatsapp-launcher">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
    </div>
    <script>
    let idleTime = 0;
    const lockTimeout = 10 * 60 * 1000; // 10 minutes for lock screen
    const totalTimeout = 20 * 60 * 1000; // 20 minutes for session expiration

    // Increment the idle time counter every minute
    let idleInterval = setInterval(timerIncrement, 60000); // 1 minute check interval

    // Reset the idle timer on user activity
    window.onmousemove = resetTimer;
    window.onkeypress = resetTimer;
    window.onscroll = resetTimer;
    window.onclick = resetTimer;

    function resetTimer() {
        idleTime = 0; // Reset the timer to 0
        updateLastActivity(); // Update the last activity on the server
    }

    function timerIncrement() {
        idleTime += 60000; // Increment idle time by 1 minute

        if (idleTime >= lockTimeout) {
            // Redirect to the lock screen after 10 minutes of inactivity
            window.location.href = '/lock';
        }

        if (idleTime >= totalTimeout) {
            // Redirect to the home page after 20 minutes of inactivity (session timeout)
            window.location.href = '/';
        }
    }

    function updateLastActivity() {
        // Use AJAX call to update the session activity timestamp
        fetch('/update-last-activity', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).catch(err => console.error('Failed to update last activity', err));
    }
</script>


    
<!-- <script>
    let idleTime = 0;
    const maxIdleTime = 180; // 10 minutes in seconds

    // Increment idle time every second
    setInterval(() => {
        idleTime++;
        if (idleTime >= maxIdleTime) {
            // Log the user out via AJAX request
            fetch('{{ route('logout') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin' // To include cookies in the request
            })
            .then(response => {
                if (response.ok) {
                    // Once logged out, change the button text to "Login/Signup"
                    document.getElementById('auth-button').innerHTML = 'Sign In / Sign Up';
                    document.getElementById('auth-button').href = '{{ route('login') }}'; // Update the button's action to the login page
                } else {
                    console.error('Logout failed');
                }
            })
            .catch(error => console.error('Error logging out:', error));
        }
    }, 1000); // Increment every second

    // Reset idle time when the user interacts with the page
    document.addEventListener('mousemove', () => idleTime = 0);
    document.addEventListener('keypress', () => idleTime = 0);
    document.addEventListener('click', () => idleTime = 0);
    document.addEventListener('scroll', () => idleTime = 0);
</script> -->



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script>
    function moveToNextSlide() {
    // Your slide logic here
}
    // This script depends on scripts.js being loaded first
    moveToNextSlide();
</script>
    @stack('scripts')
</body>
</html>
