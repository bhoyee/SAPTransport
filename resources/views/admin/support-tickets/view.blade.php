<!-- resources/views/admin/support-tickets/view.blade.php -->

@extends('admin.layouts.admin-layout')

@section('title', 'View Support Ticket')

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
            <p><strong>Priority:</strong> {{ ucfirst($ticket->priority) }}</p>
            <p><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y H:i') }}</p>

            <!-- Update Status Form -->
            <form action="{{ route('support-tickets.updateStatus', $ticket->id) }}" method="POST" class="mb-4" onsubmit="return submitStatusUpdate()">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="status">Update Ticket Status:</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <button type="submit" id="updateStatusButton" class="btn btn-primary mt-2">
                    Update Status
                </button>
            </form>

            <!-- Ticket Trend -->
            <div class="ticket-trend mt-4" id="replies-container">
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
                @foreach($ticket->replies as $reply)
                    @if($reply->user && ($reply->user->hasRole('admin') || $reply->user->hasRole('consultant')))
                    <!-- Operator's Reply -->
                    <div class="reply mt-3 p-3" style="background-color: #f7f7f7;" data-reply-id="{{ $reply->id }}"> <!-- Light gray for operator -->
                        <p>
                            <span class="badge bg-info">Operator</span>
                            {{ $reply->user->name ?? 'Unknown User' }} said:
                            <span class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y H:i') }}</span>
                        </p>
                        <p>{!! $reply->message !!}</p> <!-- Render the reply message as HTML -->
                    </div>
                    @else
                    <!-- Owner's Reply -->
                    <div class="reply mt-3 p-3" style="background-color: #e8f4f8;" data-reply-id="{{ $reply->id }}"> <!-- Same light blue for owner -->
                        <p>
                            <span class="badge bg-success">Owner</span> 
                            {{ $reply->user->name ?? 'Unknown User' }} said:
                            <span class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y H:i') }}</span>
                        </p>
                        <p>{!! $reply->message !!}</p> <!-- Render the reply message as HTML -->
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- Reply Form with Quill Editor -->
            <form id="replyForm" onsubmit="return submitReplyWithQuill()">
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
                <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-secondary mt-3">Back to Support Tickets</a>
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

    // On form submission, show spinner and disable button for Update Status
    function submitStatusUpdate() {
        const updateStatusButton = document.getElementById('updateStatusButton');
        updateStatusButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Updating..."; // Show spinner
        updateStatusButton.disabled = true; // Disable button to prevent multiple submissions

        return true; // Allow form submission
    }

    // On form submission, pass Quill content into hidden input field and show spinner for Send Reply
    function submitReplyWithQuill() {
        const replyMessageInput = document.querySelector('input[name=message]');
        replyMessageInput.value = replyQuill.root.innerHTML; // Get Quill's content

        const submitReplyButton = document.getElementById('submitReplyButton');
        submitReplyButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Sending..."; // Show spinner
        submitReplyButton.disabled = true; // Disable button to prevent multiple submissions

        // AJAX request to submit the reply
        $.ajax({
            url: "{{ route('admin.support-tickets.reply', $ticket->id) }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                message: replyMessageInput.value
            },
            success: function(response) {
                // Append the new reply to the replies container
                $('#replies-container').append(`
                    <div class="reply mt-3 p-3" style="background-color: #f7f7f7;" data-reply-id="${response.id}">
                        <p>
                            <span class="badge bg-info">Operator</span>
                            {{ Auth::user()->name }} said:
                            <span class="text-muted">${new Date().toLocaleString()}</span>
                        </p>
                        <p>${response.message}</p>
                    </div>
                `);

                // Clear the Quill editor
                replyQuill.setContents([]);
            },
            error: function() {
                console.error('Failed to send the reply.');
            },
            complete: function() {
                // Reset the button regardless of success or failure
                submitReplyButton.innerHTML = "Send Reply";
                submitReplyButton.disabled = false;
            }
        });

        return false; // Prevent default form submission
    }
</script>
@endpush
