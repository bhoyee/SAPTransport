<?php

namespace App\Mail;

use App\Models\WalkinInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;

class SendWalkinInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     *
     * @param WalkinInvoice $invoice
     */
    public function __construct(WalkinInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Generate PDF
        $pdf = \PDF::loadView('admin.invoices.walkIn-pdf', ['invoice' => $this->invoice]);

        // Build the email message
        return $this->subject('Your Custom Invoice from SAPTransport')
                    ->view('emails.walkin-invoice') // Use the Blade template for the email
                    ->attachData($pdf->output(), 'invoice_' . $this->invoice->invoice_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
