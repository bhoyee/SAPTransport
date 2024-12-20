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
     <!-- Bootstrap 5 and Other Stylesheets -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
                            (Auth::user()->hasRole('consultant') ? 'staff.dashboard' : 'passenger.dashboard')
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
                        <li><a href="{{ url('/privacy-policy') }}" class="text-white">Privacy Policy</a></li>
                        <!-- <li><a href="#" class="text-white">Sitemap</a></li> -->
                        <li><a href="{{ url('/cancellation-policy') }}" class="text-white">Cancellation</a></li>
                        <li><a href="{{ url('/terms-and-conditions') }}" class="text-white">Terms Of Service</a></li>
                        <li><a href="{{ url('/refund-policy') }}" class="text-white">Refund Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3>Quick Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/about') }}" class="text-white">About</a></li>
                        <!-- <li><a href="#" class="text-white">My Booking</a></li> -->
                        <li><a href="{{ route('payment.search') }}" class="text-white">Make Payment</a></li>
                        <li><a href="{{ url('/faq') }}" class="text-white">FAQ</a></li>
                        <li><a href="{{ url('/login') }}" class="text-white">Login / Register</a></li>
                        
                    </ul>
                </div>
                <div class="col-md-3" style="font-size: 0.9rem;">
                    <h3 style="font-size: 1.2rem;">Contact Info</h3>
                    <ul class="list-unstyled" style="font-size: 0.9rem;">
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
    <!-- <div class="whatsapp-widget">
        <div class="whatsapp-header">
            <img src="{{ asset('img/sup.png') }}" alt="User Image" class="user-img">
            <div class="user-info">
                <h4>Teresa</h4>
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
    </div> -->




    


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>

<!-- BotMan Widget Configuration -->
<script>
        var botmanWidget = {
            title: "SAP Transport Assistant",
            introMessage: "Hi! How can I assist you today?",
            mainColor: "#007bff",
            bubbleBackground: "#007bff",
            aboutText: "Chat with SAP Transport",
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js"></script>











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
