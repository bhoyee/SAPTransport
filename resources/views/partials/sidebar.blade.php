<div class="sidepanel-inner d-flex flex-column">
    <a href="#" id="sidepanel-close" class="sidepanel-close d-xl-none">&times;</a>
    <div class="app-branding">
        <a class="app-logo" href="{{ route('passenger.dashboard') }}">
            <img class="logo-icon me-2" src="{{ asset('assets/images/logo_old.png') }}" alt="logo">
            <span class="logo-text">SAPTransport</span>
        </a>
    </div>

    <nav class="app-nav app-nav-main flex-grow-1">
        <ul class="app-menu list-unstyled accordion" id="menu-accordion">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('passenger.dashboard') }}">
                    <span class="nav-icon">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-house-door">
                            <path fill-rule="evenodd" d="M7.646 1.146a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 .146.354v7a.5.5 0 0 1-.5.5H9.5a.5.5 0 0 1-.5-.5v-4H7v4a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .146-.354l6-6z"/>
                            <path fill-rule="evenodd" d="M13 2.5V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                        </svg>
                    </span>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>
            <!-- Add more menu items as needed -->
        </ul>
    </nav>
</div>
