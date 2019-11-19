<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rentalpha</title>
    <link rel="shortcut icon" href="{{url('backend/assets/images/favicon.png')}}" />
    <!--<link rel="stylesheet" href="fonts/font-awesome/font-awesome.min.css" />-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{url('backend/assets/css/perfect-scrollbar.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/jsgrid.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/jsgrid-theme.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/uploadfile.css')}}" >
    <link rel="stylesheet" href="{{url('backend/assets/css/data-table.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/style.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/plugins/datatables/css/datatables.min.css')}}" />
    @yield('additional_css')
        <script src="{{url('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
    <script src="{{url('backend/assets/js/jquery.validate.js')}}"></script>
</head>

<body class="sidebar-icon-only">
    <div class=" container-scroller">
        @include('layouts.backend.partials.admin-header')
        <div class="container-fluid page-body-wrapper">
            <div class="row row-offcanvas row-offcanvas-right">
                <!-- partial -->
                @if(Session::has('message'))
                <div class=" my-alert-success alert bg-success base-reverse alert-dismissible header-msg" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    {{ Session::get('message') }}
                </div>
                @endif

                 @if(Session::has('error'))
                    <div class=" my-alert-danger alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {{ Session::get('error') }}
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
        
                @include('layouts.backend.partials.admin-sidebar')
                @yield('content')
              
                @include('layouts.backend.partials.admin-footer')
            </div>
        </div>
    </div>
    <script src="{{url('backend/assets/js/popper.min.js')}}"></script>
    <script src="{{url('backend/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{url('backend/assets/js/perfect-scrollbar.jquery.min.js')}}"></script>
    <script src="{{url('backend/assets/js/jsgrid.min.js')}}"></script>
    <script src="{{url('backend/assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{url('backend/assets/js/misc.js')}}"></script>
    <script src="{{url('backend/assets/plugins/datatables/js/datatable.min.js')}}"></script>
     <script src="{{url('common/js/iframePopup.js')}}"></script>
    
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();

            $(".trigger").click(function(){
                if($(this).hasClass("minus")){
                    $(this).removeClass("minus"); 
                }
                else{
                    $(this).addClass("minus");   
                }

            //$(".trigger").removeClass("minus");
            //$(this).addClass("minus");

            $(this).parents("tr").next(".dpr").slideToggle();
            });
        });
    </script>
    @yield('jscript')
</body>
</html>
