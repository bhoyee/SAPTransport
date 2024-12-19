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
                    $this->say("For refunds, please visit our refund page or contact support for further assistance.");
                    $this->askFollowUp();
                    break;
                case 'speak_support':
                    $this->say("You can speak to support by calling our hotline or sending an email to support@saptransportation.com.");
                    $this->askFollowUp();
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
                    $this->askAssociatedEmail();
                }
            } else {
                $this->say("Sorry, no booking found with the reference number: {$bookingReference}. Please check and try again.");
                $this->askBookingReferenceForCancellation();
            }
        });
    }
    


    public function askAssociatedEmail()
    {
        $this->ask("Please provide the email address associated with the booking:", function ($answer) {
            $email = trim($answer->getText());
            $booking = $this->bookingData['booking'];
    
            // Check if the email matches the user associated with the booking
            $user = $booking->user; // Assuming a relationship exists between Booking and User
    
            if ($user && $email === $user->email) {
                $this->bookingData['email'] = $email;
                $this->askAssociatedPhone();
            } else {
                $this->say("The email address provided does not match our records for this booking.");
                $this->askAssociatedEmail();
            }
        });
    }
    
    public function askAssociatedPhone()
    {
        $this->ask("Please provide the phone number associated with the booking:", function ($answer) {
            $phone = trim($answer->getText());
            $booking = $this->bookingData['booking'];
    
            // Check if the phone matches the user associated with the booking
            $user = $booking->user; // Assuming the relationship exists between Booking and User
    
            if ($user && $phone === $user->phone) {
                $this->bookingData['phone'] = $phone;
                $this->cancelBooking();
            } else {
                $this->say("The phone number provided does not match our records for this booking.");
                $this->askAssociatedPhone();
            }
        });
    }
    

    public function cancelBooking()
{
    try {
        $booking = $this->bookingData['booking'];
        $user = $booking->user; // Fetch the user associated with the booking

        // Update the booking status to cancelled
        $booking->update(['status' => 'cancelled']);
        \Log::info("Booking ID: {$booking->id} status updated to cancelled");

        // Send cancellation email to the user
        try {
            Mail::to($user->email)->send(new BookingCancellation($booking, $user));
            \Log::info("Cancellation email sent to user: {$user->email}");
        } catch (\Exception $e) {
            \Log::error('Failed to send cancellation email to user: ' . $e->getMessage());
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
            \Log::info("Notification sent to admin/consultant ID: {$adminConsultant->id}");
        }

        // Send cancellation email to admin using config-based admin email
        try {
            $adminEmail = config('mail.admin_email');  // Fetch email from config
            Mail::to($adminEmail)->send(new BookingCancellationAdminNotification($booking, $adminConsultantUsers));
            \Log::info("Cancellation email sent to admin: {$adminEmail}");
        } catch (\Exception $e) {
            \Log::error('Failed to send cancellation email to admin: ' . $e->getMessage());
        }

        // Log the activity for the cancellation
        ActivityLogger::log('Booking Cancelled', 'Booking cancelled by user: ' . $user->email . ', Booking Reference: ' . $booking->booking_reference);

        $this->say("Your booking with reference number {$booking->booking_reference} has been successfully cancelled.");
    } catch (\Exception $e) {
        \Log::error("Failed to cancel booking: " . $e->getMessage());
        $this->say("An error occurred while cancelling your booking. Please try again later.");
    }

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
