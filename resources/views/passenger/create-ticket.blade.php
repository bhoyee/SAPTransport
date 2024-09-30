@extends('layouts.passenger')

@section('title', 'Passenger Open-Ticket')

@section('content')
<h1 class="app-page-title">Open Ticket</h1>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">Create a Support Ticket</div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return submitFormWithQuill()">
                @csrf

                <!-- Name and Email -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" value="{{ $user->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly>
                    </div>
                </div>

                <!-- Subject -->
                <div class="mb-3">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" class="form-control" id="subject" value="{{ old('subject') }}" required>
                </div>

                <!-- Department and Priority -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department">Department</label>
                        <select name="department" id="department" class="form-control" required>
                            <option value="support">Support</option>
                            <option value="sales">Sales</option>
                            <option value="billing">Billing</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="priority">Priority</label>
                        <select name="priority" id="priority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <!-- Message (Quill Rich Text Editor) -->
                <div class="mb-3">
                    <label for="message">Message</label>
                    <div id="editor-container" style="height: 200px;"></div>
                    <!-- Hidden input to store the Quill editor content -->
                    <input type="hidden" name="message" id="message">
                </div>

                <!-- File Attachment -->
                <div class="mb-3">
                    <label for="attachment">Attachment (Optional, .docx, .doc, .pdf, images only)</label>
                    <input type="file" name="attachment" class="form-control">
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="mb-3">
                    <button type="submit" id="submitButton" class="btn btn-primary">
                        Submit Ticket
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                </div>
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
    // Initialize Quill editor
    const quill = new Quill('#editor-container', {
        theme: 'snow'
    });

    // On form submission, pass Quill content into hidden input field and add spinner
    function submitFormWithQuill() {
        const messageInput = document.querySelector('input[name=message]');
        messageInput.value = quill.root.innerHTML; // Grab Quill's content

        // Add spinner to submit button
        const submitButton = document.getElementById('submitButton');
        submitButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Submitting...";
        submitButton.disabled = true; // Disable the button to prevent multiple submissions

        return true; // Proceed with form submission
    }
</script>
@endpush
