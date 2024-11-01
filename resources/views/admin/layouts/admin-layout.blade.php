<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', 'SAP Transportation and Logistics | Reliable Transport Services')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SAP Transportation and Logistics Dashboard">
    <meta name="author" content="Giddy Host">    
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome JS -->
    <script defer src="{{ asset('assets/plugins/fontawesome/js/all.min.js') }}"></script>
    
    <!-- Custom Portal CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/portal.css') }}">
    @stack('styles')

    <style>
        /* CSS to ensure side panel displays correctly */
        .app-sidepanel {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            z-index: 1050;
            overflow-y: auto;
            transition: transform 0.3s ease;
            transform: translateX(-100%);
        }

        .app-sidepanel.show {
            display: block;
            transform: translateX(0); /* Slide in from the left */
        }

        /* Overlay to close side panel on mobile */
        .sidepanel-drop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .app-sidepanel.show + .sidepanel-drop {
            display: block;
        }
    </style>
</head>

<body class="app">

    <!-- Header -->
    <header class="app-header fixed-top">
        <div class="app-header-inner">
            <div class="container-fluid py-2">
                <div class="app-header-content">
                    <!-- Include Navbar -->
                    @include('admin.partials.navbar')
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div id="app-sidepanel" class="app-sidepanel">
            <div id="sidepanel-drop" class="sidepanel-drop"></div>
            @include('admin.partials.sidebar')
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Script for Side Panel Toggle -->
    <script>
        $(document).ready(function() {
            $('#sidepanel-toggler').click(function(e) {
                e.preventDefault();
                $('#app-sidepanel').toggleClass('show');
                $('#sidepanel-drop').toggleClass('show');
            });

            // Close side panel when clicking outside on the overlay
            $('#sidepanel-drop').click(function() {
                $('#app-sidepanel').removeClass('show');
                $(this).removeClass('show');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
