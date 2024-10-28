<!-- resources/views/emails/ticket-reply-notification.blade.php -->
<h1>New Reply on Your Ticket #{{ $ticket->ticket_num }}</h1>
<p>Hello {{ $ticket->fullname }},</p>
<p>A new reply has been posted on your support ticket:</p>
<p><strong>Reply:</strong> {{ strip_tags($reply->message) }}</p> <!-- Strip HTML tags from the message -->
<p>You can view the ticket by clicking <a href="{{ url('viewTicket/' . $ticket->id) }}">here</a>.</p>
<p>Thank you,<br>Your Support Team</p>
