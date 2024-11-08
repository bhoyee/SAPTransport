@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('title', 'Manage Messages')

@section('content')
<div class="container mt-5">
    <h1 class="app-page-title">Manage Messages</h1>

    <div class="card">
        <div class="card-body">
            <table id="messages-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Subject</th>
                        <th>Sender Name</th>
                        <th>Sent At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody> <!-- Leave empty for AJAX to populate -->
            </table>
        </div>
    </div>
</div>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    console.log("Document ready: Initializing DataTable and fetching messages.");

    // Initialize DataTable and define columns
    var messagesTable = $('#messages-table').DataTable({
        columns: [
            { data: null, title: "S/N", render: (data, type, row, meta) => meta.row + 1 },
            { data: "subject", title: "Subject" },
            { data: "sender.name", title: "Sender Name", defaultContent: "N/A" },
            { data: "created_at", title: "Sent At", render: function(data) {
                return new Date(data).toLocaleString();
            }},
            { data: null, title: "Actions", orderable: false, render: function(data) {
                return `<a href="/admin/message/${data.id}/view" class="btn btn-info btn-sm">View</a>`;
            }}
        ]
    });

    // Function to fetch messages
    function fetchMessages() {
        console.log("Fetching messages...");
        
        $.ajax({
            url: "{{ route('admin.fetch-messages') }}",
            method: 'GET',
            success: function(data) {
                console.log("Messages data received:", data);

                if (data.error) {
                    console.error("Error in response:", data.error);
                    alert("Failed to load messages.");
                } else if (data.length === 0) {
                    console.log("No messages available.");
                    messagesTable.clear().draw();
                } else {
                    messagesTable.clear().rows.add(data).draw();
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
                console.log("XHR response:", xhr.responseText);
                alert("An error occurred while loading messages.");
            }
        });
    }

    // Initial fetch and periodic polling
    fetchMessages();
    setInterval(fetchMessages, 5000);
});
</script>
@endsection
