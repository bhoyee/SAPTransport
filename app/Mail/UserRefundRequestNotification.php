<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRefundRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $paymentReference;
    public $invoiceNumber;

    /**
     * Create a new message instance.
     *
     * @param Booking $booking
     * @param string $paymentReference
     * @param string $invoiceNumber
     */
    public function __construct(Booking $booking, $paymentReference, $invoiceNumber)
    {
        $this->booking = $booking;
        $this->paymentReference = $paymentReference;
        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Refund Request Has Been Received')
                    ->view('emails.user.refund_request')
                    ->with([
                        'bookingReference' => $this->booking->booking_reference,
                        'paymentReference' => $this->paymentReference,
                        'invoiceNumber' => $this->invoiceNumber,
                    ]);
    }
}
