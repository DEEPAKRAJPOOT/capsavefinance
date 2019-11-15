<!doctype html>
<html lang="en">
    <head>
     <!-- Basic Page Needs-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Mobile Specific Metas-->
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- For Search Engine Meta Data  -->
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="author" content="{{ config('app.name') }}" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Page Title --}}
        <title>@yield('pageTitle')</title>
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/icon" href="#" />
        <!-- Bootstrap CSS -->

    	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{asset('backend/assets/css/style.css')}}">
        <link rel="stylesheet" href="{{asset('backend/assets/plugins/datatables/css/datatable.min.css')}}">
        <link rel="stylesheet" href="{{asset('backend/assets/plugins/datatables/css/dataTables.bootstrap.css')}}">
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
        
</head>


<body class="sidebar-icon-only">
    <div class=" container-scroller">
        @include('layouts.admin_header')
        <div class="container-fluid page-body-wrapper">
            <div class="row row-offcanvas row-offcanvas-right">
                <!-- partial -->
                @yield('content')
                
               @include('layouts.admin_footer')
            </div>
        </div>
    </div>
    <script src="{{url('backend/assets/js/jquery.min.js')}}"></script>
    <script src="{{url('backend/assets/js/popper.min.js')}}"></script>
    <script src="{{url('backend/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{url('backend/assets/js/perfect-scrollbar.jquery.min.js')}}"></script>
    <script src="{{url('backend/assets/js/jsgrid.min.js')}}"></script>
    <script src="{{url('backend/assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{url('backend/assets/js/misc.js')}}"></script>
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
       <script src="{{ asset('backend/theme/assets/plugins/datatables/js/datatable.min.js') }}" type="text/javascript"></script>

</body>
           
@yield('jscript')

</html>

        
        
   









