<div class="sidepanel-inner d-flex flex-column">

    <a href="#" id="sidepanel-close" class="sidepanel-close d-xl-none">&times;</a>

    <div class="app-branding">

        <a class="app-logo" href="{{ url('/') }}">

            <img class="logo-icon me-2" src="{{ asset('assets/images/logo_old.png') }}" alt="logo">

            <span class="logo-text">SAPTransport</span>

        </a>

    </div>



    <nav class="app-nav app-nav-main flex-grow-1">

        <ul class="app-menu list-unstyled accordion" id="menu-accordion">

            <li class="nav-item">

                <a class="nav-link" href="{{ url('/') }}">

                    <span class="nav-icon">

                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-house-door">

                            <path fill-rule="evenodd" d="M7.646 1.146a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 .146.354v7a.5.5 0 0 1-.5.5H9.5a.5.5 0 0 1-.5-.5v-4H7v4a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .146-.354l6-6z"/>

                            <path fill-rule="evenodd" d="M13 2.5V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>

                        </svg>

                    </span>

                    <span class="nav-link-text">Home Page</span>

                </a>

            </li>

            <!-- Add more menu items as needed -->

            <li class="nav-item">

					        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->

					   
					            
					       <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">



						        <span class="nav-icon">

						        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-folder" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

                                  <path d="M9.828 4a3 3 0 0 1-2.12-.879l-.83-.828A1 1 0 0 0 6.173 2H2.5a1 1 0 0 0-1 .981L1.546 4h-1L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3v1z"/>

                                  <path fill-rule="evenodd" d="M13.81 4H2.19a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zM2.19 3A2 2 0 0 0 .198 5.181l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H2.19z"/>

                                </svg>

						         </span>

		                         <span class="nav-link-text">Dashboard</span>

					        </a><!--//nav-link-->

					    </li><!--//nav-item-->

                        <!-- create and manage user -->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-user" aria-expanded="false" aria-controls="submenu-user">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                    </svg>
                </span>
                <span class="nav-link-text">Users</span>
                <span class="submenu-arrow">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                    </svg>
                </span>
            </a>
            <div id="submenu-user" class="collapse submenu submenu-user {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">Create Users</a></li>
                    <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Manage Users</a></li>
                    <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('admin.users.deleted-users') ? 'active' : '' }}" href="{{ route('admin.users.deleted-users') }}">Deleted Users</a></li>
                </ul>
            </div>
        </li>



                          <!-- create and manage booking-->
            <li class="nav-item has-submenu">
    <a class="nav-link submenu-toggle {{ request()->routeIs('admin.bookings.*') || request()->is('/') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-book" aria-expanded="false" aria-controls="submenu-book">
        <span class="nav-icon">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-calendar-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
            </svg>
        </span>
        <span class="nav-link-text">Booking</span>
        <span class="submenu-arrow">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
        </span>
    </a>
    <div id="submenu-book" class="collapse submenu submenu-book {{ request()->routeIs('admin.bookings.*') || request()->is('/') ? 'show' : '' }}" data-bs-parent="#menu-accordion">
        <ul class="submenu-list list-unstyled">
            <li class="submenu-item">
                <a class="submenu-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Book My Trip</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.bookForSomeone') ? 'active' : '' }}" href="{{ route('admin.bookForSomeone') }}">Book For Someone</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.bookings.manage') ? 'active' : '' }}" href="{{ route('admin.bookings.manage') }}">Manage Booking</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.bookings.confirm-search') ? 'active' : '' }}" href="{{ route('admin.bookings.confirm-search') }}">Confirm Booking</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.bookings.assign-driver') ? 'active' : '' }}" href="{{ route('admin.bookings.assign-driver') }}">Assign Driver</a>
            </li>
        </ul>
    </div>
</li>

<!--payment menu-->
			
	<li class="nav-item has-submenu">
    <a class="nav-link submenu-toggle {{ request()->routeIs('admin.payment.*') || request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-2" aria-expanded="false" aria-controls="submenu-2">
        <span class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
            </svg>
        </span>
        <span class="nav-link-text">Payment</span>
        <span class="submenu-arrow">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
        </span>
    </a>
    <div id="submenu-2" class="collapse submenu submenu-2 {{ request()->routeIs('admin.payment.*') || request()->routeIs('admin.payments.*') ? 'show' : '' }}" data-bs-parent="#menu-accordion">
        <ul class="submenu-list list-unstyled">
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.payment.search') ? 'active' : '' }}" href="{{ route('admin.payment.search') }}">Make Payment</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.payment.cash') ? 'active' : '' }}" href="{{ route('admin.payment.cash') }}">Record Cash Payment</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">Manage Payments</a>
            </li>
        </ul>
    </div>
</li>



						<!-- manage invoice 	     -->

                      <li class="nav-item has-submenu">
    <a class="nav-link submenu-toggle {{ request()->routeIs('admin.invoices.*') || request()->routeIs('admin.customInvoices') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-invoice" aria-expanded="false" aria-controls="submenu-invoice">
        <span class="nav-icon">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-card-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M14.5 3h-13a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                <path fill-rule="evenodd" d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zM5 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5z"/>
                <circle cx="3.5" cy="5.5" r=".5"/>
                <circle cx="3.5" cy="8" r=".5"/>
                <circle cx="3.5" cy="10.5" r=".5"/>
            </svg>
        </span>
        <span class="nav-link-text">Invoices / Receipts</span>
        <span class="submenu-arrow">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
        </span>
    </a>
    <div id="submenu-invoice" class="collapse submenu submenu-invoice {{ request()->routeIs('admin.invoices.*') || request()->routeIs('admin.customInvoices') ? 'show' : '' }}" data-bs-parent="#menu-accordion">
        <ul class="submenu-list list-unstyled">
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.invoices.manage') ? 'active' : '' }}" href="{{ route('admin.invoices.manage') }}">Manage Invoices</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.invoices.createCustomForm') ? 'active' : '' }}" href="{{ route('admin.invoices.createCustomForm') }}">Create Custom Invoice</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.customInvoices') ? 'active' : '' }}" href="{{ route('admin.customInvoices') }}">Manage Custom Invoice</a>
            </li>
        </ul>
    </div>
</li>


												<!-- manage sales and report 	     -->

						<li class="nav-item has-submenu">
    <a class="nav-link submenu-toggle {{ request()->routeIs('admin.payments.report') || request()->routeIs('admin.reports.*') || request()->routeIs('admin.salesReport') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-reports" aria-expanded="false" aria-controls="submenu-reports">
        <span class="nav-icon">
            <svg fill="#000000" width="1.5em" height="1.5em" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.002 512.002" xml:space="preserve">
                <!-- SVG content here -->
            </svg>
        </span>
        <span class="nav-link-text">Sales / Reports</span>
        <span class="submenu-arrow">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
        </span>
    </a>
    <div id="submenu-reports" class="collapse submenu submenu-reports {{ request()->routeIs('admin.payments.report') || request()->routeIs('admin.reports.*') || request()->routeIs('admin.salesReport') ? 'show' : '' }}" data-bs-parent="#menu-accordion">
        <ul class="submenu-list list-unstyled">
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.payments.report') ? 'active' : '' }}" href="{{ route('admin.payments.report') }}">Payments Report</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.reports.userPaymentReport') ? 'active' : '' }}" href="{{ route('admin.reports.userPaymentReport') }}">User Payments Report</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.bookings.report') ? 'active' : '' }}" href="{{ route('admin.bookings.report') }}">Booking Report</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.users.report') ? 'active' : '' }}" href="{{ route('admin.users.report') }}">Users Report</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.salesReport') ? 'active' : '' }}" href="{{ route('admin.salesReport') }}">Sales Report</a>
            </li>
        </ul>
    </div>
</li>



							<!-- broadcast messages -->

<li class="nav-item has-submenu">
    <a class="nav-link submenu-toggle {{ request()->routeIs('admin.broadcast') || request()->routeIs('admin.manage-messages') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-message" aria-expanded="false" aria-controls="submenu-message">
        <span class="nav-icon">
            <svg fill="#000000" width="1.5em" height="1.5em" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.002 512.002" xml:space="preserve">
                <!-- SVG content here -->
            </svg>
        </span>
        <span class="nav-link-text">Broadcast Messages</span>
        <span class="submenu-arrow">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
        </span>
    </a>
    <div id="submenu-message" class="collapse submenu submenu-message {{ request()->routeIs('admin.broadcast') || request()->routeIs('admin.manage-messages') ? 'show' : '' }}" data-bs-parent="#menu-accordion">
        <ul class="submenu-list list-unstyled">
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.broadcast') ? 'active' : '' }}" href="{{ route('admin.broadcast') }}">Send Messages</a>
            </li>
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.manage-messages') ? 'active' : '' }}" href="{{ route('admin.manage-messages') }}">Manage Messages</a>
            </li>
        </ul>
    </div>
</li>




		


			


					
					<li class="nav-item has-submenu">
    <a class="nav-link submenu-toggle {{ request()->routeIs('admin.support-tickets.index') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-4" aria-expanded="false" aria-controls="submenu-4">
        <span class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ticket" viewBox="0 0 16 16">
                <path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6zM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5z"/>
            </svg>
        </span>
        <span class="nav-link-text">Support Tickets</span>
        <span class="submenu-arrow">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
        </span>
    </a>
    <div id="submenu-4" class="collapse submenu submenu-4 {{ request()->routeIs('admin.support-tickets.index') ? 'show' : '' }}" data-bs-parent="#menu-accordion">
        <ul class="submenu-list list-unstyled">
            <li class="submenu-item">
                <a class="submenu-link {{ request()->routeIs('admin.support-tickets.index') ? 'active' : '' }}" href="{{ route('admin.support-tickets.index') }}">Manage Support Tickets</a>
            </li>
        </ul>
    </div>
</li>

					<!--//nav-item-->



					   


				    </ul><!--//app-menu-->

			    </nav><!--//app-nav-->

			    <div class="app-sidepanel-footer">

				    <nav class="app-nav app-nav-footer">

					    <ul class="app-menu footer-menu list-unstyled">


<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
        <span class="nav-icon">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/>
                <path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/>
            </svg>
        </span>
        <span class="nav-link-text">Settings</span>
    </a>
</li>

<!--//nav-item-->



						    <li class="nav-item">

						        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->

						        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

							        <span class="nav-icon">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">

                              <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>

                              <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>

</svg>

							        </span>

			                        <span class="nav-link-text">Logout</span>

						        </a><!--//nav-link-->

						    </li><!--//nav-item-->

        </ul>

    </nav>

</div>





                           