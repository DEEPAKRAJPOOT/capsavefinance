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
                            <h3 class="h3-headline">Financial Information</h3>
                        </div>
                    </div>   
                    {!!
                    Form::open(
                    array(
                    'name' => 'financialInformationForm',
                    'id' => 'financialInformationForm',
                    'url' => route('financial_information',['id'=>@$userData['user_financial_info_id'],'user_kyc_id'=>@$benifinary['user_kyc_id'],'corp_user_id'=>@$benifinary['corp_user_id'],'is_by_company'=>@$benifinary['is_by_company']]),
                    'autocomplete' => 'off','class'=>'loginForm form form-cls'
                    ))
                    !!}

               
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                 
                                {{Form::label('source_funds','Source of funds',['class'=>''])}} <span class="mandatory">*<span> 
                                {!!
                                Form::select('source_funds',[''=>'Select Source','1'=>'Salary','2'=>'Profession','3'=>'Loan','4'=>'Rental','5'=>'Dividends','6'=>'Real Estate','7'=>'Other'],@$userData['source_funds'],['id'=>'source_funds','class'=>'form-control select2Cls']);
                                !!}
                                <span class="text-danger">{{ $errors->first('source_funds') }}</span>
                            </div>
                        </div>

                    </div>
                    <div class="row" id="otherSource">
                        <div class="col-md-12">
                            <div class="form-group">
                                 
                            {{ Form::label('other_source','Other Source Of Funds',['class'=>''])}} <span class="mandatory">*<span> 
                            {{ Form::text('other_source',@$userData['other_source'],['class'=>'form-control','id'=>'other_source','placeholder'=>'Other Source Of Funds'])}}
                            <span class="text-danger">{{ $errors->first('other_source') }}</span>   
                        </div>

                    </div>
                     </div>   
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                            <div class="form-group">
        
                            {{ Form::label('jurisdiction_funds','Jurisdiction of Funds',['class'=>''])}} <span class="mandatory">*<span> 
                            {!!
                                Form::select('jurisdiction_funds',
                                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),@$userData['jurisdiction_funds'],array('id' => 'jurisdiction_funds','class'=>'form-control select2Cls'))
                                !!}
                             <span class="text-danger">{{ $errors->first('jurisdiction_funds') }}</span>   
                           
                            </div>
                            </div>
                        </div>
                    </div>	

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                {{ Form::label('annual_income','Annual Income (in USD)',['class'=>''])}} <span class="mandatory">*<span> 
                                {!!Form::select('annual_income',
                                [''=>'Select','1'=>'<=20,000','2'=>'20,001 to 50,000','3'=>'50,001 to 100,000','4'=>'100,001 to 200,000','5'=>'200,001 to 500,000','6'=>'500,001 to 1,000,000','7'=>'1,000,001 to 2,500,000','8'=>'2,500,001 to 5,000,000','9'=>'5,000,001 to 10,000,000','10'=>'10,000,001 to 20,000,000','11'=>'> 20,000,000'],@$userData['annual_income'],array('id' => 'annual_income','class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('annual_income') }}</span>
                            </div>
                        </div>
                    </div>	 

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                 
                                {{ Form::label('estimated_wealth','Estimated Wealth (in USD)',['class'=>''])}} <span class="mandatory">*<span> 
                                {!!Form::select('estimated_wealth',
                                [''=>'Select','1'=>'<=25,000','2'=>'25,001 to 100,000','3'=>'100,001 to 250,000','4'=>'250,001 to 500,000','5'=>'500,001 to 1,000,000','6'=>'1000,001 to 2,500,000','7'=>'2,500,001 to 5,000,000','8'=>'5,000,001 to 10,000,000','9'=>'10,000,001 to 25,000,000','10'=>'25,000,001 to 50,000,000','11'=>'>  50,000,000'],@$userData['estimated_wealth'],array('id' => 'estimated_wealth','class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('estimated_wealth') }}</span>
                            </div>
                        </div>
                    </div>	 

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                {{ Form::label('wealth_source','Kindly provide details on the source(s) of your wealth',['class'=>''])}} <span class="mandatory">*<span> 
                                {!!Form::select('wealth_source',
                                [''=>'Select','1'=>'Commercial business activities','2'=>'Inheritance','3'=>'Acuumulated earnings','4'=>'Other'],@$userData['wealth_source'],array('id' => 'wealth_source','class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('wealth_source') }}</span>
                            </div>
                        </div>
                    </div> 
                    <div class="row" id="otherSourcewealth">
                        <div class="col-md-12">
                            <div class="form-group">
                                 
                            {{ Form::label('other_wealth_source','Other source(s) of your wealth',['class'=>''])}} <span class="mandatory">*<span> 
                            {{ Form::text('other_wealth_source',@$userData['other_wealth_source'],['class'=>'form-control','id'=>'other_wealth_source','placeholder'=>'Other source(s) of your wealth'])}}
                            <span class="text-danger">{{ $errors->first('other_wealth_source') }}</span>   
                        </div>

                    </div>
                     </div> 

                    <div class="row">
                        <div class="col-md-12">
                            <hr/>
                        </div>
                    </div>		 

                    <div class="row marT10 marB20">
                        <div class="col-md-12">
                            <label for="pwd"><b>Please fill the following details (If Applicable)</b></label>
                        </div>	
                    </div> 



                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                
                                {{ Form::label('tin_code','US TIN Code',['class'=>''])}} <span class="mandatory">*<span> 
                         
                                {{ Form::text('tin_code',@$userData['tin_code'],['class'=>'form-control','id'=>'tin_code','placeholder'=>'Enter US TIN Code'])}}
                                <span class="text-danger">{{ $errors->first('tin_code') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                               
                                {{Form::label('is_abandoned','Was US citizenship abandoned after June 2014?')}} <span class="mandatory">*<span> 
                                {!!Form::select('is_abandoned',
                                [''=>'Select','1'=>'Yes','0'=>'No'],@$userData['is_abandoned'],array('id' => 'is_abandoned','class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('is_abandoned') }}</span>
                            </div>
                        </div>


                    </div>	 
                     <?php
                            $date_of_abandonment=(@$userData['date_of_abandonment']!='' && @$userData['date_of_abandonment']!=null) ? Helpers::getDateByFormat(@$userData['date_of_abandonment'], 'Y-m-d', 'd/m/Y') :'';
                            ?>
                    <div class="row abandonment">		  
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pwd"></label>
                                {{ Form::label('date_of_abandonment','Please specify date of abandonment',['class'=>''])}} <span class="mandatory">*<span> 
                                <div class="input-group">
                                    {{ Form::text('date_of_abandonment',$date_of_abandonment,['class'=>'form-control datepicker','id'=>'date_of_abandonment','placeholder'=>''])}}
                                    <div class="input-group-append">
                                        <i class="fa fa-calendar-check-o"></i>
                                    </div>
                                    
                                </div>
                                <span class="text-danger">{{ $errors->first('date_of_abandonment') }}</span>
                                
                         
                                
                           
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            {{ Form::label('abandonment_reason','Reason',['class'=>''])}} <span class="mandatory">*<span> 
                         
                            {{ Form::text('abandonment_reason',@$userData['abandonment_reason'],['class'=>'form-control','id'=>'abandonment_reason','placeholder'=>'Enter Reason'])}}
                           <span class="text-danger">{{ $errors->first('abandonment_reason') }}</span>
                            </div>
                            
                        </div> 
                    </div>	 

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                {{ Form::label('justification','Justification (If reason B is selected)',['class'=>''])}}  <span class="mandatory">*<span> 
                         
                                {{ Form::textarea('justification',@$userData['justification'],['class'=>'form-control','id'=>'justification','placeholder'=>'Enter Reason','rows'=>'3'])}}
                                <span class="text-danger">{{ $errors->first('justification') }}</span>
                            </div>
                        </div>
                    </div> 	 


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('tin_country_name','TIN Country ',['class'=>''])}} <span class="mandatory">*<span> 
                         
                                
                                {!!
                                Form::select('tin_country_name',
                                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),@$userData['tin_country_name'],array('id' => 'tin_country_name','class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('tin_country_name') }}</span>
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            
                                {{ Form::label('tin_number','TIN (Taxpayer Identification Number) or functional equivalent of the TIN',['class'=>''])}} <span class="mandatory">*<span> 
                                {{ Form::text('tin_number',@$userData['tin_number'],['class'=>'form-control','id'=>'tin_number','placeholder'=>'Enter TIN no.'])}}
                                <span class="text-danger">{{ $errors->first('tin_number') }}</span>
                            </div>
                        </div>
                    </div>	 




                    <div class="row marT60">
                        <div class="col-md-12 text-right">
                             <a href="{{route('commercial_information')}}" class="btn btn-prev">Previous</a>	
                            {{Form::submit('Save',['class'=>'btn btn-save','name'=>'save','id'=>'save'])}}
                            {{Form::submit('Save & Next',['class'=>'btn btn-save','name'=>'save_next','id'=>'save_next'])}}
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
Financial Information
@endsection
@section('additional_css')
<link href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}" rel="stylesheet">
@endsection
@section('jscript')
<script src="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.js') }}"></script>
<script src="{{ asset('frontend/outside/js/validation/familyInfo.js')}}"></script>
<script>
$(document).ready(function () {
    
    $('.datepicker').datepicker({dateFormat: 'dd/mm/yy', maxDate: new Date(), changeMonth: true, changeYear: true});
    
    if($("#source_funds option:selected").text()=='Other'){
         $('#otherSource').show();
    }else{
       $('#otherSource').hide();
    }
    $("#source_funds").on('change',function(){
        if($("#source_funds option:selected").text()=='Other'){
            $('#otherSource').show();
        }else{
            $('#otherSource').hide();
        }
    });
    
    if($("#wealth_source option:selected").text()=='Other'){
            $('#otherSourcewealth').show();
        }else{
            $('#otherSourcewealth').hide();
        }
    $("#wealth_source").on('change',function(){
        if($("#wealth_source option:selected").text()=='Other'){
            $('#otherSourcewealth').show();
        }else{
            $('#otherSourcewealth').hide();
        }
    });

});

var messages = {
    social_media_form_limit: "{{ config('common.SOCIAL_MEDIA_LINK') }}",
    document_form_limit: "{{ config('common.DOCUMENT_LIMIT') }}",
};
</script>
@endsection

