<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/icon" href="#" />
    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="{{ asset('/frontend/outside/lending-asset/css/font.css') }}">
    <link rel="stylesheet" href="{{ asset('/frontend/outside/lending-asset/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/frontend/outside/lending-asset/css/site.css') }}">


    <title>DexterCapital</title>
</head>
<script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('frontend/outside/js/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>



<!-- dashboard part -->
<body >
    <header>
  <div class="container-fluid header-bg">
     <div class="logo">
	   <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
	 </div>
  </div>
</header>

    @yield('content')






</body>
</html>


