@extends('layouts.guest')
@section('content')
<div class="form-content no-padding sign-up mt-5">
    <div class="row justify-content-center align-items-center m-0">
        <div class="col-md-4 form-design">

            <div id="reg-box">
                <form class="loginForm form form-cls" autocomplete="off" method="POST" action="{{ url('password/email') }}" id="forgotPassFrm">
                    {{ csrf_field() }} 
                    <div class="section-header">
                        <h4 class="section-title"> Recover Your Password</h4>
                    </div>
                    <div class="failed">
                        <div class="text-center">
                            @if(Session::has("messages"))
                            <strong class="erro-sms text-success">
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
                                        <input type="email" class="form-control" placeholder="{{trans('master.loginForm.email')}}" name="email" value="{{ old('email') ? old('email') : '' }}" id="email" >
                                        @error('email')
                                        <span class="text-danger"> {{$message}} </span>
                                        @enderror
                                        <input type="hidden" class="form-control" name="user_type" value="1">
                                    </div>
                                </div>
                                
                                <div class="d-flex btn-section sign-UP col-md-12">
                                    <button class="btn btn-primary pull-right" type="submit">Reset my password</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <p class=" have-account marB15">
                                    <a class="lnk-toggler have-account marB15" data-panel=".panel-login" href="{{url('login')}}">Already have an account?</a>
                                </p>
                            </div>
                            <div class="form-group">
                                <p class=" have-account marB15">
                                    <a  class=" marB15" href="{{url('/sign-up')}}">Don’t have an account?</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
 var messages = {
    req_email: "{{ trans('error_messages.req_email') }}",
    req_password: "{{ trans('error_messages.req_password') }}",
    req_confirm_password: "{{ trans('error_messages.req_confirm_password') }}"

};
</script>
<script src="{{ asset('frontend/outside/js/validation/login.js') }}"></script>
@endsection



