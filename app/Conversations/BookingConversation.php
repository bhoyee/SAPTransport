<?php

namespace App\Conversations;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Services\ActivityLogger;
use App\Models\Notification;
use App\Mail\BookingCancellationAdminNotification;
use App\Mail\BookingCancellation;
use App\Models\Payment;
use App\Mail\AdminRefundRequestNotification;
use App\Mail\UserRefundRequestNotification;
use App\Mail\VerificationCodeEmail;


class BookingConversation extends Conversation
{
    protected $userData = [];
    protected $bookingData = [];

    public function run()
    {
        $this->showMainMenu();
    }

    public function showMainMenu()
    {
        $question = Question::create("Hi, I am Teresa from SAP Transport, your automated virtual assistant. Let me try to help you. Please select one of the services by clicking the appropriate button below:")
            ->addButtons([
                Button::create('Register')->value('register'),
                Button::create('Make a Booking')->value('make_booking'),
                Button::create('Check Booking Status')->value('check_booking_status'),
                Button::create('Cancel Booking')->value('cancel_booking'),
                Button::create('Request Refund')->value('request_refund'),
                Button::create('Speak with Support')->value('speak_support'),
            ]);

        $this->ask($question, function ($answer) {
            $userSelection = $answer->getValue();
            $this->say("You selected: " . ucfirst(str_replace('_', ' ', $userSelection)) . ".");

            switch ($userSelection) {
                case 'register':
                    $this->askFullName();
                    break;
                case 'make_booking':
                    $this->say("Making a booking is easy! Visit the booking page or provide the required details here.");
                    $this->askFollowUp();
                    break;
                case 'check_booking_status':
                    $this->askBookingReference();
                    break;
                case 'cancel_booking':
                    $this->askBookingReferenceForCancellation();
                    break;
                case 'request_refund':
                    $this->askBookingReferenceForRefund();
                   
                    break;
                case 'speak_support':
                    $this->speakWithSupport();
                    
                    break;
                default:
                    $this->say("I'm sorry, I didn't understand that. Please select one of the options from the menu.");
                    $this->showMainMenu();
                    break;
            }
        });
    }

    public function askFullName()
    {
        $this->ask("Please enter your full name:", function ($answer) {
            $this->userData['name'] = trim($answer->getText());
            $this->askSex();
        });
    }

    public function askSex()
    {
        $this->ask("What is your gender? (Male/Female)", function ($answer) {
            $sex = strtolower(trim($answer->getText()));
            if (in_array($sex, ['male', 'female'])) {
                $this->userData['gender'] = ucfirst($sex);
                $this->askEmail();
            } else {
                $this->say("Please reply with either 'Male' or 'Female'.");
                $this->askSex();
            }
        });
    }

    public function askEmail()
    {
        $this->ask("Please provide your email address:", function ($answer) {
            $email = trim($answer->getText());

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (User::where('email', $email)->exists()) {
                    $this->say("This email is already registered. Please provide a different email.");
                    $this->askEmail();
                } else {
                    $this->userData['email'] = $email;
                    $this->askPhoneNumber();
                }
            } else {
                $this->say("Please provide a valid email address.");
                $this->askEmail();
            }
        });
    }

    public function askPhoneNumber()
    {
        $this->ask("Please provide your phone number (11 digits):", function ($answer) {
            $phone = trim($answer->getText());

            if (preg_match('/^\d{11}$/', $phone)) {
                if (User::where('phone', $phone)->exists()) {
                    $this->say("This phone number is already registered. Please provide a different phone number.");
                    $this->askPhoneNumber();
                } else {
                    $this->userData['phone'] = $phone;
                    $this->askPassword();
                }
            } else {
                $this->say("Please provide a valid 11-digit phone number.");
                $this->askPhoneNumber();
            }
        });
    }

    public function askPassword()
    {
        $this->ask("Please enter your password (it will not be displayed):", function ($answer) {
            $this->userData['password'] = trim($answer->getText());
            $this->askConfirmPassword();
        }, ['inputType' => 'password']);
    }

    public function askConfirmPassword()
    {
        $this->ask("Please confirm your password (it will not be displayed):", function ($answer) {
            if (trim($answer->getText()) === $this->userData['password']) {
                $this->say("Please wait while we process your registration...");
                $this->bot->typesAndWaits(2);
                $this->registerUser();
            } else {
                $this->say("Passwords do not match. Please try again.");
                $this->askPassword();
            }
        }, ['inputType' => 'password']);
    }

    public function registerUser()
    {
        try {
            $user = User::create([
                'name' => $this->userData['name'],
                'email' => $this->userData['email'],
                'phone' => $this->userData['phone'],
                'password' => Hash::make($this->userData['password']),
                'gender' => $this->userData['gender'],
                'status' => 'inactive',
                'created_by' => $this->userData['email'],
            ]);

            $user->assignRole('passenger');
            $user->sendEmailVerificationNotification();

            Log::info('User registered via chatbot', ['email' => $user->email]);
            $this->say("Registration successful! A verification email has been sent to {$this->userData['email']}. Please check your email to verify your account.");
        } catch (\Exception $e) {
            Log::error('Registration failed', ['error' => $e->getMessage()]);
            $this->say("An error occurred during registration. Please try again later.");
        }

        $this->askFollowUp();
    }

    public function askBookingReferenceForCancellation()
    {
        $this->ask("Please provide your booking reference to proceed:", function ($answer) {
            // Remove all whitespace from the input
            $bookingReference = preg_replace('/\s+/', '', $answer->getText());
    
            $booking = Booking::where('booking_reference', $bookingReference)->first();
    
            if ($booking) {
                if ($booking->status === 'cancelled') {
                    $this->say("The booking with reference number {$bookingReference} is already cancelled. If you want to restore or activate it back, please contact support.");
                    $this->askFollowUp();
                } elseif ($booking->status === 'expired') {
                    $this->say("The booking with reference number {$bookingReference} has already expired. Please make a new booking.");
                    $this->askFollowUp();
                } else {
                    $this->bookingData['booking'] = $booking;
                    $this->sendVerificationCodeToEmailForCancellation();
                }
            } else {
                $this->say("Sorry, no booking found with the reference number: {$bookingReference}. Please check and try again.");
                $this->askBookingReferenceForCancellation();
            }
        });
    }
    
    public function sendVerificationCodeToEmailForCancellation()
    {
        $booking = $this->bookingData['booking'];
        $user = User::find($booking->user_id);
    
        if ($user) {
            $this->bookingData['user'] = $user;
    
            // Generate a random 6-digit verification code
            $verificationCode = rand(100000, 999999);
    
            // Store the verification code and timestamp in the session
            session(['verification_code' => $verificationCode, 'code_generated_at' => now()]);
    
            try {
                // Send the verification code via email
                Mail::to($user->email)->send(new VerificationCodeEmail($verificationCode));
                Log::info("Verification code sent to email: {$user->email}");
                $this->askVerificationCodeForCancellation();
            } catch (\Exception $e) {
                Log::error("Failed to send verification code email", ['error' => $e->getMessage()]);
                $this->say("An error occurred while sending the verification code. Please try again later.");
                $this->askFollowUp();
            }
        } else {
            $this->say("Unable to find the user associated with this booking. Please contact support.");
            $this->askFollowUp();
        }
    }
    
    public function askVerificationCodeForCancellation()
    {
        $this->ask("A verification code has been sent to your email. Please enter the code to proceed:", function ($answer) {
            $userCode = trim(str_replace(' ', '', $answer->getText())); // Remove spaces from user input
            $sessionCode = session('verification_code');
            $codeGeneratedAt = session('code_generated_at');
    
            // Validate the code and expiration time (5 minutes)
            if ($sessionCode && $userCode == $sessionCode && now()->diffInMinutes($codeGeneratedAt) <= 5) {
                session()->forget(['verification_code', 'code_generated_at']); // Clear the session
                $this->cancelBooking();
            } else {
                $this->say("The verification code is invalid or has expired. Please request a new one.");
                $this->askFollowUp();
            }
        });
    }
    
    public function cancelBooking()
    {
        try {
            $booking = $this->bookingData['booking'];
            $user = $this->bookingData['user'];
    
            // Update the booking status to cancelled
            $booking->update(['status' => 'cancelled']);
            Log::info("Booking ID: {$booking->id} status updated to cancelled");
    
            // Send cancellation email to the user
            try {
                Mail::to($user->email)->send(new BookingCancellation($booking, $user));
                Log::info("Cancellation email sent to user: {$user->email}");
            } catch (\Exception $e) {
                Log::error('Failed to send cancellation email to user: ' . $e->getMessage());
            }
    
            // Notify admin and consultants about the cancellation
            $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
            foreach ($adminConsultantUsers as $adminConsultant) {
                Notification::create([
                    'user_id' => $adminConsultant->id,
                    'message' => 'Booking cancelled by ' . $user->name . '. Booking Reference: ' . $booking->booking_reference,
                    'type' => 'booking',
                    'status' => 'unread',
                    'related_user_name' => $user->name,
                ]);
                Log::info("Notification sent to admin/consultant ID: {$adminConsultant->id}");
            }
    
            // Send cancellation email to admin using config-based admin email
            try {
                $adminEmail = config('mail.admin_email');  // Fetch email from config
                Mail::to($adminEmail)->send(new BookingCancellationAdminNotification($booking, $adminConsultantUsers));
                Log::info("Cancellation email sent to admin: {$adminEmail}");
            } catch (\Exception $e) {
                Log::error('Failed to send cancellation email to admin: ' . $e->getMessage());
            }
    
            // Log the activity for the cancellation
            ActivityLogger::log('Booking Cancelled', 'Booking cancelled by user: ' . $user->email . ', Booking Reference: ' . $booking->booking_reference);
    
            $this->say("Your booking with reference number {$booking->booking_reference} has been successfully cancelled.");
        } catch (\Exception $e) {
            Log::error("Failed to cancel booking: " . $e->getMessage());
            $this->say("An error occurred while cancelling your booking. Please try again later.");
        }
    
        $this->askFollowUp();
    }
    

public function askBookingReferenceForRefund()
{
    $this->ask("Please provide the booking reference for your refund request:", function ($answer) {
        $bookingReference = trim(str_replace(' ', '', $answer->getText())); // Remove spaces

        $booking = Booking::where('booking_reference', $bookingReference)->first();

        if ($booking) {
            $this->bookingData['booking'] = $booking;
            $this->sendVerificationCodeToEmail();
        } else {
            $this->say("No booking found with the reference number: {$bookingReference}. Please check and try again.");
            $this->askBookingReferenceForRefund();
        }
    });
}

public function sendVerificationCodeToEmail()
{
    $booking = $this->bookingData['booking'];
    $user = User::find($booking->user_id);

    if ($user) {
        $this->bookingData['user'] = $user;

        // Generate a random 6-digit verification code
        $verificationCode = rand(100000, 999999);

        // Store the verification code and timestamp in the session
        session(['verification_code' => $verificationCode, 'code_generated_at' => now()]);

        try {
            // Send the verification code via email
            Mail::to($user->email)->send(new VerificationCodeEmail($verificationCode));
            Log::info("Verification code sent to email: {$user->email}");
            $this->askVerificationCode();
        } catch (\Exception $e) {
            Log::error("Failed to send verification code email", ['error' => $e->getMessage()]);
            $this->say("An error occurred while sending the verification code. Please try again later.");
            $this->askFollowUp();
        }
    } else {
        $this->say("Unable to find the user associated with this booking. Please contact support.");
        $this->askFollowUp();
    }
}

public function askVerificationCode()
{
    $this->ask("A verification code has been sent to your email. Please enter the code to proceed:", function ($answer) {
        $userCode = str_replace(' ', '', trim($answer->getText())); // Trim and remove spaces
        $sessionCode = session('verification_code');
        $codeGeneratedAt = session('code_generated_at');

        // Validate the code and expiration time (5 minutes)
        if ($sessionCode && $userCode == $sessionCode && now()->diffInMinutes($codeGeneratedAt) <= 5) {
            session()->forget(['verification_code', 'code_generated_at']); // Clear the session
            $this->checkPaymentForRefund();
        } else {
            $this->say("The verification code is invalid or has expired. Please request a new one.");
            $this->askFollowUp();
        }
    });
}

public function checkPaymentForRefund()
{
    $booking = $this->bookingData['booking'];

    $payment = Payment::where('booking_id', $booking->id)->where('status', 'paid')->first();

    if ($payment) {
        $this->bookingData['payment'] = $payment;
        $this->confirmRefundRequest();
    } else {
        $this->say("No eligible payment found for this booking reference. Only paid bookings can request a refund.");
        $this->askFollowUp();
    }
}


public function confirmRefundRequest()
{
    $payment = $this->bookingData['payment'];
    $booking = $this->bookingData['booking'];

    try {
        // Update payment status to refund pending
        $payment->status = 'refund-pending';
        $payment->save();

        // Log the refund request
        Log::info("Refund requested for payment ID: {$payment->id}, Booking Reference: {$booking->booking_reference}");

        // Notify the user of the successful refund request
        $this->say("Your refund request for booking reference {$booking->booking_reference} has been submitted successfully. Our team will process it shortly.");

        // Process notifications and emails
        $this->processRefundNotificationsAndEmails($payment);

        $this->askFollowUp(); // Ask for further assistance
    } catch (\Exception $e) {
        // Handle errors
        Log::error("Error occurred while processing refund request", ['error' => $e->getMessage()]);
        $this->say("An error occurred while requesting the refund. Please try again later.");
        $this->askFollowUp(); // End the flow
    }
}

protected function processRefundNotificationsAndEmails($payment)
{
    $booking = $this->bookingData['booking'];
    $user = $this->bookingData['user'];

    try {
        // Notify admin and consultant users
        $adminConsultantUsers = User::role(['admin', 'consultant'])->get();
        foreach ($adminConsultantUsers as $adminConsultant) {
            Notification::create([
                'user_id' => $adminConsultant->id,
                'message' => 'Refund requested for Booking Ref: ' . $booking->booking_reference . ' with Invoice No: ' . $payment->invoice->invoice_number,
                'type' => 'payment',
                'status' => 'unread',
                'related_user_name' => $user->name,
            ]);

            Log::info("Push notification sent to {$adminConsultant->name} for payment ID: {$payment->id}");
        }

        // Notify the user who initiated the refund request
        Notification::create([
            'user_id' => $user->id,
            'message' => 'Your refund request has been initiated for Booking Ref: ' . $booking->booking_reference . ' with Invoice No: ' . $payment->invoice->invoice_number,
            'type' => 'payment',
            'status' => 'unread',
            'related_user_name' => $user->name,
        ]);

        Log::info("Push notification sent to user {$user->name} for payment ID: {$payment->id}");

        // Send email to admin
        $adminEmail = config('mail.admin_email');
        Mail::to($adminEmail)->send(new AdminRefundRequestNotification($booking, $payment->payment_reference, $payment->invoice->invoice_number));
        Log::info("Email notification sent to admin for payment ID: {$payment->id}");

        // Send email to user
        Mail::to($user->email)->send(new UserRefundRequestNotification($booking, $payment->payment_reference, $payment->invoice->invoice_number));
        Log::info("Email notification sent to user {$user->email} for payment ID: {$payment->id}");
    } catch (\Exception $e) {
        Log::error("Error occurred while sending notifications or emails for payment ID: {$payment->id}", ['error' => $e->getMessage()]);
    }
}

public function speakWithSupport()
{
    $supportNumber = '+2348070419826'; // Support phone number
    $whatsAppLink = "https://wa.me/".str_replace('+', '', $supportNumber)."?text=Hi%20Support,%20I%20need%20assistance.";

    $this->say("To connect with support, please use the link below:");
    $this->say("<a href='{$whatsAppLink}' target='_blank'>Click here to chat with support on WhatsApp</a>", ['parse_mode' => 'HTML']);

    // Ask for further assistance
    $this->askFollowUp();
}



    public function askFollowUp()
    {
        $this->ask("Do you need any other services or assistance? (yes/no)", function ($answer) {
            $response = strtolower(trim($answer->getText()));

            if ($response === 'yes') {
                $this->showMainMenu();
            } elseif ($response === 'no') {
                $this->say("Thank you for using our services. Have a nice day!");
            } else {
                $this->say("I'm sorry, I didn't understand that. Please reply with 'yes' or 'no'.");
                $this->askFollowUp();
            }
        });
    }
}
