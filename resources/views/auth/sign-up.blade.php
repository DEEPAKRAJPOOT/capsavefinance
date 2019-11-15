@extends('layouts.guest')
@section('content')

<div class="form-content no-padding sign-up mt-5">
    <div class="row justify-content-center align-items-center m-0">
        <div class="col-md-6 form-design">

            <div id="reg-box">
                <form class="registerForm form form-cls" autocomplete="on" enctype="multipart/form-data" method="POST" action="{{ route('user_register_open') }}" id="registerForm">

                    {{ csrf_field() }}

                    <div class="section-header">
                        <h4 class="section-title"> Registration</h4>
                    </div>
                    <div class="row form-fields">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">First Name
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="text" name="f_name" value="{{old('f_name')}}" class="form-control" tabindex="1" placeholder="First Name">
                                        <span class="text-danger error">{{$errors->first('f_name')}}</span>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtSupplierName">Middle Name
                                            <span class="mandatory"></span>
                                        </label>
                                        <input type="text" name="m_name" id="name" value="{{old('m_name')}}" class="form-control" tabindex="3" placeholder="Middle Name">
                                        <span class="text-danger error">{{$errors->first('m_name')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">Last Name
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="text" name="l_name" value="{{old('l_name')}}" class="form-control" tabindex="1" placeholder="Last Name">
                                        <span class="text-danger error">{{$errors->first('l_name')}} </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtSupplierName">Business Name
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="text" name="business_name" id="business_name" value="{{old('business_name')}}" class="form-control" tabindex="3" placeholder="Business Name">
                                        <span class="text-danger error">{{$errors->first('business_name')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtEmail">Email
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="hidden" name="send_otp" id="send-otp" value="">
                                        <input type="text" name="email" id="email" value="{{old('email')}}" class="form-control" tabindex="4" placeholder="Email">
                                        <span class="text-danger error"> {{$errors->first('email')}} </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtMobile">Mobile
                                            <span class="mandatory">*</span>
                                        </label>
                                        <div class="relative d-flex">
                                            <input class="form-control cont" name="phone-ext" id="phone-ext" type="text" value="+91" readonly="">
                                            <input class="form-control numbercls" name="mobile_no" value="{{old('mobile_no')}}" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile">


                                        </div>
                                        <span class="text-danger error"> {{$errors->first('mobile_no')}} </span>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">Password
                                            <span class="mandatory">*</span>
                                        </label>

                                        <input class="form-control" value="{{old('password')}}" name="password" type="password" tabindex="5" placeholder="Password" value="{{old('password')}}" oninput="removeSpace(this);">

                                        <span class="text-danger error"> {{$errors->first('password')}}	</span>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">Confirm Password
                                            <span class="mandatory">*</span>
                                        </label>

                                        <input class="form-control"  value="{{old('password_confirm')}}" name="password_confirm" type="password" tabindex="5" placeholder="Confirm Password" value="{{old('password_confirm')}}"  oninput="removeSpace(this);">

                                        <span class="text-danger error">{{$errors->first('password_confirm')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                            <div class="g-recaptcha" data-sitekey="{{config('common.google_recaptcha_key')}}"></div>
                            <span class="text-danger error"> {{$errors->first('g-recaptcha-response')}} </span>
                            </div>
                            </div>
                        </div>
                        <div class="d-flex btn-section sign-UP">
                            <div class="col-md-4">
                                <input type="submit" value="Submit" class="btn btn-primary"> </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
@section('scripts')
<script src='https://www.google.com/recaptcha/api.js'></script>
@endsection


