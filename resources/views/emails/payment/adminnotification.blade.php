@component('mail::message')
# Payment Notification

Dear Admin,

A payment has been successfully received for the following booking:

**Booking Reference:** {{ $booking->booking_reference }}

**Few Details:**
- **User:** {{ $booking->user->name }} ({{ $booking->user->email }})
- **Service Type:** {{ $booking->service_type }}
- **Trip Type:** {{ $booking->trip_type }}
- **Pickup Date:** {{ \Carbon\Carbon::parse($booking->pickup_date)->format('F j, Y') }}
- **Amount Paid:** ₦{{ number_format($booking->payment->amount, 2) }}

Please verify the payment and update the records if necessary.

@component('mail::button', ['url' => route('admin.booking.details', $booking->id)])
View Booking Details
@endcomponent

Thank you.

Best regards,<br>
{{ config('app.name') }} Admin Team
@endcomponent
