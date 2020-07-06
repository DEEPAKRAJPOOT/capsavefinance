@extends('layouts.guest')
@section('content')
<div class="form-content no-padding sign-up mt-5">
	<div class="row justify-content-center align-items-center m-0">
		<div class="col-md-4 form-design">

			<div id="reg-box">
				<form class="form-horizontal" method="POST" action="{{ url('password/change') }}">
					{{ csrf_field() }}
					<div class="section-header">
						<h4 class="section-title"> Change Password</h4>
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
					</div>                  


					<div class="row form-fields">
						<div class="col-md-12">
							<div class="form-group">
								<label for="pwd">{{trans('master.chgPassForm.old_pass')}}</label>
								<input type="password" class="form-control required"  placeholder="{{trans('master.chgPassForm.enter_old_pass')}}" name="current-password" id="current-password" required>
							</div>
						</div>
					
						<div class="col-md-12">
							<div class="form-group">
								<label for="pwd">{{trans('master.chgPassForm.new_pass')}}</label>
								<input type="password" class="form-control required" placeholder="{{trans('master.chgPassForm.enter_new_pass')}}" name="new-password" id="new-password" required>
							</div>
						</div>
					
						<div class="col-md-12">
							<div class="form-group">
								<label for="pwd">{{trans('master.chgPassForm.conf_new_pass')}}</label>
								<input type="password" class="form-control required"  placeholder="{{trans('master.chgPassForm.enter_conf_pass')}}" name="new-password_confirmation" id="new-password_confirmation" >
							</div>
						</div>
					
						<div class="col-md-12">
							<input type='submit' class='btn btn-primary verify-btn' name='Submit' value='{{trans('master.chgPassForm.submit')}}' />
						</div>

						@if(Auth::user()->is_pwd_changed == 1)
						<div class="col-md-12 " style="margin-top: -23px;">
							<a href="{{ url('dashboard') }}" class="pull-right"><u>Back To Dashboard</u></a>
						</div>
					@endif
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@section('jscript')
<script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>
@endsection



