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
                        <form action="{{route('education_details')}}" id="educationForm" method="post">
                            {{ csrf_field() }}
                             @php $is_scout = session()->get('is_scout')@endphp
                            <div class="tab-content">
                                <div id="account" class="content_wz">
                                   
                                    <h5 class="info-text">{{trans('master.eduDetails.heading')}}<small class = "ismandtory">{{trans('master.eduDetails.sym')}}</small></h5>
                                    @if(count($educationArr)==0)
                                    <div class="reasearch">
                                    <div class="col-md-12 clonedEducationInfo " id="clonedEducationInfo0">
                                        <div class="row education education-form-block add-sec" id="education">
                                            <div class="inner-edit">
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label>{{trans('master.eduDetails.uni_name')}}<small class = "ismandtory">{{trans('master.eduDetails.sym')}}</small></label>
                                                            <div class="form-group">
                                                                <input type="text" name="university[0]" id="university0" class="is_required form-control clsRequired is_required" placeholder="{{trans('master.eduDetails.uni_name')}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label>{{trans('master.eduDetails.course_name')}}<small class = "ismandtory">{{trans('master.eduDetails.sym')}}</small></label>
                                                            <div class="form-group">
                                                                <input value="" type="text" name="course[0]" id="course0" class=" is_required form-control clsRequired is_required" placeholder="{{trans('master.eduDetails.course_name')}}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="col-sm-12">
                                                        <label for="checkbox3">{{trans('master.eduDetails.date_att')}}<small class = "ismandtory">{{trans('master.eduDetails.sym')}}</small></label>
                                                    </div>
                                                    <div class="col-sm-12 small-select">
                                                        <div class="row">
                                                            <div class="text-field-select col-sm-6">
                                                                <div class="form-group">
                                                                    {!!
                                                                    Form::select('date_attended_from_year[0]',
                                                                    [''=>'From'] + Helpers::getYearDropdown(),
                                                                    null,
                                                                    array('id' => 'date_attended_from_year0',
                                                                    'class'=>'fromSelect form-control select2Cls clsRequired is_required'))
                                                                    !!}
                                                                </div>
                                                            </div>
                                                            <div class="text-field-select col-sm-6">
                                                                {!!
                                                                Form::select('date_attended_to_year[0]',
                                                                [''=>'To']+ Helpers::getYearDropdown(),
                                                                null,
                                                                array('id' => 'date_attended_to_year0',
                                                                'class'=>' toSelect form-control select2Cls clsRequired is_required'))
                                                                !!}
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group ">
                                                        <label>{{trans('master.eduDetails.add_info')}}</label>
                                                        <div data-error="hasError" class="textarea-field">
                                                            <textarea id="remarks0" class="form-control" name="remarks[0]" placeholder="{{trans('master.eduDetails.remark')}}"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div  class="remove deleteEducationInfo delWidth"  style="display: none;"><i class="fa fa-trash-o" title="remove" aria-hidden="true"></i></div>
                                    </div> </div>
                                    @else
                                    @foreach($educationArr as $edu => $education)
                                    <div class="reasearch">
                                    <div class="col-md-12 clonedEducationInfo add-sec" id="clonedEducationInfo{{$edu}}">
                                        <div class="row education education-form-block " id="education">
                                            <div class="inner-edit">
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label>{{trans('master.eduDetails.uni_name')}}<small class = "ismandtory">{{trans('master.eduDetails.sym')}}</small></label>
                                                            <div class="form-group">
                                                                <input type="text" value="{{(isset($education->university) && !empty($education->university)) ? $education->university : (old('university') ? old('university') : '')}}" name="university[{{$edu}}]" id="university{{$edu}}" class="form-control clsRequired is_required" placeholder="{{trans('master.eduDetails.uni_name')}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label>{{trans('master.eduDetails.course_name')}}<small class = "ismandtory">{{trans('master.eduDetails.sym')}}</small></label>
                                                            <div class="form-group">
                                                                <input type="text" value="{{(isset($education->course) && !empty($education->course)) ? $education->course : (old('course') ? old('course') : '')}}" name="course[{{$edu}}]" id="course{{$edu}}" class="form-control clsRequired is_required">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="col-sm-12">
                                                        <label for="checkbox3">{{trans('master.eduDetails.date_att')}}<small class = "ismandtory">{{trans('master.eduDetails.sym')}}</small></label>
                                                    </div>
                                                    <div class="col-sm-12 small-select">
                                                        <div class="row">
                                                            <div class="text-field-select col-sm-6">
                                                                <div class="form-group">
                                                                    {!!
                                                                    Form::select('date_attended_from_year['.$edu.']',
                                                                    [''=>'From'] + Helpers::getYearDropdown(),
                                                                    (isset($education->date_attended_from_year) && !empty($education->date_attended_from_year)) ? $education->date_attended_from_year : (old('date_attended_from_year') ? old('date_attended_from_year') : ''),
                                                                    array('id' => 'date_attended_from_year'.$edu,
                                                                    'class'=>'form-control select2Cls clsRequired is_required'))
                                                                    !!}
                                                                </div>
                                                            </div>
                                                            <div class="text-field-select col-sm-6">
                                                                {!!
                                                                Form::select('date_attended_to_year['.$edu.']',
                                                                [''=>'To']+ Helpers::getYearDropdown(),
                                                                (isset($education->date_attended_to_year) && !empty($education->date_attended_to_year)) ? $education->date_attended_to_year : (old('date_attended_to_year') ? old('date_attended_to_year') : ''),
                                                                array('id' => 'date_attended_to_year'.$edu,
                                                                'class'=>'form-control select2Cls clsRequired is_required'))
                                                                !!}
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group ">
                                                        <label>{{trans('master.eduDetails.add_info')}}</label>
                                                        <div data-error="hasError" class="textarea-field">
                                                            <textarea id="remarks{{$edu}}" class="form-control" name="remarks[{{$edu}}]" placeholder="{{trans('master.eduDetails.remark')}}">{{(isset($education->remarks) && !empty($education->remarks)) ? $education->remarks : (old('remarks') ? old('remarks') : '')}}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                        
                                        <div style="@if($edu >=1)display:block @else display:none @endif" class="remove deleteEducationInfo"><i class="fa fa-trash-o" title="Remove" aria-hidden="true"></i></div>                                      
                                    </div>

                                </div>
                                    @endforeach
                                    @endif
                                
                                </div>
                            </div>
                            <div class="wizard-footer">
                                <div class="col-sm-12" style="padding: 0 8px;">
                               

                                <div class="col-sm-6 text-left">
                                    <div class="col-sm-cus">
                                    <span style="@if(count($educationArr) < 5) visibility:visible @else visibility:hidden @endif" class="add-more-educaion add-slab btn btn-primary mb-2"><span>{{trans('master.eduDetails.add_edu')}}</span>
                                </div>
                                    
                                </div>
                                 <div class="col-sm-6 text-right">
                                     <input type='button' class='btn btn-previous btn-default btn-wd' onclick="window.location ='{{route("user_register_open")}}'" name='previous' value='{{trans('master.eduDetails.pre')}}' />
                                    <input type='submit' class='btn btn-next btn-fill btn-warning btn-wd submitEducation' name='next' value='{{trans('master.eduDetails.next')}}' />
                                </div>

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
Education Details
@endsection
@section('addtional_css')
<style>
  .delWidth{width:12px;}
</style>

<link href="{{ asset('frontend/outside/css/paper-bootstrap-wizard.css')}}" rel="stylesheet" />
<!-- Fonts and Icons -->
<link href="http://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
<link href="{{ asset('frontend/outside/css/themify-icons.css')}}" rel="stylesheet">
<link href="{{ asset('frontend/outside/css/developer.css')}}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<!--End-->
@endsection
@section('jscript')
<script>
    var messages = {
        education_form_limit:"{{ config('common.EDUCATION_FORM_LIMIT') }}",
        req_course_name: "{{ trans('error_messages.req_course_name') }}",
        is_scout: "{{ session()->get('is_scout') }}",
    };
</script>
<script src="{{ asset('frontend/outside/js/validation/education.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection