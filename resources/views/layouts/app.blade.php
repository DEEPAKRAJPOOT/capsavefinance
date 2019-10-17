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

        <link rel="stylesheet" href="{{ asset('/frontend/outside/lending-asset/css/font.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ asset('/frontend/inside/css/site.css') }}">
       <script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('frontend/outside/js/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script>
var messages = {
    social_media_form_limit: "{{ config('common.SOCIAL_MEDIA_LINK') }}",
    document_form_limit: "{{ config('common.DOCUMENT_LIMIT') }}",
    req_this_field: "{{ trans('common.error_messages.req_this_field') }}",
    alphnum_hyp_uscore: "{{ trans('common.error_messages.alphnum_hyp_uscore') }}",
    alphnum_hyp_uscore_space: "{{ trans('common.error_messages.alphnum_hyp_uscore_space') }}",
    least_2_chars: "{{ trans('common.error_messages.least_2_chars') }}",
    least_3_chars: "{{ trans('common.error_messages.least_3_chars') }}",
    least_6_chars: "{{ trans('common.error_messages.least_6_chars') }}",
    least_10_chars: "{{ trans('common.error_messages.least_10_chars') }}",
    max_10_chars: "{{ trans('common.error_messages.max_20_chars') }}",
    max_15_chars: "{{ trans('common.error_messages.max_20_chars') }}",
    max_20_chars: "{{ trans('common.error_messages.max_20_chars') }}",
    max_30_chars: "{{ trans('common.error_messages.max_30_chars') }}",
    max_60_chars: "{{ trans('common.error_messages.max_60_chars') }}",
    max_100_chars: "{{ trans('common.error_messages.max_100_chars') }}",
    max_120_chars: "{{ trans('common.error_messages.max_120_chars') }}",
    enter_valid_url: "{{ trans('common.error_messages.enter_valid_url') }}",
    alpha_num: "{{ trans('common.error_messages.alpha_num') }}",
    num_hyp_space:"{{ trans('common.error_messages.num_hyp_space') }}",
    alphnum_spacial1: "{{ trans('common.error_messages.alphnum_spacial1') }}",
    alphnum_hyp_uscore_space_fslace: "{{ trans('common.error_messages.alphnum_hyp_uscore_space_fslace') }}",
    alphnum_hyp_uscore_space_dot: "{{ trans('common.error_messages.alphnum_hyp_uscore_space_dot') }}",
    alphnum_space_spacial_chars: "{{ trans('common.error_messages.alphnum_space_spacial_chars') }}",
    invalid_format: "{{ trans('common.error_messages.invalid_format') }}",
    invalid_url: "{{ trans('common.error_messages.invalid_url') }}",
    invalid_email: "{{ trans('common.error_messages.invalid_email') }}",
    invalid_mobile: "{{ trans('common.error_messages.invalid_mobile') }}",
    invalid_post_box: "{{ trans('common.error_messages.invalid_post_box') }}",
    invalid_fax_no: "{{ trans('common.error_messages.invalid_fax_no') }}",
    invalid_postal_code: "{{ trans('common.error_messages.invalid_postal_code') }}",
    invalid_amount: "{{ trans('common.error_messages.invalid_amount') }}",
};

</script>

        @yield('additional_css')
</head>
    <!-- dashboard part -->
    <body >
        <header>
            <div class="container-fluid header-bg">
                <div class="logo">
                    <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>

                </div>
            </div>
        </header>
        @if(Session::has('message'))
        <div class=" my-alert-success alert bg-success base-reverse alert-dismissible" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
            {{ Session::get('message') }}
        </div>
        {{Session::forget('message')}}
        @endif

        @if(Session::has('error'))
        <!--<div class=" my-alert-danger alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('error') }}
        </div>-->
        @endif


        @if (count($errors) > 0)
   
        <!--<div class="alertMsgBox">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>-->
        @endif
        @yield('content')
        
        @yield('jscript')
    </body>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"></script>
    <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"></script>
</html>