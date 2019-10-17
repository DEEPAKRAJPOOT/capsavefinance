<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Page Title --}}
        <title>@yield('pageTitle')</title>

        {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('backend/theme/assets/img/favicon.png') }}">
        {{-- project theme css --}}

        <!--Datepicker-->
        <link rel='stylesheet' href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}">

        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('backend/theme/assets/plugins/fontawesome/css/font-awesome.min.css')}}" type="text/css" />
        <link rel="stylesheet" href="{{ asset('backend/theme/assets/plugins/animate/animate.min.css')}}" type="text/css" />        
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/plugins/datatables/css/datatables.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/plugins/datatables/css/dataTables.bootstrap.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/plugins/datatables/Buttons/css/buttons.dataTables.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/css/pratham.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/css/backend-common.css')}}">
        @yield('addtional_css')
        
    </head>
    <style>
        .sidebar-menu li > a:hover {
    background-color: #489fbb;
}
.sidebar-menu li.active > a {
    background-color: #48a0bc;
}
.table-border-top {
    padding: 5px;
}
input#delete_selected_user {
    margin-top: 10px;
    margin-bottom: 10px;
    margin-left: 10px;
}
.navbar {
    background-color: #48a0bc;
}
 .btn-success.search:hover,  .btn-success.search:focus,  .btn-success.search:active ,.btn-success.search:active:hover {
    color: #fff;
    background-color: #20465a;
    border-color: #20465a;
}
.btn-success.search {
    color: #fff;
    background-color: #48a0bc;
    border-color: #48a0bc;
}
  a {
    color: #48a0bc;
}

     </style>
    <body>
       <div class="prtm-wrapper">
            <header class="prtm-header">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span><span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                        <button class="c-hamburger c-hamburger--htra prtm-bars pull-right"> <span>toggle menu</span> </button>
                        <div class="prtm-logo">
                            <a class="navbar-brand" href="#"><img class="img-responsive display-ib" src="{{ asset('frontend/outside/images/brand-logo-white.png')}}" alt="logo" width="226" height="31"></a>
                        </div>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse" data-hover="dropdown">
                       <!-- <ul class="nav navbar-nav">
                            <li class="active">
                                <div class="prtm-search-icon"> <a href="javascript:;" class="prtm-menu-search"><i class="fa fa-search overlay-1"></i></a>
                                    <div class="prtm-navbar-search">
                                        <div class="prtm-search-area"></div>
                                        <form class="prtm-search-form" method="post" role="search" action="javascript:;"> <span class="prtm-search-form-title fa fa-search"></span>
                                            <input placeholder="Type and hit enter" value="" name="s" type="text"> </form>
                                    </div>
                                </div>
                            </li>
                            <li class="dropdown hidden-xs hidden-sm hidden-md"> <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Mega <span class="caret"></span></a>
                                <div class="dropdown-menu prtm-mega-menu">
                                    <div class="prtm-mega-menu-wrap pad-all-lg">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2">
                                                <h4 class="sidenav-heading text-uppercase mrgn-b-md">Dashboards</h4>
                                                <ul class="list-unstyled">
                                                    <li><a href="index.html">Dashboard 1</a></li>
                                                    <li><a href="dashboard-v2.html">Dashboard 2</a></li>
                                                    <li><a href="dashboard-v3.html">Dashboard 3</a></li>
                                                </ul>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2">
                                                <h4 class="sidenav-heading text-uppercase mrgn-b-md">Features</h4>
                                                <ul class="list-unstyled">
                                                    <li><a href="ui-buttons.html">UI Elements</a></li>
                                                    <li><a href="notification.html">Components</a></li>
                                                    <li><a href="google-chart.html">Graph and Charts</a></li>
                                                    <li><a href="googlemap.html">Maps</a></li>
                                                </ul>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2">
                                                <h4 class="sidenav-heading text-uppercase mrgn-b-md">Layouts</h4>
                                                <ul class="list-unstyled">
                                                    <li><a href="index.html">Sidebar At left</a></li>
                                                    <li><a href="right-sidebar.html">Sidebar At right</a></li>
                                                    <li><a href="fixed-header.html">Fixed Header</a></li>
                                                </ul>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2">
                                                <h4 class="sidenav-heading text-uppercase mrgn-b-md">Pages</h4>
                                                <ul class="list-unstyled">
                                                    <li><a href="users-list.html">Users</a></li>
                                                    <li><a href="ecommerce-product.html">Ecommerce</a></li>
                                                    <li><a href="email.html">Mailbox</a></li>
                                                    <li><a href="login.html">Extra Pages</a></li>
                                                </ul>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                                <div class="prtm-sparkline">
                                                    <div class="prtm-sparkline-list bg-success clearfix prtm-card-box">
                                                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"> <span class="show count-item" data-count="5000">0</span> <span>New visitors</span> </div>
                                                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                            <div class="chart sparkline text-center" data-chart="sparkline" data-type="bar" data-height="50px" data-barwidth="6" data-width="100%" data-barspacing="2" data-barcolor="#ffffff" data-values="[9, 8, 9, 7, 6, 8, 7, 8]"> </div>
                                                        </div>
                                                    </div>
                                                    <div class="prtm-sparkline-list clearfix bg-info prtm-card-box">
                                                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"> <span class="show count-item" data-count="3000">0</span> <span>New Users</span> </div>
                                                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                            <div class="chart sparkline text-center" data-chart="sparkline" data-type="bar" data-height="50px" data-barwidth="6" data-width="100%" data-barspacing="2" data-barcolor="#ffffff" data-values="[5, 6, 8, 9, 5, 8, 4, 6]"> </div>
                                                        </div>
                                                    </div>
                                                    <div class="prtm-sparkline-list clearfix bg-secondary prtm-card-box">
                                                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"> <span class="show count-item" data-count="7000">0</span> <span>Active Users</span> </div>
                                                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                            <div class="chart sparkline text-center" data-chart="sparkline" data-type="bar" data-height="50px" data-barwidth="6" data-width="100%" data-barspacing="2" data-barcolor="#ffffff" data-values="[9, 8, 9, 7, 6, 8, 7, 8]"> </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="dropdown hidden-xs hidden-sm hidden-md"> <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">New<span class="caret"></span></a>
                                <ul class="dropdown-menu ">
                                    <li><a href="javascript:;">New Page 1</a></li>
                                    <li><a href="javascript:;">New Page 2</a></li>
                                    <li><a href="javascript:;">New Page 3</a></li>
                                </ul>
                            </li>
                        </ul> -->
                        <ul class="nav navbar-nav navbar-right">
                          <!--   <li class="dropdown"><a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell-o"> </i><span class="badge badge-danger">2</span></a>
                               <ul class="dropdown-menu dropdown-custom dropdown-notifi">
                                    <li> <strong class="drop-title"><span><i class="fa fa-bell-o" aria-hidden="true"></i></span>Notifications</strong><a href="javascript:;" class="pull-right bg-primary base-reverse">Marks As Read</a></li>
                                    <li>
                                        <a href="javascript:;" class="pos-relative">
                                            <div class="mrgn-b-xs"> <span class="notification-icon"><i class="fa fa-database text-danger"></i></span> <span class="notification-title">Database overload</span> <span class="notification-ago">3 min ago</span> </div>
                                            <p class="mrgn-all-none">Database overload due to incorrect queries</p>
                                            <div class="clearfix"></div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" class="pos-relative">
                                            <div class="mrgn-b-xs"> <span class="notification-icon"><i class="fa fa-circle-o-notch fa-spin text-success" aria-hidden="true"></i></span> <span class="notification-title">Installing App v1.2.1</span> <span class="notification-ago ">60 % Done</span> </div>
                                            <div class="progress progress-sm-height mrgn-all-none">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:60%"> </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" class="pos-relative">
                                            <div class="mrgn-b-xs"> <span class="notification-icon"><i class="fa fa-exclamation-triangle text-warning"></i></span> <span class="notification-title">Application Error</span> <span class="notification-ago ">10 min ago</span> </div>
                                            <p class="mrgn-all-none">failed to initialize the application due to error weblogic.application.moduleexception</p>
                                            <div class="clearfix"></div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <div class="mrgn-b-xs"> <span class="notification-icon"><i class="fa fa-server text-info"></i></span> <span class="notification-title">Server Status</span> <span class="notification-ago ">30GB Free Space</span> </div>
                                            <div class="progress progress-sm-height mrgn-all-none">
                                                <div class="progress-bar progress-bar-info" role="progressbar" style="width:40%"></div>
                                                <div class="progress-bar progress-bar-success" role="progressbar" style="width:10%"></div>
                                                <div class="progress-bar progress-bar-danger" role="progressbar" style="width:20%"></div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <div class="mrgn-b-xs"> <span class="notification-icon"><i class="fa fa-cogs text-success"></i></span> <span class="notification-title">Application Configured</span> <span class="notification-ago ">30 min ago</span> </div>
                                            <p class="mrgn-all-none">Your setting is updated on server Sav3060</p>
                                            <div class="clearfix"></div>
                                        </a>
                                    </li>
                                    <li> <a class="text-center" href="javascript:;"> See all notifications <i class="fa fa-angle-right mrgn-l-xs"></i> </a> </li>
                                </ul>
                            </li>-->
                             
                            <li class="dropdown"> 
                                @php 
                                $userId =  \Auth::user()->id;
                                $userDetails = Helpers::getUserDetail($userId);
                                @endphp
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    {{ isset($userDetails->first_name) ? ucfirst($userDetails->first_name)." ".$userDetails->last_name : '' }}
                                    <span class="caret"></span>
                                    @if(isset($userDetails->user_photo) &&  $userDetails->user_photo!="")
<!--                                        <img class="img-responsive display-ib mrgn-l-sm img-circle" src="{{ asset('storage/app/appDocs/profile/'.$userDetails->user_photo) }}" width="64" height="64">-->
                                        <img class="img-responsive display-ib mrgn-l-sm img-circle" src="{{ storage_path('app/appDocs/profile/45031551788131.png') }}" width="64" height="64">
                                    @else
                                        <img class="img-responsive display-ib mrgn-l-sm img-circle" src="{{ asset('backend/theme/assets/img/dami-user.png') }}" width="64" height="64">
                                    @endif
                                </a>
                                <ul class="dropdown-menu">
 
                                    <li><a href="{{ route('view_profile') }}"><i class="fa fa-user"></i> My Profile</a></li>
                                    <li><a href="{{ route('change_password') }}"><i class="fa fa-user"></i> Change password</a></li>
                                     
<li><a href="{{ route('backend_logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i>Logout</a></li>
                                    <form id="logout-form" action="{{ route('backend_logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </nav>
        </header>

           <div class="prtm-main">
            <div class="prtm-sidebar">
                <div class="prtm-sidebar-back"> 
                    @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                </div>
                <div class="prtm-sidebar-nav-wrapper">
                    <div class="prtm-sidebar-menu">
                        <nav class="sidebar-nav collapse">
                            <ul class="list-unstyled sidebar-menu">
                                <li class="sidenav-heading text-uppercase">Dashboard</li>
<!--                                <li class="active opened"><a href="{{ route('backend_dashboard') }}"><i class="fa fa-tachometer" aria-hidden="true"></i><span>Dashboard</span></a></li>
                                <li class="has-children opened"><a href="javascript:void(0);"><i class="fa fa-sliders" aria-hidden="true"></i><span>Manage Masters</span></a>
                                    <ul class="list-unstyled sub-menu">
                                        <li><a href="{{ route('manage_country') }}" class="active"><span>{{trans('master.manage_country')}}</span></a></li>
                                        <li><a href="{{ route('manage_state') }}" class="active"><span>{{trans('master.manage_state')}}</span></a></li>
                                        <li><a href="{{ route('manage_cluster') }}" class="active"><span>{{trans('master.manage_cluster')}}</span></a></li>
                                        <li><a href="{{ route('manage_right_type') }}" class="active"><span>{{trans('master.manage_right_type')}}</span></a></li>
                                        <li><a href="{{ route('manage_source') }}" class="active"><span>{{trans('master.manage_source')}}</span></a></li>
                                    </ul>
                                </li>-->
<!--                                <li class="has-children opened"><a href="javascript:void(0);"><i class="fa fa-user" aria-hidden="true"></i><span>Manage Users</span></a>
                                    <ul class="list-unstyled sub-menu">
                                        <li><a href="{{ route('manage_users') }}" class="active"><span>User list</span></a></li>
                                    </ul>
                                </li>-->

                                <li class="has-children opened"><a href="javascript:void(0);"><i class="fa fa-user" aria-hidden="true"></i><span>Manage Validator</span></a>
                                    <ul class="list-unstyled sub-menu">
                                        <li><a href="{{ route('show_scout') }}" class="active"><span>Validator list</span></a></li>
                                        <li><a href="{{ route('show_user') }}" class="active"><span>User list</span></a></li>
                                    </ul>
                                </li>

<!--                                <li class="has-children opened  "><a href="javascript:void(0);"><i class="fa fa-cogs" aria-hidden="true"></i><span>Manage Rights</span></a>
                                    <ul class="list-unstyled sub-menu">
                                        <li><a href="{{ route('manage_rights') }}" class="active"><span>{{trans('master.manage_right')}}</span></a></li>
                                    </ul>
                                </li>-->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
                <div class="prtm-content-wrapper">
                <div class="prtm-content">
                    @if(Session::has('message'))
                        <div class="alert bg-success base-reverse alert-dismissible fade in" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                            {{ Session::get('message') }} 
                        </div> 
                    @endif
                    @if (count($errors) > 0)
                    <div class="alertMsgBox">
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                    @yield('content')
                </div>
                <footer class="footer-wrapper">
                    <div class="prtm-footer clearfix">
                        <div class="row footer-area pad-lr-md">
                            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                <!--<p>Copyright 2017 Pratham Admin Theme | All Rights Reserved | Made With Love In India By The IRON Network</p>-->
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 text-right">
                                <a href="index.html"><img src="{{ asset('frontend/outside/images/brand-logo-white.png')}}" width="218" height="23" alt="footer logo"></a>
                            </div>
                        </div>
                    </div>
                    <a href="#" id="back-top" class="to-top scrolled"> <span class="to-top-icon"></span> </a>
                </footer>
            </div>
        </div>
    </div>
        

        {{-- Theme JS --}}
    <script src="{{ asset('backend/theme/assets/plugins/jquery/jquery-2.2.4.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/theme/assets/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/theme/assets/plugins/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/theme/assets/plugins/bootstrap-hover/bootstrap-hover.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/theme/assets/plugins/datatables/js/datatable.min.js') }}" type="text/javascript"></script>
<script>
    window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 2000);
</script>
    @yield('jscript')

                    </body>
                </html>
