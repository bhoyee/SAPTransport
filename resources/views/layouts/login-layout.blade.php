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
    
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <link rel="stylesheet" href="{{ asset('css/forgot-password.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LfZtDQqAAAAAN1N3RiSZVGdHGBpV27rD9tRAguI"></script>


    @stack('head')
</head>
<body style="background-image: url('{{ asset('img/sectionBG.png') }}');">


    <!-- ========== Main Content Section ========== -->
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
            
            <!-- ========== Logo Area Section ========== -->
            <section class="logo-area text-center mb-4">
                <a href="{{ url('/') }}">
                 <img src="{{ asset('img/logo.png') }}" alt="SapTransport Logo" class="logo" style="width: 95%; height: 55px;">
                </a>
            </section>
            
            <!-- ========== Form Section ========== -->
          
            <!-- Page Content -->
            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- ========== Scripts Section ========== -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
    <script src="{{ asset('js/login.js') }}"></script>


    @yield('scripts') <!-- Placeholder for page-specific scripts -->
</body>
</html>