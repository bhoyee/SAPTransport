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
                    @include('admin.partials.navbar')<!-- Updated path -->
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div id="app-sidepanel" class="app-sidepanel">
            <div id="sidepanel-drop" class="sidepanel-drop"></div>
            @include('admin.partials.sidebar')  <!-- Updated path -->
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
    const lockTimeout = 600000;  // 10 minutes (600,000 milliseconds)
    let idleTimer;
    let lastActivity = Date.now();

    function lockSession() {
        console.log('Locking session due to inactivity');
        fetch('/lock-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        }).then(() => {
            window.location.href = "{{ route('lockscreen.show') }}";
        }).catch((error) => {
            console.error('Error locking session:', error);
        });
    }

    function resetIdleTimer() {
        lastActivity = Date.now();
        clearTimeout(idleTimer);
        idleTimer = setTimeout(lockSession, lockTimeout);
    }

    function monitorActivity() {
        const now = Date.now();
        if (now - lastActivity > lockTimeout) {
            lockSession();
        }
        requestAnimationFrame(monitorActivity);
    }

    function attachUserActivityListeners() {
        window.addEventListener('mousemove', resetIdleTimer);
        window.addEventListener('mousedown', resetIdleTimer);
        window.addEventListener('touchstart', resetIdleTimer);
        window.addEventListener('click', resetIdleTimer);
        window.addEventListener('keypress', resetIdleTimer);
        window.addEventListener('scroll', resetIdleTimer);
        window.addEventListener('touchmove', resetIdleTimer);
    }

    window.onload = function () {
        resetIdleTimer();
        monitorActivity();
        attachUserActivityListeners();
    };
    </script>

    <script src="{{ asset('assets/plugins/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/index-charts.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts')

</body>

</html>
