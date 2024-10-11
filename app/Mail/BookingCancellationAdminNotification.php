<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCancellationAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $adminUsers;

    public function __construct(Booking $booking, $adminUsers)
    {
        $this->booking = $booking;
        $this->adminUsers = $adminUsers;
    }

    public function build()
    {
        return $this->subject('Booking Cancellation Notice')
                    ->view('emails.admin_booking_cancellation');
    }
}
