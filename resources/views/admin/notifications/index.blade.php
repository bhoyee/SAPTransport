@extends('admin.layouts.admin-layout')

@section('content')
    <h1 class="app-page-title">Notifications</h1>

    <div class="app-card app-card-details shadow-sm mb-4">
        <div class="app-card-body p-4">
            <!-- Filter Dropdown -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div> <!-- Empty div for alignment -->

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ request('type', 'All') }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="{{ route('notifications.index', ['type' => 'all']) }}">All</a></li>
                        <li><a class="dropdown-item" href="{{ route('notifications.index', ['type' => 'booking']) }}">Booking</a></li>
                        <li><a class="dropdown-item" href="{{ route('notifications.index', ['type' => 'payment']) }}">Payment</a></li>
                        <li><a class="dropdown-item" href="{{ route('notifications.index', ['type' => 'message']) }}">Message</a></li>
                    </ul>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="app-card shadow-sm mb-4">
                <div class="app-card-body">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <div class="card mb-3 notification-card" data-id="{{ $notification->id }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge 
                                                @if($notification->type === 'booking') bg-primary
                                                @elseif($notification->type === 'payment') bg-success
                                                @elseif($notification->type === 'message') bg-secondary
                                                @endif
                                            ">
                                                {{ ucfirst($notification->type) }}
                                            </span>
                                            <h5 class="card-title" style="font-size: 1rem;">
                                                {{ $notification->message }}
                                            </h5>
                                            <p class="card-text" style="font-size: 0.875rem;">
                                                {{ $notification->created_at->diffForHumans() }}
                                                <span class="text-muted">| {{ $notification->related_user_name }}</span>
                                            </p>
                                        </div>
                                        
                                        <!-- Mark as Read Button -->
                                        @if($notification->status == 'unread')
                                            <button class="btn btn-sm btn-primary mark-as-read" data-id="{{ $notification->id }}">
                                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
                                                Mark as Read
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $notifications->links('pagination::bootstrap-4') }}
                        </div>

                    @else
                        <p>No notifications found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Attach click event listener to the mark-as-read button
    $('.mark-as-read').on('click', function(e) {
        e.preventDefault();

        const notificationId = $(this).data('id');
        const notificationCard = $(this).closest('.notification-card');
        const markAsReadButton = $(this);
        const spinner = markAsReadButton.find('.spinner-border'); // Find the spinner inside the button

        // Show the spinner and disable the button
        spinner.show();
        markAsReadButton.prop('disabled', true); // Disable the button to prevent multiple clicks

        // Make the AJAX request to mark the notification as read
        $.ajax({
            url: `/notifications/${notificationId}/mark-as-read`,  // Use the POST route
            method: 'POST',  // POST request
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
            },
            success: function(response) {
                console.log('Notification marked as read:', response);

                // Update the notification UI to show it's read
                notificationCard.removeClass('unread').addClass('read');
                markAsReadButton.remove(); // Remove the "Mark as Read" button

                // Dynamically update the unread count in the badge
                updateUnreadBadge(response.unreadCount);
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
                
                // Hide the spinner and re-enable the button in case of an error
                spinner.hide();
                markAsReadButton.prop('disabled', false);
            },
            complete: function() {
                spinner.hide(); // Hide the spinner after request completes
            }
        });
    });

    // Function to update the unread badge with the given count
    function updateUnreadBadge(unreadCount) {
        const badgeElement = $('.icon-badge');
        
        if (unreadCount > 0) {
            badgeElement.text(unreadCount).show();
        } else {
            badgeElement.hide();
        }
    }

    // Poll for unread count updates every 30 seconds (30000 milliseconds)
    setInterval(fetchUnreadCount, 30000);  // Call fetchUnreadCount every 30 seconds

    // Function to fetch the latest unread count from the server
    function fetchUnreadCount() {
        $.ajax({
            url: '/notifications/recent',  // Fetch recent notifications
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(data) {
                const unreadCount = data.unreadCount;
                updateUnreadBadge(unreadCount);
            },
            error: function() {
                console.error('Error fetching unread count.');
            }
        });
    }
});
</script>
@endpush
