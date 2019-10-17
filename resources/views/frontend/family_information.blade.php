@extends('layouts.app')

@section('content')


<section>
    <div class="container">
        <div class="row">
            <div id="header" class="col-md-3">
                @include('layouts.user-inner.left-menu')
            </div>
            <div class="col-md-9 dashbord-white">
                <div class="form-section">
                    <div class="row marB10">
                        <div class="col-md-12">
                            <h3 class="h3-headline">Family Information</h3>
                        </div>
                    </div>   

                    {!!
                    Form::open(
                    array(
                    'name' => 'familyInformationForm',
                    'id' => 'familyInformationForm',
                    'url' => route('family_information',['id'=>@$userData['user_kyc_family_id'],'user_kyc_id'=>@$benifinary['user_kyc_id'],'corp_user_id'=>@$benifinary['corp_user_id'],'is_by_company'=>@$benifinary['is_by_company']]),
                    'autocomplete' => 'off','class'=>'loginForm form form-cls'
                    ))
                    !!}
            
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">

                                {{ Form::label('spouse_f_name', 'Spouse first name', array('class' => ''))}} <span class="mandatory">*<span> 
                                {{ Form::text('spouse_f_name',@$userData['spouse_f_name'], ['class' => 'form-control','placeholder'=>'Enter First Name','id' => 'spouse_f_name']) }}
                                <span class="text-danger">{{ $errors->first('spouse_f_name') }}</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('spouse_m_name', 'Spouse maiden name', array('class' => ''))}} <span class="mandatory">*<span> 
                                {{ Form::text('spouse_m_name',@$userData['spouse_m_name'], ['class' => 'form-control','placeholder'=>'Enter Middle Name','id' => 'spouse_m_name']) }}
                                <span class="text-danger">{{ $errors->first('spouse_m_name') }}</span>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('is_professional_status', 'Spouse professional Status', array('class' => ''))}} <span class="mandatory">*<span> 
                                {!!
                                Form::select('is_professional_status',
                                [''=>'Select professional status','1'=>'Employed','2'=>'Un-employed','3'=>'Self-employed','4'=>'Retired','5'=>'Other'],@$userData['is_spouse_profession'],
                                array('id' => 'is_professional_status','class'=>'form-control'))
                                !!}
                                <span class="text-danger">{{ $errors->first('is_professional_status') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    {{ Form::label('spouse_profession', "Spouse’s profession (if only)", array('class' => ''))}}
                                    {{ Form::text('spouse_profession',@$userData['spouse_profession'], ['class' => 'form-control','placeholder'=>'Enter Profession Name','id' => 'spouse_profession']) }}
                                     <span class="text-danger">{{ $errors->first('spouse_profession') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <div class="form-group">

                                    {{ Form::label('spouse_employer', "Spouse’s employer (if only)", array('class' => ''))}}
                                    {{ Form::text('spouse_employer',@$userData['spouse_employer'], ['class' => 'form-control','placeholder'=>'Enter Employer Name','id' => 'spouse_employer']) }}
                                     <span class="text-danger">{{ $errors->first('spouse_employer') }}</span>

                                </div>
                            </div>
                        </div>



                    </div>					

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">Children Information </label>
                            </div>
                        </div>	  	
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">{{ Form::checkbox('is_child',0,@$userData['is_child'], array('id'=>'is_child')) }} No Children</label>
                            </div>
                        </div>	  	
                    </div>

                    <?php
                    $childInfo=@$userData['spouce_child_info'];
                    $arrChildInfo=[];
                    if($childInfo!='' && $childInfo!=null){
                        $arrChildInfo=  json_decode($childInfo);
                    }
                    if(count($arrChildInfo)>0){
                    ?>
                    <div id="childInfo" class="is_child">
                    <?php
                        
                        foreach($arrChildInfo as $key=>$chinfo){
                       
                    ?>
                 
                    
                    
                        <div id="TrainingPeriod0" class="trainingperiod">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        {{ Form::label('child_name', "Child 1", array('class' => 'lblname'))}}
                                        {{ Form::text('child_name[]',$chinfo->child_name, ['class' => 'form-control','placeholder'=>'Enter Child Name','id' => 'child_name'.$key]) }}
                                         <span class="text-danger">{{ $errors->first('child_name.'.$key) }}</span>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('child_dob', "Date of Birth", array('class' => ''))}}
                                        <div class="input-group">
                                            {{ Form::text('child_dob[]',$chinfo->child_dob, ['class' => 'form-control datepicker','placeholder'=>'Select Date of Birth','id' => 'child_dobaa0']) }}
                                            <div class="input-group-append">
                                                <i class="fa fa-calendar-check-o"></i>
                                            </div>
                                            <span class="text-danger">{{ $errors->first('child_dob.0') }}</span>
                                        </div>
                                       
                                        <div class="deleteChildbtnbck remove"  style="display: none;"><i class="fa fa-trash-o deleteFamily" title="Remove" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   
                    <?php
                        }
                    ?>
                         </div>
                    <?php    
                    }else{
                    ?>
                    
                      <div id="childInfo" class="is_child">
                        <div id="TrainingPeriod0" class="trainingperiod">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        {{ Form::label('child_name', "Child 1", array('class' => 'lblname'))}}
                                        {{ Form::text('child_name[]','', ['class' => 'form-control','placeholder'=>'Enter Child Name','id' => 'child_name0']) }}
                                        <span class="text-danger">{{ $errors->first('child_name.0') }}</span>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('child_dob', "Date of Birth", array('class' => ''))}}
                                        <div class="input-group">
                                            {{ Form::text('child_dob[]','', ['class' => 'form-control datepicker','placeholder'=>'Select Date of Birth','id' => 'child_dobaa0']) }}
                                            <div class="input-group-append">
                                                <i class="fa fa-calendar-check-o"></i>
                                            </div>
                                            <span class="text-danger">{{ $errors->first('child_dob.0') }}</span>
                                        </div>
                                        <!--<a href="#" class="add-skills pull-right marT10 text-color">+Add</a>>-->
                                        <div class="deleteChildbtnbck remove"  style="display: none;"><i class="fa fa-trash-o deleteFamily" title="Remove" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                  
                    <?php
                    }
                    ?>
                   
                    
                    <div class="row is_child">
                        <div class="col-md-8"></div>
                        <div class="col-md-1">
                            <span class="add-child pull-right marT10 text-color" style="">+Add</span>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                 
                    <div class="row marT60">
                        <div class="col-md-12 text-right">
                            <a href="{{url('profile')}}" class="btn btn-prev">Previous</a>	

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

@endsection
@section('pageTitle')
Family Information
@endsection
@section('additional_css')
<link href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}" rel="stylesheet">
@endsection
@section('jscript')
<script src="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.js') }}"></script>
<script src="{{ asset('frontend/outside/js/validation/familyInfo.js')}}"></script>
<script src="{{ asset('frontend/outside/js/validation/familyInfoForm.js')}}"></script>
<script>
$(document).ready(function () {
    // $('.datepicker').datepicker();
    $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
    var date = $('.maturityDate').datepicker({dateFormat: 'yy-mm-dd'});

});


</script>
@endsection