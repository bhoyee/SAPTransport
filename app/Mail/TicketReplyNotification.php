<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $reply;

    /**
     * Create a new message instance.
     *
     * @param $ticket
     * @param $reply
     */
    public function __construct($ticket, $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Reply on Your Support Ticket')
                    ->view('emails.ticket-reply-notification');
    }
}
