<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $status; // Add status variable

    /**
     * Create a new message instance.
     *
     * @param Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->status = $booking->status; // Assign booking status here
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Booking Created')
            ->view('emails.booking_admin_notification')
            ->with([
                'bookingReference' => $this->booking->booking_reference,
                'serviceType' => $this->booking->service_type,
                'tripType' => $this->booking->trip_type,
                'pickupDate' => $this->booking->pickup_date,
                'pickupTime' => $this->booking->pickup_time,
                'userName' => $this->booking->user->name,
                'status' => $this->status, // Pass status to the view
                'bookingDetails' => $this->booking // Pass all booking details to the view
            ]);
    }
}
