@extends('layouts.withought_login')
@section('content')

<div class="content-wrap height-100">
    <div class="login-section sign-box-bk">
        <div class="logo-box text-center marB20">
            <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
            <h2 class="head-line2 marT25">{{trans('master.sign_up_indivisual')}}</h2>
        </div>

        <div class="sign-up-box">

            <form class="registerForm form form-cls" autocomplete="on" enctype="multipart/form-data" method="POST" action="{{ route('user_register_open') }}" id="registerForm">

                {{ csrf_field() }}

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="uname">{{trans('master.nationality')}}</label>
                            {!!
                            Form::select('country_id',
                            [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                            (isset($userArr->country_id) && !empty($userArr->country_id)) ? $userArr->country_id : (old('country_id') ? old('country_id') : ''),
                            array('id' => 'country_id','countryname'=>'country',
                            'class'=>'form-control select2Cls'))
                            !!}
                            {{$errors->first('country_id')}}

                            <div class="valid-feedback">Valid.</div>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.first_name')}}</label>
                            <input type="text" class="form-control"  placeholder="{{trans('master.first_name')}}" name="first_name" id="first_name"  maxlength="50" value="{{old('first_name')}}">
                            {{$errors->first('first_name')}}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.middle_name')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('master.middle_name')}}" name="middle_name" id="middle_name"  maxlength="50" value="{{old('middle_name')}}">
                            {{$errors->first('middle_name')}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.last_name')}}</label>
                            <input type="text" class="form-control required" placeholder="{{trans('master.last_name')}}" name="last_name" id="last_name"  maxlength="50" value="{{old('last_name')}}">
                            {{$errors->first('last_name')}}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.date_of_birth')}}</label>

                            <div class="input-group">
                                <input type="text" class="form-control datepicker"  placeholder="{{trans('master.date_of_birth')}}" name="dob" id="dob" value="{{old('dob')}}">
                                <div class="input-group-append">
                                    <i class="fa fa-calendar-check-o"></i>
                                </div>
                            </div>  
                            {{$errors->first('dob')}} 
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.official_email_address')}}</label>
                            <input type="text" class="form-control"  placeholder="{{trans('master.official_email_address')}}" id="email" name="email" value="{{old('email')}}">
                            {{$errors->first('email')}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.official_mobile_no')}}</label>
                            <input type="text" class="form-control required"  placeholder="{{trans('master.official_mobile_no')}}" maxlength="10" id="phone" name="phone" value="{{old('phone')}}">
                            {{$errors->first('phone')}}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p class="text-center have-account marB15">{{trans('master.already_have_an_account')}} <a href="login">{{trans('master.sign_in')}}</a></p>
                    </div>
                    <div class="col-md-12">
                        <!--<a href="thanks.html" class="btn btn-sign verify-btn">Sign up</a>-->
                        <input type='submit' class='btn btn-sign verify-btn' name='next' value='Sign up' />
                    </div>
                </div>
            </form>


        </div>


    </div>
    <script>
        var messages = {
            // req_first_name: "{{ trans('error_messages.req_first_name') }}",
            invalid_first_name: "{{ trans('error_messages.invalid_first_name') }}",
            first_name_max_length: "{{ trans('error_messages.first_name_max_length') }}",
            req_middle_name: "{{ trans('error_messages.req_middle_name') }}",
            invalid_middle_name: "{{ trans('error_messages.invalid_middle_name') }}",
            middle_name_max_length: "{{ trans('error_messages.middle_name_max_length') }}",
            req_last_name: "{{ trans('error_messages.req_last_name') }}",
            last_name_max_length: "{{ trans('error_messages.last_name_max_length') }}",
            invalid_last_name: "{{ trans('error_messages.invalid_last_name') }}",
            req_email: "{{ trans('error_messages.req_email') }}",
            invalid_email: "{{trans('error_messages.invalid_email')}}",
            req_dob: "{{trans('error_messages.req_dob')}}",
            invalid_age: "{{ trans('error_messages.invalid_age')}}",
            email_max_length: "{{ trans('error_messages.email_max_length') }}",
            phone_minlength: "{{ trans('error_messages.phone_minlength') }}",
            phone_maxlength: "{{ trans('error_messages.phone_maxlength') }}",
            positive_phone_no: "{{ trans('error_messages.positive_phone_no') }}",
            invalid_phone: "{{ trans('error_messages.invalid_phone') }}",
            req_country: "{{ trans('error_messages.req_country') }}",
            invalid_country: "{{ trans('error_messages.invalid_country') }}",
        };
    </script>
    <link href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}" rel="stylesheet">
    <script src="{{ asset('frontend/outside/js/validation/register.js') }}"></script>
    <script src="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.js') }}"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"></script>

    <script>
        $(document).ready(function () {


            var date = $('.datepicker').datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true});

            $('.datepicker').keydown(function (e) {
                e.preventDefault();
                return false;
            });

            $('.datepicker').on('paste', function (e) {
                e.preventDefault();
                return false;
            });
        });



    </script>
    @endsection



