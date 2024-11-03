<div class="row justify-content-between align-items-center">
    <!-- Sidepanel Toggler for Mobile -->
    <div class="col-auto">
        <a id="sidepanel-toggler" class="sidepanel-toggler d-inline-block d-xl-none" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img">
                <title>Menu</title>
                <path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"></path>
            </svg>
        </a>
    </div>

    <!-- Search Box -->
    <div class="app-search-box col">
        <form class="app-search-form">
            <input type="text" placeholder="Search..." name="search" class="form-control search-input">
            <button type="submit" class="btn search-btn btn-primary" value="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <!-- Utility Section (Notifications, User Dropdown) -->
    <div class="app-utilities col-auto">
        <!-- Notifications Dropdown -->
        <div class="app-utility-item app-notifications-dropdown dropdown">
            <a class="dropdown-toggle no-toggle-arrow" id="notifications-dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" title="Notifications">
                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-bell icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2z"/>
                    <path fill-rule="evenodd" d="M8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                </svg>
                <span class="icon-badge">{{ $unreadCount ?? 0 }}</span>
            </a>

            <div class="dropdown-menu p-0" aria-labelledby="notifications-dropdown-toggle">
                <div class="dropdown-menu-header p-3">
                    <h5 class="dropdown-menu-title mb-0">Notifications</h5>
                </div>
                <div class="dropdown-menu-content" id="notifications-list">
                    @if(isset($consultantNotifications) && $consultantNotifications->count() > 0)
                        @foreach($consultantNotifications as $notification)
                            <div class="item p-3 open-notification" data-id="{{ $notification->id }}" data-bs-toggle="modal" data-bs-target="#notificationModal">
                                <div class="row gx-2 justify-content-between align-items-center">
                                    <div class="col-auto">
                                        <img class="profile-image" src="{{ asset('assets/images/profiles/profile-1.png') }}" alt="">
                                    </div>
                                    <div class="col">
                                        <div class="info">
                                            <div class="desc {{ $notification->status == 'unread' ? 'fw-bold' : '' }}">
                                                {{ $notification->message }}
                                            </div>
                                            <div class="meta">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center p-3">No new notifications.</p>
                    @endif
                </div>


                <div class="dropdown-menu-footer p-2 text-center">
                    <a href="{{ route('notifications.index') }}">View all</a>
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div class="app-utility-item app-user-dropdown dropdown">
            <a class="dropdown-toggle" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <img src="{{ Auth::user()->profile_image ? asset('storage/profile_images/' . Auth::user()->profile_image) : asset('/images/user.png') }}" alt="User Profile" class="img-thumbnail" width="150">
            </a>
            <ul class="dropdown-menu" aria-labelledby="user-dropdown-toggle">
            <li><a class="dropdown-item" href="{{ route('account.settings') }}">Account</a></li>

                <li><a class="dropdown-item" href="{{ route('admin.settings') }}">Settings</a></li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
                </li>
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
<!-- Custom Modal Structure -->
<div id="customModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="close-modal">&times;</span>
        <h5 id="customModalTitle">Notification Details</h5>
        <div id="customModalBody">Loading...</div>
    </div>
</div>
<style>
/* Custom Modal Styles */
.custom-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.custom-modal-content {
    background-color: white;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    max-width: 600px;
    border-radius: 8px;
}

.close-modal {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close-modal:hover,
.close-modal:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

#customModalBody {
    margin-top: 20px;
}
</style>


<!-- Notification JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the modal and elements
    const customModal = document.getElementById('customModal');
    const modalBody = document.getElementById('customModalBody');
    const closeModal = document.querySelector('.close-modal');

    // When the user clicks on the close (X), close the modal
    closeModal.onclick = function() {
        customModal.style.display = "none";
    };

    // When the user clicks anywhere outside the modal, close it
    window.onclick = function(event) {
        if (event.target === customModal) {
            customModal.style.display = "none";
        }
    };

    // Attach click event listener to notification items
    // Event delegation for dynamically added notifications
    document.getElementById('notifications-list').addEventListener('click', function(e) {
        if (e.target.closest('.open-notification')) {
            e.preventDefault();

            const notificationItem = e.target.closest('.open-notification');
            const notificationId = notificationItem.getAttribute('data-id');
            modalBody.innerHTML = "Loading..."; // Show loading state initially

            // Open the custom modal
            customModal.style.display = "flex";

            // Fetch the notification details
            fetch(`/notifications/${notificationId}/fetch`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) throw new Error(data.error);

                // Display the notification details
                modalBody.innerHTML = `<p>${data.message}</p><small>Received: ${data.created_at}</small>`;

                // Mark the notification as read
                return fetch(`/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
            })
            .then(() => {
                // Remove bold styling to show it’s read
                notificationItem.querySelector('.desc').classList.remove('fw-bold');

                // Update unread count
                updateUnreadCount();
            })
            .catch(error => {
                modalBody.innerHTML = `<p class="text-danger">Error loading notification. Please try again later.</p>`;
                console.error('Failed to load notification details:', error);
            });
        }
    });

    // Poll for recent notifications every 15 seconds
    setInterval(fetchRecentNotifications, 15000);

    // Function to fetch the latest unread count and recent notifications from the server
    function fetchRecentNotifications() {
        fetch('/notifications/fetch-recent', {  // Make sure this route matches your setup
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            // Update the unread count
            const unreadCount = data.unreadCount;
            const badgeElement = document.querySelector('.icon-badge');

            if (unreadCount > 0) {
                badgeElement.textContent = unreadCount;
                badgeElement.style.display = 'inline-block';
            } else {
                badgeElement.style.display = 'none';
            }

            // Optionally, update the recent notifications dropdown
            const notificationsList = document.getElementById('notifications-list');
            notificationsList.innerHTML = ''; // Clear current list

            if (data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    notificationsList.innerHTML += `
                        <div class="item p-3 open-notification" data-id="${notification.id}" data-bs-toggle="modal" data-bs-target="#notificationModal">
                            <div class="row gx-2 justify-content-between align-items-center">
                                <div class="col-auto">
                                    <img class="profile-image" src="{{ asset('assets/images/profiles/profile-1.png') }}" alt="">
                                </div>
                                <div class="col">
                                    <div class="info">
                                        <div class="desc ${notification.status === 'unread' ? 'fw-bold' : ''}">
                                            ${notification.message}
                                        </div>
                                        <div class="meta">${notification.created_at}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                notificationsList.innerHTML = '<p class="text-center p-3">No new notifications.</p>';
            }
        })
        .catch(error => {
            console.error('Failed to fetch recent notifications:', error);
        });
    }

    // Call the function once to set the initial count on page load
    fetchRecentNotifications();

    // Function to fetch the latest unread count from the server
    function updateUnreadCount() {
        fetch('/notifications/recent', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Unread count response:', data); // Debugging log
            const unreadCount = data.unreadCount;
            const badgeElement = document.querySelector('.icon-badge');

            // Update the badge with the latest unread count
            if (unreadCount > 0) {
                badgeElement.textContent = unreadCount;
                badgeElement.style.display = 'inline-block';
            } else {
                badgeElement.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Failed to update the unread count:', error);
        });
    }
    
});

</script>
