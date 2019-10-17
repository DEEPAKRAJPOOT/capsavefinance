@extends('layouts.withought_login')
@section('content')

 <div class="content-wrap height-auto">
    <div class="login-section">
	  <div class="logo-box text-center">
	     <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
	  </div>
	  <div class="right-sign">
	      <div class="rounded-circle border-circle">
		    <a href="#"><i class="fa fa-check"></i></a>
		  </div>
	  </div>
	 <div class="thanks-conent">
	   <h2 class="head-line2 marT20 marB15 text-center">{{trans('master.otpThanks.message')}}</h2>
	   <a href="{{url('/')}}/login" class="btn btn-sign verify-btn">{{trans('master.otpThanks.login')}}</a>
	 </div>


	</div>
 </div>

        <script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>
    @endsection


