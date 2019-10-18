<div class="container-scroller ps ps--theme_default">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-left navbar-brand-wrapper">
            <a class="navbar-brand brand-logo" href="#"><img src="{{ url('backend/assets/images/logo.png') }}" alt="" width="200"></a>
            <a class="navbar-brand brand-logo-mini" href="#"><img src="{{ url('backend/assets/images/logo.png') }}" alt=""></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-self-stretch align-items-center">
            <button class="navbar-toggler navbar-toggler align-self-center mr-2" type="button" data-toggle="minimize">
                <span class="mdi mdi-menu"><i class="fa fa-bars"></i></span>
            </button>
            <form class="form-inline mt-2 mt-md-0 d-none d-lg-block">
                <input class="form-control mr-sm-2 search" name="q" type="text" placeholder="Search">
            </form>
            <ul class="navbar-nav ml-lg-auto">
                <li class="nav-item nav-profile">
                    <div class="nav-link"> Master Admin<img src="{{ url('backend/assets/images/user2-160x160.png') }}" class="img-circle" alt="User Image"> </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-toggle="dropdown">
                        <i class="fa fa-bell"></i>
                    </a>
                    <div class="dropdown-menu navbar-dropdown notification-drop-down" aria-labelledby="notificationDropdown">
                        <div class="view-all-bg">
                        </div>
                        <label class="view-all">No Notification Found </label>
                    </div>
                </li>
                <li class="dropdown dropdown-user nav-item">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="true">
                        <i class="fa fa-cog"></i></a>
                    <ul class="dropdown-menu">
                        
                        <li><a href="{{route('profile')}}"><i class="mdi mdi-account-settings-variant"></i> My Profile</a></li>
                        
                        <li>
                            
 <a  href="{{ route('frontend_logout') }}" onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
                           <i class="mdi mdi-logout-variant"></i> Logout</a>
                           </a>
                           <form id="logout-form" action="{{ route('frontend_logout') }}" method="POST" style="display: none;">
                               {{ csrf_field() }}
                           </form>
                       

</li>
                    </ul>
                </li>
            </ul>
            <input type="hidden" name="url" id="url" value="">
        </div>
    </nav>
    
    
   
                          