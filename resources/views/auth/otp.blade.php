@extends('layouts.withought_login')
@section('content')
        @if (count($errors) > 0)
        <div class="alertMsgBox">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
            <div class="content-wrap height-auto">
                <div class="login-section">
                    <div class="logo-box text-center">
                        <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
                    </div>

                    <div class="thanks-conent marT50">
                        <h2 class="head-line2 marT20">{{trans('master.otpForm.thanks_email_verify')}}</h2>
                        <p class="p-conent">{{trans('master.otpForm.enter_otp_below')}}</p>
                    </div>

                    <!--startotp-->

                    <div class="sign-up-box">

                        
                        <form class="registerForm" autocomplete="off" enctype="multipart/form-data" method="POST" action="{{ route('verify_otp') }}" id="registerForm">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="pwd">{{trans('master.otpForm.otp_heading')}}</label>
                                        <input type="text" class="form-control"  placeholder="{{trans('master.otpForm.enter_otp')}}" name="otp" id="otp" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type='submit' class='btn btn-next btn-fill btn-warning btn-wd' name='next' value="{{trans('master.otpForm.verify_otp')}}" />
                                   <input type="hidden" name="token" id="token" value="{{$tokenarr['token'] }}" />
                                    <a href="{{ route('resend_otp',['token' => $tokenarr['token']]) }}" class="btn btn-next btn-fill btn-warning btn-wd">Resend OTP</a>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
<script>
    var messages = {
        req_otp: "{{ trans('error_messages.req_otp') }}",
        invalid_otp: "{{ trans('error_messages.invalid_otp') }}",

    };
</script>
<script  type="text/javascript" src="{{ asset('frontend/outside/js/validation/otp.js') }}"></script>
@endsection
@section('jscript')
<script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>
 @endsection

    

