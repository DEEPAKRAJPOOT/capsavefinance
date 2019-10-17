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
	   <h2 class="head-line2 marT20 marB15">Password Reset Successful</h2>
           <p class="p-conent">You have successfully changed your password. Click here to <a href="{{url('/')}}/login">login</a> with your new password.</p>
	 </div>


	</div>
 </div>
@endsection


