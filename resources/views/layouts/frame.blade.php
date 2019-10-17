<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/icon" href="{{ asset('frontend/outside/images/favicon.png') }}"/>
        <!--select-2-css-->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <!--select-2-css-end-->
        <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,400i,700|Open+Sans:400,600" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('/frontend/inside/css/Hover_css_hover.css') }}">
        <link rel="stylesheet" href="{{ asset('/frontend/inside/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/frontend/inside/css/developer.css') }}">
        <!-- font-awesome CSS -->


        <!--Datepicker-->        
        <link rel='stylesheet' href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}">

        <link rel="stylesheet" href="{{ asset('frontend/inside/owl/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/inside/owl/css/owl.theme.default.min.css') }}">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/inside/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/inside/css/style.css') }}">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        
        @yield('additional_css')
        {{-- Page Title --}}
        <title>@yield('pageTitle')</title>
        
    </head>

    <body>
         <div id="preload-block">
            <div class="square-block"></div>
        </div>
        
        <div id="my-loading" style="display:none">
            <div class="square-blocks">
                <img src="{{ asset('frontend/inside/images/ajax-loader.gif') }}" alt="Loading...">
            </div>
        </div>
        
        @yield('content')

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous">
        </script>
        <script src="{{ asset('frontend/outside/js/login.js') }}"></script>   
        <script type="text/javascript" src="{{ asset('common/js/jquery.validate.js') }}"></script>
          <script>
               $(document).ready(function () {
              $('form').submit(function () {
            if($(this).valid()) {
                $('#my-loading').css('display','block');
               $(':submit', this).attr('disabled', 'disabled');
            }
        });
          });
              </script>
        @yield('jscript')
    </body>
</html>
