@extends('layouts.app')

@section('content')

<section>
    <div class="container">
        <div class="row">
            <div id="header" class="col-md-3">
                @include('layouts.partials.left-menu')

            </div>
            
            <div class="col-md-9 dashbord-white">
                <div class="form-section">
                    <div class="row marB10">
                        <div class="col-md-12">
                            <h3 class="h3-headline">{{trans('master.personalProfile.item1')}}</h3>
                        </div>
                    </div>

                    {!!
                    Form::open(
                    array(
                    'name' => 'personalInformationForm',
                    'id' => 'personalInformationForm',
                    'url' => route('update_personal_profile',['id'=>@$userPersonalData['user_personal_id'],'user_kyc_id'=>@$benifinary['user_kyc_id'],'corp_user_id'=>@$benifinary['corp_user_id'],'is_by_company'=>@$benifinary['is_by_company']]),
                    'autocomplete' => 'off','class'=>'loginForm form form-cls'
                    ))
                    !!}
                    
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{Form::label('f_name','First Name')}} <span class="mandatory">*<span> 
                                    <div class="input-group input-action">
                                        <div class="input-group-btn">
                                            {!!
                                            Form::select('title',
                                            ['Mr.'=>'Mr.',' Ms.'=>' Ms.','Mrs'=>'Mrs','Dr.'=>'Dr.'],@$userPersonalData['title'],
                                            array('id' => 'title','class'=>'form-control drop-title'))
                                            !!}
                                        </div>
                                        <input type="text" class="form-control"   placeholder="Enter First Name" name="f_name"  value="{{ (@$userPersonalData['f_name'])}}">

                                    </div>
                                    <span class="text-danger">{{ $errors->first('f_name') }}</span>                 
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                {{Form::label('m_name','Middle Name')}}
                                <input type="text" class="form-control"  placeholder="Enter middle name" name="m_name" value="{{ (@$userPersonalData['m_name']) }}">
                                <span class="text-danger">{{ $errors->first('m_name') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{Form::label('l_name','Last Name')}} <span class="mandatory">*<span>
                                <input type="text" class="form-control"  placeholder="Enter Last Name" name="l_name" value="{{ (@$userPersonalData['l_name'])}}">
                                <span class="text-danger">{{ $errors->first('l_name') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">

                                {{Form::label('gender','Gender')}} <span class="mandatory">*<span>
                                {!!
                                Form::select('gender',
                                [''=>'Select Gender ','M'=>'Male','F'=>'Female'],@$userPersonalData['gender'],
                                array('id' => 'gender','class'=>'form-control'))
                                !!}
                                <span class="text-danger">{{ $errors->first('gender') }}</span>
                            </div>
                        </div>
                        <?php
                        $date_of_birth = (@$userPersonalData['date_of_birth'] != '' && @$userPersonalData['date_of_birth'] != null) ? Helpers::getDateByFormat(@$userPersonalData['date_of_birth'], 'Y-m-d', 'd/m/Y') : '';
                        ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('date_of_birth', "Date of Birth", array('class' => ''))}} <span class="mandatory">*<span>
                                <div class="input-group">
                                    {{ Form::text('date_of_birth',$date_of_birth, ['class' => 'form-control datepicker','placeholder'=>'Select Date of Birth','id' => 'date_of_birth']) }}
                                    <div class="input-group-append">
                                        <i class="fa fa-calendar-check-o"></i>
                                    </div>
                                </div>
                                <span class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">{{trans('master.personalProfile.nationality')}}</label> <span class="mandatory">*<span>
                                {!!
                                Form::select('birth_country_id',
                                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                                @$userPersonalData['birth_country_id'],
                                array('id' => 'birth_country',
                                'class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('birth_country_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">State of birth</label> 

                                {!!
                                Form::select('birth_state_id',
                                [''=>'Select State of birth','1'=>'Uttar Pradesh','2'=>'Madhya Pradesh '],@$userPersonalData['birth_state_id'],
                                array('id' => 'birth_state_id','class'=>'form-control'))
                                !!}
                                <span class="text-danger">{{ $errors->first('birth_state_id') }}</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">City of birth</label> <span class="mandatory">*<span>

                                {{Form::text('birth_city_id',@$userPersonalData['birth_city_id'],['class'=>'form-control','placeholder'=>'Enter City of birth','id' => 'birth_city_id'])}}
                                <span class="text-danger">{{ $errors->first('birth_city_id') }}</span>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">Father's Name</label> <span class="mandatory">*<span>
                                <input type="text" class="form-control"  placeholder="Enter Father's Name" name="father_name" value="{{@$userPersonalData['father_name']}}" >
                                <span class="text-danger">{{ $errors->first('father_name') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">Mother's first name</label> <span class="mandatory">*<span>
                                <input type="text" class="form-control"  placeholder="Enter Mother's first name" name="mother_f_name" value="{{@$userPersonalData['mother_f_name']}}" >
                                <span class="text-danger">{{ $errors->first('mother_f_name') }}</span>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">Mother's maiden name</label> <span class="mandatory">*<span>
                                <input type="text" class="form-control"  placeholder="Enter Mother's maiden name" name="mother_m_name" value="{{@$userPersonalData['mother_m_name']}}">
                                <span class="text-danger">{{ $errors->first('mother_m_name') }}</span>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reg_no">Registration No</label> <span class="mandatory">*<span>
                                <input type="text" class="form-control"  placeholder="Enter Registration No" name="reg_no" id="registration_no" value="{{@$userPersonalData['reg_no']}}">
                                <span class="text-danger">{{ $errors->first('reg_no') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reg_place">Registration  Place</label> <span class="mandatory">*<span>
                                <input type="text" class="form-control"  placeholder="Enter Registration  Place" name="reg_place"  id="registration_place" value="{{@$userPersonalData['reg_place']}}">
                                <span class="text-danger">{{ $errors->first('reg_place') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                 
                                {{Form::label('f_nationality_id','Nationality ',['class'=>''])}} <span class="mandatory">*<span>
                                {!!
                                Form::select('f_nationality_id',
                                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                                @$userPersonalData['f_nationality_id'],
                                array('id' => 'nationality',
                                'class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('f_nationality_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{Form::label('sec_nationality_id','Secondary Nationality',['class'=>''])}}
                                {!!
                                Form::select('sec_nationality_id',
                                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                                @$userPersonalData['sec_nationality_id'],
                                array('id' => 'secondry_nationality',
                                'class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('sec_nationality_id') }}</span>
                            </div>
                        </div>
                    </div>

                    @if(is_array($userDocumentType) && count($userDocumentType))
                    <div id="childInfo">
                        @php
                        $i=0;
                        @endphp
                        @foreach($userDocumentType as $objDoc)
                        @php
                        $issuance_date=(@$objDoc['issuance_date']!='' && @$objDoc['issuance_date']!=null) ? Helpers::getDateByFormat($objDoc['issuance_date'], 'Y-m-d', 'd/m/Y') :'';
                        $expire_date=(@$objDoc['expire_date']!='' && @$objDoc['expire_date']!=null) ? Helpers::getDateByFormat($objDoc['expire_date'], 'Y-m-d', 'd/m/Y') :'';
                        @endphp
                        <div id="TrainingPeriod{{$i}}" class="trainingperiod">
                            <div class="row">
                                <div class="col-md-3">


                                    <div class="form-group">
                                        <label for="pwd">{{trans('master.personalProfile.document_type')}}</label> <span class="mandatory">*<span>
                                        {!!
                                        Form::select('document_type_id[]',
                                        [''=>'Select Document Type'] + Helpers::getDocumentsDropDown()->toArray(),
                                        $objDoc['document_type'],
                                        array('id' => 'document_id'.$i,'data' => $i,
                                        'class'=>'form-control clsRequired is_required Document'))
                                        !!}

                                        <span class="text-danger">{{ $errors->first('document_type_id.0') }}</span>

                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="pwd">Document Number</label>
                                        <input type="text" class="form-control "  placeholder="Document Number" name="document_number[]" value="{{$objDoc['document_number']}}" id="document_number{{$i}}">
                                         <span class="text-danger">{{ $errors->first('document_number.0') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{ Form::label('issuance_date', "Issuance Date", array('class' => ''))}}
                                        <div class="input-group">
                                            {{ Form::text('issuance_date[]',$issuance_date, ['class' => 'form-control datepicker issuance-date','placeholder'=>'Issuance Date','id' => 'issuance_date'.$i]) }}
                                            <div class="input-group-append">
                                                <i class="fa fa-calendar-check-o"></i>
                                            </div>
                                        </div>
                                        
                                        <span class="text-danger">{{ $errors->first('issuance_date.0') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{ Form::label('expiry_date', "Expiry Date", array('class' => ''))}}
                                        <div class="input-group">
                                            {{ Form::text('expiry_date[]',$expire_date, ['class' => 'form-control datepicker','placeholder'=>'Expiry Date','id' => 'expiry_date'.$i]) }}
                                            <div class="input-group-append">
                                                <i class="fa fa-calendar-check-o"></i>
                                            </div>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('expiry_date.0') }}</span>
                                        <div class="deleteDocumentbtn remove "  style="display:block;"><i class="fa fa-trash-o deleteSkill" title="Remove" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php $i++; @endphp
                        @endforeach
                        <div class="row">
                            <div class="col-md-12">
                                <span class="add-Documents pull-right marT10 text-color" style="">{{trans('master.personalProfile.add')}}</span>
                            </div>
                        </div>
                    </div>
                    @else

                    <div id="childInfo">
                        <div id="TrainingPeriod0" class="trainingperiod">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <label for="pwd">{{trans('master.personalProfile.document_type')}}</label>
                                        @php

                                        $result=Helpers::getUserDocuments();
                                        @endphp
                                        <select name="document_type_id[]" class="form-control" >
                                            <option value="">Select Document Type</option>
                                        @foreach($result as $val)
                                         <option value="{{$val->user_req_doc_id}}"> {{$val->upload_doc_name}} 
                                         </option>        
                                         @endforeach
                                        </select>

                                        <span class="text-danger">{{ $errors->first('document_type_id[]') }}</span>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="pwd">Document Number</label>
                                        <input type="text" class="form-control"  placeholder="Document Number" name="document_number[]" id="document_number0">
                                        <span class="text-danger">{{ $errors->first('document_number.0') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{ Form::label('issuance_date', "Issuance Date", array('class' => ''))}}
                                        <div class="input-group">
                                            {{ Form::text('issuance_date[]','', ['class' => 'form-control datepicker issuance-date','placeholder'=>'Issuance Date','id' => 'issuance_date0']) }}
                                            <div class="input-group-append">
                                                <i class="fa fa-calendar-check-o"></i>
                                            </div>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('issuance_date.0') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{ Form::label('expiry_date', "Expiry Date", array('class' => ''))}}
                                        <div class="input-group">
                                            {{ Form::text('expiry_date[]','', ['class' => 'form-control datepicker','placeholder'=>'Expiry Date','id' => 'expiry_date0']) }}
                                            <div class="input-group-append">
                                                <i class="fa fa-calendar-check-o"></i>
                                            </div>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('expiry_date.0') }}</span>
                                        <div class="deleteDocumentbtn remove "  style="display: none;"><i class="fa fa-trash-o deleteSkill" title="Remove" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <span class="add-Documents pull-right marT10 text-color" style="">{{trans('master.personalProfile.add')}}</span>
                        </div>
                    </div>
                    @endif

                    @if(is_array($userSocialMedia) && count($userSocialMedia))
                    @php
                    $j=0;
                    @endphp
                    @foreach($userSocialMedia as $objSocial)
                    <div class="row clonedclonedSocialmedias" id="clonedSocialmedias0">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pwd">{{trans('master.personalProfile.social_media')}}</label> <span class="mandatory">*<span>
                                {!!
                                Form::select('social_media_id[]',
                                [''=>'Select Social Media'] + Helpers::getSocialmediaDropDown()->toArray(),
                                $objSocial['social_media'],
                                array('id' => 'social_media_id'.$j,'data' => 0,
                                'class'=>'form-control'))
                                !!}
                                <span class="text-danger">{{ $errors->first('social_media_id.0') }}</span>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pwd">Social Media Link</label>
                                <input type="text" class="form-control"  placeholder="Enter Social Media Link" name='social_media_link[]' value="{{$objSocial['social_media_link']}}" id='social_media_link{{$j}}}'>
                                <span class="text-danger">{{ $errors->first('social_media_link[]') }}</span>
                                <div class="deleteSkillbtn remove "  style="display:block;"><i class="fa fa-trash-o deleteSkill" title="Remove" aria-hidden="true"></i></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @php $j++; @endphp
                    <div class="row">
                        <div class="col-md-12">
                            <span class="add-socialmedia pull-right marT10 text-color" style="">{{trans('master.personalProfile.add')}}</span>
                        </div>
                    </div>
                    @else 
                    <div class="row clonedclonedSocialmedias" id="clonedSocialmedias0">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pwd">{{trans('master.personalProfile.social_media')}}</label> <span class="mandatory">*<span>
                                {!!
                                Form::select('social_media_id[]',
                                [''=>'Select Social Media'] + Helpers::getSocialmediaDropDown()->toArray(),
                                null,
                                array('id' => 'social_media_id0','data' => 0,
                                'class'=>'form-control'))
                                !!}

                                <span class="text-danger">{{ $errors->first('social_media_id.0') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pwd">Social Media Link</label>
                                <input type="text" class="form-control"  placeholder="Enter Social Media Link" name='social_media_link[]' id='social_media_link0'>
                                <span class="text-danger">{{ $errors->first('social_media_link.0') }}</span>
                                <div class="deleteSkillbtn remove "  style="display: none;"><i class="fa fa-trash-o deleteSkill" title="Remove" aria-hidden="true"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <span class="add-socialmedia pull-right marT10 text-color" style="">{{trans('master.personalProfile.add')}}</span>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <hr></hr>
                        </div>
                    </div>
                    {{ (isset($userPersonalData->residence_status) ? ( (1 == $userPersonalData->residence_status) ? 'selected' : '' ) : '')}}

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{Form::label('residence_status','Residence Status',['class'=>''])}} <span class="mandatory">*<span>

                                {!!
                                Form::select('residence_status',
                                [''=>'Select Residence Status','1'=>'Resident','2'=>'Non-Resident'],
                                @$userPersonalData['residence_status'],
                                array('id' => 'residence_status',
                                'class'=>'form-control'))
                                !!}
                                <span class="text-danger">{{ $errors->first('residence_status') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{Form::label('family_status','Family Status',['class'=>''])}} <span class="mandatory">*<span>

                                {!!
                                Form::select('family_status',
                                [''=>'Select Family Status','1'=>'Single','2'=>'Married','3'=>'Divorced','4'=>'Separated','5'=>'Minor','6'=>'Engaged','7'=>'Widowed'],
                                @$userPersonalData['family_status'],
                                array('id' => 'family_status',
                                'class'=>'form-control'))
                                !!}
                                <span class="text-danger">{{ $errors->first('family_status') }}</span>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{Form::label('guardian_name','Legal Guardian’s Name (if applicable)',['class'=>''])}}
                                {{Form::text('guardian_name',@$userPersonalData['guardian_name'],['class'=>'form-control','palceholder'=>'Enter Guardian\'s name','id'=>'guardian_name'])}}
                                <span class="text-danger">{{ $errors->first('guardian_name') }}</span>
                            </div>
                        </div>
                        @php
                        $legal_maturity_date=(@$userPersonalData['legal_maturity_date']!='' && @$userPersonalData['legal_maturity_date']!=null) ? Helpers::getDateByFormat(@$userPersonalData['legal_maturity_date'], 'Y-m-d', 'd/m/Y') :'';

                        @endphp
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('legal_maturity_date', "Legal maturity date", array('class' => ''))}}
                                <div class="input-group">

                                    {{ Form::text('legal_maturity_date',$legal_maturity_date, ['class' => 'form-control datepicker','placeholder'=>'Legal Maturity Date','id' => 'legal_maturity_date']) }}
                                    <div class="input-group-append">
                                        <i class="fa fa-calendar-check-o"></i>
                                    </div>
                                </div>
                                <span class="text-danger">{{ $errors->first('legal_maturity_date') }}</span>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{Form::label('educational_level','Educational Level',['class'=>''])}} <span class="mandatory">*<span>

                                {!!
                                Form::select('educational_level',
                                [''=>'Select Educational Level','1'=>'Less than Baccalaureate','2'=>'Baccalaureate','3'=>'Bachelor','4'=>'Post Graduated','5'=>'Illiterate','6'=>'Other'],
                                @$userPersonalData['educational_level'],
                                array('id' => 'educational_level',
                                'class'=>'form-control'))
                                !!}  
                                <span class="text-danger">{{ $errors->first('educational_level') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{Form::label('is_residency_card','Do you have any residency card',['class'=>''])}} <span class="mandatory">*<span>

                                {!!
                                Form::select('is_residency_card',
                                [''=>'Select ','1'=>'No','2'=>'Resident Alien Card (Green Card)','3'=>'Carte de sejour (France)','4'=>'Permanent Residency Card (Canada)']+['5'=>'Others'],
                                @$userPersonalData['is_residency_card'],
                                array('id' => 'is_residency_card','class'=>'form-control'))
                                !!}
                                <span class="text-danger">{{ $errors->first('is_residency_card') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pwd">{{trans('master.personalProfile.current_position')}}</label>
                                <div class="clearfix marB15"></div>
                                <div class="form-check-inline">
                                    <label class="form-check-label" for="check1">
<!--                                            <input type="radio" class="form-check-input" id="check1" name="political_position" value="public-sector"  {{ (isset($userPersonalData->political_position) ? ( ('public-sector' == $userPersonalData->political_positions) ? 'selected' : '' ) : '')}}>Senior position in the public sector-->
                                        {{Form::radio('political_position','public-sector',(@$userPersonalData['political_position']=='public-sector')?true:false,['class'=>'form-check-input','id'=>'check1'])}} Senior position in the public sector
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label" for="check2">

                                        {{Form::radio('political_position_dec','political-position',(@$userPersonalData['political_position']=='political-position')?true:false,['class'=>'form-check-input','id'=>'check1'])}} Political position
                                    </label>
                                </div>
                                <span class="text-danger">{{ $errors->first('political_position_dec') }}</span>
                            </div>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {{Form::label('','If yes, please specify position (s)',['class'=>''])}}
                            {{Form::textarea('political_position_dec',@$userPersonalData['political_position_dec'],['class'=>'form-control','id'=>'specify_position','rows'=>'3'])}}
                            <span class="text-danger">{{ $errors->first('political_position_dec') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.personalProfile.related_current_position')}}</label>
                            <div class="clearfix marB15"></div>
                            <div class="form-check-inline">
                                <label class="form-check-label" for="check3">
<!--                                            <input type="radio" class="form-check-input" id="check1" name="political_position" value="public-sector"  {{ (isset($userPersonalData->political_position) ? ( ('public-sector' == $userPersonalData->political_positions) ? 'selected' : '' ) : '')}}>Senior position in the public sector-->
                                    {{Form::radio('related_political_position','public-sector',(@$userPersonalData['related_political_position']=='public-sector')?true:false,['class'=>'form-check-input'])}} Senior position in the public sector
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label" for="check4">

                                    {{Form::radio('related_political_position','political-position',(@$userPersonalData['related_political_position']=='political-position')?true:false,['class'=>'form-check-input'])}} Political position
                                </label>
                            </div>
                            <span class="text-danger">{{ $errors->first('related_political_position') }}</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {{Form::label('','If yes, please specify position (s)',['class'=>''])}}
                            {{Form::textarea('related_political_position_dec',@$userPersonalData['related_political_position_dec'],['class'=>'form-control','id'=>'related_political_position_dec','rows'=>'3'])}}
                            <span class="text-danger">{{ $errors->first('related_political_position_dec') }}</span>
                        </div>
                    </div>
                </div>


                <div class="row marT60">
                    <div class="col-md-12 text-right">


                    {{ Form::submit('Save',['class'=>'btn btn-save','name'=>'save']) }}
                    {{ Form::submit('Save & Next',['class'=>'btn btn-save','name'=>'save_next']) }}


                    </div>
                </div>
            {{ Form::close() }}
            </div>
        </div>

    </div>
</div>
</section>

                                            <!--models-->
<div class="modal model-popup" id="myModal">
   <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>

                                                        <!-- Modal body -->
            <div class="modal-body">
                <h4 class="headline-h4 marB15">{{trans('master.personalProfile.message1')}}</h4>
                <p>{{trans('master.personalProfile.message2')}}</p>

                <p>{{trans('master.personalProfile.message3')}}</p>
                <p>{{trans('master.personalProfile.message4')}}<a href="{{trans('master.personalProfile.message5')}}">{{trans('master.personalProfile.message5')}}</a></p>
            </div>

        </div>
    </div>
</div>


@endsection

@section('pageTitle')
Personal Information
@endsection
@section('additional_css')
<!--<link rel="stylesheet" href="{{ asset('backend/theme/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}">-->
<link href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}" rel="stylesheet">

@endsection
@section('jscript')
 

<script src="{{ asset('frontend/outside/js/validation/socialmedia.js')}}"></script>
<script src="{{ asset('frontend/outside/js/validation/personalForm.js')}}"></script>


<script src="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.js') }}"></script>   
<script>
$(document).ready(function () {

    $('#date_of_birth').datepicker({dateFormat: 'dd/mm/yy', maxDate: new Date(), changeMonth: true, changeYear: true});
    $('#legal_maturity_date').datepicker({dateFormat: 'dd/mm/yy', maxDate: new Date(), changeMonth: true, changeYear: true});
    $('.issuance-date').datepicker({
        dateFormat: 'dd/mm/yy',
        maxDate: new Date(),
    });


});



</script>
@endsection
