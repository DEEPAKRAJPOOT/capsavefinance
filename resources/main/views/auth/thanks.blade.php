@extends('layouts.withought_login')
@section('content')
 <div class="content-wrap height-auto">
    <div class="login-section">
	  <div class="logo-box text-center">
	    <img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive">
	  </div>
	  <div class="right-sign">
	      <div class="rounded-circle border-circle">
		    <a href="#"><i class="fa fa-check"></i></a>
		  </div>
	  </div>
	 <div class="thanks-conent">
	   <h2 class="head-line2 marT20 marB15">Thank you for signing up with us!</h2>
	   <p class="p-conent">We have sent you an verification link on your email ID. Please verify your email ID to complete your registration.</p>
	 </div>


	</div>
 </div>


     
     <script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
     <script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>


@endsection


