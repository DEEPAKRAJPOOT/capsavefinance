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
        <link rel="shortcut icon" href="">
        {{-- project theme css --}}

        <!--Datepicker-->
        <link rel='stylesheet' href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}">

        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('backend/theme/assets/plugins/fontawesome/css/font-awesome.min.css')}}" type="text/css" />
        <link rel="stylesheet" href="{{ asset('backend/theme/assets/plugins/animate/animate.min.css')}}" type="text/css" />        
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/plugins/datatables/css/datatables.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/plugins/datatables/css/dataTables.bootstrap.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/plugins/datatables/Buttons/css/buttons.dataTables.min.css')}}">
<!--        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/css/pratham.min.css')}}">-->
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/css/backend-common.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/css/site.css')}}">
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

        <header>
            <div class="container-fluid header-bg">
                <div class="logo">
                    <a href="#"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
                </div>
            </div>
        </header>


        <section>
            <div class="container-fluid">
                <div class="row">

                    <!--sidebar-->
                    <div id="header" class="col-md-2">
                        <div class="list-section">
                            <div class="kyc">
                                <ul class="menu-left">
                                    <li><a class="active" onclick="window.location.href = '{{ route('backend_dashboard') }}'">DashBoard</a></li>
                                    <li><a class="" onclick="window.location.href = '{{ route('show_user') }}'">Manage Users</a></li>
                                    <li><a href="#">Professional Access</a></li>
                                    <li><a href="#">Manage Documents</a></li>
                                </ul>


                            </div>
                        </div>
                    </div>
                    <!--sidebar-->
                    <div class="col-md-10 dashbord-white">
                        @yield('content')
                    </div>
                </div>
            </div>
        </section>


        {{-- Theme JS --}}
        <script src="{{ asset('backend/theme/assets/plugins/jquery/jquery-2.2.4.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/bootstrap-hover/bootstrap-hover.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/datatables/js/datatable.min.js') }}" type="text/javascript"></script>
        <script>
            window.setTimeout(function () {
                $(".alert").fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            }, 2000);


               

        </script>



       



        @yield('jscript')

    </body>
</html>
