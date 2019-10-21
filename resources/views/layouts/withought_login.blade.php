<!doctype html>
<html lang="en">

    <head>
        <title>Login V14</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

        <link href="{{asset('backend/assets/css/login-form.css')}}" type="text/css" rel="stylesheet">

    </head>

    <!-- dashboard part -->
    <body class="login-page">
        <section>
            @yield('content')

        </section>




    </body>
    <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('frontend/outside/js/validation/login.js') }}"></script>
   
</html>




