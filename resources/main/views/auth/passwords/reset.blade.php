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
    <!-- dashboard part -->
    <body class="login-page">
        <section>
            <div class="content-wrap height-auto">
                <div class="login-section">
                    <div class="logo-box text-center marB20">
                        <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
                        <h2 class="head-line2 marT25">Change Password</h2>
                    </div>

                    <div class="sign-up-box">

                        <form class="resetPassword" autocomplete="off" enctype="multipart/form-data" method="POST" action="{{ url('change') }}" id="resetPassword">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="pwd">Old Password</label>
                                        <input type="password" class="form-control"  placeholder="Enter Old Password" name="current-password" id="current-password" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="pwd">Old Password</label>
                                        <input type="password" class="form-control" placeholder="Enter New Password" name="new-password" required id="new-password">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="pwd">Confirm New Password</label>
                                        <input type="password" class="form-control"  placeholder="Enter Confirm New Password" name="confirm-new-password" required id="confirm-new-password">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <input type='submit' class='btn btn-sign verify-btn' name='next' value='Submit' />
                                    <!--<a href="sign-in.html" class="btn btn-sign verify-btn">Submit</a>-->
                                </div>
                            </div>
                        </form>


                    </div>


                </div>
            </div>
        </section>
    </body>
</html>


