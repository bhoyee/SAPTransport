<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', 'SAP Transportation and Logistics | Reliable Transport Services')</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="SAP Transportation and Logistics Dashboard">
    <meta name="author" content="Giddy Host">    
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}"> 

    <!-- FontAwesome JS -->
    <script defer src="{{ asset('assets/plugins/fontawesome/js/all.min.js') }}"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.min.css">

    <!-- Custom Portal CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/portal.css') }}">

    @stack('styles') <!-- For additional page-specific CSS -->

</head>

<body class="app">

    <!-- Header -->
    <header class="app-header fixed-top">
        <div class="app-header-inner">
            <div class="container-fluid py-2">
                <div class="app-header-content">
                    <!-- Include Navbar -->
                    @include('partials.passengerDash.navbar')
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div id="app-sidepanel" class="app-sidepanel">
            <div id="sidepanel-drop" class="sidepanel-drop"></div>
            @include('partials.passengerDash.sidebar')
        </div>
    </header>

    <!-- Main Wrapper -->
    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <footer class="app-footer">
            <div class="container text-center py-3">
                <small class="copyright">Designed by <a class="app-link" href="https://giddyhost.com" target="_blank">Giddy Host</a></small>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <!-- jQuery (if you are using it) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.min.js"></script>

    <!-- Custom JS for Idle Timer (lock screen) -->
    <script>
        let idleTime = 0;
        const maxIdleTime = 10 * 60; // 10 minutes

        // Function to reset the idle timer when there's user interaction
        function resetIdleTimer() {
            console.log('User activity detected, resetting idle timer'); // Debugging log
            idleTime = 0;
        }

        // Increment the idle timer every second
        setInterval(() => {
            idleTime++;
            console.log('Idle time:', idleTime); // Debugging log to check idle time increment

            // If idle time exceeds the max limit, redirect to the lock screen
            if (idleTime >= maxIdleTime) {
                console.log('Max idle time reached. Redirecting to lock screen.');
                window.location.href = "{{ route('lockscreen.show') }}";
            }
        }, 1000); // Check every second

        // Reset idle timer on user interaction (mouse movement or keypress)
        window.onmousemove = resetIdleTimer;
        window.onkeypress = resetIdleTimer;

        // Optional: Also reset timer on touch events for mobile devices
        window.ontouchstart = resetIdleTimer;
    </script>

    <!-- Other Plugin JS -->
    <script src="{{ asset('assets/plugins/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/index-charts.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts') <!-- For additional page-specific JS -->

</body>

</html>
