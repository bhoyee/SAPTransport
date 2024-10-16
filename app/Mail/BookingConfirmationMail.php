<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $invoice;

    public function __construct($booking, $invoice)
    {
        $this->booking = $booking;
        $this->invoice = $invoice;
    }

    public function build()
    {
        $invoiceLink = route('passenger.invoice', ['id' => $this->invoice->id]); // Link to the invoice
        return $this->subject('Booking Confirmation')
                    ->view('emails.booking_confirm_mail')
                    ->with([
                        'invoiceLink' => $invoiceLink, // Pass the link to the view
                        'bookingReference' => $this->booking->booking_reference,
                        'status' => $this->booking->status,
                        'bookingDetails' => $this->booking, // Pass full booking details if needed
                    ]);
    }
}
