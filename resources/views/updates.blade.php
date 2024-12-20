<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Dashboard</title>
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
            Last Updated: September 19, 2024
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
                                    <li>A fully functional platform that allows users to book airport pickups/drop-offs, charter services, and haulage services.</li>
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
                                    <li>Login API <span class="status completed">Completed</span></li>
                                    <li>Registration API <span class="status completed">Completed</span></li>
                                    <li>Forgot Password API <span class="status completed">Completed</span></li>
                                     <li>Google API signIn/ SignUp <span class="status completed">Completed</span></li>
                                       <li>Email verification <span class="status completed">Completed</span></li>
                                      <li>Booking API (Airport transfer, chater) <span class="status completed">Completed</span></li>
                                       <li>Booking status on home page <span class="status completed">Completed</span></li>
                                       <li>Booking Comfirmation email <span class="status completed">Completed</span></li>
                                          <li>User Activities integration <span class="status completed">Completed</span></li>
                                        <li>Passenger Dashboard <span class="status in-progress">In Progress</span></li>
                                       
                                    <!--<li>Payment Gateway Integration <span class="status in-progress">In Progress</span></li>-->
                                    <li>Passenger Managing booking <span class="status pending">Pending</span></li>
                                </ul>
                            </li>
                            <li class="list-group-item">
                                <span class="task">Frontend Integration:</span>
                                <ul>
                                     <li>Platform Website <span class="status completed">Completed</span></li>
                                        <li>Login, Register UI <span class="status completed">Completed</span></li>
                                          <li>Forgot Password  UI <span class="status completed">Completed</span></li>
                                    <li>Passenger Dashboard UI <span class="status completed">Completed</span></li>
                                      <li>Booking Form Integration <span class="status completed">Completed</span></li>
                                    <li>Passenger Profile Update Form <span class="status in-progress">In Progress</span></li>
                                    <li>Payment Form Integration <span class="status pending">Pending</span></li>
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
                                    <li>Booking Confirmation Page <span class="status pending">Pending</span></li>
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

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
