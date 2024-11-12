<?php
namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Your Booking Update')
                    ->view('emails.booking_update')
                    ->with([
                        'bookingReference' => $this->booking->booking_reference,
                        'driverName' => $this->booking->driver_name,
                        'vehicleDetails' => $this->booking->vehicle_details,
                    ]);
    }
}
