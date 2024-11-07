<!-- resources/views/admin/support-tickets/view.blade.php -->

@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

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
<!-- Ticket Trend -->
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
        @php
            // Determine the role text and badge color based on user role
            if ($reply->user && $reply->user->hasRole('admin')) {
                $roleText = 'Admin';
                $badgeClass = 'bg-danger'; // Red for admin
            } elseif ($reply->user && $reply->user->hasRole('consultant')) {
                $roleText = 'Operator';
                $badgeClass = 'bg-info'; // Blue for consultant/operator
            } else {
                $roleText = 'Owner';
                $badgeClass = 'bg-success'; // Green for passenger/owner
            }
        @endphp
        
        <div class="reply mt-3 p-3" style="background-color: #f7f7f7;" data-reply-id="{{ $reply->id }}">
            <p>
                <span class="badge {{ $badgeClass }}">{{ $roleText }}</span>
                {{ $reply->user->name ?? 'Unknown User' }} said:
                <span class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y H:i') }}</span>
            </p>
            <p>{!! $reply->message !!}</p> <!-- Render the reply message as HTML -->
        </div>
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
    const ticketId = {{ $ticket->id }};
    let lastReplyTimestamp = "{{ $ticket->replies->last()?->created_at ?? $ticket->created_at }}"; // Track last reply timestamp



    // On form submission, show spinner and disable button for Update Status
    function submitStatusUpdate() {
        const updateStatusButton = document.getElementById('updateStatusButton');
        updateStatusButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Updating..."; // Show spinner
        updateStatusButton.disabled = true; // Disable button to prevent multiple submissions

        return true; // Allow form submission
    }

    function fetchNewReplies() {
    $.ajax({
        url: `{{ route('admin.support-tickets.fetch-replies', $ticket->id) }}`,
        method: 'GET',
        data: { lastReplyTimestamp: lastReplyTimestamp },
        success: function(response) {
            if (response.newReplies && response.newReplies.length > 0) {
                response.newReplies.forEach(reply => {
                    if ($(`[data-reply-id="${reply.id}"]`).length === 0) {
                        // Log the received role to confirm correctness
                        console.log("Received role for new reply:", reply.user_role);

                        // Assign colors and roles based on user_role accurately
                        const roleText = reply.user_role === 'admin' ? 'Admin' : (reply.user_role === 'consultant' ? 'Operator' : 'Owner');
                        const badgeClass = reply.user_role === 'admin' ? 'bg-danger' : (reply.user_role === 'consultant' ? 'bg-info' : 'bg-success');
                        const backgroundColor = reply.user_role === 'owner' ? '#e8f4f8' : '#f7f7f7';

                        $('#replies-container').append(`
                            <div class="reply mt-3 p-3" style="background-color: ${backgroundColor};" data-reply-id="${reply.id}">
                                <p>
                                    <span class="badge ${badgeClass}">${roleText}</span>
                                    ${reply.user_name} said:
                                    <span class="text-muted">${reply.created_at}</span>
                                </p>
                                <p>${reply.message}</p>
                            </div>
                        `);
                    }
                });
                // Update last reply timestamp for the next fetch
                lastReplyTimestamp = response.newReplies[response.newReplies.length - 1].timestamp;
            }
        },
        error: function() {
            console.error('Failed to fetch new replies.');
        }
    });
}

// Poll for new replies every 5 seconds
setInterval(fetchNewReplies, 5000);

function submitReplyWithQuill() {
    const replyMessageInput = document.querySelector('input[name=message]');
    replyMessageInput.value = replyQuill.root.innerHTML;

    const submitReplyButton = document.getElementById('submitReplyButton');
    submitReplyButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Sending...";
    submitReplyButton.disabled = true;

    $.ajax({
        url: "{{ route('admin.support-tickets.reply', $ticket->id) }}",
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            message: replyMessageInput.value
        },
        success: function(response) {
            const roleText = response.user_role === 'admin' ? 'Admin' : (response.user_role === 'consultant' ? 'Operator' : 'Owner');
            const badgeClass = response.user_role === 'admin' ? 'bg-danger' : (response.user_role === 'consultant' ? 'bg-info' : 'bg-success');
            const backgroundColor = response.user_role === 'owner' ? '#e8f4f8' : '#f7f7f7';

            $('#replies-container').append(`
                <div class="reply mt-3 p-3" style="background-color: ${backgroundColor};" data-reply-id="${response.id}">
                    <p>
                        <span class="badge ${badgeClass}">${roleText}</span>
                        ${response.user_name} said:
                        <span class="text-muted">${new Date().toLocaleString()}</span>
                    </p>
                    <p>${response.message}</p>
                </div>
            `);

            replyQuill.setText(''); // Clear the Quill editor
        },
        error: function() {
            console.error('Failed to send the reply.');
        },
        complete: function() {
            submitReplyButton.innerHTML = "Send Reply";
            submitReplyButton.disabled = false;
        }
    });

    return false;
}


</script>
@endpush
