<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Dashboard with Countdown Timer</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Custom styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
        }
        .container {
            margin-top: 30px;
        }
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #343a40;
            text-align: center;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 20px;
        }
        .list-group-item {
            display: block;
            padding: 10px 15px;
            font-size: 1rem;
            background-color: #f8f9fa;
        }
        .task {
            font-weight: bold;
            color: #495057;
        }
        .status {
            font-weight: bold;
            text-transform: capitalize;
            margin-left: 15px;
        }
        .completed {
            color: #28a745;
        }
        .in-progress {
            color: #ffc107;
        }
        .pending {
            color: #dc3545;
        }
        /* Styling for "New" badge */
        .new-badge {
            background-color: #28a745;
            color: #ffffff;
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 10px;
        }
        /* Styling for "Maintenance" badge */
        .maintenance-badge {
            background-color: #dc3545;
            color: #ffffff;
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 10px;
        }
        /* Flexbox Header Styling */
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .timeline {
            font-size: 1rem;
            color: #6c757d;
            text-align: right;
        }
        /* Last Updated Date */
        .last-updated {
            text-align: center;
            font-size: 2rem;
            color: #343a40;
            font-weight: bold;
            margin-bottom: 20px;
        }
        /* Countdown Timer Styling */
        .countdown {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #dc3545; /* Bold red color for the countdown */
            margin-bottom: 30px;
        }
        /* Mobile Responsiveness */
        @media (max-width: 575.98px) {
            .section-title {
                font-size: 1.5rem;
            }
            .dashboard-header {
                flex-direction: column;
            }
            .timeline {
                text-align: center;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Last Updated Date -->
        <div class="last-updated">
            Last Updated: September 25, 2024
        </div>
        <!-- Countdown Timer -->
        <div class="countdown" id="countdown-timer">
            Countdown: 00d 00h 00m 00s
        </div>

        <!-- Header and Project Timeline -->
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="dashboard-header">
                    <h1>Project Dashboard</h1>
                    <div class="timeline">
                        <strong>Project Timeline:</strong> <br>
                        Initiation: Week 1 | Requirements: Week 2-3 | Design: Week 4-5 <br>
                        Development: Week 6-18 | Testing: Week 19-22 | Deployment: Week 23-24
                    </div>
                </div>
            </div>
        </div>

        <!-- Deliverables Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Deliverables</h2>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="task">Web-based Transport Booking Platform:</span>
                                <ul>
                                    <li>A fully functional platform that allows users to book airport pickups/drop-offs and charter services</li>
                                    <li>Accessible to passengers, admins, and consultants, each with appropriate functionalities.</li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">User Features (Passenger Area):</span>
                                <ul>
                                    <li>Booking Options: Book Airport Pickup/Drop-off, Book Charter Trip</li>
                                    <li>Booking Management: View invoices/receipts, Manage bookings, Manage payments</li>
                                    <li>Profile and Security: Manage profile and change password</li>
                                    <li>Support: Open support tickets, Send messages to support</li>
                                    <li>Dashboard: Overview of bookings, payments, trip history, and booking status</li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Admin Features:</span>
                                <ul>
                                    <li>User Management: Create, manage, and delete staff users, Manage passengers</li>
                                    <li>Dashboard: Display booking and sales data</li>
                                    <li>Booking Management: Confirm, view, edit, and delete bookings</li>
                                    <li>Payment Management: View, process payments, manage receipts/invoices</li>
                                    <li>Messaging: Send, receive, and delete messages</li>
                                    <li>Security: Change password functionality</li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Consultant Features:</span>
                                <ul>
                                    <li>Dashboard: View reports and booking data analysis</li>
                                    <li>Booking Management: Confirm, view, and edit bookings</li>
                                    <li>User Management: View and edit passenger details</li>
                                    <li>Payment Management: View and process payments</li>
                                    <li>Messaging: Send and receive messages</li>
                                    <li>Security: Change password functionality</li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">General Site Features:</span>
                                <ul>
                                    <li>Booking options, Service descriptions, About the company, Contact information, and User authentication</li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Documentation:</span>
                                <ul>
                                    <li>User manuals and deployment guides for all roles (passenger, admin, consultant)</li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Source Code:</span>
                                <ul>
                                    <li>Full source code with all related files provided to the client</li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Deployment:</span>
                                <ul>
                                    <li>A staging and production environment setup for testing and final release</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Log Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Daily Log</h2>
                        <ul class="list-group">
                            <li class="list-group-item">
                                Email verification entering spam folder
                                <span class="maintenance-badge">Maintenance</span>
                                <span class="status">25/09/2024 at 06:45</span>
                            </li>

                            <li class="list-group-item">
                               Recent Booking and Payment History tables on Passanger Dashboad responsiveness fixed
                                <span class="new-badge">New</span>
                                <span class="status">25/09/2024 at 11:30</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Status Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Current Status</h2>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="task">Backend API Development:</span>
                                <ul>
                                    <li>Login API <span class="status completed">Completed</span><span class="new-badge">New</span></li>
                                    <li>Registration API <span class="status completed">Completed</span><span class="new-badge">New</span></li>
                                    <li>Payment Gateway Integration <span class="status in-progress">In Progress</span></li>
                                    <!-- <li>Booking API <span class="status pending">Pending</span></li> -->
                                    <li>Booking API <span class="status completed">Completed</span><span class="new-badge">New</span></li>

                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Frontend Integration:</span>
                                <ul>
                                    <li>Passenger Dashboard UI <span class="status completed">Completed</span></li>
                                    <li>PassengerBooking Form Integration <span class="status completed">Completed</span></li>
                                    <li>Booking Confirmation Page <span class="status completed">Completed</span></li>

                                    <li>Passenger: Payment Form Integration  <span class="status in-progress">In Progress</span></li>
                                    <li>Passenger: My Trip / Bookings <span class="status pending">Pending</span></li>
                                    <li>Passenger: Invoices / Receipts<span class="status pending">Pending</span></li>
                                    <li>Passenger: Support / Ticket<span class="status pending">Pending</span></li>
                                    <li>Passenger: Help<span class="status pending">Pending</span></li>
                                    <li>Passenger: Settings<span class="status pending">Pending</span></li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Testing and Bug Fixing:</span>
                                <ul>
                                    <li>Unit Testing <span class="status in-progress">In Progress</span></li>
                                    <li>End-to-End Testing <span class="status pending">Pending</span></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Actions Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Next Actions</h2>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="task">Complete Frontend Integration:</span>
                                <ul>
                                    <li>Payment Form Integration <span class="status pending">Pending</span></li>
                                    <li>Admin Portal <span class="status pending">Pending</span></li>
                                    <li>Staff Portal <span class="status pending">Pending</span></li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Finalize API Documentation:</span>
                                <ul>
                                    <li>API Endpoint Documentation <span class="status pending">Pending</span></li>
                                    <li>Developer Manual <span class="status pending">Pending</span></li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Perform End-to-End Testing:</span>
                                <ul>
                                    <li>User Journey Testing <span class="status pending">Pending</span></li>
                                    <li>Load Testing <span class="status pending">Pending</span></li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Deploy to Staging Server:</span>
                                <ul>
                                    <li>Setup Staging Environment <span class="status pending">Pending</span></li>
                                    <li>Data Migration <span class="status pending">Pending</span></li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Prepare User Training Materials:</span>
                                <ul>
                                    <li>User Manual <span class="status pending">Pending</span></li>
                                    <li>Admin Training Guide <span class="status pending">Pending</span></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- Countdown Timer JavaScript -->
<script>
        function startCountdown() {
            // Set the start date to 15 days ago
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 15);

            // Set the end date to 3 months from the start date
            const endDate = new Date(startDate);
            endDate.setMonth(startDate.getMonth() + 3);

            // Function to update the countdown
            function updateCountdown() {
                const now = new Date().getTime();
                const timeLeft = endDate - now;

                // Calculate time left
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                // Display the countdown
                document.getElementById("countdown-timer").innerHTML = `Countdown: ${days}d ${hours}h ${minutes}m ${seconds}s`;

                // If the countdown is over, display EXPIRED
                if (timeLeft < 0) {
                    clearInterval(timerInterval); // Stop the countdown
                    document.getElementById("countdown-timer").innerHTML = "Countdown: EXPIRED";
                }
            }

            // Update countdown every second
            const timerInterval = setInterval(updateCountdown, 1000);

            // Run the function once immediately
            updateCountdown();
        }

        // Start the countdown when the page loads
        window.onload = startCountdown;
    </script>

</body>
</html>