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
                            <h3 class="h3-headline">Residential Information</h3>
                        </div>
                    </div>   

                    {!!
                    Form::open(
                    array(
                    'name' => 'residentialInformationForm',
                    'id' => 'residentialInformationForm',
                    'url' => route('residential_information',['id'=>@$userData['user_kyc_addr_id'],'user_kyc_id'=>@$benifinary['user_kyc_id'],'corp_user_id'=>@$benifinary['corp_user_id'],'is_by_company'=>@$benifinary['is_by_company']]),
                    'autocomplete' => 'off','class'=>'loginForm form form-cls'
                    ))
                    !!}
              
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">Country</label> <span class="mandatory">*<span> 

                                {!!
                                Form::select('country_id',
                                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),@$userData['country_id'],array('id' => 'country_id','class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('country_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                {{Form::label('city_id','City',['class'=>''])}} <span class="mandatory">*<span> 
                                {{Form::text('city_id',@$userData['city_id'],['id'=>'region','placeholder'=>'Enter City','class'=>'form-control'])}}
                                <span class="text-danger">{{ $errors->first('city_id') }}</span> 
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pwd">Region</label>
                                {{Form::label('region','Region',['class'=>''])}} <span class="mandatory">*<span> 
                                {{Form::text('region',@$userData['region'],['id'=>'region','placeholder'=>'Enter Region','class'=>'form-control'])}}
                                <span class="text-danger">{{ $errors->first('region') }}</span>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    {{ Form::label('building_no','Building',[]) }} <span class="mandatory">*<span> 
                                    {{ Form::text('building_no',@$userData['building_no'],['id'=>'building_no','class'=>'form-control','placeholder'=>'Enter Building'])}}
                                    <span class="text-danger">{{ $errors->first('building_no') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    {{ Form::label('floor_no','Floor No',['class'=>''])}} <span class="mandatory">*<span> 
                                    {{ Form::text('floor_no',@$userData['floor_no'],['id'=>'floor_no','class'=>'form-control','placeholder'=>'Enter floor']) }}
                                    <span class="text-danger">{{ $errors->first('floor_no') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <div class="form-group">
                                   
                                    {{ Form::label('street_addr','Street',['class'=>''])}} <span class="mandatory">*<span> 
                                    {{ Form::text('street_addr',@$userData['street_addr'],['id'=>'street_addr','class'=>'form-control','placeholder'=>'Enter Street'])}}
                                    <span class="text-danger">{{ $errors->first('street_addr') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>					

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                   
                                    {{ Form::label('postal_code','Postal Code',['class'=>''])}} <span class="mandatory">*<span> 
                                    {{ Form::text('postal_code',@$userData['postal_code'],['class'=>'form-control','id'=>'postal_code','placeholder'=>'Enter postal code'])}}
                                    <span class="text-danger">{{ $errors->first('postal_code') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    
                                    {{ Form::label('post_box','P.O Box',['class'=>''])}} <span class="mandatory">*<span> 
                                    {{ Form::text('post_box',@$userData['post_box'],['class'=>'form-control','id'=>'post_box','placeholder'=>'Enter P.O. Box no.'])}}
                                    <span class="text-danger">{{ $errors->first('post_box') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>	

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-group">
                                    
                                    {{ Form::label('addr_email','Email',['class'=>''])}} <span class="mandatory">*<span> 
                                    {{ Form::text('addr_email',@$userData['addr_email'],['id'=>'addr_email','class'=>'form-control','palceholder'=>'Enter email'])}}
                                    <span class="text-danger">{{ $errors->first('addr_email') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>	 

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    
                                    {{ Form::label('addr_phone_no','Telephone No.',['class'=>''])}}
                                    {{ Form::text('addr_phone_no',@$userData['addr_phone_no'],['class'=>'form-control','id'=>'addr_phone_no','placeholder'=>'Enter Telephone No.'])}}
                                    <span class="text-danger">{{ $errors->first('addr_phone_no') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    
                                    {{Form::label('addr_mobile_no','Mobile No.',['class'=>''])}} <span class="mandatory">*<span> 
                                    {{ Form::text('addr_mobile_no',@$userData['addr_mobile_no'],['class'=>'form-control','id'=>'addr_mobile_no','palaceholder'=>'Enter mobile no.','maxlength'=>'10'])}}
                                    <span class="text-danger">{{ $errors->first('addr_mobile_no') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    {{ Form::label('addr_fax_no','Fax No.',['class'=>''])}} <span class="mandatory">*<span> 
                                    {{ Form::text('addr_fax_no',@$userData['addr_fax_no'],['class'=>'form-control','id'=>'addr_fax_no','placeholder'=>'Enter fax no.'])}}
                                    <span class="text-danger">{{ $errors->first('addr_fax_no') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>		 

                    <div class="row marT60">
                        <div class="col-md-12 text-right">
                            <a href="{{route('family_information')}}" class="btn btn-prev">Previous</a>	
                            
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



<script src="{{ asset('frontend/outside/js/validation/residentialInfoForm.js')}}"></script>

<script>
$(document).ready(function () {
   
});


</script>

@endsection
