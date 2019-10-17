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
	    <a href="#"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
		<h2 class="head-line2 marT25">Sign in (Individual)</h2>
	  </div>

	  <div class="sign-up-box">

		<form class="loginForm" autocomplete="off" method="POST" action="{{ route('backend_login_open') }}" id="frmLogin">
                                    {{ csrf_field() }}

		 <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Username</label>
			  <input type="text" class="form-control"  placeholder="Enter username" name="username" value="{{ old('username') }}" id="username" required>
			</div>
		  </div>
         </div>

		 <div class="row">
		   <div class="col-md-12">
			<div class="form-group">
			   <label class="float-left" for="pwd">Password</label>
			   <a class="float-right theme-colors" href="#">Forgot Password?</a></label>
			  <input type="password" id="password" class="form-control" placeholder="Enter Password" name="password" required>
			</div>
		  </div>

		  </div>

		 <div class="row">
         <div class="col-md-12">
         
		 
                  <input type='submit' class='btn btn-sign verify-btn' name='Sign-in' value='Sign in' />
		 </div>
		</div>
	</form>


	  </div>


	</div>
 </div>
</section>
    </body>
</html>


