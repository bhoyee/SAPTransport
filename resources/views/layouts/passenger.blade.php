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
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Ensures the columns have equal distribution */
    }

    th,
    td {
        padding: 4px 8px; /* Default padding */
        text-align: left;  /* Left-align text */
        white-space: nowrap; /* Prevent wrapping */
    }

    th {
        font-size: 14px; /* Reduce the font size for the table header */
        font-weight: bold;
        white-space: nowrap; /* Ensure headers don't wrap */
        text-align: left; /* Align headers in a straight line */
    }

    tr {
        border-bottom: 1px solid #ddd; /* Apply only row borders */
    }

    /* Adjust the width of each column */
    th:nth-child(1), td:nth-child(1) {
        width: 15%; /* Booking Ref */
    }

    th:nth-child(2), td:nth-child(2) {
        width: 15%; /* Payment Date */
    }

    th:nth-child(3), td:nth-child(3) {
        width: 25%; /* Amount Paid */
        padding-left: 20px; /* Add space to the left of Amount Paid */
    }

    th:nth-child(4), td:nth-child(4) {
        width: 15%; /* Status */
    }

    /* Button group with alignment to the right edge */
    td.button-group {
        width: 30%; /* Adjust button group width */
        text-align: right; /* Push buttons to the right */
        padding-right: 0;  /* Remove any right padding to align to the edge */
    }

    /* Smaller buttons */
    .button-group a {
        display: inline-block; /* Ensure buttons are inline */
        margin-right: 3px; /* Small space between buttons */
        padding: 4px 8px; /* Reduced padding for smaller buttons */
        border-radius: 4px; /* Smaller rounded corners */
        text-align: center;
        font-size: 12px; /* Smaller font size */
        font-weight: bold;
        color: white;
        text-decoration: none;
    }

    /* Button colors */
    .btn-warning {
        background-color: #ffc107;
        color: black;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    /* Mobile-specific styling */
    @media screen and (max-width: 600px) {
        table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block;
        }

        thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        tr {
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        td {
            border: none;
            position: relative;
            padding-left: 50%;
        }

        td:before {
            position: absolute;
            left: 6px;
            content: attr(data-label);
            font-weight: bold;
        }

        /* On mobile, ensure buttons are aligned properly */
        .button-group {
            text-align: left;
            display: flex;         /* Enable flexbox for the buttons */
            flex-wrap: wrap;        /* Allow buttons to wrap */
            gap: 5px;               /* Gap between buttons */
        }

        /* Ensure two buttons per line */
        .button-group a {
            flex: 1 1 calc(50% - 10px); /* Take 50% of the width, with gap accounted */
            margin-bottom: 5px; /* Small space between rows */
        }
    }
</style>





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

    <!-- DataTables JS -->
    <!-- <script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.js"></script> -->

    <!-- <script>
        // Initialize the DataTable without search, pagination, and length change
        new DataTable('#example', {
            responsive: true,
            searching: false, // Disable search bar
            paging: false,
                // Disable pagination
            lengthChange: false, // Disable the entries per page dropdown
            bInfo : false,
            fixedHeader: {
                header: true,
                footer: true
            }
        });
    </script> -->

    <script src="{{ asset('assets/plugins/popper.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>  

    <script src="{{ asset('assets/plugins/chart.js/chart.min.js') }}"></script> 

    <script src="{{ asset('assets/js/index-charts.js') }}"></script> 

    <script src="{{ asset('assets/js/app.js') }}"></script> 

    @stack('scripts') <!-- For additional page-specific JS -->

</body>

</html>

