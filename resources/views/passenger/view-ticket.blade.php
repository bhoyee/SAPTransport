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
            <div class="ticket-trend">
                <!-- Owner's Original Message -->
                <div class="reply p-3 mb-3" style="background-color: #e8f4f8;"> <!-- Light blue for owner -->
                    <p>
                        <span class="badge bg-success">Owner</span> 
                        {{ $ticket->user->name ?? 'Unknown User' }} said:
                        <span class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') }}</span>
                    </p>
                    <p>{!! $ticket->message !!}</p> <!-- Render the message as HTML -->
                </div>

                <!-- Display Replies -->
                @foreach($replies as $reply)
                    @if($reply->user->role == 'admin' || $reply->user->role == 'consultant')
                    <!-- Operator's Reply -->
                    <div class="reply mt-3 p-3" style="background-color: #f7f7f7;"> <!-- Light gray for operator -->
                        <p>
                            <span class="badge bg-info">Operator</span>
                            {{ $reply->user->name ?? 'Unknown User' }} said:
                            <span class="text-muted">{{ \Carbon\Carbon::parse($reply->updated_at)->format('d M Y H:i') }}</span>
                        </p>
                        <p>{!! $reply->message !!}</p> <!-- Render the reply message as HTML -->
                    </div>
                    @else
                    <!-- Owner's Reply -->
                    <div class="reply mt-3 p-3" style="background-color: #e8f4f8;"> <!-- Same light blue for owner -->
                        <p>
                            <span class="badge bg-success">Owner</span> 
                            {{ $reply->user->name ?? 'Unknown User' }} said:
                            <span class="text-muted">{{ \Carbon\Carbon::parse($reply->updated_at)->format('d M Y H:i') }}</span>
                        </p>
                        <p>{!! $reply->message !!}</p> <!-- Render the reply message as HTML -->
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- Reply Form with Quill Editor -->
            <form action="{{ route('replyTicket', $ticket->id) }}" method="POST" onsubmit="return submitReplyWithQuill()">
                @csrf
                <div class="form-group mt-4">
                    <label for="message">Your Reply</label>
                    <!-- Quill editor container -->
                    <div id="reply-editor-container" style="height: 150px;"></div>
                    <!-- Hidden input to store the Quill editor content -->
                    <input type="hidden" name="message" id="reply-message">
                </div>
                <button type="submit" id="submitReplyButton" class="btn btn-primary mt-3">
                    Send Reply
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
    const replyQuill = new Quill('#reply-editor-container', {
        theme: 'snow'
    });

    // On form submission, pass Quill content into hidden input field and show spinner
    function submitReplyWithQuill() {
        const replyMessageInput = document.querySelector('input[name=message]');
        replyMessageInput.value = replyQuill.root.innerHTML; // Grab Quill's content

        // Add spinner to submit button and disable it to prevent multiple submissions
        const submitReplyButton = document.getElementById('submitReplyButton');
        submitReplyButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Sending...";
        submitReplyButton.disabled = true;

        return true; // Proceed with form submission
    }
</script>
@endpush
