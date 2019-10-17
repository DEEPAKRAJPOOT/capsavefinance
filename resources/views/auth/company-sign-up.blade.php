@extends('layouts.withought_login')
@section('content')
<div class="content-wrap height-100">
    <div class="login-section sign-box-bk">
        <div class="logo-box text-center marB20">
            <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
            <h2 class="head-line2 marT25">{{trans('master.sign_up_corporate')}}</h2>
        </div>

        <div class="sign-up-box">

            <form class="registerForm form form-cls" autocomplete="off" enctype="multipart/form-data" method="POST" action="{{ route('company_register_open') }}" id="compregisterForm">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="uname">{{trans('master.country_of_registration')}}</label>
                            {!!
                            Form::select('country_id',
                            [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                            (isset($userArr->country_id) && !empty($userArr->country_id)) ? $userArr->country_id : (old('country_id') ? old('country_id') : ''),
                            array('id' => 'country_id',
                            'class'=>'form-control'))
                            !!}

                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.company_name')}} </label>
                            <input type="text" class="form-control"  placeholder="{{trans('master.company_name')}}" name="company_name" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.company_date_of_formation')}}</label>

                            
                            <div class="input-group">
                                <input type="text" class="form-control datepicker"  placeholder="{{trans('master.company_date_of_formation')}}" name="comp_dof" id="comp_dof" value="{{old('comp_dof')}}">
                                <div class="input-group-append">
                                    <i class="fa fa-calendar-check-o"></i>
                                </div>
                            </div>
                            {{$errors->first('comp_dof')}} 
                        </div>

                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.company_trade_license_number')}}</label>
                            <input type="text" class="form-control required"  placeholder="{{trans('master.company_trade_license_number')}}" name="comp_trade_in" id="comp_trade_in">
                        </div>
                    </div>
                </div>




                <div class="row">
                    <div class="col-md-12 marT10">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.signatory_company_authorized')}}</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.first_name')}} </label>
                            <input type="text" class="form-control"  placeholder="{{trans('master.first_name')}}" name="first_name">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.middle_name')}}</label>
                            <input type="text" class="form-control required" placeholder="{{trans('master.middle_name')}}" name="middle_name">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.last_name')}}</label>
                            <input type="text" class="form-control required" placeholder="{{trans('master.last_name')}}" name="last_name">

                        </div>
                    </div>
                </div>





                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.authorized_signatory_date_of_birth')}}</label>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker required"  placeholder="{{trans('master.authorized_signatory_date_of_birth')}}" name="dob">
                                <div class="input-group-append">
                                    <i class="fa fa-calendar-check-o"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.official_email_address')}}</label>
                            <input type="email" class="form-control required"  placeholder="{{trans('master.official_email_address')}}" name="email">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.official_mobile_no')}}</label>
                            <input type="text" class="form-control required"  placeholder="{{trans('master.official_mobile_no')}}" id="phone" name="phone" maxlength="10">
                            <div class="valid-feedback">Valid.</div>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p class="text-center have-account marB15">{{trans('master.already_have_an_account')}} <a href="{{url('/')}}/login">{{trans('master.sign_in')}}</a></p>
                    </div>
                    <div class="col-md-12">

                        <input type='submit' class='btn btn-sign verify-btn' name='next' value='{{trans('master.sign_up')}}' />
                    </div>
                </div>
            </form>


        </div>
    </div>
</div>



<script>
    var messages = {
        req_first_name: "{{ trans('error_messages.req_first_name') }}",
        first_name_max_length: "{{ trans('error_messages.first_name_max_length') }}",
        req_middle_name: "{{ trans('error_messages.req_middle_name') }}",
        middle_name_max_length: "{{ trans('error_messages.middle_name_max_length') }}",
        req_last_name: "{{ trans('error_messages.req_last_name') }}",
        last_name_max_length: "{{ trans('error_messages.last_name_max_length') }}",
        req_email: "{{ trans('error_messages.req_email') }}",
        invalid_email: "{{ trans('error_messages.invalid_email') }}",
    };
</script>
<script src="{{ asset('frontend/outside/js/validation/compregister.js') }}"></script>
<link href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}" rel="stylesheet">
<script src="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.js') }}"></script>
<script>
    $(document).ready(function () {

        var date = $('.datepicker').datepicker({dateFormat: 'dd/mm/yy',  changeMonth: true, changeYear: true});

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

