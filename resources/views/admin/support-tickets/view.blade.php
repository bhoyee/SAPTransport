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

            
            <!-- Display Attachment -->
            @if ($ticket->attachment)
                <p>
                    <strong>Attachment:</strong>
                    <a href="{{ asset('uploads/attachments/' . $ticket->attachment) }}" target="_blank">
                        {{ $ticket->attachment }}
                    </a>
                </p>
            @endif

            <!-- Ticket Replies Section -->
            <div class="ticket-trend mt-4" id="replies-container">
                <!-- Owner's Original Message -->
                <div class="reply p-3 mb-3" style="background-color: #e8f4f8;">
                    <p>
                        <span class="badge bg-success">Owner</span>
                        <a href="javascript:void(0);" class="text-primary" id="viewOwnerDetails">{{ $ticket->fullname }} </a>
                        said:
                        <span class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') }}</span>
                    </p>
                    <p>{!! $ticket->message !!}</p>
                </div>

                <!-- Display Replies -->
                @foreach($ticket->replies as $reply)
                    @php
                        // Determine role and badge
                        $roleText = 'Owner';
                        $badgeClass = 'bg-success';

                        if ($reply->user && $reply->user->hasRole('admin')) {
                            $roleText = 'Admin';
                            $badgeClass = 'bg-danger';
                        } elseif ($reply->user && $reply->user->hasRole('consultant')) {
                            $roleText = 'Operator';
                            $badgeClass = 'bg-info';
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

            <!-- Reply Form -->
            <form id="replyForm" onsubmit="return submitReplyWithQuill()">
                @csrf
                <div class="form-group mt-4">
                    <label for="message">Your Reply</label>
                    <div id="reply-editor-container" style="height: 150px;"></div>
                    <input type="hidden" name="message" id="reply-message">
                </div>
                <button type="submit" id="submitReplyButton" class="btn btn-primary mt-3">Send Reply</button>
                <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-secondary mt-3">Back to Support Tickets</a>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Owner Details -->
<div class="modal fade" id="ownerDetailsModal" tabindex="-1" aria-labelledby="ownerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ownerDetailsModalLabel">Owner Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="ownerName">{{ $ticket->fullname }}</span></p>
                <p><strong>Email:</strong> <span id="ownerEmail">{{ $ticket->email_address }}</span></p>
                <p><strong>Phone:</strong> <span id="ownerPhone">{{ $ticket->phone_number ?? 'N/A' }}</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    // Trigger modal when "Owner" link is clicked
    document.getElementById('viewOwnerDetails').addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('ownerDetailsModal'), {});
        modal.show();
    });
</script>
<script>
    const replyQuill = new Quill('#reply-editor-container', { theme: 'snow' });
    const ticketId = {{ $ticket->id }};
    let lastReplyTimestamp = "{{ $ticket->replies->last()?->created_at ?? $ticket->created_at }}"; // Track last reply timestamp
let lastReplyId = null; // Track the ID of the latest reply to avoid duplication

// Fetch new replies
function fetchNewReplies() {
    $.ajax({
        url: `{{ route('admin.support-tickets.fetch-replies', $ticket->id) }}`,
        method: 'GET',
        data: { lastReplyTimestamp: lastReplyTimestamp },
        success: function(response) {
            if (response.newReplies && response.newReplies.length > 0) {
                response.newReplies.forEach(reply => {
                    // Avoid duplicating already appended replies
                    if (reply.id !== lastReplyId && $(`[data-reply-id="${reply.id}"]`).length === 0) {
                        const roleText = reply.user_role === 'admin' ? 'Admin' : 
                                         (reply.user_role === 'consultant' ? 'Operator' : 'Owner');
                        const badgeClass = reply.user_role === 'admin' ? 'bg-danger' :
                                           (reply.user_role === 'consultant' ? 'bg-info' : 'bg-success');

                        $('#replies-container').append(`
                            <div class="reply mt-3 p-3" style="background-color: #f7f7f7;" data-reply-id="${reply.id}">
                                <p><span class="badge ${badgeClass}">${roleText}</span> ${reply.user_name} said:
                                <span class="text-muted">${reply.created_at}</span></p>
                                <p>${reply.message}</p>
                            </div>
                        `);

                        // Update lastReplyId
                        lastReplyId = reply.id;
                    }
                });

                // Update lastReplyTimestamp
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

// Submit reply using Quill editor
function submitReplyWithQuill() {
    const replyMessageInput = document.querySelector('input[name=message]');
    replyMessageInput.value = replyQuill.root.innerHTML;

    const submitReplyButton = document.getElementById('submitReplyButton');
    submitReplyButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Sending...";
    submitReplyButton.disabled = true;

    $.ajax({
        url: "{{ route('admin.support-tickets.reply', $ticket->id) }}",
        method: 'POST',
        data: { _token: "{{ csrf_token() }}", message: replyMessageInput.value },
        success: function(response) {
            const roleText = response.user_role === 'admin' ? 'Admin' :
                             (response.user_role === 'consultant' ? 'Operator' : 'Owner');
            const badgeClass = response.user_role === 'admin' ? 'bg-danger' :
                               (response.user_role === 'consultant' ? 'bg-info' : 'bg-success');

            // Append the reply directly after submission
            $('#replies-container').append(`
                <div class="reply mt-3 p-3" style="background-color: #f7f7f7;" data-reply-id="${response.id}">
                    <p><span class="badge ${badgeClass}">${roleText}</span> ${response.user_name} said:
                    <span class="text-muted">${new Date().toLocaleString()}</span></p>
                    <p>${response.message}</p>
                </div>
            `);

            // Update lastReplyId and lastReplyTimestamp to prevent duplication
            lastReplyId = response.id;
            lastReplyTimestamp = new Date().toISOString(); // Use the current time for the next poll

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

    return false; // Prevent the form from refreshing the page
}

    
</script>
@endpush
