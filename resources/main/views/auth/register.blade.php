@extends('layouts.guest')

@section('content')
<div class="image-container set-full-height register-page" style="background-image: url('assets/img/paper-1.jpeg')">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">

                <!--      Wizard container        -->
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="orange" id="wizardProfile">
                        @include('auth.progress')
                        <form class="registerForm" autocomplete="off" enctype="multipart/form-data" method="POST" action="{{ route('user_register_open') }}" id="registerForm">
                            {{ csrf_field() }}
                            <div class="tab-content">
                                <div class="" id="about">
                                    <h5 class="info-text">Basic Details</h5>
                                    <div class="row">


                                        <div class="col-sm-8">
                                            <div class="row">
                                            <div class="col-lg-4">


                                                <label>First Name <small>*</small></label>
                                                <input name="first_name" id="first_name" value="{{(isset($userArr->first_name) && !empty($userArr->first_name)) ? $userArr->first_name : (old('first_name') ? old('first_name') : Session::has('socialName') ? Session::pull('socialName') : '')}}" type="text" class="form-control" placeholder="First Name">

                                            </div>
                                            <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Last Name <small>*</small></label>
                                                <input name="last_name" id="last_name" value="{{(isset($userArr->last_name) && !empty($userArr->last_name)) ? $userArr->last_name : (old('last_name') ? old('last_name') : '')}}" type="text" class="form-control" placeholder="Last Name">
                                            </div>   </div>
                                                
                                                
                                            <div class="col-lg-4">
                                            <label>Phone</label>
                                            <div class="form-group" data-error="hasError">
                                                <input type="text" maxlength="10" id="phone" name="phone" value="{{(isset($userArr->phone) && !empty($userArr->phone)) ? $userArr->phone : (old('phone') ? old('phone') : '')}}" class="form-control numcls" placeholder="Phone">
                                            </div>
                                        </div>
                                                 <div class="col-lg-4">
                                            <div class="form-group" data-error="hasError">
                                                <label>Email <span class="mandatory">*</span></label>
                                                <input type="text" id="email" name="email" value="{{(isset($userArr->email) && !empty($userArr->email)) ? $userArr->email : (old('email') ? old('email') : Session::has('socialEmail') ? Session::pull('socialEmail') : '')}}" class="form-control" placeholder="Email">
                                            </div>
                                        </div>
                                                

                                        
                                                
                                                
                                                <div class="col-lg-4">

                                            <div class="relative">
                                                <label>Country <span class="mandatory">*</span> </label>
                                                {!!
                                                Form::select('country_id',
                                                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                                                (isset($userArr->country_id) && !empty($userArr->country_id)) ? $userArr->country_id : (old('country_id') ? old('country_id') : ''),
                                                array('id' => 'country_id',
                                                'class'=>'form-control select2Cls'))
                                                !!}
                                            </div>
                                        </div>
                                                
                                        </div>



                                        </div>
                                        
                                    </div>


                                    
                                </div>
                            </div>
                            <div class="wizard-footer">
                                <div class="col-sm-12 text-right">
                                    <input type='submit' class='btn btn-next btn-fill btn-warning btn-wd' name='next' value='Sign up' />
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- wizard container -->
            </div>
        </div>
        <!-- end row -->
    </div>
    <!--  big container -->
</div>
@endsection
@section('pageTitle')
Registration
@endsection
@section('addtional_css')
<link href="{{ asset('frontend/outside/css/paper-bootstrap-wizard.css')}}" rel="stylesheet" />
<!-- Fonts and Icons -->
<link href="http://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
<link href="{{ asset('frontend/outside/css/themify-icons.css')}}" rel="stylesheet">
<link href="{{ asset('frontend/outside/css/developer.css')}}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
 <link rel="stylesheet" href="{{ asset('frontend/inside/css/custom.css') }}">
<!--End-->
@endsection
@section('jscript')
<script>
    var messages = {
        req_first_name: "{{ trans('error_messages.req_first_name') }}",
        invalid_first_name: "{{ trans('error_messages.invalid_first_name') }}",
        first_name_max_length: "{{ trans('error_messages.first_name_max_length') }}",

        req_last_name: "{{ trans('error_messages.req_last_name') }}",
        invalid_last_name: "{{ trans('error_messages.invalid_last_name') }}",
        last_name_max_length: "{{ trans('error_messages.last_name_max_length') }}",

        req_email: "{{ trans('error_messages.req_email') }}",
        invalid_email: "{{ trans('error_messages.invalid_email') }}",
        email_max_length: "{{ trans('error_messages.email_max_length') }}",
        email_already_exists: "{{ trans('error_messages.email_already_exists') }}",

        phone_minlength: "{{ trans('error_messages.phone_minlength') }}",
        phone_maxlength: "{{ trans('error_messages.phone_maxlength') }}",
        invalid_phone: "{{ trans('error_messages.invalid_phone') }}",

        req_country: "{{ trans('error_messages.req_country') }}",
        invalid_country: "{{ trans('error_messages.invalid_country') }}",

        
    };
</script>
<script src="{{ asset('frontend/outside/js/validation/register.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection