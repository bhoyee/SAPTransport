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

					        <a class="nav-link active" href="{{ route('admin.dashboard') }}">


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

			

					        <a class="nav-link submenu-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-user" aria-expanded="false" aria-controls="submenu-user">

						        <span class="nav-icon">

				
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                                </svg>

						         </span>

		                         <span class="nav-link-text">Users</span>

		                         <span class="submenu-arrow">

		                             <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

										<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>

									</svg>

							</span>

					        </a>

					        <div id="submenu-user" class="collapse submenu submenu-user" data-bs-parent="#menu-accordion">

						        <ul class="submenu-list list-unstyled">

                                <li class="submenu-item">
                                    <a class="submenu-link" href="{{ route('admin.users.create') }}">Create Users</a> <!-- Correct the route -->
                                </li>


                                <li class="submenu-item"><a class="submenu-link" href="{{ route('admin.users.index') }}">Manage Users</a></li>

								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.users.deleted-users') }}">Deleted Users</a>
								</li>


                          
						        </ul>

					        </div>

					    </li>



                          <!-- create and manage booking-->
                          <li class="nav-item has-submenu">

			

                            <a class="nav-link submenu-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-book" aria-expanded="false" aria-controls="submenu-book">

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

                            <div id="submenu-book" class="collapse submenu submenu-book" data-bs-parent="#menu-accordion">

                                <ul class="submenu-list list-unstyled">

                                <li class="submenu-item">
                                    <a class="submenu-link" href="{{ url('/') }}">Book My Trip</a> <!-- Correct the route -->
                                </li>


                                <li class="submenu-item">
                                    <a class="submenu-link" href="{{ route('admin.bookForSomeone') }}">Book For Someone</a>
                                </li>

                                <li class="submenu-item">
								<a class="submenu-link" href="{{ route('admin.bookings.manage') }}">Manage Booking</a>
                                </li>

								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.bookings.confirm-search') }}">Confirm Booking</a>
								</li>
				


                                </ul>

                            </div>

                            </li>


			
						<li class="nav-item has-submenu">

					        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->

					        <a class="nav-link submenu-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-2" aria-expanded="false" aria-controls="submenu-2">

						        <span class="nav-icon">

						        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->

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

	                             </span><!--//submenu-arrow-->

					        </a><!--//nav-link-->

					        <div id="submenu-2" class="collapse submenu submenu-2" data-bs-parent="#menu-accordion">

						        <ul class="submenu-list list-unstyled">
							
								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.payment.search') }}">Make Payment</a>
								</li>
								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.payment.cash') }}">Record Cash Payment</a>
								</li>



								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.payments.index') }}">Manage Payments</a>
								</li>



						        </ul>

					        </div>

					    </li><!--//nav-item-->


						<!-- manage invoice 	     -->

                        <li class="nav-item has-submenu">

			

					        <a class="nav-link submenu-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-invoice" aria-expanded="false" aria-controls="submenu-invoice">

						        <span class="nav-icon">

				
								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-card-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

									<path fill-rule="evenodd" d="M14.5 3h-13a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>

									<path fill-rule="evenodd" d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5z"/>

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

					        <div id="submenu-invoice" class="collapse submenu submenu-invoice" data-bs-parent="#menu-accordion">

						        <ul class="submenu-list list-unstyled">

								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.invoices.manage') }}">Manage Invoices</a>
								</li>


								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.invoices.createCustomForm') }}">Create Custom Invoice</a>
								</li>
								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.customInvoices') }}">Manage Custom Invoice</a>
								</li>

								
						        </ul>

					        </div>

					    </li>


												<!-- manage sales and report 	     -->

												<li class="nav-item has-submenu">

			

							<a class="nav-link submenu-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-reports" aria-expanded="false" aria-controls="submenu-reports">

								<span class="nav-icon">


						<svg fill="#000000" width="1.5em" height="1.5em" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.002 512.002" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <g> <path d="M128.257,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533s8.533-3.823,8.533-8.533 S132.967,392.533,128.257,392.533z"></path> <path d="M179.457,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533s8.533-3.823,8.533-8.533 S184.167,392.533,179.457,392.533z"></path> <path d="M247.468,273.067h-68.267c-4.719,0-8.533,3.823-8.533,8.533s3.814,8.533,8.533,8.533h68.267 c4.719,0,8.533-3.823,8.533-8.533S252.187,273.067,247.468,273.067z"></path> <path d="M213.334,324.267h-34.133c-4.719,0-8.533,3.823-8.533,8.533c0,4.71,3.814,8.533,8.533,8.533h34.133 c4.719,0,8.533-3.823,8.533-8.533C221.868,328.09,218.053,324.267,213.334,324.267z"></path> <path d="M358.401,298.667c-9.412,0-17.067-7.654-17.067-17.067c0-4.71-3.814-8.533-8.533-8.533s-8.533,3.823-8.533,8.533 c0,15.855,10.914,29.107,25.6,32.922v1.212c0,4.71,3.814,8.533,8.533,8.533c4.719,0,8.533-3.823,8.533-8.533v-1.212 c14.686-3.814,25.6-17.067,25.6-32.922c0-18.825-15.309-34.133-34.133-34.133c-9.412,0-17.067-7.654-17.067-17.067 c0-9.412,7.654-17.067,17.067-17.067c9.412,0,17.067,7.654,17.067,17.067c0,4.71,3.814,8.533,8.533,8.533 s8.533-3.823,8.533-8.533c0-15.855-10.914-29.107-25.6-32.922v-1.212c0-4.71-3.814-8.533-8.533-8.533 c-4.719,0-8.533,3.823-8.533,8.533v1.212c-14.686,3.814-25.6,17.067-25.6,32.922c0,18.825,15.309,34.133,34.133,34.133 c9.412,0,17.067,7.654,17.067,17.067C375.468,291.012,367.813,298.667,358.401,298.667z"></path> <path d="M333.057,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533c4.71,0,8.533-3.823,8.533-8.533 S337.768,392.533,333.057,392.533z"></path> <path d="M435.457,409.6c4.71,0,8.533-3.823,8.533-8.533s-3.823-8.533-8.533-8.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533 S430.747,409.6,435.457,409.6z"></path> <path d="M384.257,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533c4.71,0,8.533-3.823,8.533-8.533 S388.968,392.533,384.257,392.533z"></path> <path d="M349.868,102.4h34.133c4.719,0,8.533-3.823,8.533-8.533c0-4.71-3.814-8.533-8.533-8.533h-34.133 c-4.719,0-8.533,3.823-8.533,8.533C341.334,98.577,345.149,102.4,349.868,102.4z"></path> <path d="M230.657,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533s8.533-3.823,8.533-8.533 S235.367,392.533,230.657,392.533z"></path> <path d="M128.001,290.133h17.067c4.719,0,8.533-3.823,8.533-8.533s-3.814-8.533-8.533-8.533h-17.067 c-4.719,0-8.533,3.823-8.533,8.533S123.282,290.133,128.001,290.133z"></path> <path d="M128.001,341.333h17.067c4.719,0,8.533-3.823,8.533-8.533c0-4.71-3.814-8.533-8.533-8.533h-17.067 c-4.719,0-8.533,3.823-8.533,8.533C119.468,337.51,123.282,341.333,128.001,341.333z"></path> <path d="M469.334,0H42.668c-4.719,0-8.533,3.823-8.533,8.533v494.933c0,3.447,2.074,6.562,5.265,7.885 c1.058,0.435,2.167,0.648,3.268,0.648c2.219,0,4.403-0.87,6.033-2.5l45.167-45.167l45.167,45.167 c3.337,3.337,8.73,3.337,12.066,0l28.1-28.1l28.1,28.1c3.337,3.337,8.73,3.337,12.066,0l28.1-28.1l11.034,11.034 c3.337,3.336,8.73,3.336,12.066,0l28.1-28.1l28.1,28.1c3.337,3.336,8.73,3.336,12.066,0l19.567-19.567l19.567,19.567 c3.337,3.336,8.73,3.336,12.066,0l45.167-45.167l28.1,28.1c0.171,0.179,0.35,0.341,0.538,0.495c0,0.009,0.008,0.009,0.008,0.009 v0.009c1.399,1.169,3.174,1.894,5.112,1.98c0.316,0.009,0.631,0.009,0.964-0.009h0.009c1.724-0.128,3.311-0.751,4.599-1.749 c0.461-0.35,0.879-0.751,1.254-1.186c1.289-1.493,2.074-3.43,2.082-5.564v-0.017V8.533C477.868,3.823,474.053,0,469.334,0z M460.801,448.734l-19.567-19.567c-3.337-3.336-8.73-3.336-12.066,0l-45.167,45.167l-19.567-19.567 c-3.336-3.337-8.73-3.337-12.066,0l-19.567,19.567l-28.1-28.1c-3.336-3.337-8.73-3.337-12.066,0l-28.1,28.1L253.501,463.3 c-3.337-3.337-8.73-3.337-12.066,0l-28.1,28.1l-28.1-28.1c-1.664-1.664-3.849-2.5-6.033-2.5c-2.185,0-4.369,0.836-6.033,2.5 l-28.1,28.1l-45.167-45.167c-3.337-3.337-8.73-3.337-12.066,0l-36.634,36.634V17.067h409.6V448.734z"></path> <path d="M77.057,409.6c4.71,0,8.533-3.823,8.533-8.533s-3.823-8.533-8.533-8.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533 S72.347,409.6,77.057,409.6z"></path> <path d="M128.001,238.933h17.067c4.719,0,8.533-3.823,8.533-8.533s-3.814-8.533-8.533-8.533h-17.067 c-4.719,0-8.533,3.823-8.533,8.533S123.282,238.933,128.001,238.933z"></path> <path d="M281.857,409.6c4.71,0,8.533-3.823,8.533-8.533s-3.823-8.533-8.533-8.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533 S277.147,409.6,281.857,409.6z"></path> <path d="M179.201,187.733h51.2c4.719,0,8.533-3.823,8.533-8.533s-3.814-8.533-8.533-8.533h-51.2 c-4.719,0-8.533,3.823-8.533,8.533S174.482,187.733,179.201,187.733z"></path> <path d="M128.001,187.733h17.067c4.719,0,8.533-3.823,8.533-8.533s-3.814-8.533-8.533-8.533h-17.067 c-4.719,0-8.533,3.823-8.533,8.533S123.282,187.733,128.001,187.733z"></path> <path d="M264.534,221.867h-85.333c-4.719,0-8.533,3.823-8.533,8.533s3.814,8.533,8.533,8.533h85.333 c4.719,0,8.533-3.823,8.533-8.533S269.253,221.867,264.534,221.867z"></path> <path d="M128.001,102.4h136.533c4.719,0,8.533-3.823,8.533-8.533c0-4.71-3.814-8.533-8.533-8.533H128.001 c-4.719,0-8.533,3.823-8.533,8.533C119.468,98.577,123.282,102.4,128.001,102.4z"></path> </g> </g> </g> </g></svg>


								</span>

								<span class="nav-link-text">Sales / Reports</span>

								<span class="submenu-arrow">

									<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

										<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>

									</svg>

							</span>

							</a>

							<div id="submenu-reports" class="collapse submenu submenu-reports" data-bs-parent="#menu-accordion">

								<ul class="submenu-list list-unstyled">

								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.payments.report') }}">Payments Report</a>
								</li>

								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.reports.userPaymentReport') }}">User Payments Report</a>
								</li>
								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.bookings.report') }}">Booking Report</a>
								</li>
								<li class="submenu-item">
                                <a class="submenu-link" href="{{ route('admin.users.report') }}">Users Report</a>
                                </li>

								<li class="submenu-item">
									<a class="submenu-link" href="{{ route('admin.salesReport') }}">Sales Report</a>
								</li>


								
								</ul>

							</div>

							</li>



							<!-- broadcast messages -->


							<li class="nav-item has-submenu">

			

								<a class="nav-link submenu-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-message" aria-expanded="false" aria-controls="submenu-message">

									<span class="nav-icon">


								<svg fill="#000000" width="1.5em" height="1.5em" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.002 512.002" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <g> <path d="M128.257,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533s8.533-3.823,8.533-8.533 S132.967,392.533,128.257,392.533z"></path> <path d="M179.457,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533s8.533-3.823,8.533-8.533 S184.167,392.533,179.457,392.533z"></path> <path d="M247.468,273.067h-68.267c-4.719,0-8.533,3.823-8.533,8.533s3.814,8.533,8.533,8.533h68.267 c4.719,0,8.533-3.823,8.533-8.533S252.187,273.067,247.468,273.067z"></path> <path d="M213.334,324.267h-34.133c-4.719,0-8.533,3.823-8.533,8.533c0,4.71,3.814,8.533,8.533,8.533h34.133 c4.719,0,8.533-3.823,8.533-8.533C221.868,328.09,218.053,324.267,213.334,324.267z"></path> <path d="M358.401,298.667c-9.412,0-17.067-7.654-17.067-17.067c0-4.71-3.814-8.533-8.533-8.533s-8.533,3.823-8.533,8.533 c0,15.855,10.914,29.107,25.6,32.922v1.212c0,4.71,3.814,8.533,8.533,8.533c4.719,0,8.533-3.823,8.533-8.533v-1.212 c14.686-3.814,25.6-17.067,25.6-32.922c0-18.825-15.309-34.133-34.133-34.133c-9.412,0-17.067-7.654-17.067-17.067 c0-9.412,7.654-17.067,17.067-17.067c9.412,0,17.067,7.654,17.067,17.067c0,4.71,3.814,8.533,8.533,8.533 s8.533-3.823,8.533-8.533c0-15.855-10.914-29.107-25.6-32.922v-1.212c0-4.71-3.814-8.533-8.533-8.533 c-4.719,0-8.533,3.823-8.533,8.533v1.212c-14.686,3.814-25.6,17.067-25.6,32.922c0,18.825,15.309,34.133,34.133,34.133 c9.412,0,17.067,7.654,17.067,17.067C375.468,291.012,367.813,298.667,358.401,298.667z"></path> <path d="M333.057,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533c4.71,0,8.533-3.823,8.533-8.533 S337.768,392.533,333.057,392.533z"></path> <path d="M435.457,409.6c4.71,0,8.533-3.823,8.533-8.533s-3.823-8.533-8.533-8.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533 S430.747,409.6,435.457,409.6z"></path> <path d="M384.257,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533c4.71,0,8.533-3.823,8.533-8.533 S388.968,392.533,384.257,392.533z"></path> <path d="M349.868,102.4h34.133c4.719,0,8.533-3.823,8.533-8.533c0-4.71-3.814-8.533-8.533-8.533h-34.133 c-4.719,0-8.533,3.823-8.533,8.533C341.334,98.577,345.149,102.4,349.868,102.4z"></path> <path d="M230.657,392.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533s3.866,8.533,8.576,8.533s8.533-3.823,8.533-8.533 S235.367,392.533,230.657,392.533z"></path> <path d="M128.001,290.133h17.067c4.719,0,8.533-3.823,8.533-8.533s-3.814-8.533-8.533-8.533h-17.067 c-4.719,0-8.533,3.823-8.533,8.533S123.282,290.133,128.001,290.133z"></path> <path d="M128.001,341.333h17.067c4.719,0,8.533-3.823,8.533-8.533c0-4.71-3.814-8.533-8.533-8.533h-17.067 c-4.719,0-8.533,3.823-8.533,8.533C119.468,337.51,123.282,341.333,128.001,341.333z"></path> <path d="M469.334,0H42.668c-4.719,0-8.533,3.823-8.533,8.533v494.933c0,3.447,2.074,6.562,5.265,7.885 c1.058,0.435,2.167,0.648,3.268,0.648c2.219,0,4.403-0.87,6.033-2.5l45.167-45.167l45.167,45.167 c3.337,3.337,8.73,3.337,12.066,0l28.1-28.1l28.1,28.1c3.337,3.337,8.73,3.337,12.066,0l28.1-28.1l11.034,11.034 c3.337,3.336,8.73,3.336,12.066,0l28.1-28.1l28.1,28.1c3.337,3.336,8.73,3.336,12.066,0l19.567-19.567l19.567,19.567 c3.337,3.336,8.73,3.336,12.066,0l45.167-45.167l28.1,28.1c0.171,0.179,0.35,0.341,0.538,0.495c0,0.009,0.008,0.009,0.008,0.009 v0.009c1.399,1.169,3.174,1.894,5.112,1.98c0.316,0.009,0.631,0.009,0.964-0.009h0.009c1.724-0.128,3.311-0.751,4.599-1.749 c0.461-0.35,0.879-0.751,1.254-1.186c1.289-1.493,2.074-3.43,2.082-5.564v-0.017V8.533C477.868,3.823,474.053,0,469.334,0z M460.801,448.734l-19.567-19.567c-3.337-3.336-8.73-3.336-12.066,0l-45.167,45.167l-19.567-19.567 c-3.336-3.337-8.73-3.337-12.066,0l-19.567,19.567l-28.1-28.1c-3.336-3.337-8.73-3.337-12.066,0l-28.1,28.1L253.501,463.3 c-3.337-3.337-8.73-3.337-12.066,0l-28.1,28.1l-28.1-28.1c-1.664-1.664-3.849-2.5-6.033-2.5c-2.185,0-4.369,0.836-6.033,2.5 l-28.1,28.1l-45.167-45.167c-3.337-3.337-8.73-3.337-12.066,0l-36.634,36.634V17.067h409.6V448.734z"></path> <path d="M77.057,409.6c4.71,0,8.533-3.823,8.533-8.533s-3.823-8.533-8.533-8.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533 S72.347,409.6,77.057,409.6z"></path> <path d="M128.001,238.933h17.067c4.719,0,8.533-3.823,8.533-8.533s-3.814-8.533-8.533-8.533h-17.067 c-4.719,0-8.533,3.823-8.533,8.533S123.282,238.933,128.001,238.933z"></path> <path d="M281.857,409.6c4.71,0,8.533-3.823,8.533-8.533s-3.823-8.533-8.533-8.533h-0.085c-4.71,0-8.491,3.823-8.491,8.533 S277.147,409.6,281.857,409.6z"></path> <path d="M179.201,187.733h51.2c4.719,0,8.533-3.823,8.533-8.533s-3.814-8.533-8.533-8.533h-51.2 c-4.719,0-8.533,3.823-8.533,8.533S174.482,187.733,179.201,187.733z"></path> <path d="M128.001,187.733h17.067c4.719,0,8.533-3.823,8.533-8.533s-3.814-8.533-8.533-8.533h-17.067 c-4.719,0-8.533,3.823-8.533,8.533S123.282,187.733,128.001,187.733z"></path> <path d="M264.534,221.867h-85.333c-4.719,0-8.533,3.823-8.533,8.533s3.814,8.533,8.533,8.533h85.333 c4.719,0,8.533-3.823,8.533-8.533S269.253,221.867,264.534,221.867z"></path> <path d="M128.001,102.4h136.533c4.719,0,8.533-3.823,8.533-8.533c0-4.71-3.814-8.533-8.533-8.533H128.001 c-4.719,0-8.533,3.823-8.533,8.533C119.468,98.577,123.282,102.4,128.001,102.4z"></path> </g> </g> </g> </g></svg>


									</span>

									<span class="nav-link-text">Broadcast Messages</span>

									<span class="submenu-arrow">

										<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

											<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>

										</svg>

								</span>

								</a>

								<div id="submenu-message" class="collapse submenu submenu-message" data-bs-parent="#menu-accordion">

									<ul class="submenu-list list-unstyled">

									<li class="submenu-item">
										<a class="submenu-link" href="">Send Messages</a>
									</li>

									<li class="submenu-item">
										<a class="submenu-link" href="">Manage Messages</a>
									</li>
						

									
									</ul>

								</div>

								</li>




		


			


						<li class="nav-item has-submenu">

					        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->

					        <a class="nav-link submenu-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-4" aria-expanded="false" aria-controls="submenu-4">

						        <span class="nav-icon">

						        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->

								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ticket" viewBox="0 0 16 16">

									<path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6zM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5z"/>

								  </svg>

						         </span>

		                         <span class="nav-link-text">Support Tickets</span>

		                         <span class="submenu-arrow">

		                             <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

										<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>

									</svg>

							</span><!--//submenu-arrow-->

					        </a><!--//nav-link-->

					        <div id="submenu-4" class="collapse submenu submenu-4" data-bs-parent="#menu-accordion">

						        <ul class="submenu-list list-unstyled">


								<li class="submenu-item">
									<!-- resources/views/admin/partials/sidebar.blade.php -->
<li class="submenu-item"><a class="submenu-link" href="{{ route('admin.support-tickets.index') }}">Manage Support Tickets</a></li>

								</li>

						        </ul>

					        </div>

					    </li><!--//nav-item-->



					   



					    
<!-- 
					    <li class="nav-item">

					      

					        <a class="nav-link" href="help.html">

						        <span class="nav-icon">

						        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-question-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

                                          <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>

                                          <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>

                                        </svg>

						         </span>

		                         <span class="nav-link-text">Help</span>

					        </a>

					    </li>				     -->

				    </ul><!--//app-menu-->

			    </nav><!--//app-nav-->

			    <div class="app-sidepanel-footer">

				    <nav class="app-nav app-nav-footer">

					    <ul class="app-menu footer-menu list-unstyled">

						    <li class="nav-item">

						        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->

						        <a class="nav-link" href="{{ route('admin.settings') }}">



							        <span class="nav-icon">

							            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear" fill="currentColor" xmlns="http://www.w3.org/2000/svg">

                                        	  <path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/>

                                        	  <path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/>

                                        	</svg>

							        </span>

			                        <span class="nav-link-text">Settings</span>

						        </a><!--//nav-link-->

						    </li><!--//nav-item-->



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





                           