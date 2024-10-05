<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\PassengerController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\LockScreenController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PassengerHomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookingEditController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AccountController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // Alias for Admin DashboardController
use App\Http\Controllers\Admin\UserController as AdminUserController; // Alias for Admin UserController

use App\Http\Controllers\Staff\DashboardController as StaffDashboardController; // Alias for Staff DashboardController
use App\Http\Controllers\Staff\SupportController as StaffSupportController; // Alias for Staff SupportController







// Public Routes

Route::get('/', function () {

    return view('index');

});



Route::get('/update', function () {

    return view('update');

});



Route::get('/about', function () {

    return view('about');

});



Route::get('/faq', function () {

    return view('faq');

});



Route::get('/contact', function () {

    return view('contact');

});



// Registration Routes

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

Route::get('/register/thankyou', [RegisterController::class, 'thankyou'])->name('register.thankyou');



// Login Routes

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



// Email Verification Routes

Route::get('/email/verify', [VerificationController::class, 'show'])->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])

    ->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/resend', [VerificationController::class, 'resend'])

    ->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

Route::get('/email/verified-success', function () {

    return view('auth.verification-success');

})->name('verification.success');



// Social Login Routes

Route::get('/auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');

Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);



Route::get('/auth/facebook', [SocialLoginController::class, 'redirectToFacebook'])->name('auth.facebook');

Route::get('/auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);



// Profile Completion Route for Social Login

Route::get('/complete-profile', [ProfileController::class, 'showCompleteProfileForm'])->name('complete.profile');

Route::post('/complete-profile', [ProfileController::class, 'saveProfile']);



// Password Reset Routes

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');

Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

Route::get('/password/reset/success', function () {

    return view('auth.passwords.success');

})->name('password.reset.success');



// Screen Locker Routes

// Route::get('/lock', [LockScreenController::class, 'show'])->name('lockscreen.show');

// Route::post('/unlock', [LockScreenController::class, 'unlock'])->name('lockscreen.unlock');

// Screen Locker Routes
// Route::middleware(['auth'])->group(function () {
//     Route::get('/lock', [LockScreenController::class, 'show'])->name('lockscreen.show');
//     Route::post('/unlock', [LockScreenController::class, 'unlock'])->name('lockscreen.unlock');
    
//     // Route for unlocking via Google social login
//     Route::get('/unlock/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google.unlock');
//     Route::get('/unlock/google/callback', [LockScreenController::class, 'handleGoogleUnlock']);
// });


Route::fallback(function () {
    return redirect()->route('login')->with('message', 'Session expired or page expired. Please log in again.');
});


Route::get('/auth/google/unlock', [LockScreenController::class, 'handleGoogleUnlock'])->name('auth.google.unlock');

    

Route::post('/check-booking-status', [BookingController::class, 'checkStatus'])->name('booking.check-status');



// Routes for Ticket Management
Route::middleware(['auth'])->group(function () {
    // Route to create a ticket
    // Route::get('/passenger/support/ticket', [ContactController::class, 'createTicketForm'])->name('contact.create');
    // Route::post('/passenger/support/ticket', [ContactController::class, 'storeTicket'])->name('contact.store');


});

Route::get('/passenger/dashboard', [PassengerController::class, 'dashboard'])
    ->name('passenger.dashboard');
    
Route::get('/lock', [LockScreenController::class, 'show'])->name('lockscreen.show');
Route::post('/unlock', [LockScreenController::class, 'unlock'])->name('lockscreen.unlock');

Route::post('/lock-session', function () {
    session()->put('is_locked', true);
    return response()->json(['status' => 'locked']);
});


// Route::get('/about', 'Index')->name('about.page')->middleware('check');

// Route::get('/passenger/dashboard', [PassengerController::class, 'dashboard'])->name('passenger.dashboard');



Route::middleware(['auth', 'verified'])->group(function () {

// Route::middleware(['auth', 'verified'])->group(function () {

    // Airport Transfer Booking Route

    Route::post('/book-airport-transfer', [BookingController::class, 'store'])->name('booking.store');

    // Passenger Dashboard

    // Route::get('/passenger/dashboard', [PassengerController::class, 'dashboard'])->name('passenger.dashboard');

    // Route to fetch recent bookings
    Route::get('/passenger/recent-bookings', [PassengerHomeController::class, 'getRecentBookings'])->name('passenger.recent.bookings');

    Route::get('/passenger/dashboard-data', [PassengerHomeController::class, 'fetchDashboardData'])->name('passenger.dashboard.data');

    // Route to fetch recent Payment history
    Route::get('/passenger/payment-history', [PassengerHomeController::class, 'getPaymentHistory']);

    // Route for cancelling a booking
    Route::post('/booking/cancel/{id}', [BookingController::class, 'cancelBooking'])->middleware('auth');



    // Route for editing a booking
    Route::get('/booking/{id}/edit', [BookingEditController::class, 'edit'])->name('booking.edit');

    // Route for updating the booking
    Route::put('/booking/{id}', [BookingEditController::class, 'update'])->name('booking.update');

    // Rote for viewing details of booking
    Route::get('/booking/{id}/view', [BookingEditController::class, 'show'])->name('booking.view');


    // route to the payment page and the "Pay Now" action
    Route::get('/passenger/makepayments', [InvoiceController::class, 'unpaidPayments'])->name('passenger.makepayments');
    //Route::get('/payment/{id}/pay', [PaymentController::class, 'pay'])->name('payment.pay'); // You can handle the payment logic in the 'pay' method.

    // Route to display a specific invoice by ID
    Route::get('/passenger/invoice/{id}', [InvoiceController::class, 'showInvoice'])->name('passenger.invoice');

    // route to download unpaid invoice
    Route::get('/passenger/invoice/download/{id}', [InvoiceController::class, 'downloadInvoice'])->name('passenger.downloadInvoice');

    //paystack payment rouvte
    Route::get('/payments/unpaid', [PaymentController::class, 'unpaidPayments'])->name('payments.unpaid');

    // Route for payment initiation (already defined in your previous setup)
    Route::post('/pay', [PaymentController::class, 'pay'])->name('pay');

    Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback'])->name('payment.callback');

    // Route to handle the failed payment
    Route::get('/invoice/failed', [PaymentController::class, 'failedInvoice'])->name('invoice.failed');

    Route::get('/invoice/paid/{invoice}', [PaymentController::class, 'paidInvoice'])->name('invoice.paid');


    // Route to show payment history
    Route::get('/payments/history', [PaymentController::class, 'paymentHistory'])->name('payment.history');

    // Route to handle refund requests
    Route::post('/payments/refund', [PaymentController::class, 'requestRefund'])->name('payment.refund');


    // Route for viewing an invoice
    Route::get('/invoice/view/{id}', [InvoiceController::class, 'view'])->name('invoice.view');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoice/pay/{id}', [InvoiceController::class, 'pay'])->name('invoice.pay');

    Route::post('/invoice/pay', [PaymentController::class, 'pay'])->name('invoice.pay');

    // route for my booking
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');

    // Route for ticket
    Route::get('/support/ticket', [ContactController::class, 'createTicketForm'])->name('passenger.open-ticket');
    Route::post('/support/ticket', [ContactController::class, 'storeTicket'])->name('contact.store');

        // Route to view user's tickets
    Route::get('/passenger/my-tickets', [ContactController::class, 'myTickets'])->name('passenger.my-tickets');

        // Route to view a specific ticket
    Route::get('/passenger/my-tickets/{id}', [ContactController::class, 'viewTicket'])->name('viewTicket');
    
        // Route to reply to a specific ticket
    Route::post('/passenger/my-tickets/{id}/reply', [ContactController::class, 'replyToTicket'])->name('replyTicket');

    //Routes for settings
    Route::get('/passenger/settings', [SettingsController::class, 'showSettingsPage'])->name('passenger.settings');
    Route::post('/passenger/change-password', [SettingsController::class, 'changePassword'])->name('passenger.change-password');

// Routes for account management
    Route::get('/passenger/account', [AccountController::class, 'showAccountPage'])->name('passenger.account');
    Route::post('/passenger/account', [AccountController::class, 'updateAccount'])->name('passenger.update-account');

    // routes to display passenger chart and activities
    Route::get('/passenger/bookings/chart-data', [PassengerHomeController::class, 'getChartData'])->name('passenger.bookings.chartData');
    Route::get('/passenger/activities', [PassengerHomeController::class, 'getUserActivities'])->name('passenger.activities');
});


// Admin routes
// Admin routes
// Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
//     Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
//     // Add more admin routes here
// });

// // Staff routes
// Route::prefix('staff')->middleware(['auth', 'role:staff'])->group(function () {
//     Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
//     Route::get('/support', [StaffSupportController::class, 'index'])->name('staff.support');
//     // Add more staff routes here
// });


// Admin routes
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

// Staff (Consultant) routes
Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');



// Clear Cache Route (for admin use)

Route::get('/clear-cache', function() {

    Artisan::call('config:clear');

    Artisan::call('cache:clear');

    Artisan::call('route:clear');

    Artisan::call('view:clear');

    return "Caches cleared";

});


