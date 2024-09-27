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

Route::get('/lock', [LockScreenController::class, 'show'])->name('lockscreen.show');

Route::post('/unlock', [LockScreenController::class, 'unlock'])->name('lockscreen.unlock');



// Airport Transfer Booking Route

Route::post('/book-airport-transfer', [BookingController::class, 'store'])->name('booking.store');



// Clear Cache Route (for admin use)

Route::get('/clear-cache', function() {

    Artisan::call('config:clear');

    Artisan::call('cache:clear');

    Artisan::call('route:clear');

    Artisan::call('view:clear');

    return "Caches cleared";

});





// Passenger Dashboard (protected by 'auth' and 'verified' middleware)

Route::middleware(['auth', 'verified'])->group(function () {

Route::get('/passenger/dashboard', [PassengerController::class, 'dashboard'])->name('passenger.dashboard'); });

    

Route::post('/check-booking-status', [BookingController::class, 'checkStatus'])->name('booking.check-status');



// Route::get('/passenger/recent-bookings', [BookingController::class, 'getRecentBookings'])->name('passenger.recent.bookings');


    // Route to fetch recent bookings
Route::get('/passenger/recent-bookings', [PassengerHomeController::class, 'getRecentBookings'])->name('passenger.recent.bookings');

Route::get('/passenger/dashboard-data', [PassengerHomeController::class, 'fetchDashboardData'])->name('passenger.dashboard.data');

   
Route::get('/passenger/payment-history', [PaymentController::class, 'getPaymentHistory']);

// Route for cancelling a booking
Route::post('/booking/cancel/{id}', [BookingController::class, 'cancelBooking'])->middleware('auth');



// Route for editing a booking
Route::get('/booking/{id}/edit', [BookingEditController::class, 'edit'])->name('booking.edit');

// Route for updating the booking
Route::put('/booking/{id}', [BookingEditController::class, 'update'])->name('booking.update');

// Rote for viewing details of booking
Route::get('/booking/{id}/view', [BookingEditController::class, 'show'])->name('booking.view');


// route to the payment page and the "Pay Now" action
Route::get('/passenger/makepayments', [PaymentController::class, 'unpaidPayments'])->name('passenger.makepayments');
Route::get('/payment/{id}/pay', [PaymentController::class, 'pay'])->name('payment.pay'); // You can handle the payment logic in the 'pay' method.

