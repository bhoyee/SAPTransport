<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </style>
</head>

<body>
    <div class="container lock-screen-container">
        <div class="row w-100">
            <!-- Left side: form -->
            <div class="col-md-6 form-container">
                <h2>Screen Locked</h2>
                <span>Hey! Unlock Your Screen.</span>
                <form method="POST" action="{{ route('lockscreen.unlock') }}">
                    @csrf
                    <div class="form-group mt-5">
                        <label for="password">Enter your password to unlock</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        @if($errors->any())
                            <div class="text-danger mt-2">{{ $errors->first() }}</div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Unlock</button>
                </form>
                
                <!-- Google login button for social login users -->
                <p class="text-center mt-3">OR</p>
                <a href="{{ route('auth.google.unlock') }}" class="btn btn-danger btn-lg btn-block">
                    Unlock with Google
                </a>

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
</body>

</html>
