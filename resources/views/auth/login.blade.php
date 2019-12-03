@extends('layouts.guest')
@section('content')
<div class="form-content no-padding sign-up mt-5">
    <div class="row justify-content-center align-items-center m-0">
        <div class="col-md-4 form-design">

            <div id="reg-box">
                <form class="loginForm form form-cls" autocomplete="off" method="POST" action="{{ route('login_open') }}" id="frmLogin">
                {{ csrf_field() }} 
                    <div class="section-header">
                        <h4 class="section-title"> Login</h4>
                    </div>
                    <div class="failed">
                        <div>
                            @if(Session::has("messages"))
                            <strong class="erro-sms text-danger">
                                {{ Session::get('messages') }}
                            </strong>
                            @endif
                        </div>
                        <div>
                            @if($errors->has('messages'))
                            <strong class="erro-sms text-danger">
                                {{trans('auth.throttle')}}
                            </strong>
                            @endif
                        </div>
                    </div>
                    <div class="row form-fields">
                        <div class="col-md-12">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="txtEmail">{{trans('master.loginForm.email')}}
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="hidden" name="send_otp" id="send-otp" value="">
                                        <input type="email" class="form-control" placeholder="{{trans('master.loginForm.email')}}" name="email" value="{{ old('email') }}" id="email" >
                                        @error('email')
                                            <span class="colorRed"> {{$message}} </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">{{trans('master.loginForm.password')}}
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input  type="password"  class="form-control" name="password"  placeholder="{{trans('master.loginForm.enter_pass')}}" name="password" oninput="removeSpace(this);" >
                                        @error('password')
                                        <span class="colorRed"> {{$message}} </span>
                                        @enderror
                                        <a href="{{ url('password/email') }}" style="display:block; margin-top:5px; text-decoration:underline;">Forgot Password</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="d-flex btn-section sign-UP col-md-12">
                            <input type='submit' class='btn btn-primary pull-right' name='Sign-in' value="{{trans('master.loginForm.sign_in')}}" />
<!--                            <span class=" mt-2 ml-2">or</span>
                            <a class=" mt-2 ml-2" href="{{ url('/sign-up') }}">Sign up</a>-->
                        </div>
                    </div>
                </form>
            </div>
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



