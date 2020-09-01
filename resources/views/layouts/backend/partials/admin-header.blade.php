<!-- partial:partials/_navbar.html -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-left navbar-brand-wrapper">
        <a class="navbar-brand brand-logo" href="{{ route('backend_dashboard') }}"><img src="{{url('backend/assets/images/logo.svg')}}"/></a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('backend_dashboard') }}"><img src="{{url('backend/assets/images/logo_mini.svg')}}"/></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-self-stretch align-items-center">
        <button class="navbar-toggler navbar-toggler align-self-center mr-2" type="button" data-toggle="minimize">
          <i class="fa fa-bars" aria-hidden="true"></i>
        </button>
<!--        <form class="form-inline mt-2 mt-md-0 d-none d-lg-block">
            <input class="form-control mr-sm-2 search" type="text" placeholder="Search">
        </form>-->
        <ul class="navbar-nav ml-lg-auto relative">
            <li class="nav-item nav-profile">
                <span style="color: #328964; position: absolute; right: 28px; min-width: 350px; top: 3px;"><b>Current System Time:</b><b id="_current_sys_date"> {{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}</b></span>
                <a class="nav-link" href="#" style="margin-top: 15px;">
                    <span style="color: #328964;">{{ucwords(Auth::user()->f_name.' '.Auth::user()->l_name)}}</span>
                    <img src="{{url('backend/assets/images/faces/face9.jpg')}}" />
                </a>
            </li>
            <!-- <li class="nav-item dropdown">
                <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-toggle="dropdown">
                    <i class="fa fa-bell-o" aria-hidden="true"></i>
                    <span class="count">7</span>
                </a>
                <div class="dropdown-menu navbar-dropdown notification-drop-down" aria-labelledby="notificationDropdown">
                    <a class="dropdown-item" href="#">
                        <i class="fa fa-birthday-cake text-success fa-fw"></i>
                        <span class="notification-text">Today is John's birthday</span>
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fa fa-phone text-danger fa-fw"></i>
                        <span class="notification-text">Call John Doe</span>
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fa fa-handshake-o text-primary fa-fw"></i>
                        <span class="notification-text">Meeting Alisa</span>
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fa fa-exclamation-triangle text-danger fa-fw"></i>
                        <span class="notification-text">Server space almost full</span>
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fa fa-bell text-warning fa-fw"></i>
                        <span class="notification-text">Payment Due</span>
                    </a>
                </div>
            </li> -->
            <!-- <li class="nav-item dropdown">
                <a class="nav-link count-indicator" id="MailDropdown" href="#" data-toggle="dropdown">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    <span class="count">4</span>
                </a>
                <div class="dropdown-menu navbar-dropdown mail-notification" aria-labelledby="MailDropdown">
                    <a class="dropdown-item" href="#">
                        <div class="sender-img">
                            <img src="{{url('backend/assets/images/faces/face6.jpg')}}" alt="">
                            <span class="badge badge-success">&nbsp;</span>
                        </div>
                        <div class="sender">
                            <p class="Sende-name">John Doe</p>
                            <p class="Sender-message">Hey, We have a meeting planned at the end of the day.</p>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="sender-img">
                            <img src="{{url('backend/assets/images/faces/face2.jpg')}}" alt="">
                            <span class="badge badge-success">&nbsp;</span>
                        </div>
                        <div class="sender">
                            <p class="Sende-name">Leanne Jones</p>
                            <p class="Sender-message">Can we schedule a call this afternoon?</p>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="sender-img">
                            <img src="{{url('backend/assets/images/faces/face3.jpg')}}" alt="">
                            <span class="badge badge-primary">&nbsp;</span>
                        </div>
                        <div class="sender">
                            <p class="Sende-name">Stella</p>
                            <p class="Sender-message">Great presentation the other day. Keep up the good work!</p>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="sender-img">
                            <img src="{{url('backend/assets/images/faces/face4.jpg')}}" alt="">
                            <span class="badge badge-warning">&nbsp;</span>
                        </div>
                        <div class="sender">
                            <p class="Sende-name">James Brown</p>
                            <p class="Sender-message">Need the updates of the project at the end of the week.</p>
                        </div>
                    </a>
                    <a href="#" class="dropdown-item view-all">View all</a>
                </div>
            </li> -->
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator" id="settingDropdown" href="#" data-toggle="dropdown">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                </a>
                <div class="dropdown-menu navbar-dropdown notification-drop-down" aria-labelledby="settingDropdown">
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="fa fa-user"></i>
                        <span class="notification-text">My Profile</span>
                    </a>
                    <a class="dropdown-item" href="{{ route('backend_logout') }}" onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out"></i>
                        <span class="notification-text">Logout</span>
                        <form id="logout-form" action="{{ route('backend_logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
                    </a>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
<!-- partial -->