@extends('layouts.guest')
@section('content')

<div class="form-content no-padding sign-up mt-5">
    <div class="row justify-content-center align-items-center m-0">
        <div class="col-md-6 form-design">

            <div id="reg-box">
                <form class="registerForm form form-cls" autocomplete="on" enctype="multipart/form-data" method="POST" action="{{ route('user_register_save') }}" id="registerForm">

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
                                        <input type="text" name="f_name" value="@if($anchorDetail){{$anchorDetail->name}}@else{{old('f_name')}}@endif" class="form-control" tabindex="1"  placeholder="First Name" @if($anchorDetail)readonly @else @endif>
                                        <span class="text-danger error">{{$errors->first('f_name')}}</span>
                                    </div>

                                </div>
                                <!--
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtSupplierName">Middle Name
                                            <span class="mandatory"></span>
                                        </label>
                                        <input type="text" name="m_name" id="name" value="{{old('m_name')}}" class="form-control" tabindex="2" placeholder="Middle Name">
                                        <span class="text-danger error">{{$errors->first('m_name')}}</span>
                                    </div>
                                </div>
                                -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">Last Name
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="text" name="l_name" value="@if($anchorDetail){{$anchorDetail->l_name}}@else{{old('l_name')}}@endif" class="form-control" tabindex="3" placeholder="Last Name" @if($anchorDetail)readonly @else @endif >
                                        <span class="text-danger error">{{$errors->first('l_name')}} </span>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtSupplierName">Business Name
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="text" name="business_name" id="business_name" value="@if($anchorDetail){{$anchorDetail->biz_name}}@else{{old('business_name')}}@endif" class="form-control" tabindex="4" placeholder="Business Name"  @if($anchorDetail)readonly @else @endif >
                                        <span class="text-danger error">{{$errors->first('business_name')}}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                              <div class="form-group">
                                 <label for="pan_no">PAN No.
                                 <span class="mandatory">*</span>
                                 </label>
                                  <input type="text" name="pan_no" id="pan_no" value="{{old('pan_no')}}" maxlength="10" class="form-control pan_no" tabindex="3" placeholder="PAN Number" >
                                 <span class="text-danger error">{{$errors->first('pan_no')}}</span>
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
                                        <input type="text" name="email" id="email" value="@if($anchorDetail){{$anchorDetail->email}}@else{{old('email')}}@endif" class="form-control" tabindex="4" placeholder="Email"  @if($anchorDetail)readonly @else @endif>

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
                                            <input class="form-control numbercls" name="mobile_no" value="@if($anchorDetail){{$anchorDetail->phone}}@else{{old('mobile_no')}}@endif" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" @if($anchorDetail)readonly @else @endif>


                                        </div>
                                        <span class="text-danger error"> {{$errors->first('mobile_no')}} </span>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group password-input">
                                        <label for="password">Password
                                            <span class="mandatory">*</span>
                                        </label>

                                        <input class="form-control password" name="password" type="password" tabindex="7" placeholder="Password" value="{{old('password')}}" oninput="removeSpace(this);">

                                        <span class="text-danger error"> {{$errors->first('password')}}	</span>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group password-input">
                                        <label for="password_confirm">Confirm Password
                                            <span class="mandatory">*</span>
                                        </label>

                                        <input class="form-control password_confirm"  value="{{old('password_confirm')}}" name="password_confirm" type="password" tabindex="8" placeholder="Confirm Password" value="{{old('password_confirm')}}"  oninput="removeSpace(this);">

                                        <span class="text-danger error">{{$errors->first('password_confirm')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                            <div class="g-recaptcha" id="recaptcha" data-sitekey="{{config('common.google_recaptcha_key')}}"></div>
                            <span class="text-danger error"> {{$errors->first('g-recaptcha-response')}} </span>
                            </div>
                            </div>
                        </div>
                        <div class="d-flex btn-section sign-UP">
                            <div class="col-md-4 mt-3">
                                <input type="hidden" name="anch_user_id" id="anchor_user_id" value="@if($anchorDetail){{$anchorDetail->anchor_user_id}}@endif">
                                <input type="hidden" name="h_anchor_id" id="h_anchor_id" value="@if($anchorDetail){{$anchorDetail->anchor_id}}@endif">
                                <input type="hidden" name="lead_type" id="lead_type" value="@if($anchorDetail){{$anchorDetail->user_type}}@endif">
                                <input type="submit" value="Submit" tabindex="9" id="SaveUser" class="btn btn-primary"> </div>
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
<script type="text/javascript">
    $(document).ready(function () {
            
            
            $.validator.addMethod("panValidator", function(value, element) {
                var values = value;
                var pannoformat = new RegExp('^[A-Z]{5}[0-9]{4}[A-Z]{1}$');

                if (/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/.test(values)) {
                    if (pannoformat.test(values)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            });
            
//            $.validator.addMethod("isexistemail", function(value, element) {
//                var email = value;
//                let status = false;
//                $.ajax({
//                    url: messages.check_exist_email,
//                    type: 'POST',
//                    datatype: 'json',
//                    async: false,
//                    cache: false,
//                    data: {
//                        'email' : email,
//                        '_token' : messages.token
//                    },
//                    success: function(response){
//                       if(response['status'] === 'true'){
//                          status = true;
//                      }
//                    }
//                });
//                return this.optional(element) || (status === true);
//            });
            
            $('#SaveUser').on('click', function (event) {
                
                $('input.pan_no').each(function () {
                    $(this).rules("add",
                        {
                            required: true,
                            maxlength: 10,
                            panValidator: true,
                            messages: {'panValidator': 'Please enter correct PAN No.'}
                        });
                });
                $('input.password').each(function () {
                    $(this).rules("add",
                        {
                            required: true
                        });
                });
                $('input.password_confirm').each(function () {
                    $(this).rules("add",
                        {
                            required: true
                        });
                });
//                $('input.email').each(function () {
//                    $(this).rules("add",
//                    {
//                        required: true,
//                        email: true,
//                        isexistemail: true,
//                        messages:{'isexistemail' : "This email is already exist."}
//                    });
//                });               
                
            });
            
            $('form#registerForm').validate();
        });
</script>
@endsection


