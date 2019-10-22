@extends('layouts.withought_login')
@section('content')




<div class="login-wrapper col-sm-6 col-sm-offset-3">
		<div class="logo-box"><img src="{{url('backend/assets/images/logo.png')}}"></div>
		<div class="container-center">
                    <form class="loginForm form form-cls" autocomplete="off" method="POST" action="{{ route('login_open') }}" id="frmLogin">
                {{ csrf_field() }}            
			<div class="panel mb-0">
				<div class="panel-heading">
					<div class="view-header">
						<div class="header-title">
							<h3>Login</h3>
							<small>
								<strong>Please enter your credentials to login.</strong>
							</small>

							<div class="failed">
								<div style="color:#FF0000">
									<strong class="erro-sms">
									</strong>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<form id="loginForm">
						<div class="form-group mb-2">
                                                    
                                                     <label for="pwd" class="control-label" >{{trans('master.loginForm.username')}}</label>
                            <input type="text" class="form-control required"  placeholder="{{trans('master.loginForm.enter_uname')}}" name="username" value="{{ old('username') }}" id="username" >
                        @if(Session::has("messages"))
                        <p>{{ Session::get('messages') }}</p>
                        @endif
				</div>
						<div class="form-group">
<label class="control-label" for="pwd">{{trans('master.loginForm.password')}}</label>
							<div class="hideShowPassword-wrapper">
								
      
                            <input type="password" id="password" class="form-control required" placeholder="{{trans('master.loginForm.enter_pass')}}" name="password" >
                        
								<button type="button" class="show-pass" ><span class="fa fa-eye" id="passwordonoff"></span></button>
							</div>

						</div>
						<div class="form-group mt-3 Forgot">
							<a href="{{ url('password/email') }}" class="forgot-link"> Forgot Password </a>
							<div>
                        <input type='submit' class='btn btn-primary pull-right' name='Sign-in' value="{{trans('master.loginForm.sign_in')}}" />
                                              
                                                            
                                                            
                                                            
                                             </div>
					</form>
				</div>
			</div>
                    </form>
		</div>
	</div>

<script>
$(document).ready(function(){
$("#passwordonoff").click(function(){

    var getClass  =  $(this).attr('class');
    
     $("#passwordonoff").removeClass(getClass);
  
    if(getClass=='fa fa-eye')
    {

          $("#password").attr('type','text');
          $("#passwordonoff").addClass('fa fa-eye-slash');

    }
    else
    {
    	$("#password").attr('type','password');
        $("#passwordonoff").addClass('fa fa-eye');

    }
    
   


});

});


</script>	

<style>
            .login-wrapper input.btn.btn-primary {
    padding: 8px 30px;
    font-weight: 600;
    text-shadow: none;
    font-size: 20px;
    display: block;
    float: left;
    margin: 20px 0px 0;
    cursor: pointer;
            </style>
            <script>
    var messages = {
        req_email: "{{ trans('error_messages.req_user_name') }}",
        req_password: "{{ trans('error_messages.req_password') }}",

    };
</script>
<script src="{{ asset('frontend/outside/js/validation/login.js') }}"></script>
@endsection



