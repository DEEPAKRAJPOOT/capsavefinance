<!-- partial:partials/_navbar.html -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-left navbar-brand-wrapper">
        <a class="navbar-brand brand-logo" href="{{ route('backend_dashboard') }}"><img src="{{url('backend/assets/images/logo.svg')}}"/></a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('backend_dashboard') }}"><img src="{{url('backend/assets/images/logo_mini.svg')}}"/></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-self-stretch align-items-center">
        <button class="navbar-toggler navbar-toggler align-self-center mr-2" type="button" data-toggle="minimize">
          <i class="fa fa-bars" aria-hidden="true"></i>
        </button><ul class="navbar-nav mr-lg-auto relative">
            <li class="nav-item arka-logo">
                <a class="nav-link" href="#">
                    <img src="https://admin-arka-dev.zuron.in/frontend/assets/images/arka_email_logo.png" alt="Arka Fincap Limited">
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ml-lg-auto relative">
            <li class="nav-item nav-profile">
                <a class="nav-link" href="#" style="">
                    <span style="color: #328964;"><b>Current System Time:</b><b id="_current_sys_date"> {{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}</b></span>
                </a>
            </li>
            <li class="nav-item nav-profile">
                <a class="nav-link" href="#">
                    <span style="color: #328964;">{{ucwords(Auth::user()->f_name.' '.Auth::user()->l_name)}}</span>
                    <img src="{{url('backend/assets/images/faces/face9.jpg')}}" />
                </a>
            </li>
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
            <li class="nav-item arka-logo">
              <a class="nav-link" href="#">
                  <img src="https://admin-arka-dev.zuron.in/frontend/assets/images/arka_email_logo.png" alt="Arka Fincap Limited">
              </a>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
<!-- partial -->