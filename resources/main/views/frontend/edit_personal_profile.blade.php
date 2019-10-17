@extends('layouts.app')

@section('content')
<section class="pt-4 mt-4">
     
    {!!
    Form::open(
        array('name' => 'updateProfileForm',
        'autocomplete' => 'off', 
        'id' => 'updateProfileForm',
        'class' => 'form-horizontal',
        'url' => route('update_personal_profile'),
        'files' => true,
        )
      )             
    !!}
    
    <div class="container">
        <div class="edit-profile-sec ">
    <div id="accordion ">
          <div id="accordion">
   
            </div>
  <div class="card">
    <div class="card-header">Basic Details
      <a class="card-link" data-toggle="collapse" href="#collapseOne">
     <i class="fa fa-edit" ></i>
      </a>
    </div>
    <div id="collapseOne" class="collapse show" data-parent="#accordion">
      <div class="card-body">
       <div id="about">
                                        
                                        <div class="row">

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>First Name <small>*</small></label>
                                                     {!!
                                                        Form::text('first_name',
                                                        isset($userData->first_name) ? $userData->first_name : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'First Name',
                                                        'id'=>'first_name',
                                                        'required' =>'required'
                                                        ])
                                                    !!}
                                                    
                                                </div>
                                                <div class="form-group">
                                                    <label>Last Name <small>*</small></label>
                                                     {!!
                                                        Form::text('last_name',
                                                        isset($userData->last_name) ? $userData->last_name : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Last Name',
                                                        'id'=>'last_name',
                                                        'required' =>'required'
                                                        ])
                                                    !!}
                                                 </div>
                                                <div class="form-group">
                                                    <label>Role <small>*</small></label>
                                                     {!!
                                                        Form::text('role',
                                                        isset($userData->role) ? $userData->role : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Role',
                                                        'id'=>'role',
                                                        'required' =>'required'
                                                        ])
                                                    !!}
                                                 </div>

                                                <div class="form-group">
                                                    <label>Pseudo Name <small>*</small></label>
                                                     {!!
                                                        Form::text('sudoname',
                                                        isset($userData->sudoname) ? $userData->sudoname : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Eg. XYZ',
                                                        'id'=>'sudoname',
                                                        'required' =>'required'
                                                        ])
                                                    !!}
                                                 </div>


                                            </div>
                                            <div class="col-sm-6 ">
                                                <div class="picture-container">
                                                    <div class="picture">
                                                        <img style="width: 100%;height: 100%;" src="{{ route('view_inside_register_profile',['folder'=> 'profile', 'file' => encrypt($userData->user_photo)]) }}" class="" alt="Profile image" id="wizardPicturePreview11"/>
                                                        <input type="file" name ="user_photo" id="user_photo">
                                                    </div>
                                                    <h6>Choose Picture</h6>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <!--<div class="col-lg-6">
                                                <div class="form-group" data-error="hasError">
                                                    <label>Email <span class="mandatory">*</span></label>
                                                    <input type="hidden" name="email" id="email" value="<?php //echo $userData->email;?>" />
                                                </div>
                                            </div>-->
                                            <input type="hidden" name="email" id="email" value="{{$userData->email}}">
                                            <div class="col-lg-6">
                                                <label>Phone</label>
                                                <div class="form-group" data-error="hasError">
                                                    {!!
                                                        Form::text('phone',
                                                        isset($userData->phone) ? $userData->phone : '',
                                                        [
                                                        'class' => 'form-control numcls',
                                                        'placeholder' => 'Phone',
                                                        'id'=>'phone',
                                                        'maxlength' => 10,
                                                        ])
                                                    !!}
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <label>Address Line 1 </label>
                                                <div class="form-group" data-error="hasError">
                                                {!!
                                                        Form::text('addr1',
                                                        isset($userData->addr1) ? $userData->addr1 : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Address Line 1',
                                                        'id'=>'addr1'
                                                        ])
                                                    !!}
                                                
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Address Line 2</label>
                                                    {!!
                                                        Form::text('addr2',
                                                        isset($userData->addr2) ? $userData->addr2 : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Address Line 2',
                                                        'id'=>'addr2'
                                                        ])
                                                    !!}
                                                
                                                
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Area </label>
                                                <div class="form-group" data-error="hasError">
                                                    {!!
                                                        Form::text('area',
                                                        isset($userData->area) ? $userData->area : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Area',
                                                        'id'=>'area'
                                                        ])
                                                    !!}
                                                    
                                                    
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>City </label>
                                                <div class="form-group" data-error="hasError">
                                                    {!!
                                                        Form::text('city',
                                                        isset($userData->city) ? $userData->city : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'City',
                                                        'id'=>'city'
                                                        ])
                                                    !!}
                                                    
                                                    
                                                    
                                                </div>
                                            </div>
                                            <div class="col-lg-6">

                                                <div class="relative">
                                                    <label>Country <span class="mandatory">*</span> </label>

                                                    {!!
                                                        Form::select('country_id',
                                                        [''=>'Type of Rights*'] + Helpers::getCountryDropDown()->toArray(),
                                                        (isset($userData->country_id) && !empty($userData->country_id)) ? $userData->country_id : (old('country_id') ? old('country_id') : ''),
                                                        array('id' => 'country_id',
                                                        'class'=>'form-control',
                                                        'required' =>'required'))
                                                    !!}
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Zip Code <span class="mandatory">*</span></label>
                                                <div class="form-group" data-error="hasError">
                                                     {!!
                                                        Form::text('zip_code',
                                                        isset($userData->zip_code) ? $userData->zip_code : '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Zipcode',
                                                        'id'=>'zip_code',
                                                        'maxlength' => 16,
                                                        ])
                                                    !!}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"> Education Details
      <a class="collapsed card-link" data-toggle="collapse" href="#collapseTwo">
       <i class="fa fa-edit" ></i>
      </a>
    </div>
    <div id="collapseTwo" class="collapse" data-parent="#accordion">
      <div class="card-body">
        <div id="account">
            
                                        <div class="col-md-12">
                                            <div class=" education">
                                                 
                                                @if(count($educationData) == 0)
                                                <div class="inner-edit clonedEducationInfo" id="clonedEducationInfo0">
                                                    <div class="row">
                                                        
                                                            <div class="col-sm-6">
                                                                <label>University Name</label>
                                                                <div class="form-group">
                                                                   <input type="text" name="university[0]" id="university0" class="form-control clsRequired is_required" placeholder="University Name">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label>Course Name<small>*</small></label>
                                                                <div class="form-group">
                                                                    <input type="text" name="course[0]" id="course0" class="form-control clsRequired is_required">
                                                                </div>
                                                            </div>
                                                       

                                                    </div>
                                                    <div class="row">
                                                    <div class="col-sm-6">

                                                        <div class=" small-select">
                                                         <div class="col-sm-12">
                                                            <label for="checkbox3">Dates Attended</label>
                                                        </div>
                                                            <div class="row">
                                                                <div class="text-field-select col-sm-6">

                                                                    <div class="  form-group">
                                                                        {!!
                                                                    Form::select('date_attended_from_year[0]',
                                                                    [''=>'From'] + Helpers::getYearDropdown(),
                                                                     null,
                                                                    array('id' => 'date_attended_from_year0',
                                                                    'class'=>'form-control select2Cls clsRequired is_required'))
                                                                    !!}

                                                                    </div>
                                                                </div>
                                                                <div class="text-field-select col-sm-6">
                                                                    {!!
                                                                Form::select('date_attended_to_year[0]',
                                                                [''=>'To']+ Helpers::getYearDropdown(),
                                                                null,
                                                                array('id' => 'date_attended_to_year0',
                                                                'class'=>'form-control select2Cls clsRequired is_required'))
                                                                !!}
                                                                </div>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group ">
                                                            <label>Additional information</label>
                                                            <div data-error="hasError" class="textarea-field">
                                                             <textarea id="remarks0" class="form-control" name="remarks[0]" placeholder="Remark"></textarea>    
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                   
                                                <span><i style="display:none" class="fa fa-trash-o  deleteEducationInfo" title="Remove" aria-hidden="true"></i></span>
                                               
                                                </div>
                                                @else
                                               @foreach($educationData as $edu => $education) 
                                               
                                               <div class="inner-edit clonedEducationInfo" id="clonedEducationInfo{{$edu}}">
                                                    <div class="row">
                                                        
                                                            <div class="col-sm-6">
                                                                <label>University Name</label>
                                                                <div class="form-group">
                                                                   <input type="text" value="{{(isset($education->university) && !empty($education->university)) ? $education->university : (old('university') ? old('university') : '')}}" name="university[{{$edu}}]" id="university{{$edu}}" class="form-control clsRequired is_required" placeholder="University Name">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label>Course Name<small>*</small></label>
                                                                <div class="form-group">
                                                                    <input type="text" value="{{(isset($education->course) && !empty($education->course)) ? $education->course : (old('course') ? old('course') : '')}}" name="course[{{$edu}}]" id="course{{$edu}}" class="form-control clsRequired is_required">
                                                                </div>
                                                            </div>
                                                       

                                                    </div>
                                                    <div class="row">
                                                    <div class="col-sm-6">

                                                        <div class=" small-select">
                                                         <div class="col-sm-12">
                                                            <label for="checkbox3">Dates Attended</label>
                                                        </div>
                                                            <div class="row">
                                                                <div class="text-field-select col-sm-6">

                                                                    <div class="  form-group">
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
                                                    <div class="col-sm-6">
                                                        <div class="form-group ">
                                                            <label>Additional information</label>
                                                            <div data-error="hasError" class="textarea-field">
                                                             <textarea id="remarks{{$edu}}" class="form-control is_required clsRequired" name="remarks[{{$edu}}]" placeholder="Remark">{{(isset($education->remarks) && !empty($education->remarks)) ? $education->remarks : (old('remarks') ? old('remarks') : '')}}</textarea>    
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                   
                                                <span><i style="@if($edu >=1)display:block @else display:none @endif" class="fa fa-trash-o  deleteEducationInfo" title="Remove" aria-hidden="true"></i></span>
                                               
                                                </div>
                                               @endforeach
                                               @endif
                                            </div>

                                        </div>
                                        @if(count($educationData) < 5)
                                         <span class="add-slab btn btn-primary mb-2 add-more-educaion"><span>+</span> Add Education</span>
                                        @endif
                                        </div>
      </div>
    </div>
  </div>
     
  <div class="card">
    <div class="card-header"> Skills
      <a class="collapsed card-link" data-toggle="collapse" href="#collapseThree">
       <i class="fa fa-edit" ></i>
      </a>
    </div>
    <div id="collapseThree" class="collapse" data-parent="#accordion">
      <div class="card-body">
                                            <div id="address">

                                        
                                        <div class="col-md-12">
                                            <div class="row">

                                                <div class="col-sm-3 ">
                                                    <div class="form-group">
                                                        <label>Add Skills<small>*</small></label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 ">

                                                </div>
                                                <div class="col-sm-2 text-center">
                                                    <div class="form-group">
                                                        <label>Beginner</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 text-center">
                                                    <div class="form-group">
                                                        <label>Intermediate</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 text-center">
                                                    <div class="form-group">
                                                        <label>Professional</label>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-md-12 experience">
                                                @if(count($skillData) == 0)
                                                <div class="experience-edit clonedSkills" id="clonedSkills0">
                                                    <div class="small-select form-group">
                                                        <div class="row">

                                                            <div class="text-field-select col-md-3">
                                                            @php $arrSKills = [];
                                                            $arrSkills = Helpers::getSkillsDropDown()->toArray();
                                                            $arrSkills['35'] = 'Other';@endphp
                                                                {!!
                                                            Form::select('skill_id[0]',
                                                            [''=>'Select Skill'] + Helpers::getSkillsDropDown()->toArray() + ['35'=>'Other'],
                                                            null,
                                                            array('id' => 'skill_id0','data'=> 0,
                                                            'class'=>'form-control clsRequired is_required skill',
                                                            ))
                                                            !!}
                                                            </div>
                                                            <label class="radio-bg col-md-2 text-center">
                                                            <input type="text" name="other_skill[0]" id="other_0" class="clsRequired form-control required" value=""/>
                                                            </label>
                                                            
                                                            <div class="col-md-2 text-center">
                                                            <label class="radio-bg text-center">
                                                            <input type="radio" value="1" checked="checked" id="beginner0" name="level[0]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                            <label class="radio-bg text-center">
                                                            <input type="radio" value="2" id="intermediate0" name="level[0]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                             <label class="radio-bg  text-center">
                                                            <input type="radio" value="3" id="professional0"name="level[0]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                            </div>
                                                           

                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="deleteSkillbtn remove "  style="display: none;"><i class="fa fa-trash-o deleteSkill" title="Remove" aria-hidden="true"></i></div>
                                                    
                                                </div>
                                                @else
                                                @foreach($skillData as $skl => $skill)
                                                 <div class="experience-edit clonedSkills" id="clonedSkills{{$skl}}">
                                                    <div class="small-select form-group">
                                                        <div class="row">

                                                            <div class="text-field-select col-md-3">
                                                            @php $arrSKills = [];
                                                            $arrSkills = Helpers::getSkillsDropDown()->toArray();
                                                            $arrSkills['35'] = 'Other';@endphp
                                                                {!!
                                                            Form::select('skill_id['.$skl.']',
                                                            [''=>'Select Skill'] + Helpers::getSkillsDropDown()->toArray() + ['35'=>'Other'],
                                                            (isset($skill->skill_id) && !empty($skill->skill_id)) ? $skill->skill_id : (old('skill_id') ? old('skill_id') : ''),
                                                            array('id' => 'skill_id'.$skl,
                                                            'data' => $skl,
                                                            'class'=>'form-control clsRequired is_required skill',
                                                            ))
                                                            !!}
                                                          @php $arrOtherdata = Helpers::getOtherSkillsbyId($skill->id,$skill->user_id) @endphp
                                                         
                                                            
                                                         
                                                            </div>
                                                            <div class="col-md-2 text-center"> <input type="text" name="other_skill[{{ $skl}}]" id="other_{{$skl}}" value="{{($arrOtherdata)?$arrOtherdata->other_skill:''}}" class="clsRequired form-control required" value=""/></div>
                                                            <div class="col-md-2 text-center">
                                                            <label class="radio-bg text-center">
                                                            <input type="radio" value="1" id="beginner{{$skl}}" @if(isset($skill->level) && !empty($skill->level) && $skill->level == 1) checked @elseif(old('level') && old('level')==1) checked @else '' @endif name="level[{{$skl}}]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                            <label class="radio-bg text-center">
                                                            <input type="radio" value="2" id="intermediate{{$skl}}" @if(isset($skill->level) && !empty($skill->level) && $skill->level == 2) checked @elseif(old('level') && old('level')==2) checked @else '' @endif name="level[{{$skl}}]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                             <label class="radio-bg  text-center">
                                                            <input type="radio" value="3" id="professional{{$skl}}" @if(isset($skill->level) && !empty($skill->level) && $skill->level == 3) checked @elseif(old('level') && old('level')==3) checked @else '' @endif name="level[{{$skl}}]">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                            </div>
                                                            <div class="col-md-1 text-center">
                                                            <span ><i style="@if($skl >=1)display:block @else display:none @endif" class="fa fa-trash-o deleteSkillbtn deleteSkill" title="Remove" aria-hidden="true"></i></span>
                                                           
                                                        </label>
                                                            </div>
                                                            

                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                     
                                                </div>
                                                @endforeach
                                                @endif    
                                            </div>
                                            
                                            <span class="add-skills  btn btn-primary mb-2" style="@if(count($skillData) < 5)visibility:visible @else visibility:hidden @endif"><span>+</span> Add Skills</span>
                                        </div>
                                            
                                    </div>
      </div>
    </div>
  </div>
  
  <div class="card">
    <div class="card-header">Research Publication
      <a class="collapsed card-link" data-toggle="collapse" href="#collapsefour">
        <i class="fa fa-edit" ></i>
      </a>
    </div>
    <div id="collapsefour" class="collapse" data-parent="#accordion">
      <div class="card-body">
                       <div id="reasearch">
                                        <div class="col-md-12">
                                            @if(count($researchData) == 0)
                                            <div class="reasearch clonedResearch" id="clonedResearch0">
                                                <div class=" row reasearch-edit">
                                                    <div class=" col-sm-3">
                                                        <div class="form-group">
                                                            <label>Title<small>*</small></label>
                                                             <input type="text" name="res_title[0]" id="res_title0" class="form-control is_required clsRequired" placeholder="Title">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Journal/Magazine</label>
                                                                <input type="text" name="journal_magazine[0]" id="journal_magazine0" class="form-control clsRequired is_required" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6 small-select form-group">
                                                        <div class="row">
                                                            <div class="col-sm-12">

                                                                <label>Month/Year of Publication
                                                                </label>

                                                            </div>
                                                            <div class="text-field-select col-md-6">
                                                                {!!
                                                            Form::select('publication_month[0]',
                                                            [''=>'Select Month'] + Helpers::getMonthDropdown(),
                                                            null,
                                                            array('id' => 'publication_month0',
                                                            'class'=>'form-control select2Cls clsRequired is_required'))
                                                            !!}

                                                            </div>

                                                            <div class="text-field-select col-md-6">
                                                                    {!!
                                                                Form::select('publication_year[0]',
                                                                [''=>'Select year'] + Helpers::getYearDropdown(),
                                                                null,
                                                                array('id' => 'publication_year0',
                                                                'class'=>'form-control select2Cls clsRequired is_required'))
                                                                !!}
                                                            </div>
                                                        </div>
                                                        
                                                    </div> <div class="clearfix"></div> </div>
                                                
                                                    <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group ">

                                                            <div class="file-browse">
                                                                                                                             
                                                                <span class="button button-browse">
                                                                  <img src="{{asset('frontend/inside/images/icon/attachment-icon.svg')}}">
                                                                  <input name ="attachment[0]" id="attachment0" type="file" class = "attach_photo is_required">
                                                                <label class = "fileslabel" id ="picName0">Attach File ( one attachment at a time )</label>
                                                                
                                                               </span>
                                                                
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <span><i style="display:none" class="fa fa-trash-o deleteResearch deleteResearchbtn" title="Remove" aria-hidden="true"></i></span>
                                            </div>
                                            @else
                                            @foreach($researchData as $res => $research)
                                            <div class="reasearch clonedResearch" id="clonedResearch{{$res}}">
                                                <div class=" row reasearch-edit">
                                                    <div class=" col-sm-3">
                                                        <div class="form-group">
                                                            <label>Title<small>*</small></label>
                                                             <input type="text" value = "{{(isset($research->title) && !empty($research->title)) ? $research->title : (old('title') ? old('title') : '')}}" name="res_title[{{$res}}]" id="res_title{{$res}}" class="form-control is_required clsRequired" placeholder="Title">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Journal/Magazine</label>
                                                                <input type="text" value = "{{ (isset($research->journal_magazine)?$research->journal_magazine :'')}}" name="journal_magazine[{{$res}}]" id="journal_magazine{{$res}}" class="form-control is_required clsRequired" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6 small-select form-group">
                                                        <div class="row">
                                                            <div class="col-sm-12">

                                                                <label>Month/Year of Publication
                                                                </label>

                                                            </div>
                                                            <div class="text-field-select col-md-6">
                                                                {!!
                                                            Form::select('publication_month['.$res.']',
                                                            [''=>'Select Month'] + Helpers::getMonthDropdown(),
                                                            (isset($research->publication_month) && !empty($research->publication_month)) ? $research->publication_month : (old('publication_month') ? old('publication_month') : ''),
                                                            array('id' => 'publication_month'.$res,
                                                            'class'=>'form-control select2Cls clsRequired is_required'))
                                                            !!}

                                                            </div>

                                                            <div class="text-field-select col-md-6">
                                                                    {!!
                                                                Form::select('publication_year['.$res.']',
                                                                [''=>'Select year'] + Helpers::getYearDropdown(),
                                                                (isset($research->publication_year) && !empty($research->publication_year)) ? $research->publication_year : (old('publication_year') ? old('publication_year') : ''),
                                                                array('id' => 'publication_year'.$res,
                                                                'class'=>'form-control select2Cls clsRequired is_required'))
                                                                !!}
                                                            </div>
                                                        </div>
                                                        
                                                    </div> <div class="clearfix"></div> </div>
                                                
                                                    <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group ">

                                                            <div class="file-browse">
                                                                                                                             
                                                                <span class="button button-browse">
                                                                  <img class ="up-image"  src="{{asset('frontend/inside/images/icon/attachment-icon.svg')}}">
                                                                  <input style="font-size: 1px;" name ="attachment[{{$res}}]" type="file" id="attachment{{$res}}" class = "attach_photo is_required">
                                                                <label class ="fileslabel" id ="picName{{$res}}" >Attach File ( one attachment at a time )</label>
                                                                <input type="hidden" name="tmp_research" id="tmp_research" value="{{ (@$research['tmp_research']) ? @$research['tmp_research'] : ''  }}">
                                                               </span>
                                                                
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <span><i style="@if($res >=1)display:block @else display:none @endif" class="fa fa-trash-o deleteResearch deleteResearchbtn" title="Remove" aria-hidden="true"></i></span>
                                            </div>
                                           @endforeach 
                                           @endif
                                        </div>
                                                                                    
                                        <span class="add-reasearch btn btn-primary mb-2"><span>+</span> Add Research Publication</span>
                                    </div>
                                  </div>
                                </div>
                              </div>
        
  <div class="card">
                                <div class="card-header">Awards & Honors
                                  <a class="collapsed card-link" data-toggle="collapse" href="#collapsefive">
                                   <i class="fa fa-edit" ></i>
                                  </a>
                                </div>
                                <div id="collapsefive" class="collapse" data-parent="#accordion">
                                  <div class="card-body">
                                        <div id="awards">

                                        <div class="col-md-12">
                                            <div class="awards">
                                                @if(count($awardsData) == 0)
                                                <div class="awards-edit clonedAwards" id="clonedAwards0">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Title<small>*</small></label>
                                                             <input  type="text"name="title[0]" id="title0" class="form-control clsRequired is_required" placeholder="Title">    
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group ">
                                                            <label>Brief Description</label>
                                                            <div data-error="hasError" class="textarea-field">
                                                                <textarea name="description[0]" id="description0" class="form-control clsRequired is_required"  placeholder="Brief Description"></textarea>    
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span><i style="display:none" class="fa fa-trash-o deleteAwards deleteAwardsbtn" title="Remove" aria-hidden="true"></i></span>
                                                    
                                                </div>
                                                @else
                                                 @foreach($awardsData as $aws => $awards)
                                                <div class="awards-edit clonedAwards" id="clonedAwards{{$aws}}">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Title<small>*</small></label>
                                                             <input  type="text" value = "{{(isset($awards->title) && !empty($awards->title)) ? $awards->title : (old('title') ? old('title') : '')}}" name="title[{{$aws}}]" id="title{{$aws}}" class="form-control clsRequired is_required" placeholder="Title">    
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group ">
                                                            <label>Brief Description</label>
                                                            <div data-error="hasError" class="textarea-field">
                                                                <textarea name="description[{{$aws}}]" id="description{{$aws}}" class="form-control"  placeholder="Brief Description">{{(isset($awards->description) && !empty($awards->description)) ? $awards->description : (old('description') ? old('description') : '')}}</textarea>    
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span><i style="@if($aws >=1)display:block @else display:none @endif" class="fa fa-trash-o deleteAwards deleteAwardsbtn" title="Remove" aria-hidden="true"></i></span>
                                                    
                                                </div>
                                                 @endforeach
                                                 @endif
                                            </div>

                                        </div>
                                       <span class="add-awards btn btn-primary mb-2"><span>+</span> Add Awards & Honors</span>
  
<!--                                        <div class="row">
                                            <div class="col-xs-12">
                                              <label class="checkbox text-left check-left">
                                                <input type="checkbox" value="remember-me">
                                                <span class="label-text">I accept the terms and conditions for signing up to this service, and hereby confirm I have read the privacy policy.</span>
                                              </label>
                                            </div>
                                        </div>-->
                                        
                                      </div>
                                      </div>
                                    </div>
                                  </div>
                                 <div class="pull-right">
                                    <button type="submit" class="mysubmit btn btn-save" name="save" value="Save" style="">Save</button></div>      
                                </div>
                                </div></div>
{{Form::close()}}
</section>
                                        <!-- / END SECTION -->
<!-- / END SECTION -->
@endsection
@section('pageTitle')
Profile
@endsection


@section('additional_css')
<style>
    .up-image:hover{cursor: pointer}
    .error{color:red;}
    
</style>    
@endsection
@section('jscript')
<script>
    var messages = {
        education_form_limit:"{{ config('common.EDUCATION_FORM_LIMIT') }}",
         skill_form_limit:"{{ config('common.SKILL_FORM_LIMIT') }}",
        req_course_name: "{{ trans('error_messages.req_course_name') }}",
        research_form_limit:"{{ config('common.RESEARCH_FORM_LIMIT') }}",
        award_form_limit:"{{ config('common.AWARD_FORM_LIMIT') }}",
        is_scout: "{{ $is_scout }}",
    };
</script>
<script src="{{ asset('frontend/outside/js/validation/personalProgile.js')}}"></script>
<script src="{{ asset('frontend/outside/js/validation/education.js')}}"></script>
<script src="{{ asset('frontend/outside/js/validation/skills.js')}}"></script>
<script src="{{ asset('frontend/outside/js/validation/research.js')}}"></script>
<script src="{{ asset('frontend/outside/js/validation/awards.js')}}"></script>
@endsection
