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
    <link rel="stylesheet" href="{{ asset('/frontend/inside/css/site.css') }}">
 <link href="https://fonts.googleapis.com/css?family=Lato:400,700,700i&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>DexterCapital</title>
</head>
<!--<script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>-->

<script src="{{ asset('frontend/outside/js/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>



<!-- dashboard part -->
<body class="login-page">
<section>
    @yield('content')
 
</section>


 

</body>
</html>


