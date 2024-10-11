@component('mail::message')
# Payment Confirmation

Dear {{ $booking->user->name }},

Thank you for your payment. Your booking with the reference **{{ $booking->booking_reference }}** has been successfully paid.

**Few Details:**

- **Service Type:** {{ $booking->service_type }}
- **Trip Type:** {{ $booking->trip_type }}
- **Pickup Date:** {{ \Carbon\Carbon::parse($booking->pickup_date)->format('F j, Y') }}
- **Amount Paid:** ₦{{ number_format($booking->payment->amount, 2) }}

If you have any questions, feel free to contact us.

@component('mail::button', ['url' => route('booking.details', $booking->id)])
View Booking Details
@endcomponent

Thank you for choosing SAP Transportation.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
