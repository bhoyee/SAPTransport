<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lock Screen</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
        }

        .lock-screen-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .image-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .car-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .lock-screen-container {
                flex-direction: column;
            }

            .image-container {
                margin-top: 20px;
            }
        }

        .spinner-border {
            display: none; /* Hide spinner initially */
        }
    </style>
</head>

<body>
    <div class="container lock-screen-container">
        <div class="row w-100">
            <!-- Left side: form -->
            <div class="col-md-6 form-container">
                <h2 class="text-center">Screen Locked</h2>
                <p class="text-center">Hey! Unlock Your Screen.</p>
                <form method="POST" action="{{ route('lockscreen.unlock') }}" id="unlock-form">
                    @csrf
                    <div class="form-group">
                        <label for="password">Enter your password to unlock</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        @if($errors->any())
                            <div class="text-danger mt-2">{{ $errors->first() }}</div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="unlock-btn">
                        Unlock
                        <span class="spinner-border spinner-border-sm" role="status" id="spinner" aria-hidden="true"></span>
                    </button>
                </form>

                <!-- Google login button for social login users -->
                <p class="text-center mt-3">OR</p>
                <a href="{{ route('auth.google.unlock') }}" class="btn btn-danger btn-lg btn-block">Unlock with Google</a>

                <p class="text-center mt-3">Back to <a href="{{ route('login') }}">Login</a></p>
            </div>

            <!-- Right side: car image -->
            <div class="col-md-6 image-container">
                <img src="{{ asset('img/p22.png') }}" alt="Car Image" class="car-image">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const lockScreenTimeout = 60000; // 1 minute (in milliseconds)
        const logoutTimeout = 120000; // 2 minutes (total session expiration time)

        let lockTimer;
        let logoutTimer;

        function redirectToHomePage() {
            console.log("Session expired. Redirecting to home...");
            window.location.href = "{{ route('home') }}"; // Redirect to the home page
        }

        function startLockScreenTimer() {
            console.log("Starting lock screen timer...");
            lockTimer = setTimeout(function () {
                console.log("Redirecting to lock screen...");
                window.location.href = "{{ route('lockscreen.show') }}"; // Redirect to lock screen
            }, lockScreenTimeout);
        }

        function startLogoutTimer() {
            console.log("Starting logout timer...");
            logoutTimer = setTimeout(redirectToHomePage, logoutTimeout); // Redirect to home page on session expiration
        }

        function resetTimers() {
            clearTimeout(lockTimer);
            clearTimeout(logoutTimer);
            startLockScreenTimer();
            startLogoutTimer();
        }

        // Reset timers on any user activity
        function attachUserActivityListeners() {
            ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach((event) => {
                window.addEventListener(event, resetTimers);
            });
        }

        function checkSessionStatus() {
            fetch('/check-session', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.active) {
                        console.log("Session expired on the server. Redirecting to home...");
                        redirectToHomePage(); // Redirect to home page when session is inactive
                    }
                })
                .catch((error) => console.error('Error checking session status:', error));
        }

        // Periodically validate session status on the lock screen
        if (window.location.pathname.includes('lockscreen')) {
            console.log("Lockscreen detected. Checking session status periodically...");
            setInterval(checkSessionStatus, 30000); // Check every 30 seconds
        }

        // Initialize timers and activity listeners
        resetTimers();
        attachUserActivityListeners();
    });
</script>

</body>

</html>
