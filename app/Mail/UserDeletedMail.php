<?php
// app/Mail/UserDeletedMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $deletedUser;
    protected $deletedBy;

    public function __construct($deletedUser, $deletedBy)
    {
        $this->deletedUser = $deletedUser;
        $this->deletedBy = $deletedBy;
    }

    public function build()
    {
        return $this->subject('User Account Deleted')
                    ->view('emails.user_deleted')
                    ->with([
                        'deletedUser' => $this->deletedUser,
                        'deletedBy' => $this->deletedBy,
                        'deletedAt' => now(),
                    ]);
    }
}
