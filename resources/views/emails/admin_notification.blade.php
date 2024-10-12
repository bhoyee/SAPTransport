<h1>New Contact Form Submission</h1>
<p><strong>From:</strong> {{ $contact->fullname }} ({{ $contact->email_address }})</p>
<p><strong>Category:</strong> {{ ucfirst($contact->category) }}</p>
<p><strong>Message:</strong> {{ $contact->message }}</p>
<p><strong>Ticket Number:</strong> {{ $contact->ticket_num }}</p>
