<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Basic Page Needs-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Mobile Specific Metas-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- For Search Engine Meta Data  -->
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="author" content="{{ config('app.name') }}" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Page Title --}}
        <title>@yield('pageTitle')</title>
        {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('frontend/outside/images/favicon.png') }}">
        {{-- project css --}}
        <link  rel="stylesheet" href="{{ asset('frontend/outside/css/login3-style.css') }}">        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if IE]>
              <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->        
        @yield('addtional_css')
        <style>
            form#frmLogin  input#email , form#frmLogin  input#password 
            {position:relative;margin-bottom: 30px;}        

            form#frmLogin   label#email-error {
                position: absolute;
                top: 49px;
                left:17px;
                color: #f00;
                font-weight: normal;
                font-size: 14px;
            }
            
            form#frmLogin   label#password-error        {
                position: absolute;
                top: 50px;
                left: 5px;
                color: #f00;
                font-weight: normal;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <!-- Start Preloader -->
        <div id="preload-block">
            <div class="square-block"></div>
        </div>
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
        <!-- Preloader End -->
        @yield('content')
        <!-- Scripts -->
        <!-- initialize jQuery Library -->
        <script src="{{ asset('frontend/outside/js/jquery-2.2.4.min.js') }}"></script>
        <!-- for Bootstrap js -->
        <script src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>        
        <script src="{{ asset('frontend/outside/js/login.js') }}"></script>        
        <!--validation js-->
        <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
        <script type="text/javascript" src="{{ asset('common/js/validate_functions.js') }}"></script>
        @yield('jscript')
    </body>


    <script>
    window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
    });
}, 7000);
</script>
</html>
