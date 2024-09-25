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

    

    <!-- FontAwesome JS-->

    <script defer src="{{ asset('assets/plugins/fontawesome/js/all.min.js') }}"></script>

    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App CSS -->  

    <link id="theme-style" rel="stylesheet" href="{{ asset('assets/css/portal.css') }}">
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    
    <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css"> -->
    




</head>


    @stack('styles') <!-- For additional page-specific CSS -->

</head> 



<body class="app">   	

    <header class="app-header fixed-top">	   	            

        <div class="app-header-inner">  

	        <div class="container-fluid py-2">

		        <div class="app-header-content"> 

		            <!-- Header Content -->

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

    

    <div class="app-wrapper">

	    <div class="app-content pt-3 p-md-3 p-lg-4">

			<div class="container-xl">

                @yield('content')

			</div>

	    </div><!--//app-content-->

	    

	    <footer class="app-footer">

		    <div class="container text-center py-3">

		        <small class="copyright">Designed by <a class="app-link" href="https://giddyhost.com" target="_blank">Giddy Host</a></small>

		    </div>

	    </footer>

    </div><!--//app-wrapper-->

    

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





    <!-- JavaScript -->

       <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

  

    <script src="{{ asset('assets/plugins/popper.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>  

    <script src="{{ asset('assets/plugins/chart.js/chart.min.js') }}"></script> 

    <script src="{{ asset('assets/js/index-charts.js') }}"></script> 

    <script src="{{ asset('assets/js/app.js') }}"></script> 

    @stack('scripts') <!-- For additional page-specific JS -->

</body>

</html>

