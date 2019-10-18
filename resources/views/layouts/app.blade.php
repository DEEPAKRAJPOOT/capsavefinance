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

        
</head>
    <!-- dashboard part -->
    <body class="">
	@include('layouts.admin_header')
        
        @yield('content')
        
        @include('layouts.admin_footer')
       

</body>
<script src="{{ asset('backend/assets/js/jquery.min.js')}}" type="text/javascript"></script>


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>


</html>

        
        
   









