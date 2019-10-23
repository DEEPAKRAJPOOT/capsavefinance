@extends('layouts.guest')
@section('content')

<div class="form-content no-padding sign-up mt-5">
    
	<div class="row justify-content-center align-items-center m-0">
		<div class="col-md-6">

			
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
	
</div>
 
    @endsection

