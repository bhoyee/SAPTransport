<div class="row justify-content-between align-items-center">
    <div class="col-auto">
        <a id="sidepanel-toggler" class="sidepanel-toggler d-inline-block d-xl-none" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img">
                <title>Menu</title>
                <path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"></path>
            </svg>
        </a>
    </div>
    <div class="col">
        <form class="app-search-form">   
            <input type="text" placeholder="Search..." name="search" class="form-control search-input">
            <button type="submit" class="btn search-btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>
    <div class="col-auto">
        <div class="app-utility-item app-user-dropdown dropdown">
            <a class="dropdown-toggle" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#">
                <img src="{{ asset('assets/images/user.png') }}" alt="user profile">
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Account</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="{{ route('logout') }}" 
                      onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      Log Out
                    </a>
                </li>
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
