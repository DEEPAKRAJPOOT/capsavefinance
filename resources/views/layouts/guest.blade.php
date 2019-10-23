<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
      <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{url('backend/signup-assets/css/style.css')}}">

      
    </head>
   <body>

    <header>
        <div class="container">
            <div class="d-flex">

                <a href="#"><img src="{{url('backend/signup-assets/images/logo.png')}}" alt="logo" width="70px"> </a>

            </div>

    </header>


        @yield('content')






 <script src="{{url('backend/signup-assets/js/jquery-3.4.1.min.js')}}"> </script>


 <script>
    $(".sign-UP .btn").click(function() {
        $(".otp-section").fadeIn();
        $("body").addClass("scroll-hiddin");

    })


    $(".section-header button").click(function() {
        $(".otp-section").fadeOut();
        $("body").removeClass("scroll-hiddin");

    })

 </script>


 </body>

 </html>
