@extends('layouts.withought_login')
@section('content')
            <div class="content-wrap height-auto">
    <div class="login-section">
	  <div class="logo-box text-center marB20">
	    <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
		<h2 class="head-line2 marT25">{{trans('master.chgPassForm.heading')}}</h2>
	  </div>

	  <div class="sign-up-box">
                @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
		<form class="form-horizontal" method="POST" action="{{ url('password/change') }}">
                            {{ csrf_field() }}

		 <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">{{trans('master.chgPassForm.old_pass')}}</label>
			  <input type="password" class="form-control required"  placeholder="{{trans('master.chgPassForm.enter_old_pass')}}" name="current-password" id="current-password" required>
			</div>
		  </div>
         </div>

		 <div class="row">
		   <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">{{trans('master.chgPassForm.new_pass')}}</label>
			  <input type="password" class="form-control required" placeholder="{{trans('master.chgPassForm.enter_new_pass')}}" name="new-password" id="new-password" required>
			</div>
		  </div>

		  </div>

		 <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">{{trans('master.chgPassForm.conf_new_pass')}}</label>
			  <input type="password" class="form-control required"  placeholder="{{trans('master.chgPassForm.enter_conf_pass')}}" name="new-password_confirmation" id="new-password_confirmation" >
			</div>
		  </div>
         </div>


		 <div class="row">
                    <div class="col-md-12">
		  <a class="btn btn-sign verify-btn"><input type='submit' class='btn btn-sign verify-btn' name='Submit' value='{{trans('master.chgPassForm.submit')}}' /></a>
                  
		 </div>

		</div>
                @if(Auth::user()->is_pwd_changed == 1)
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
		  <a href="{{ url('dashboard') }}" class="btn btn-sign verify-btn">Dashboard</a>

		 </div>

		</div>
                @endif

	</form>


	  </div>


	</div>
 </div>

@endsection
@section('jscript')
<script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>
 @endsection



