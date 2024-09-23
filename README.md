# GidiTransport - Web-Based Transport Booking System

## Project Overview

**GidiTransport** is a web-based platform designed to provide easy and efficient booking services for various transport options, such as airport pickups, drop-offs, charters, and haulage services. Targeted specifically for Lagos, Nigeria, GidiTransport allows users to book rides, track bookings, manage payments, and customize their experiences via a user-friendly dashboard.

The application caters to both passengers and operators, with features designed to simplify the booking process and ensure a seamless experience.

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Features](#features)
3. [Technology Stack](#technology-stack)
4. [Setup & Installation](#setup--installation)
5. [Usage](#usage)
6. [API Endpoints](#api-endpoints)
7. [License](#license)
8. [Contributing](#contributing)

---

## Features

- **User Authentication**: Secure login and signup functionality for passengers.
- **Transport Bookings**: Book transport services for airport pickups, charters, and more.
- **Payment System**: Track and manage payments with a detailed payment history and real-time updates.
- **User Dashboard**: View trip summaries, track recent bookings, manage upcoming trips, and view cancellations.
- **Support & Help**: Contact customer support through a simple and effective communication channel.
- **Customizable Settings**: Manage personal settings for a personalized experience.

---

## Technology Stack

The project uses a combination of modern web technologies to provide a scalable and user-friendly application:

### Frontend:
- **HTML5, CSS3**: For the structure and styling of the web pages.
- **JavaScript**: Interactivity and real-time updates using JavaScript and AJAX.
- **Bootstrap 5**: For responsive design and layout, ensuring mobile and desktop compatibility.

### Backend:
- **PHP (Laravel 8)**: The core framework for handling backend logic and RESTful APIs.
- **MySQL**: Database management for storing user information, booking data, and payment records.
- **AJAX**: For asynchronous data fetching and real-time updates without page reloads.

### Tools & Services:
- **Git**: For version control and collaboration.
- **GitHub**: Repository hosting and project tracking.
- **Composer**: PHP package manager for managing dependencies.

---

## Setup & Installation

### Prerequisites

Ensure you have the following installed before setting up the project:
- PHP 8.x
- Composer
- MySQL
- Node.js & NPM

### Install Dependencies

Use Composer to install PHP dependencies and NPM for frontend dependencies:

```bash
composer install
npm install
```

### Set Up Environment Variables

Copy `.env.example` to `.env` and update the necessary database and app credentials:

```bash
cp .env.example .env
php artisan key:generate
```

Update the `.env` file with your database credentials and app URL.

### Run Migrations

Set up the database schema using Laravel's migrations:

```bash
php artisan migrate
```

### Start the Application

Run the local server:

```bash
php artisan serve
```

### Compile Assets

If using Laravel Mix for asset compilation (JS/CSS), run:

```bash
npm run dev
```

Your project should now be running on [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## Usage

### Passenger Dashboard

Once logged in, passengers can:
- Book new trips
- View recent and upcoming bookings
- Track payment history
- Customize personal settings

### Admin Dashboard

For operators and admins:
- Manage bookings across multiple services
- View real-time payment and booking status
- Access support requests and customer inquiries

---

## API Endpoints

The following are key API endpoints in the project. These can be accessed by technical users for integration purposes.

### Login

`POST /api/login`  
Description: Allows users to authenticate and log in to the system.

### Booking a Trip

`POST /api/book`  
Description: Creates a new booking for the logged-in user.

### Fetch Payment History

`GET /api/payment-history`  
Description: Fetches all payments made by the logged-in user.

### Cancel Booking

`POST /api/booking/cancel`  
Description: Allows the user to cancel an upcoming booking.

For a complete list of API endpoints and their descriptions, visit the API documentation (if available).

---

## License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## Contributing

We welcome contributions from the community to improve the project. Here’s how you can contribute:

1. Fork the repository.
2. Create a new branch for your feature or bug fix:

```bash
git checkout -b feature-branch
```

3. Commit your changes and push to your fork:

```bash
git commit -m "Add your message here"
git push origin feature-branch
```

4. Open a pull request to the main repository.

For major changes, please open an issue first to discuss what you would like to change.

---

## Contact

For support, inquiries, or collaboration opportunities, reach out to us at: support@giditransport.com
