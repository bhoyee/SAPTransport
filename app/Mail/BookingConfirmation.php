<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Import Log for debugging

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $user;
    public $status; // Define the status property

    /**
     * Create a new message instance.
     *
     * @param Booking $booking
     * @param User $user
     * @param string $status
     */
    public function __construct(Booking $booking, User $user, $status)
    {
        $this->booking = $booking;
        $this->user = $user;
        $this->status = $status; // Set the status property
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Log that the mail is being built for debugging
        Log::info('Building BookingConfirmation mail for user', ['email' => $this->user->email]);

        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Your Booking Confirmation')
                    ->view('emails.booking_confirmation')  // Make sure the view exists
                    ->with([
                        'userName' => $this->user->name,
                        'bookingDetails' => $this->booking,
                        'bookingReference' => $this->booking->booking_reference, // Pass the booking reference
                        'status' => $this->status, // Pass the status to the view
                        'returnPickupDate' => $this->booking->return_pickup_date,  // Pass the return pickup date
                        'returnPickupTime' => $this->booking->return_pickup_time,  // Pass the return pickup time


                    ]);
    }
}
