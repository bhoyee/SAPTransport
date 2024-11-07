@extends('layouts.passenger')

@section('title', 'View Ticket')

@section('content')
<h1 class="app-page-title">Ticket #{{ $ticket->ticket_num }}</h1>

<div class="container mt-5">
    <div class="card mb-4">
        <!-- Card Header with Status -->
        <div class="card-header {{ $ticket->status == 'open' ? 'bg-danger text-white' : 'bg-success text-white' }}">
            {{ $ticket->subject }} ({{ ucfirst($ticket->status) }})
        </div>

        <div class="card-body">
            <p><strong>Department:</strong> {{ ucfirst($ticket->department) }}</p>
            <p><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y H:i') }}</p>

            <!-- Ticket Trend -->
            <div class="ticket-trend" id="replies-container">
                <!-- Owner's Original Message -->
                <div class="reply p-3 mb-3" style="background-color: #e8f4f8;">
                    <p>
                        <span class="badge bg-success">Owner</span> 
                        {{ $ticket->fullname }} said:
                        <span class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') }}</span>
                    </p>
                    <p>{!! $ticket->message !!}</p>
                </div>

                <!-- Display Replies -->
                @foreach($replies as $reply)
                    @php
                        // Determine role and badge based on user role
                        $roleText = 'User';
                        $badgeClass = 'bg-secondary';
                        if ($reply->user && $reply->user->hasRole('admin')) {
                            $roleText = 'Admin';
                            $badgeClass = 'bg-danger';
                        } elseif ($reply->user && $reply->user->hasRole('consultant')) {
                            $roleText = 'Operator';
                            $badgeClass = 'bg-info';
                        } elseif ($reply->user && $reply->user->email === $ticket->email_address) {
                            $roleText = 'Owner';
                            $badgeClass = 'bg-success';
                        }
                    @endphp

                    <div class="reply mt-3 p-3" style="background-color: #f7f7f7;" data-reply-id="{{ $reply->id }}">
                        <p>
                            <span class="badge {{ $badgeClass }}">{{ $roleText }}</span>
                            {{ $reply->user->name ?? 'Unknown User' }} said:
                            <span class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y H:i') }}</span>
                        </p>
                        <p>{!! $reply->message !!}</p>
                    </div>
                @endforeach
            </div>

            <!-- Reply Form with Quill Editor -->
            <form id="replyForm">
                @csrf
                <div class="form-group mt-4">
                    <label for="message">Your Reply</label>
                    <div id="reply-editor-container" style="height: 150px;"></div>
                    <input type="hidden" name="message" id="reply-message" {{ $ticket->status == 'closed' ? 'disabled' : '' }}>
                </div>
                <button type="button" id="submitReplyButton" class="btn btn-primary mt-3">
                    {{ $ticket->status == 'closed' ? 'Reopen Ticket' : 'Send Reply' }}
                </button>
                <a href="{{ route('passenger.my-tickets') }}" class="btn btn-secondary mt-3">Back to My Tickets</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
const replyQuill = new Quill('#reply-editor-container', {
    theme: 'snow',
    readOnly: "{{ $ticket->status == 'closed' }}" === "1"
});

let lastReplyTimestamp = "{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}";
let isTicketClosed = "{{ $ticket->status }}" === "closed";
if (isTicketClosed) replyQuill.enable(false);

document.getElementById('submitReplyButton').addEventListener('click', function () {
    const submitReplyButton = document.getElementById('submitReplyButton');
    submitReplyButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Sending...";
    submitReplyButton.disabled = true;

    let message = replyQuill.root.innerHTML.trim();
    if (isTicketClosed) message = `${"{{ Auth::user()->name }}"}` + ' wants to reopen the ticket.';

    $.ajax({
        url: "{{ route('replyTicket', $ticket->id) }}",
        method: 'POST',
        data: { _token: "{{ csrf_token() }}", message: message },
        success: function (response) {
            if (response && response.message) {
                if (isTicketClosed) {
                    replyQuill.enable(true);
                    isTicketClosed = false;
                    submitReplyButton.innerHTML = "Send Reply";
                    submitReplyButton.disabled = false;
                }

                const roleText = response.user_role === 'admin' ? 'Admin' : (response.user_role === 'consultant' ? 'Operator' : 'Owner');
                const badgeClass = response.user_role === 'admin' ? 'bg-danger' : (response.user_role === 'consultant' ? 'bg-info' : 'bg-success');

                $('#replies-container').append(`
                    <div class="reply mt-3 p-3" style="background-color: ${roleText === 'Owner' ? '#e8f4f8' : '#f7f7f7'};" data-reply-id="${response.id}">
                        <p><span class="badge ${badgeClass}">${roleText}</span> {{ Auth::user()->name }} said:
                        <span class="text-muted">${new Date().toLocaleString()}</span></p>
                        <p>${response.message}</p>
                    </div>
                `);

                replyQuill.setContents([]);
            }
        },
        error: function () { console.error('Failed to send the reply.'); },
        complete: function () {
            submitReplyButton.innerHTML = isTicketClosed ? "Reopen Ticket" : "Send Reply";
            submitReplyButton.disabled = false;
        }
    });
});

setInterval(fetchNewReplies, 5000);

function fetchNewReplies() {
    $.ajax({
        url: "{{ route('fetchNewReplies', $ticket->id) }}",
        method: 'GET',
        data: { lastReplyTimestamp: lastReplyTimestamp },
        success: function (response) {
            if (response.newReplies && response.newReplies.length > 0) {
                response.newReplies.forEach(reply => {
                    if ($(`#replies-container [data-reply-id="${reply.id}"]`).length === 0) {
                        const roleText = reply.user_role === 'admin' ? 'Admin' : (reply.user_role === 'consultant' ? 'Operator' : 'Owner');
                        const badgeClass = reply.user_role === 'admin' ? 'bg-danger' : (reply.user_role === 'consultant' ? 'bg-info' : 'bg-success');

                        $('#replies-container').append(`
                            <div class="reply mt-3 p-3" style="background-color: ${roleText === 'Owner' ? '#e8f4f8' : '#f7f7f7'};" data-reply-id="${reply.id}">
                                <p><span class="badge ${badgeClass}">${roleText}</span> ${reply.user_name} said:
                                <span class="text-muted">${reply.created_at}</span></p>
                                <p>${reply.message}</p>
                            </div>
                        `);
                    }

                    lastReplyTimestamp = reply.timestamp;
                });

                const repliesContainer = document.getElementById('replies-container');
                repliesContainer.scrollTop = repliesContainer.scrollHeight;
            }
        },
        error: function () { console.error('Failed to fetch new replies.'); }
    });
}
</script>
@endpush
