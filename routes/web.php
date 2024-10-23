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
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\SupportController as StaffSupportController;
use App\Http\Controllers\Admin\AdminBookingReportController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\WalkinPayController;
use App\Http\Controllers\Admin\PaymentReportController;
use App\Http\Controllers\Admin\UserPaymentReportController;




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

//check booking status
Route::post('/check-booking-status', [BookingController::class, 'checkStatus']);


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
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.resend');
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

// Fallback Route
Route::fallback(function () {
    return redirect()->route('login')->with('message', 'Session expired or page expired. Please log in again.');
});

// Passenger Dashboard Routes (Protected by Role)
Route::middleware(['auth', 'role:passenger'])->group(function () {
    Route::get('/passenger/dashboard', [PassengerController::class, 'dashboard'])->name('passenger.dashboard');
    Route::get('/passenger/recent-bookings', [PassengerHomeController::class, 'getRecentBookings'])->name('passenger.recent.bookings');
    Route::get('/passenger/payment-history', [PassengerHomeController::class, 'getPaymentHistory']);
    Route::get('/passenger/dashboard-data', [PassengerHomeController::class, 'fetchDashboardData'])->name('passenger.dashboard.data');
    Route::get('/passenger/makepayments', [InvoiceController::class, 'unpaidPayments'])->name('passenger.makepayments');
    Route::get('/passenger/invoice/{id}', [InvoiceController::class, 'showInvoice'])->name('passenger.invoice');
    

    
    Route::get('/passenger/invoice/download/{id}', [InvoiceController::class, 'downloadInvoice'])->name('passenger.downloadInvoice');
    Route::get('/passenger/bookings/chart-data', [PassengerHomeController::class, 'getChartData'])->name('passenger.bookings.chartData');
    Route::get('/passenger/activities', [PassengerHomeController::class, 'getUserActivities'])->name('passenger.activities');
});

// Admin Routes (Protected by Spatie's Role Middleware)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'delete'])->name('admin.users.delete');
    Route::post('users/report/pdf', [UserReportController::class, 'generatePDF'])->name('admin.users.report.pdf');
    Route::get('users/report', [UserReportController::class, 'showReportPage'])->name('admin.users.report');
    Route::get('users/fetch-stats', [UserReportController::class, 'fetchStats'])->name('admin.users.fetch-stats');

    //admin delete user page 
    Route::get('users/deleted-users', [AdminUserController::class, 'showDeletedUsers'])->name('admin.users.deleted-users'); // To display the page
    // Route::get('users/deleted', [AdminUserController::class, 'showDeletedUsers'])->name('admin.users.deleted'); // To display the page
    Route::get('users/fetch-deleted-stats', [AdminUserController::class, 'fetchDeletedStats'])->name('admin.users.fetch-deleted-stats'); // For the card
    Route::get('users/deleted-list', [AdminUserController::class, 'getDeletedUsers'])->name('admin.users.deleted-list'); // For the DataTable
    Route::post('users/permanent-delete', [AdminUserController::class, 'permanentDelete'])->name('admin.users.permanent-delete'); // For permanent deletion


    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show'); // Add this route

    // Admin routes for managing bookings / Admin Booking Routes
    Route::get('/manage-bookings', [AdminBookingController::class, 'manageBookings'])->name('admin.bookings.manage');
    Route::post('/bookings/update-status/{id}', [AdminBookingController::class, 'updateBookingStatus'])->name('admin.bookings.updateStatus');

    Route::post('/bookings/{id}/complete', [AdminBookingController::class, 'completeBooking'])->name('admin.bookings.complete');

    Route::get('/bookings/{id}/view', [AdminBookingController::class, 'viewBooking'])->name('admin.bookings.view');
    Route::get('/bookings/{id}/edit', [AdminBookingController::class, 'editBooking'])->name('admin.bookings.edit');
    Route::put('/admin/bookings/{id}', [AdminBookingController::class, 'updateBooking'])->name('admin.bookings.update');
    Route::post('/bookings/{id}/cancel', [AdminBookingController::class, 'cancelBooking'])->name('admin.bookings.cancel');
    Route::delete('/bookings/{id}/delete', [AdminBookingController::class, 'deleteBooking'])->name('admin.bookings.delete');
    Route::post('/bookings/{id}/update-status', [BookingController::class, 'updateBookingStatus'])->name('admin.bookings.updateStatus');

    // Route to load the search page where the admin enters the booking reference
    Route::get('/admin/bookings/confirm', [AdminBookingController::class, 'searchBooking'])->name('admin.bookings.confirm-search');

    // Route to handle the search and display the booking info if found
    Route::post('/admin/bookings/confirm-search', [AdminBookingController::class, 'searchBooking'])->name('admin.bookings.confirm-search-post');

    // Route to confirm a booking (requires the booking ID)
    Route::post('/admin/bookings/{id}/confirm', [AdminBookingController::class, 'confirmBooking'])->name('admin.bookings.confirm');

    //admin booking report routes 
    Route::get('/bookings/report', [AdminBookingReportController::class, 'index'])->name('admin.bookings.report');
    Route::post('/bookings/report/pdf', [AdminBookingReportController::class, 'generatePdf'])->name('admin.bookings.report.pdf');
    // Route::get('/bookings/report-data/{range}', [AdminBookingReportController::class, 'getReportData']);

    Route::get('/bookings/report-data/{range}', [AdminBookingReportController::class, 'getReportData'])->name('admin.bookings.report.data');
    Route::post('/admin/bookings/report/pdf', [AdminBookingReportController::class, 'generatePdf'])->name('admin.bookings.report.pdf');


    Route::get('/payment/search', [AdminPaymentController::class, 'searchBooking'])->name('admin.payment.search');
    Route::post('/pay', [AdminPaymentController::class, 'pay'])->name('admin.payment.pay');

    Route::get('/invoice/paid/{invoice}', [AdminPaymentController::class, 'paidInvoice'])->name('admin.invoice.paid');
    Route::get('/invoice/failed', [AdminPaymentController::class, 'failedInvoice'])->name('admin.invoice.failed');
    
    //manage payment route
    // Route::get('/payments', [AdminPaymentController::class, 'managePayments'])->name('admin.payments.index');
    // Route::post('/payments/{id}/refund', [AdminPaymentController::class, 'processRefund'])->name('admin.payment.refund');

 // Manage payment route
 Route::get('/payments', [AdminPaymentController::class, 'managePayments'])->name('admin.payments.index');
    
 // Process refund route
 Route::post('/payments/{id}/refund', [AdminPaymentController::class, 'processRefund'])->name('admin.payment.refund');
 
 // Decline refund route
 Route::post('/payments/{id}/refund/decline', [AdminPaymentController::class, 'declineRefund'])->name('admin.payment.refund.decline');

 //cash payment routes
 //Route::get('/payments/cash', [AdminPaymentController::class, 'showCashPaymentForm'])->name('admin.payment.cash');
 Route::post('/payments/cash/update', [AdminPaymentController::class, 'recordCashPayment'])->name('admin.payment.cash.update');
 Route::get('/payment/cash', [AdminPaymentController::class, 'showCashPaymentForm'])->name('admin.payment.cash');
 Route::post('/payments/{id}/refund/cash', [AdminPaymentController::class, 'refundCash'])->name('admin.payments.refund.cash');


  // Show the payment report page
  Route::get('/payments/report', [PaymentReportController::class, 'showPaymentReportPage'])->name('admin.payments.report');

  // Fetch report data for cards (based on daily, weekly, monthly, yearly)
  Route::get('/payments/report-data/{timeframe}', [PaymentReportController::class, 'fetchReportData'])->name('admin.payments.report.data');

  // Generate PDF for payments report
  Route::post('/payments/report/pdf', [PaymentReportController::class, 'generatePdf'])->name('admin.payments.report.pdf');


  // Routes for User Payment Report
// Route::get('/reports/user-payment-report', [UserPaymentReportController::class, 'index'])->name('admin.reports.user-payment');
Route::post('/reports/user-payment-report/pdf', [UserPaymentReportController::class, 'generateUserPaymentReport'])->name('admin.reports.user-payment.pdf');
Route::get('/reports/user-payment-report', [UserPaymentReportController::class, 'index'])->name('admin.reports.userPaymentReport');

});


 Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback'])->name('payment.callback');





// Staff Routes (Protected by Role Middleware)
Route::prefix('staff')->middleware(['auth', 'role:consultant'])->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/support', [StaffSupportController::class, 'index'])->name('staff.support');
});


// Notification Routes (Accessible to Authenticated Users)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/recent', [NotificationController::class, 'fetchRecentNotifications'])->name('notifications.recent');
    Route::get('/notifications', [NotificationController::class, 'viewAll'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});


// Admin Dashboard Route without Middleware (Redirect if not Admin)
Route::get('/admin/dashboard', function () {
    if (Auth::check() && Auth::user()->hasRole('admin')) {
        return view('admin.dashboard');
    }
    return redirect('/login')->with('error', 'Unauthorized access');
})->name('admin.dashboard')->middleware('auth');

// Contact Form Submission
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Screen Lock Routes (Optional)

Route::middleware(['auth'])->group(function () {
    Route::get('/lock', [LockScreenController::class, 'show'])->name('lockscreen.show');
    Route::post('/unlock', [LockScreenController::class, 'unlock'])->name('lockscreen.unlock');
    Route::get('/auth/google/unlock', [LockScreenController::class, 'handleGoogleUnlock'])->name('auth.google.unlock');
});

// Add this route in your web.php
Route::get('/check-session', [LockScreenController::class, 'checkSessionStatus']);



Route::post('/update-last-activity', function () {
    session()->put('lastActivityTime', now()->timestamp);
    return response()->json(['status' => 'success']);
})->middleware('auth');


// Passenger Routes (for managing settings, account, and tickets)
Route::middleware(['auth', 'role:passenger'])->group(function () {
    Route::get('/passenger/settings', [SettingsController::class, 'showSettingsPage'])->name('passenger.settings');
    Route::post('/passenger/change-password', [SettingsController::class, 'changePassword'])->name('passenger.change-password');

    Route::get('/passenger/account', [AccountController::class, 'showAccountPage'])->name('passenger.account');
    Route::post('/passenger/account', [AccountController::class, 'updateAccount'])->name('passenger.update-account');

    Route::get('/support/ticket', [ContactController::class, 'createTicketForm'])->name('passenger.open-ticket');
    Route::post('/support/ticket', [ContactController::class, 'storeTicket'])->name('contact.store');
    Route::get('/passenger/my-tickets', [ContactController::class, 'myTickets'])->name('passenger.my-tickets');
    Route::get('/passenger/my-tickets/{id}', [ContactController::class, 'viewTicket'])->name('viewTicket');
    Route::post('/passenger/my-tickets/{id}/reply', [ContactController::class, 'replyToTicket'])->name('replyTicket');
});

// Admin User Management Routes (Only for Admin Role)
    Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/delete', [AdminUserController::class, 'delete'])->name('admin.users.delete');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');

    // Admin Report Management
    Route::get('users/report', [UserReportController::class, 'showReportPage'])->name('admin.users.report');
    Route::post('users/report/pdf', [UserReportController::class, 'generatePDF'])->name('admin.users.report.pdf');
    
});

// Booking for Admin and Staff (Admins and Consultants)
Route::middleware(['auth', 'role:admin|consultant'])->group(function () {
    Route::get('/admin/book-for-someone', [AdminBookingController::class, 'showBookingForm'])->name('admin.bookForSomeone');
    Route::post('/admin/check-user', [AdminBookingController::class, 'checkUser']);
    Route::post('/admin/make-booking', [AdminBookingController::class, 'store']);
});



// Booking and Payment Routes (Both for Admins and Passengers)
Route::post('/book-airport-transfer', [BookingController::class, 'store'])->name('booking.store');

Route::prefix('passenger')->middleware(['auth', 'role:passenger'])->group(function () {

// Route::middleware(['auth', 'role:passenger'])->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');
    // Route::post('/booking/cancel/{id}', [BookingController::class, 'cancelBooking'])->middleware('auth');

    Route::post('/booking/cancel/{id}', [BookingController::class, 'cancelBooking']);

    Route::get('/booking/{id}/edit', [BookingEditController::class, 'edit'])->name('booking.edit');
    Route::put('/booking/{id}', [BookingEditController::class, 'update'])->name('booking.update');
    Route::get('/booking/{id}/view', [BookingEditController::class, 'show'])->name('booking.view');

    // Payment Routes
    Route::post('/pay', [PaymentController::class, 'pay'])->name('pay');
    // Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback'])->name('payment.callback');
    Route::get('/invoice/failed', [PaymentController::class, 'failedInvoice'])->name('invoice.failed');
    Route::get('/invoice/paid/{invoice}', [PaymentController::class, 'paidInvoice'])->name('invoice.paid');
    Route::get('/payments/history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    Route::post('/payments/refund', [PaymentController::class, 'requestRefund'])->name('payment.refund');

    // Invoice Routes
    Route::get('/invoice/view/{id}', [InvoiceController::class, 'view'])->name('invoice.view');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoice/pay/{id}', [InvoiceController::class, 'pay'])->name('invoice.pay');
    Route::post('/invoice/pay', [PaymentController::class, 'pay'])->name('invoice.pay');
});

// Clear Cache Route (for Admin Use)
Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Caches cleared";
});


Route::get('/pay', [WalkinPayController::class, 'search'])->name('payment.search');

Route::post('/walkinpay/process', [WalkinPayController::class, 'pay'])->name('payment.process');
// Route::get('/payment/callback', [WalkinPayController::class, 'handleGatewayCallback'])->name('payment.callback');
Route::get('/payment/success', function () {
    return view('walkinpay.success');
})->name('payment.success');
Route::get('/payment/failed', function () {
    return view('walkinpay.failed');
})->name('payment.failed');
