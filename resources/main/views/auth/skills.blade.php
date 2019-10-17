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
                        <form action="{{route('skills')}}" id="skillForm" method="post">
                            {{ csrf_field() }}
                            <div class="tab-content Skills">
                                <div id="address">
                                    <h5 class="info-text">Skills</h5>
                                    <div class="col-md-12">
                                        <div class="row   ">
                                            <div class="col-sm-3 ">
                                                <div class="form-groupp">
                                                    <label>Add Skills<small class = "ismandtory">*</small></label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 text-center">
                                            </div>
                                            <div class="col-sm-2 text-center">
                                                <div class="form-groupp">
                                                    <label>Beginner</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 text-center">
                                                <div class="form-groupp">
                                                    <label>Intermediate</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 text-center">
                                                <div class="form-groupp">
                                                    <label>Professional</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row experience">
                                            @if(count($skillArr)==0)
                                            
                                            <div class=" reasearch">
                                            <div class="experience-edit clonedSkills add-sec" id="clonedSkills0">
                                                <div class="col-sm-12 small-select  ">
                                                    <div class="row flex align-center">
                                                        <div class="text-field-select col-md-3">
                                                            {!!
                                                            Form::select('skill_id[0]',
                                                             [''=>'Select Skill'] + Helpers::getSkillsDropDown()->toArray() + ['35'=>'Other'],
                                                            null,
                                                            array('id' => 'skill_id0','data' => 0,
                                                            'class'=>'form-control clsRequired is_required skill'))
                                                            !!}
                                                        </div>
                                                       
                                                            
                                                         <label class="radio-bg col-md-2 text-center">
                                                          <input type="text" name="other_skill[0]" id='other_0' class="clsRequired form-control required" value=""/>
                                                        </label>
                                                        <label class="radio-bg col-md-2 text-center">
                                                            <input type="radio" value="1" id="beginner0" checked="checked" name="level[0]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <label class="radio-bg col-md-2 text-center">
                                                            <input type="radio" value="2" id="intermediate0" name="level[0]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <label class="radio-bg col-md-2 text-center">
                                                            <input type="radio" value="3" id="professional0" name="level[0]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                      
                                                            <div class="deleteSkillbtn remove "  style="display: none;"><i class="fa fa-trash-o deleteSkill" title="Remove" aria-hidden="true"></i></div>
                                                      
                                                         </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                
                                            </div> </div>
                                            @else
                                            @foreach($skillArr as $skl => $skill)
                                            <div class="experience-edit clonedSkills add-sec" id="clonedSkills{{$skl}}">
                                                <div class="col-sm-12 small-select form-groupp">
                                                    <div class="row">
                                                        <div class="text-field-select col-md-3">
                                                            {!!
                                                            Form::select('skill_id['.$skl.']',
                                                            [''=>'Select Skill'] + Helpers::getSkillsDropDown()->toArray() + ['35'=>'Other'],
                                                            (isset($skill->skill_id) && !empty($skill->skill_id)) ? $skill->skill_id : (old('skill_id') ? old('skill_id') : ''),
                                                            array('id' => 'skill_id'.$skl,'data' => $skl,
                                                            'class'=>'form-control clsRequired is_required skill'))
                                                            !!}
                                                        </div>
                                                       @php $arrOtherdata = Helpers::getOtherSkillsbyId($skill->id,$skill->user_id) @endphp
                                                            
                                                        <label class="radio-bg col-md-2 text-center">
                                                     <input type="text" name="other_skill[{{ $skl}}]" id="other_{{$skl}}" class="clsRequired form-control required" 
                                                                   value="{{($arrOtherdata)?$arrOtherdata->other_skill:''}}"/>
                                                        </label>
                                                        <label class="radio-bg col-md-2 text-center">
                                                            <input type="radio" value="1" id="beginner{{$skl}}" @if(isset($skill->level) && !empty($skill->level) && $skill->level == 1) checked @elseif(old('level') && old('level')==1) checked @else '' @endif name="level[{{$skl}}]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <label class="radio-bg col-md-2 text-center">
                                                            <input type="radio" value="2" id="intermediate{{$skl}}" @if(isset($skill->level) && !empty($skill->level) && $skill->level == 2) checked @elseif(old('level') && old('level')==2) checked @else '' @endif name="level[{{$skl}}]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <label class="radio-bg col-md-2 text-center">
                                                            <input type="radio" value="3" id="professional{{$skl}}" @if(isset($skill->level) && !empty($skill->level) && $skill->level == 3) checked @elseif(old('level') && old('level')==3) checked @else '' @endif name="level[{{$skl}}]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <label class="radio-bg col-md-1 text-center">
                                                            <div class="remove deleteSkill deleteSkillbtn"  style="@if($skl >=1)display:block @else display:none @endif"><i class="fa fa-trash-o" title="Remove" aria-hidden="true"></i></div>
                                                        </label>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>                                         
                                         
                                    </div>
                                </div>
                            </div>
                            <div class="wizard-footer">
                                <div class="col-sm-6 text-left">
                                    <span class="add-skills btn btn-primary mb-2" style="@if(count($skillArr) < 5)visibility:visible @else visibility:hidden @endif"><span>+</span> Add Skills</span>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <input type='button' onclick="window.location='{{route("education_details")}}'" class='btn btn-previous btn-default btn-wd' name='previous' value='Previous' />
                                    <input type='submit' class='submitSkills btn btn-next btn-fill btn-warning btn-wd' name='next' value='Next' />
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
Skills
@endsection
@section('addtional_css')
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
         skill_form_limit:"{{ config('common.SKILL_FORM_LIMIT') }}",
         is_scout: "{{ session()->get('is_scout') }}",
    };
</script>
<script src="{{ asset('frontend/outside/js/validation/skills.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection