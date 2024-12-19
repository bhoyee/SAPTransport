<?php

namespace App\Conversations;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class BookingConversation extends Conversation
{
    protected $userData = [];

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
                    $this->askCancelBooking();
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
        $this->ask("Please enter your password (Pls note your password will not be masked):", function ($answer) {
            $this->userData['password'] = trim($answer->getText());
            $this->askConfirmPassword();
        }, ['inputType' => 'password']);
    }
    
    public function askConfirmPassword()
    {
        $this->ask("Please confirm your password (Pls note your password will not be masked):", function ($answer) {
            if (trim($answer->getText()) === $this->userData['password']) {
                // Show waiting message immediately after confirming the password
                $this->say("Please wait while we process your registration...");
    
                // Optionally simulate bot typing for user feedback
                $this->bot->typesAndWaits(2);
    
                // Proceed with registration
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
            'status' => 'inactive', // Mark as inactive until verified
            'created_by' => $this->userData['email'], // Set created_by field
        ]);

        // Assign default role
        $user->assignRole('passenger');

        // Send verification email
        $user->sendEmailVerificationNotification();

        Log::info('User registered via chatbot', ['email' => $user->email]);

        // Inform the user of successful registration
        $this->say("Registration successful! A verification email has been sent to {$this->userData['email']}. Please check your email to verify your account.");
    } catch (\Exception $e) {
        Log::error('Registration failed', ['error' => $e->getMessage()]);
        $this->say("An error occurred during registration. Please try again later.");
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
