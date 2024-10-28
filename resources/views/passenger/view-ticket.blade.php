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
            <!-- Ticket Information -->
            <p><strong>Department:</strong> {{ ucfirst($ticket->department) }}</p>
            <p><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y H:i') }}</p>

            <!-- Ticket Trend -->
            <div class="ticket-trend" id="replies-container">
                <!-- Owner's Original Message -->
                <div class="reply p-3 mb-3" style="background-color: #e8f4f8;"> <!-- Light blue for owner -->
                    <p>
                        <span class="badge bg-success">Owner</span> 
                        {{ $ticket->fullname }} said:
                        <span class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') }}</span>
                    </p>
                    <p>{!! $ticket->message !!}</p> <!-- Render the message as HTML -->
                </div>

                <!-- Display Replies -->
                @foreach($replies as $reply)
                    @if($reply->user && $reply->user->email === $ticket->email_address)
                    <!-- Owner's Reply -->
                    <div class="reply mt-3 p-3" style="background-color: #e8f4f8;"> <!-- Same light blue for owner -->
                        <p>
                            <span class="badge bg-success">Owner</span> 
                            {{ $reply->user->name ?? 'Unknown User' }} said:
                            <span class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y H:i') }}</span>
                        </p>
                        <p>{!! $reply->message !!}</p> <!-- Render the reply message as HTML -->
                    </div>
                    @elseif($reply->user && ($reply->user->hasRole('admin') || $reply->user->hasRole('consultant')))
                    <!-- Operator's Reply -->
                    <div class="reply mt-3 p-3" style="background-color: #f7f7f7;"> <!-- Light gray for operator -->
                        <p>
                            <span class="badge bg-info">Operator</span>
                            {{ $reply->user->name ?? 'Unknown User' }} said:
                            <span class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y H:i') }}</span>
                        </p>
                        <p>{!! $reply->message !!}</p> <!-- Render the reply message as HTML -->
                    </div>
                    @else
                    <!-- Other User's Reply -->
                    <div class="reply mt-3 p-3" style="background-color: #f7f7f7;"> <!-- Light gray for others -->
                        <p>
                            <span class="badge bg-secondary">User</span> 
                            {{ $reply->user->name ?? 'Unknown User' }} said:
                            <span class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y H:i') }}</span>
                        </p>
                        <p>{!! $reply->message !!}</p> <!-- Render the reply message as HTML -->
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- Reply Form with Quill Editor -->
            <form id="replyForm">
                @csrf
                <div class="form-group mt-4">
                    <label for="message">Your Reply</label>
                    <!-- Quill editor container -->
                    <div id="reply-editor-container" style="height: 150px;"></div>
                    <!-- Hidden input to store the Quill editor content -->
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
<!-- Include Quill stylesheet -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

<!-- Include Quill script -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
// Initialize Quill editor for the reply
// Initialize Quill editor for the reply
const replyQuill = new Quill('#reply-editor-container', {
    theme: 'snow',
    readOnly: "{{ $ticket->status == 'closed' }}" === "1" // Make the editor read-only if the ticket is closed
});

// Track the last reply timestamp to fetch only new replies
let lastReplyTimestamp = "{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}";

// Set initial states based on ticket status
let isTicketClosed = "{{ $ticket->status }}" === "closed";
if (isTicketClosed) {
    replyQuill.enable(false); // Disable Quill editor if the ticket is closed
}

// Submit reply or reopen ticket with AJAX
document.getElementById('submitReplyButton').addEventListener('click', function () {
    const submitReplyButton = document.getElementById('submitReplyButton');
    submitReplyButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Sending...";
    submitReplyButton.disabled = true;

    let message = replyQuill.root.innerHTML.trim();
    if (isTicketClosed) {
        message = `${"{{ Auth::user()->name }}"}` + ' wants to reopen the ticket.';
    }

    // AJAX request to submit the reply or reopen ticket
    $.ajax({
        url: "{{ route('replyTicket', $ticket->id) }}",
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            message: message
        },
        success: function (response) {
            if (response && response.message) {
                if (isTicketClosed) {
                    // If the ticket was closed, reopen it and enable Quill editor
                    replyQuill.enable(true);
                    isTicketClosed = false; // Update the status
                    submitReplyButton.innerHTML = "Send Reply"; // Change button text
                    submitReplyButton.disabled = false; // Enable button

                    // Optionally, you can display a success message
                    console.log('Ticket has been reopened successfully.');
                }
                
                // Append the new reply to the replies container
                $('#replies-container').append(`
                    <div class="reply mt-3 p-3" style="background-color: #e8f4f8;" data-reply-id="${response.id}">
                        <p>
                            <span class="badge bg-success">Owner</span> 
                            {{ Auth::user()->name }} said:
                            <span class="text-muted">${new Date().toLocaleString()}</span>
                        </p>
                        <p>${response.message}</p>
                    </div>
                `);
                
                // Clear the Quill editor
                replyQuill.setContents([]);
            } else {
                console.error('Unexpected response format:', response);
            }
        },
        error: function () {
            console.error('Failed to send the reply.');
        },
        complete: function () {
            // Reset the button regardless of success or failure
            submitReplyButton.innerHTML = isTicketClosed ? "Reopen Ticket" : "Send Reply";
            submitReplyButton.disabled = false;
        }
    });
});

// Fetch new replies periodically
setInterval(fetchNewReplies, 5000); // Every 5 seconds

function fetchNewReplies() {
    $.ajax({
        url: "{{ route('fetchNewReplies', $ticket->id) }}",
        method: 'GET',
        data: {
            lastReplyTimestamp: lastReplyTimestamp
        },
        success: function (response) {
            if (response.newReplies && response.newReplies.length > 0) {
                response.newReplies.forEach(reply => {
                    // Check if the reply already exists in the DOM
                    if ($(`#replies-container [data-reply-id="${reply.id}"]`).length === 0) {
                        // Append the new reply to the replies container
                        $('#replies-container').append(`
                            <div class="reply mt-3 p-3" style="background-color: ${reply.user_role === 'owner' ? '#e8f4f8' : '#f7f7f7'};" data-reply-id="${reply.id}">
                                <p>
                                    <span class="badge ${reply.user_role === 'owner' ? 'bg-success' : 'bg-info'}">${reply.user_role === 'owner' ? 'Owner' : 'Operator'}</span> 
                                    ${reply.user_name} said:
                                    <span class="text-muted">${reply.created_at}</span>
                                </p>
                                <p>${reply.message}</p>
                            </div>
                        `);
                    }

                    // Update the last reply timestamp to the latest one received
                    lastReplyTimestamp = reply.timestamp;
                });

                // Scroll to the bottom of the replies container to show the new replies
                const repliesContainer = document.getElementById('replies-container');
                repliesContainer.scrollTop = repliesContainer.scrollHeight;
            }
        },
        error: function () {
            console.error('Failed to fetch new replies.');
        }
    });
}


</script>
@endpush
