@component('mail::message')
# Refund Processed

A refund has been successfully processed for the following booking:

- **Booking Reference:** {{ $bookingReference }}
- **Payment Reference:** {{ $paymentReference }}
- **Invoice Number:** {{ $invoiceNumber }}

You can review the booking and refund details in the admin panel.

@component('mail::button', ['url' => route('admin.bookings.view', $bookingReference)])
View Booking
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
