<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRefundNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $paymentReference;
    public $invoiceNumber;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($booking, $paymentReference, $invoiceNumber)
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
        return $this->markdown('emails.refunds.user')
                    ->subject('Refund Processed for Your Booking: ' . $this->booking->booking_reference)
                    ->with([
                        'bookingReference' => $this->booking->booking_reference,
                        'paymentReference' => $this->paymentReference,
                        'invoiceNumber' => $this->invoiceNumber,
                    ]);
    }
}
