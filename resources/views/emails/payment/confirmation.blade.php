@component('mail::message')
# Payment Confirmation

Dear {{ $booking->user->name }},

Thank you for your payment. Your booking with the reference **{{ $booking->booking_reference }}** has been successfully paid.

**Few Details:**

- **Service Type:** {{ $booking->service_type }}
- **Trip Type:** {{ $booking->trip_type }}
- **Pickup Date:** {{ \Carbon\Carbon::parse($booking->pickup_date)->format('F j, Y') }}
@if(isset($booking->booking_reference))
- **Booking Reference:** {{ $booking->booking_reference }}
@else
- **Booking Reference:** Not Available
@endif

**Amount Paid:** ₦{{ isset($booking->invoice->amount) ? number_format($booking->invoice->amount, 2) : 'Not Available' }}


If you have any questions, feel free to contact us.

@component('mail::button', ['url' => route('booking.view', $booking->id)])
View Booking Details
@endcomponent

Thank you for choosing SAP Transportation.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
