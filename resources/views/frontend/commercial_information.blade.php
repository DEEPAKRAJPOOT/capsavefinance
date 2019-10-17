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
                            <h3 class="h3-headline">For Sole Proprietorship/Self Employed, Please Specify</h3>
                        </div>
                    </div>   
                    {!!
                    Form::open(
                    array(
                    'name' => 'commercialInformationForm',
                    'id' => 'commercialInformationForm',
                    'url' => route('commercial_information',['id'=>@$userData['user_kyc_propr_id'],'user_kyc_id'=>@$benifinary['user_kyc_id'],'corp_user_id'=>@$benifinary['corp_user_id'],'is_by_company'=>@$benifinary['is_by_company']]),
                    'autocomplete' => 'off','class'=>'loginForm form form-cls'
                    ))
                    !!}
                    
                 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{Form::label('comm_name','Commercial name',['class'=>''])}}
                                {{Form::text('comm_name',@$userData['comm_name'],['class'=>'form-control','id'=>'comm_name','palceholder'=>'Enter Commercial name'])}}
                                <span class="text-danger">{{ $errors->first('comm_name') }}</span>
                            </div>
                        </div>

                    </div>
                        <?php
                        $date_of_establish  =   (@$userData['date_of_establish']!='' && @$userData['date_of_establish']!=null) ? Helpers::getDateByFormat(@$userData['date_of_establish'], 'Y-m-d', 'd/m/Y') :'';
                        ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">

                                {{Form::label('date_of_establish','Date of establishment',['class'=>''])}}
                                <div class="input-group">

                                    {{Form::text('date_of_establish',$date_of_establish,['class'=>'form-control datepicker','id'=>'date_of_establish','placeholder'=>'Select Date'])}}
                                    <div class="input-group-append">
                                        <i class="fa fa-calendar-check-o"></i>
                                    </div>
                                </div>
                                <span class="text-danger">{{ $errors->first('date_of_establish') }}</span>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">

                                {{Form::label('country_establish_id','Country of establishment',['class'=>''])}}
                                {!!
                                Form::select('country_establish_id',
                                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                                @$userData['country_establish_id'],
                                array('id' => 'country_establish_id','class'=>'form-control select2Cls'))
                                !!}
                                <span class="text-danger">{{ $errors->first('country_establish_id') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">

                                    {{Form::label('comm_reg_no','Commercial Register No.',['class'=>''])}}
                                    {{Form::text('comm_reg_no',@$userData['comm_reg_no'],['class'=>'form-control','id'=>'comm_reg_no','placeholder'=>'Enter Commercial Register No.'])}}
                                    <span class="text-danger">{{ $errors->first('comm_reg_no') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    {{Form::label('comm_reg_place','Place',['class'=>''])}}
                                    {{Form::text('comm_reg_place',@$userData['comm_reg_place'],['class'=>'form-control','id'=>'comm_reg_place','placeholder'=>'Enter Place'])}}
                                    <span class="text-danger">{{ $errors->first('comm_reg_place') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                {{Form::label('comm_country_id','Country',['class'=>''])}}
                                {{
                                Form::select('comm_country_id',[''=>'Select Country']+Helpers::getCountryDropDown()->toArray(),@$userData['comm_country_id'],['class'=>'form-control'])
                                }}
                                <span class="text-danger">{{ $errors->first('comm_country_id') }}</span>
                            </div>
                        </div>
                    </div>	

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                {{Form::label('country_activity','Country(ies) of Activity',['class'=>''])}}
                                {{
                                Form::select('country_activity',[''=>'Select Country']+Helpers::getCountryDropDown()->toArray(),@$userData['country_activity'],['class'=>'form-control','id'=>'country_activity'])
                                }}
                                <span class="text-danger">{{ $errors->first('country_activity') }}</span>
                            </div>
                        </div>
                    </div>	 

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">   
                            {{Form::label('syndicate_no','Syndicate No.',['class'=>''])}}
                            {{Form::text('syndicate_no',@$userData['syndicate_no'],['class'=>'form-control','id'=>'syndicate_no','placeholder'=>'Enter Syndicate No.'])}}
                            <span class="text-danger">{{ $errors->first('syndicate_no') }}</span>
                            </div>
                        </div>
                    </div>	 

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="pwd"></label>
                                 
                                {{Form::label('taxation_no','Taxation ID No.',['class'=>''])}}
                                {{Form::text('taxation_no',@$userData['taxation_no'],['class'=>'form-control','id'=>'taxation_no','placeholder'=>'Enter Taxation ID No.'])}}
                                <span class="text-danger">{{ $errors->first('taxation_no') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                   
                                {{Form::label('taxation_id','Taxation ID',['class'=>''])}}
                                {{Form::text('taxation_id',@$userData['taxation_id'],['class'=>'form-control','id'=>'taxation_id','placeholder'=>'Enter Taxation ID'])}}
                                <span class="text-danger">{{ $errors->first('taxation_id') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>		 

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                {{Form::label('annual_turnover','Annual Business Turnover (in $)',['class'=>''])}}
                                {{Form::text('annual_turnover',@$userData['annual_turnover'],['class'=>'form-control','id'=>'annual_turnover','placeholder'=>'Enter Annual Business Turnover (in $)'])}}
                                <span class="text-danger">{{ $errors->first('annual_turnover') }}</span>
                            </div>
                        </div>
                    </div>	 

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{Form::label('main_suppliers','Main Suppliers',['class'=>''])}}
                                {{Form::text('main_suppliers',@$userData['main_suppliers'],['class'=>'form-control','id'=>'main_suppliers','placeholder'=>'Enter Main Suppliers'])}}
                                <span class="text-danger">{{ $errors->first('main_suppliers') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                
                                {{Form::label('main_clients','Main Clients',['class'=>''])}}
                                {{Form::text('main_clients',@$userData['main_clients'],['class'=>'form-control','id'=>'main_clients','placeholder'=>'Enter Main Clients'])}}
                                <span class="text-danger">{{ $errors->first('main_clients') }}</span>
                            </div>
                        </div>
                    </div>	 

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                {{Form::label('authorized_signatory','Name of authorized signatory',['class'=>''])}}
                                {{Form::text('authorized_signatory',@$userData['authorized_signatory'],['class'=>'form-control','id'=>'authorized_signatory','placeholder'=>'Enter Name of authorized signatory'])}}
                                <span class="text-danger">{{ $errors->first('authorized_signatory') }}</span>
                            </div>
                        </div>
                    </div>	 

                    <div class="row marT25 marB10">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pwd"><b>Business Address</b></label>
                                
                            </div>
                        </div>
                    </div>	 

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                {{Form::label('buss_country_id','Country',['class'=>''])}}
                                {{
                                Form::select('buss_country_id',[''=>'Select Country']+Helpers::getCountryDropDown()->toArray(),@$bussData['buss_country_id'],['class'=>'form-control','id'=>'buss_country_id'])
                                }}
                                <span class="text-danger">{{ $errors->first('buss_country_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              
                                {{Form::label('buss_city_id','City',['class'=>''])}}
                                
                                {{Form::text('buss_city_id',@$bussData['buss_city_id'],['class'=>'form-control','id'=>'buss_city_id','placehoder'=>'Enter City'])}}
                                <span class="text-danger">{{ $errors->first('buss_city_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                {{Form::label('buss_region','Region',['class'=>''])}}
                                {{Form::text('buss_region',@$bussData['buss_region'],['class'=>'form-control','id'=>'buss_region','placehoder'=>'Enter Region'])}}
                                <span class="text-danger">{{ $errors->first('buss_region') }}</span>
                            </div>
                        </div>

                    </div>	

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                {{Form::label('buss_building','Building',['class'=>''])}}
                                {{Form::text('buss_building',@$bussData['buss_building'],['class'=>'form-control','id'=>'buss_building','placehoder'=>'Enter building'])}}
                                <span class="text-danger">{{ $errors->first('buss_building') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                
                                {{Form::label('buss_floor','Floor',['class'=>''])}}
                                {{Form::text('buss_floor',@$bussData['buss_floor'],['class'=>'form-control','id'=>'buss_floor','placehoder'=>'Enter floor'])}}
                                <span class="text-danger">{{ $errors->first('buss_floor') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                               
                                
                                {{Form::label('buss_street','Street',['class'=>''])}}
                                {{Form::text('buss_street',@$bussData['buss_street'],['class'=>'form-control','id'=>'buss_street','placehoder'=>'Enter Street'])}}
                                <span class="text-danger">{{ $errors->first('buss_street') }}</span>
                            </div>
                        </div>

                    </div>	

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                
                                
                                {{Form::label('buss_postal_code','Postal Code',['class'=>''])}}
                                {{Form::text('buss_postal_code',@$bussData['buss_postal_code'],['class'=>'form-control','id'=>'buss_postal_code','placehoder'=>'Enter postal code'])}}
                                <span class="text-danger">{{ $errors->first('buss_postal_code') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              
                                {{Form::label('buss_po_box_no','P.O Box',['class'=>''])}}
                                {{Form::text('buss_po_box_no',@$bussData['buss_po_box_no'],['class'=>'form-control','id'=>'buss_po_box_no','placehoder'=>'Enter P.O. Box no.'])}}
                                <span class="text-danger">{{ $errors->first('buss_po_box_no') }}</span>
                            </div>
                        </div>
                    </div>	

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                {{Form::label('buss_email','Email',['class'=>''])}}
                                {{Form::email('buss_email',@$bussData['buss_email'],['class'=>'form-control','id'=>'buss_email','placehoder'=>'Enter email'])}}
                                <span class="text-danger">{{ $errors->first('buss_email') }}</span>
                            </div>
                        </div>
                    </div>	

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                            
                                
                                {{Form::label('buss_telephone_no','Telephone No.',['class'=>''])}}
                                {{Form::text('buss_telephone_no',@$bussData['buss_telephone_no'],['class'=>'form-control','id'=>'buss_telephone_no','placehoder'=>'Enter Telephone No.'])}}
                                <span class="text-danger">{{ $errors->first('buss_telephone_no') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                {{Form::label('buss_mobile_no','Mobile No.',['class'=>''])}}
                                {{Form::text('buss_mobile_no',@$bussData['buss_mobile_no'],['class'=>'form-control','id'=>'buss_mobile_no','placehoder'=>'Enter mobile no.'])}}
                                <span class="text-danger">{{ $errors->first('buss_mobile_no') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                {{Form::label('buss_fax_no','Fax No.',['class'=>''])}}
                                {{Form::text('buss_fax_no',@$bussData['buss_fax_no'],['class'=>'form-control','id'=>'buss_fax_no','placehoder'=>'Enter fax no.'])}}
                                <span class="text-danger">{{ $errors->first('buss_fax_no') }}</span>
                            </div>
                        </div>
                    </div>	
                    

                    <div class="row marT25 marB10">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pwd"><b>Mailing Address</b></label>	
                            </div>
                        </div>
                    </div>			 

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                
                                
                                {{Form::label('is_hold_mail','Hold Mail',['class'=>''])}}
                                {{Form::select('is_hold_mail',[''=>'select','1'=>'Yes','0'=>'No'],@$userData['is_hold_mail'],['class'=>'form-control','id'=>'is_hold_mail'])}}
                                <span class="text-danger">{{ $errors->first('is_hold_mail') }}</span>
                            </div>
                        </div>
                    </div>	

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                               
                                {{Form::label('mailing_address','In case of sending documents through mail, please specify mailing address',['class'=>''])}}
                                {{Form::select('mailing_address',[''=>'select','1'=>'Residential Address','2'=>'Secondary Address','3'=>'Business Address'],@$userData['mailing_address'],['class'=>'form-control','id'=>'mailing_address'])}}
                                <span class="text-danger">{{ $errors->first('mailing_address') }}</span>
                            </div>
                        </div>
                    </div>		 

                    <div class="row marT25">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pwd"></label>
                                {{Form::label('','Relation with Exchange Company/ Establishment')}}
                                <p class="text-color font-normal marT15">Are you or your spouse or any of your dependents (ascendants and descendants) the owner or shareholder or partner or director or signatory of an exchange establishment/ company? If yes please disclose the full names of the concerned parties and the full name and details of the establishment / company</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                
                                
                                {{Form::select('relation_exchange_company',[''=>'select','1'=>'Yes','0'=>'No'],@$userData['relation_exchange_company'],['class'=>'form-control','id'=>'relation_exchange_company'])}}
                                <span class="text-danger">{{ $errors->first('relation_exchange_company') }}</span>
                            </div>	
                        </div>		  
                    </div>			 
                    <div class="row relation-yes">
                        <div class="col-md-12">
                            <div class="form-group">
                                
                                {{Form::label('concerned_party','Name of Concerned Party',['class'=>''])}}
                                {{Form::textarea('concerned_party',@$userData['concerned_party'],['class'=>'form-control','id'=>'concerned_party','placehoder'=>'Enter Name of Concerned Party','rows'=>'3'])}}
                                <span class="text-danger">{{ $errors->first('concerned_party') }}</span>
                            </div>
                        </div>
                    </div>	 
                    <div class="row relation-yes">
                        <div class="col-md-12">
                            <div class="form-group">

                                {{Form::label('details_of_company','Name/Details of Establishment/Company',['class'=>''])}}
                                {{Form::textarea('details_of_company',@$userData['details_of_company'],['class'=>'form-control','id'=>'details_of_company','placehoder'=>'Enter Name/Details of Establishment/Company','rows'=>'3'])}}
                                <span class="text-danger">{{ $errors->first('details_of_company') }}</span>
                            </div>
                        </div>
                    </div> 

                    <div class="row marT60">
                        <div class="col-md-12 text-right">
                            <a href="{{route('professional_information')}}" class="btn btn-prev">Previous</a>	

                            <input type='submit' class='btn btn-save' name='save' value='Save' />
                            <input type='submit' class='btn btn-save ' name='save_next' id="save_next" value='Save & Next' />
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
Proprietorship Information
@endsection
@section('additional_css')
<link href="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.css') }}" rel="stylesheet">
@endsection
@section('jscript')
<script src="{{ asset('frontend/inside/plugin/datepicker/jquery-ui.js') }}"></script>
<script src="{{ asset('frontend/outside/js/validation/comercialInfoForm.js')}}"></script>
<script>
$(document).ready(function () {
    // $('.datepicker').datepicker();
    $('.datepicker').datepicker({dateFormat: 'dd/mm/yy'});
    if($("#relation_exchange_company").val()=='1'){
        $('.relation-yes').show();
    }else{
        $('.relation-yes').hide();
    }
    $("#relation_exchange_company").on('change',function(){
        if($(this).val()=='1'){
            $('.relation-yes').show();
        }else{
            $('.relation-yes').hide();
        }
    });

});

var messages = {
    social_media_form_limit: "{{ config('common.SOCIAL_MEDIA_LINK') }}",
    document_form_limit: "{{ config('common.DOCUMENT_LIMIT') }}",
};
</script>
@endsection
