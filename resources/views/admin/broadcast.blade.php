@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
    $isAdmin = auth()->user()->hasRole('admin');
    $isConsultant = auth()->user()->hasRole('consultant');
@endphp

@extends($layout)

@section('content')
<div class="container">
    <h1>Send Broadcast Message</h1>

    <form id="broadcast-form">
        @csrf
        <div class="mb-3">
            <label for="recipientType" class="form-label">Recipient Type</label>
            <select id="recipientType" name="recipient_type" class="form-select" required>
                @if ($isAdmin)
                    <option value="all">All</option>
                    <option value="passenger">Passengers</option>
                    <option value="staff">Staff</option>
                    <option value="individual">Individual</option>
                @elseif ($isConsultant)
                    <option value="passenger">Passengers</option>
                    <option value="individual">Individual</option>
                @endif
            </select>
        </div>

        <div class="mb-3" id="individualEmailField" style="display: none;">
            <label for="individualEmail" class="form-label">Email Address</label>
            <input type="email" id="individualEmail" name="individual_email" class="form-control">
        </div>

        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" id="subject" name="subject" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="messageBody" class="form-label">Message</label>
            <div id="messageBody" style="height: 200px;"></div>
            <textarea id="messageBodyInput" name="message_body" hidden></textarea>
        </div>

        <button type="submit" class="btn btn-primary" id="sendButton">
            <span id="spinner" class="spinner-border spinner-border-sm" style="display: none;"></span> Send
        </button>
    </form>
</div>

@endsection

@push('scripts')
<!-- Include Quill library -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var quill = new Quill('#messageBody', { theme: 'snow' });
    
    document.getElementById('broadcast-form').addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('messageBodyInput').value = quill.root.innerHTML;

        // Show spinner and disable button
        document.getElementById('sendButton').disabled = true;
        document.getElementById('spinner').style.display = 'inline-block';

        // Perform AJAX request
        fetch("{{ route('admin.broadcast.send') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(Object.fromEntries(new FormData(this))),
        }).then(response => response.json()).then(data => {
            alert(data.success);

            // Hide spinner and re-enable button
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('sendButton').disabled = false;

            // Clear subject, Quill editor, and hidden textarea
            document.getElementById('subject').value = '';
            quill.root.innerHTML = '';
            document.getElementById('messageBodyInput').value = '';
        }).catch(() => {
            alert("An error occurred while sending the message.");
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('sendButton').disabled = false;
        });
    });

    document.getElementById('recipientType').addEventListener('change', function() {
        document.getElementById('individualEmailField').style.display = this.value === 'individual' ? 'block' : 'none';
    });
});
</script>

@endpush