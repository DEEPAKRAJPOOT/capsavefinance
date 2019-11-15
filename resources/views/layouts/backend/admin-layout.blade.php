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

</head>

<body class="sidebar-icon-only">
    <div class=" container-scroller">
        @include('layouts.backend.partials.admin-header')
        <div class="container-fluid page-body-wrapper">
            <div class="row row-offcanvas row-offcanvas-right">
                @include('layouts.backend.partials.admin-sidebar')
                <ul class="main-menu">
                    <li>
                        <a href="company-details.php" class="active">Application details</a>
                    </li>
                    <li>
                        <a href="cam.php">CAM</a>
                    </li>
                    <li>
                        <a href="residence.php">FI/RCU</a>
                    </li>
                    <li>
                        <a href="Collateral.php">Collateral</a>
                    </li>
                    <li>
                        <a href="notes.php">Notes</a>
                    </li>
                    <li>
                        <a href="commercial.php">Submit Commercial</a>
                    </li>
                </ul>
                <!-- partial -->
                @yield('content')
                
                @include('layouts.backend.partials.admin-footer')
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
    @yield('jscript')
</body>
</html>
