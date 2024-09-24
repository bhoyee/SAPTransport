<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Make sure this is imported

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Log that the mail is being built (debugging)
        Log::info('Building the UserRegistered mail for', ['user' => $this->user->email]);

        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Welcome to SAP Transportation and Logistics')
                    ->view('emails.user_registered')
                    ->with([
                        'name' => $this->user->name,
                        'email' => $this->user->email,
                    ]);
    }
}
