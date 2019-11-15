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
</head>
    <!-- dashboard part -->
    <body class="">
	@include('layouts.front_header')
        
    @yield('content')
        
<<<<<<< HEAD
        @include('layouts.front_footer')
       

=======
    @include('layouts.admin_footer')
>>>>>>> 8ebf5dc48666c628e70d4ec5e47b1c8ac36ab6f8
</body>
<script src="{{ asset('backend/assets/js/jquery.min.js')}}" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="{{ asset('backend/assets/plugins/datatables/js/datatable.min.js') }}" type="text/javascript"></script>      
@yield('jscript')
</html>