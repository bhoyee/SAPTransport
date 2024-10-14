<!DOCTYPE html>
<html>
<head>
    <title>Your Account Has Been Created</title>
    <style>
        .button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>Hello {{ $userName }},</h2>

    <p>Your SAP Transport account has been successfully created. You can log in using the following credentials:</p>

    <p><strong>Email:</strong> {{ $userEmail }}</p>
    <p><strong>Password:</strong> {{ $userPassword }}</p>

    <p>Please remember to change your password after logging in.</p>

    <!-- Call to action button -->
    <p>
        <a href="{{ url('/login') }}" class="button">Login</a>
    </p>

    <p>Thank you for registering with us!</p>

    <p>Best Regards,<br>SAPTransport Team</p>
</body>
</html>
