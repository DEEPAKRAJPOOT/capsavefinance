@extends('layouts.withought_login')
@section('content')

<div class="login-wrapper col-sm-6 col-sm-offset-3">
    <div class="container-center">
        <div class="panel mb-0">
            <div class="panel-heading">
                <div class="view-header">
                    <div class="logo-box p-2"><img src="{{url('backend/assets/images/logo.png')}}"></div>
                    <div class="header-title">
                        <h3>Login</h3>
                        <small>
                            <strong>Please enter your credentials to login.</strong>
                        </small>

                        <div class="failed">
                            <div>
                                @if(Session::has("messages"))
                                <strong class="erro-sms">
                                    {{ Session::get('messages') }}
                                </strong>
                                @endif
<<<<<<< HEAD
=======
                                
>>>>>>> 5c15199bd5e1fe13c98591b61ceccafbc74a339b
                            </div>
                             <div>
                                 @if($errors->has('messages'))
                                 <strong class="erro-sms">
                                    {{trans('auth.throttle')}}
                                </strong>
                                 @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <form class="loginForm form form-cls" autocomplete="off" method="POST" action="{{ route('login_open') }}" id="frmLogin">
                    {{ csrf_field() }} 
                    <div class="form-group mb-2">

                        <label for="email" class="control-label" >{{trans('master.loginForm.email')}}</label>
                        <input type="text" class="form-control required"  placeholder="{{trans('master.loginForm.email')}}" name="email" value="{{ old('email') }}" id="email" >

                    </div>
                    <div class="form-group">
                        <label class="control-label" for="pwd">{{trans('master.loginForm.password')}}</label>
                        <div class="hideShowPassword-wrapper">
                            <input type="password" id="password" class="form-control required" placeholder="{{trans('master.loginForm.enter_pass')}}" name="password" >
<<<<<<< HEAD
                        </div>
=======
                    </div>
>>>>>>> 5c15199bd5e1fe13c98591b61ceccafbc74a339b
                    </div>
                    <div class="form-group mt-3 Forgot">
                        <a href="{{ url('password/email') }}" class="forgot-link"> Forgot Password </a>
                        <a href="{{ url('/sign-up')}}" class="forgot-link pull-right"> Sign Up ? </a>
                        <div>
                            <input type='submit' class='btn btn-primary pull-right' name='Sign-in' value="{{trans('master.loginForm.sign_in')}}" />
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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



