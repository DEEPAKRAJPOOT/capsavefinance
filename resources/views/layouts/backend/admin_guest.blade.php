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
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('backend/theme/assets/plugins/animate/animate.min.css')}}" type="text/css" />
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/theme/assets/css/pratham.min.css')}}">        
        @yield('addtional_css')
        <style>
            .error
            {
                color:red !important;
            }
        </style>
    </head>
    <body>
        @if (Session::has('message'))
      
        <div class="alert-box alert-success alert-dismissible" role="alert"">
            <ul><li>{{ Session::get('message') }}</li></ul>
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

        {{-- Theme JS --}}
        <script src="{{ asset('backend/theme/assets/plugins/jquery/jquery-2.2.4.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/matchMedia/matchMedia.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/countUp/countUp.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/countdown/jquery.countdown.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/scrollspy/scrollspy.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/sparkline/jquery.sparkline.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/bootstrap-hover/bootstrap-hover.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/plugins/compose/js/bootstrap-wysihtml5.js') }}"></script>
        <script src="{{ asset('backend/theme/assets/js/chart.js') }}" type="text/javascript"></script>
        <script src="{{ asset('backend/theme/assets/js/main.js') }}" type="text/javascript"></script>
        {{-- Custom JS --}}
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        @yield('jscript')
    </body>

    <script>
        function unsetError(ele){
            $(ele+' +span').remove();
        }

        function setError(ele, msg){
            $(ele).after('<span class="error">'+msg+'</span>');
        }
    </script>

    @yield('jscript')
</html>
