<!doctype html>
<html lang="en">

    <head>
        <title>Rent Alpha Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

        <link href="{{asset('backend/assets/css/login-form.css')}}" type="text/css" rel="stylesheet">
        @yield('style')
    </head>

    <!-- dashboard part -->
    <body class="login-page">
        <section>
            @yield('content')
        </section>
    </body>
    <script src="{{ asset('backend/assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('frontend/outside/js/validation/login.js') }}"></script> 
</html>