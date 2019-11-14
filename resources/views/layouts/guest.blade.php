<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet"> <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{url('backend/signup-assets/css/style.css')}}">
        <link rel="stylesheet" href="{{ url('frontend/assets/css/perfect-scrollbar.min.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/jsgrid.min.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/jsgrid-theme.min.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/uploadfile.css') }}">
        <link rel="stylesheet" href="{{ url('frontend/assets/css/data-table.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/style.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/custom.css') }}" />
        <script src="{{url('backend/signup-assets/js/jquery-3.4.1.min.js')}}"></script>
        <script src="{{url('backend/signup-assets/js/jquery.validate.js')}}"></script>
    </head>
    <body>

        <header>
            <div class="container">
                <div class="d-flex">

                    <a href="#"><img src="{{url('backend/signup-assets/images/logo.png')}}" alt="logo" width="70px"> </a>

                </div>

        </header>


        @yield('content')


  <div class="isloader" style="display:none;">
        
        <img src="{{asset('backend/assets/images/loader.gif')}}">
    </div>
<style>
        .error
        {

            color:red;
        }
        .isloader{
            
            position: fixed;
            
top: 0;
left: 0;
width: 100%;
height: 100%;
background: rgba(0,0,0,.6);
display: flex;
flex-wrap: wrap;
justify-content: center;
align-content: center;
z-index: 9;
        }
    </style>
        <script>
            $(".sign-UP .btn").click(function () {
                $(".otp-section").fadeIn();
                $("body").addClass("scroll-hiddin");

            })


            $(".section-header button").click(function () {
                $(".otp-section").fadeOut();
                $("body").removeClass("scroll-hiddin");

            })

        </script>
        @yield('scripts')

    </body>

</html>
