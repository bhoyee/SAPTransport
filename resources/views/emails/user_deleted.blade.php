<!-- resources/views/emails/user_deleted.blade.php -->

<p>Dear Admin,</p>
<p>The user <strong>{{ $deletedUser->name }}</strong> (Email: {{ $deletedUser->email }}) was deleted by <strong>{{ $deletedBy->email }}</strong> on {{ $deletedAt }}.</p>
<p>Thank you.</p>
