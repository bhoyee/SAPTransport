@component('mail::message')
# Refund Processed

Dear Customer,

Your refund has been successfully processed for the following booking:

- **Booking Reference:** {{ $bookingReference }}
- **Payment Reference:** {{ $paymentReference }}
- **Invoice Number:** {{ $invoiceNumber }}

Please check your account for the refund. Note that it may take up to **7 working days** to reflect back to the card you used for payment. If you have any questions, feel free to contact us.


@component('mail::button', ['url' => route('booking.view', $booking->id)])
View Booking Details
@endcomponent


Thank you,<br>
{{ config('app.name') }}
@endcomponent
