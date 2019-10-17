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
                            <h3 class="h3-headline">Professional Information</h3>
                        </div>
                    </div>   
                    
                        {!!
                        Form::open(
                        array(
                        'name' => 'professionalInformationForm',
                        'id' => 'professionalInformationForm',
                        'url' => route('professional_information',['id'=>@$userData['user_kyc_prof_id'],'user_kyc_id'=>@$benifinary['user_kyc_id'],'corp_user_id'=>@$benifinary['corp_user_id'],'is_by_company'=>@$benifinary['is_by_company']]),
                        'autocomplete' => 'off','class'=>'loginForm form form-cls'
                        ))
                        !!}
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">

                                    {{ Form::label('prof_status','Profession Status',['class'=>''])}} <span class="mandatory">*<span> 
                                    {!!
                                    Form::select('prof_status',[''=>'Select Status','1'=>'Employed','2'=>'Unemployed','3'=>'Business Owner','4'=>'Self Employed','5'=>'At Home','6'=>'Retired','7'=>'Student','8'=>'Other'],@$userData['prof_status'],['id'=>'prof_status','class'=>'form-control']);
                                    !!}
                                    <span class="text-danger">{{ $errors->first('prof_status') }}</span>
                                </div>
                            </div>

                        </div>
                        <div class="row" id='otherProfStatus'>
                            <div class="col-md-12">
                                <div class="form-group">

                                    {{Form::label('other_prof_status','Other Profession Status',['class'=>''])}} <span class="mandatory">*<span> 
                                    {{Form::text('other_prof_status',@$userData['other_prof_status'],['class'=>'form-control','id'=>'other_prof_status'])}}
                                    <span class="text-danger">{{ $errors->first('other_prof_status') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">

                                    {{Form::label('prof_detail','Profession/ Occupation in detail Previous Profession/ Occupation if retired',['class'=>''])}} 
                                    {{Form::textarea('prof_detail',@$userData['prof_detail'],['class'=>'form-control','id'=>'prof_detail','rows'=>'3'])}}
                                    <span class="text-danger">{{ $errors->first('prof_detail') }}</span>
                                </div>
                            </div>
                        </div>	

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">

                                    {{Form::label('position_title','Position/ Job title Last Position/ Job title if retired',['class'=>''])}}
                                    {{Form::textarea('position_title',@$userData['position_title'],['class'=>'form-control','id'=>'position_title','rows'=>'3'])}}
                                    <span class="text-danger">{{ $errors->first('position_title') }}</span>
                                </div>
                            </div>
                        </div>			
                        <?php
                            $date_employment=(@$userData['date_employment']!='' && @$userData['date_employment']!=null) ? Helpers::getDateByFormat(@$userData['date_employment'], 'Y-m-d', 'd/m/Y') :'';
                        ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">

                                    {{Form::label('date_employment','Date of employment/ Retirement',['class'=>''])}}
                                    <div class="input-group">
                                        {{ Form::text('date_employment',$date_employment, ['class' => 'form-control datepicker','placeholder'=>'Select Date of Birth','id' => 'date_employment']) }}
                                        <div class="input-group-append">
                                            <i class="fa fa-calendar-check-o"></i>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('date_employment') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-group">
                                       
                                        {{Form::label('last_monthly_salary','Last monthly salary if retired',['class'=>''])}}
                                        {{Form::text('last_monthly_salary',@$userData['last_monthly_salary'],['class'=>'form-control','id'=>'last_monthly_salary','placeholder'=>'Enter here'])}}
                                        <span class="text-danger">{{ $errors->first('last_monthly_salary') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>	


                        <div class="row marT60">
                            <div class="col-md-12 text-right">
                                <a href="{{route('residential_information')}}" class="btn btn-prev">Previous</a>	
                                {{Form::submit('Save',['class'=>'btn btn-save','name'=>'save','id'=>'saveBtn'])}}
                                {{Form::submit('Save & Next',['class'=>'btn btn-save','name'=>'save_next','id'=>'saveNextBtn'])}}
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
                <h4 class="headline-h4 marB15">Dear Applicant;</h4>
                <p>Welcome to the Compliance platform of Dexter Capital Financial Consultancy LLC. </p>

                <p> According to the United Arab Emirates rules and regulations and the International applicable laws, you are kindly requested to proceed with the due diligence application allowing you to validate your profile and access many financial platforms.</p>
                <p> Dexter Capital Financial Consultancy LLC being regulated by Securities and Commodities Authority in the UAE, is committed to maintain all your information confidential and highly protected by the most sophisticated security tools and is in full compliance with the requirements of the European Union related to the General Data Protection Regulation (GDPR). <a href="https://ec.europa.eu/info/law/law-topic/data-protection/data-protection-eu_en"> https://ec.europa.eu/info/law/law-topic/data-protection/data-protection-eu_en</a></p>
            </div>

        </div>
    </div>
</div>
@endsection

@section('pageTitle')
Professional Information
@endsection

@section('additional_css')
<link href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}" rel="stylesheet">
@endsection
@section('jscript')
<script src="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.js') }}"></script>
<script src="{{ asset('frontend/outside/js/validation/professionalInfoForm.js')}}"></script>
<script>
$(document).ready(function () {

   var date = $('.datepicker').datepicker({dateFormat: 'dd/mm/yy', maxDate: new Date(), changeMonth: true, changeYear: true});

        $('.datepicker').keydown(function (e) {
            e.preventDefault();
            return false;
        });

        $('.datepicker').on('paste', function (e) {
            e.preventDefault();
            return false;
        });


});
//
    if($("#prof_status option:selected").text()=='Other'){
        $('#otherProfStatus').show();

    }else{
        $('#otherProfStatus').hide();
    }
        
    $("#prof_status").on('change',function(){
        var prof1    =   ['1','3','4'];
        var prof2    =   ['1','3','4','6'];
        if($("#prof_status option:selected").text()=='Other'){
            
            $('#otherProfStatus').show();
        }else{
            $('#otherProfStatus').hide();
        }
        var selVal  =   $("#prof_status option:selected").val();
        if($.inArray(selVal,prof1)){
            
        }
        
    });


</script>
@endsection