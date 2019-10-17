@extends('layouts.withought_login')
@section('content')
<div class="content-wrap height-auto">
    <div class="login-section">
        <div class="logo-box text-center marB20">
            <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
            <h2 class="head-line2 marT25">{{trans('master.loginForm.heading')}}</h2>
        </div>

        <div class="sign-up-box">

            <form class="loginForm form form-cls" autocomplete="off" method="POST" action="{{ route('login_open') }}" id="frmLogin">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.loginForm.username')}}</label>
                            <input type="text" class="form-control required"  placeholder="{{trans('master.loginForm.enter_uname')}}" name="username" value="{{ old('username') }}" id="username" >
                        @if(Session::has("messages"))
                        <p>{{ Session::get('messages') }}</p>
                        @endif
                        </div>

                       

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="float-left" for="pwd">{{trans('master.loginForm.password')}}</label>
                            <a class="float-right theme-colors" href="{{ url('password/email') }}">{{trans('master.loginForm.forgot_pass')}}</a></label>
                            <input type="password" id="password" class="form-control required" placeholder="{{trans('master.loginForm.enter_pass')}}" name="password" >
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p class="text-center have-account marB15">{{trans('master.loginForm.ques')}}<a href="{{url('/')}}">{{trans('master.loginForm.sign_up')}}</a></p>
                        <!-- <a href="personal-information.html" class="btn btn-sign verify-btn">Sign in</a>-->
                        <input type='submit' class='btn btn-sign verify-btn' name='Sign-in' value="{{trans('master.loginForm.sign_in')}}" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    var messages = {
        req_email: "{{ trans('error_messages.req_user_name') }}",
        req_password: "{{ trans('error_messages.req_password') }}",

    };
</script>
<script src="{{ asset('frontend/outside/js/validation/login.js') }}"></script>
@endsection



