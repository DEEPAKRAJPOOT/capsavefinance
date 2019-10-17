@extends('layouts.withought_login')
@section('content')
            <div class="content-wrap height-auto">
                <div class="login-section">
                    <div class="logo-box text-center">
                        <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
                    </div>

                    <div class="thanks-conent marT50">
                        <h2 class="head-line2 marT20">{{trans('master.thanks_email_verify')}}</h2>
                        <p class="p-conent">{{trans('master.enter_otp_below')}}</p>
                    </div>

                    <!--startotp-->

                    <div class="sign-up-box">

                        
                        <form class="registerForm" autocomplete="off" enctype="multipart/form-data" method="POST" action="{{ route('verify_otp') }}" id="registerForm">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="pwd">{{trans('master.otp_heading')}}</label>
                                        <input type="text" class="form-control"  placeholder="{{trans('master.enter_oTP')}}" name="otp" id="otp" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type='submit' class='btn btn-next btn-fill btn-warning btn-wd' name='next' value='{{trans('master.verify_oTP')}}' />
                                   <!-- <a href="mobile-verification.html" class="btn btn-sign verify-btn">Verify OTP</a>-->
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </section>
@endsection
@section('jscript')
<script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>
 @endsection

    

