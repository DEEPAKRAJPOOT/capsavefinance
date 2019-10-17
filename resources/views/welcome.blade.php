@extends('layouts.withought_login')
@section('content')

<div class="content-wrap">
    <div class="login-section">
	  <div class="logo-box text-center">
	    <img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive" >
	  </div>

            <div class="d-flex justify-content-center align-items-center height-100">
	     <div class="login-link">
		   <a href="#" class="register">{{trans('master.register_as')}}</a>
		   <a href="{{url('/')}}/sign-up" class="btn individual-btn marB15">{{trans('master.individual')}}</a>
		   <a href="{{url('/')}}/company-sign-up" class="btn corporate-btn">{{trans('master.corporate')}}</a>

                   <div class="col-md-12">
                       <p class="text-center have-account marB15 marT10" style="font-size: 14px;display: flex;justify-content: center;font-weight: 700;color: #000;">Already have an account ? <a href="{{url('/')}}/login">Sign in</a></p>
		 </div>
		 </div>
	  </div>
    </div>
 </div>
@endsection



