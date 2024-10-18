<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success text-center">
            <h1>Payment Successful!</h1>
            <p>Your payment was successfully processed.</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Go to Home</a>
        </div>
    </div>
</body>
</html>
