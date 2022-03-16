@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit_offer')}}" target="_top" onsubmit="return checkLeasingValidations()">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id">
    <input type="hidden" value="{{request()->get('prgm_offer_id')}}" name="offer_id">
    <input type="hidden" value="2" name="security_deposit_type">
    
    <div class="row">
        <div class="col-md-6 d-none">
          <div class="form-group INR">
            <label for="txtPassword" ><b>Limit</b></label> 
            <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
            <input type="text" name="prgm_limit_amt" class="form-control number_format" value="{{isset($limitData->limit_amt)? number_format($limitData->limit_amt): ''}}" placeholder="Limit" maxlength="15" readonly>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group ">
            <label for="txtPassword" ><b>Facility Type</b> <span style="color: red;"> *</span></label> 
            <select class="form-control" name="facility_type_id">
                @foreach($facilityTypeList as $key => $facilityType)
                    @if($facilityType == "Term Loan")
                    <option value="{{ $key }}" {{ (isset($offerData->facility_type_id) && $offerData->facility_type_id == $key) ? 'selected' : ''}}>{{ $facilityType }}</option>
                    @endif
                @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group INR">
            <label for="txtPassword" ><b>Limit</b> <span style="color: red;"> *</span></label>
            <span class="float-right text-success">Balance: <i class="fa fa-inr"></i>{{(int)$limitData->limit_amt - (int)$subTotalAmount + (int)$currentOfferAmount}}</span>
            <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a> 
            <input type="text" name="sub_limit" class="form-control number_format" value="{{isset($offerData->prgm_limit_amt)? number_format($offerData->prgm_limit_amt): ''}}" placeholder="Limit" maxlength="15">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group ">
            <label for="txtPassword" ><b>Tenor (Months)</b> <span style="color: red;"> *</span></label> 
            <input type="text" name="tenor" class="form-control" value="{{isset($offerData->tenor)? $offerData->tenor: ''}}" placeholder="Tenor" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group ">
            <label for="txtPassword" ><b>Asset Type</b> <span style="color: red;"> *</span></label> 
            <select class="form-control" name="asset_type_id">
                <option value="">Select Asset type</option>
                @foreach($assets as $key => $asset)
                <option value="{{ $key }}" {{ (isset($offerData->asset_type_id) && $offerData->asset_type_id == $key) ? 'selected': ''}}>{{ $asset }}</option>
                @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="txtPassword">Rate of Interest (%) <span style="color: red;"> *</span></label>
                <small><span class="float-right text-success limit"></span></small>
                <input type="text" name="interest_rate" class="form-control" value="{{ isset($offerData->interest_rate) ? $offerData->interest_rate : ''}}" placeholder="Interest Rate" maxlength="5">
            </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="txtPassword"><b>Payment Frequency</b> <span style="color: red;"> *</span></label> 
                <select name="rental_frequency" class="form-control">
                    <option value="">Select Payment Frequency</option>
                    <option value="4" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 4)? 'selected': ''):''}}>Monthly</option>
                    <option value="3" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 3)? 'selected': ''):''}}>Quarterly</option>
                    <option value="2" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 2)? 'selected': ''):''}}>Bi-Yearly</option>
                    <option value="1" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 1)? 'selected': ''):''}}>Yearly</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtPassword" ><b>Frequency Type</b> <span style="color: red;"> *</span></label> 
                <select name="rental_frequency_type" class="form-control">
                    <option value="">Select Frequency Type</option>
                    <option value="1" {{isset($offerData->rental_frequency_type)? (($offerData->rental_frequency_type == 1)? 'selected': ''):''}}>Advance</option>
                    <option value="2" {{isset($offerData->rental_frequency_type)? (($offerData->rental_frequency_type == 2)? 'selected': ''):''}}>Arrears</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            <label for="txtPassword"><b>Processing Fee (%) @Sanction level</b> <span style="color: red;"> *</span></label>
            <input type="text" name="processing_fee" class="form-control" value="{{isset($offerData->processing_fee)? $offerData->processing_fee: ''}}" placeholder="Processing Fee" maxlength="6">
          </div>
        </div>

        <div class="col-md-6">
            <div class="form-group INR">
                <label for="txtPassword"><b>Security Deposit (%)</b> <span style="color: red;"> *</span></label>
                <input type="text" name="security_deposit" class="form-control" value="{{ isset($offerData->security_deposit) ? (int) $offerData->security_deposit : '' }}" placeholder="Security Deposit" maxlength="15">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group INR">
                <label for="txtPassword"><b>Margin Money (%)</b> <span style="color: red;"> *</span></label>
                <input type="text" name="margin" class="form-control" value="{{ isset($offerData->margin) ? (int) $offerData->margin : '' }}" placeholder="Margin Money" maxlength="15">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group INR">
                <label for="txtPassword"><b>IRR (%)</b> <span style="color: red;"> *</span></label>
                <input type="text" name="irr" class="form-control" value="{{ isset($offerData->irr) ? $offerData->irr : '' }}" placeholder="IRR" maxlength="15">
            </div>
        </div>
        <!-- -------------- ASSET INSURANCE BLOCK ------------ -->
        <div class="col-md-12">
            <div class="form-group row">
                <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;">Asset Insurance</label>
                <div class="col-md-6">
                    <select name="asset_insurance" class="form-control show-hide" data-block_id="#asset-insurance-block">
                        <option value="">Select Asset Insurance</option>
                        <option value="1" {{ isset($offerData->asset_insurance) && $offerData->asset_insurance == 1 ? 'selected' : '' }}>Applicable</option>
                        <option value="2" {{ isset($offerData->asset_insurance) && $offerData->asset_insurance == 2 ? 'selected' : ''}}>Not Applicable</option>
                    </select>
                </div>
                <div class="col-md-12" id="asset-insurance-block" style="display: {{ (isset($offerData->asset_insurance) && $offerData->asset_insurance == 1) ? 'block': 'none'}};">                
                    <div class="row mt10">
                        <div class="col-md-3">
                            <label for="txtPassword">Asset Name</label>
                            <input name="asset_name" class="form-control" value="{{ $offerData->asset_name ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="txtPassword">Timelines for Insurance</label>
                            <input name="timelines_for_insurance" class="form-control" value="{{ $offerData->timelines_for_insurance ?? '' }}">
                        </div>
                        <div class="col-md-5">
                            <label for="txtPassword" >Comments if any</label>
                            <input name="asset_comment" class="form-control" value="{{ $offerData->asset_comment ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- -------------- -->    
        <!-- -------------- PERSONAL GUARANTEE BLOCK ------------ -->
        <div class="col-md-12">
            <div class="form-group row">
                <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;">Personal Guarantee</label>
                <div class="col-md-6">
                    <select name="personal_guarantee" class="form-control show-hide" data-block_id="#personal-guarantee-block">
                        <option value="">Select Personal Guarantee</option>
                        <option value="1" {{(isset($offerData->offerPg) && count($offerData->offerPg) > 0)? 'selected': ''}}>Applicable</option>
                        <option value="2" {{(isset($offerData->offerPg) && count($offerData->offerPg) == 0)? 'selected': ''}}>Not Applicable</option>
                    </select>
                </div>
                <div class="col-md-12" id="personal-guarantee-block" style="display: {{(isset($offerData->offerPg) && count($offerData->offerPg) > 0)? 'block': 'none'}};">
                @if(isset($offerData->offerPg) && count($offerData->offerPg) > 0)
                    @foreach($offerData->offerPg as $key=>$pg)
                    <div class="row mt10">
                        <div class="col-md-2">
                            @if($loop->first)
                            <label for="txtPassword" >Select<br> Guarantor</label>
                            @endif
                            <select name="pg[pg_name_of_guarantor_id][]" class="form-control">
                                <option value="">Select Guarantor</option>
                                @foreach($bizOwners as $key=>$bizOwner)
                                <option value="{{$bizOwner->biz_owner_id}}" {{($pg->pg_name_of_guarantor_id == $bizOwner->biz_owner_id)? 'selected': ''}}>{{ucwords($bizOwner->first_name)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            @if($loop->first)
                            <label for="txtPassword" >Time for<br> security</label>
                            @endif
                            <select name="pg[pg_time_for_perfecting_security_id][]" class="form-control">
                                <option value="">Select time for perfecting security</option>
                                <option value="1" {{($pg->pg_time_for_perfecting_security_id == 1)? 'selected': ''}}>Before Disbusrement</option>
                                <option value="2" {{($pg->pg_time_for_perfecting_security_id == 2)? 'selected': ''}}>With in 30 days from date of first disbusrement</option>
                                <option value="3" {{($pg->pg_time_for_perfecting_security_id == 3)? 'selected': ''}}>With in 60 days from date of first disbsurement</option>
                                <option value="4" {{($pg->pg_time_for_perfecting_security_id == 4)? 'selected': ''}}>With in 90 days from date of first disbursement </option>
                                <option value="5" {{($pg->pg_time_for_perfecting_security_id == 5)? 'selected': ''}}>With in 120 days from date of first disbursement</option>
                                <option value="6" {{($pg->pg_time_for_perfecting_security_id == 6)? 'selected': ''}}>with in 180 days from date of first disbursement</option>
                                <option value="7" {{($pg->pg_time_for_perfecting_security_id == 7)? 'selected': ''}}>with in 360 days from date of first disbsurement</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            @if($loop->first)
                            <label for="txtPassword">Residential<br> Address </label>
                            @endif
                            <input name="pg[pg_residential_address][]" class="form-control" value="{{$pg->pg_residential_address}}">
                        </div>
                        <div class="col-md-2">
                            @if($loop->first)
                            <label for="txtPassword">Net worth as<br> per ITR/CA Cert</label>
                            @endif
                            <input name="pg[pg_net_worth][]" class="form-control" value="{{$pg->pg_net_worth}}">
                        </div>
                        <div class="col-md-2">
                            @if($loop->first)
                            <label for="txtPassword" >Comments if<br> any</label>
                            @endif
                            <input name="pg[pg_comments][]" class="form-control" value="{{$pg->pg_comments}}">
                        </div>
                        <div class="col-md-2 center">
                            @if($loop->first)
                            <i class="fa fa-2x fa-plus-circle add-personal-guarantee-block mt-8"></i>
                            @else
                            <i class="fa fa-2x fa-times-circle remove-personal-guarantee-block" style="color: red;"></i>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="row mt10">
                        <div class="col-md-2">
                            <label for="txtPassword" >Select<br> Guarantor</label>
                            <select name="pg[pg_name_of_guarantor_id][]" class="form-control">
                                <option value="">Select Guarantor</option>
                                @foreach($bizOwners as $key=>$bizOwner)
                                <option value="{{$bizOwner->biz_owner_id}}">{{ucwords($bizOwner->first_name)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="txtPassword" >Time for<br> security</label>
                            <select name="pg[pg_time_for_perfecting_security_id][]" class="form-control">
                                <option value="">Select time for perfecting security</option>
                                <option value="1">Before Disbusrement</option>
                                <option value="2">With in 30 days from date of first disbusrement</option>
                                <option value="3">With in 60 days from date of first disbsurement</option>
                                <option value="4">With in 90 days from date of first disbursement </option>
                                <option value="5">With in 120 days from date of first disbursement</option>
                                <option value="6">with in 180 days from date of first disbursement</option>
                                <option value="7">with in 360 days from date of first disbsurement</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="txtPassword">Residential<br>  Address </label>
                            <input name="pg[pg_residential_address][]" class="form-control" value="">
                        </div>
                        <div class="col-md-2">
                            <label for="txtPassword">Net worth as<br> ITR/CA Cert</label>
                            <input name="pg[pg_net_worth][]" class="form-control" value="">
                        </div>
                        <div class="col-md-2">
                            <label for="txtPassword" >Comments if<br>  any</label>
                            <input name="pg[pg_comments][]" class="form-control" value="">
                        </div>
                        <div class="col-md-2 center">
                            <i class="fa fa-2x fa-plus-circle add-personal-guarantee-block mt-8"></i>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- -------------- -->
        
        <div class="col-md-12">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12"><b>Additional Security</b></label>
            <div id="check_block" style="width: 100%;">
                <div class="col-md-12" style="display: inline;">
                    <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="1" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '1') !== false)? 'checked': ''): '')}}> BG</label>
                    <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="2" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '2') !== false)? 'checked': ''): '')}}> FD</label>
                    <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="3" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '3') !== false)? 'checked': ''): '')}}> MF</label>
                    <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="4" name="addl_security[]" id="other_sec" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '4') !== false)? 'checked': ''): '')}}> Others</label>
                </div>                
            </div>
          </div>
        </div>

        <div class="col-md-12" style="float: right;">
            <div class="form-group row">
                <label for="txtPassword" class="col-md-12"><b>Comment</b></label>
                <div class="col-md-12">
                    <textarea name="comment" class="form-control" maxlength="200" placeholder="Comment">{{isset($offerData->comment)? $offerData->comment: ''}}</textarea>
                </div>    
            </div>    
        </div>
    </div>
    <div class="row">
    @can('update_limit_offer')
      <div class="col-md-12">
        <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
      </div>
    @endcan
    </div>   
  </form>
 
@endsection

@section('jscript')
<script>
  var bizOwners = {!! json_encode($bizOwners) !!};

  function checkLeasingValidations(){
    let limit_amt = "{{$limitData->limit_amt}}"; //limit from app_prgm_limit table
    let total_limit = "{{$totalLimit}}"; //total exposure limit amount
    let total_offered_amount = "{{$totalOfferedAmount}}"; //total offered amount including all product type from offer table
    let current_offer_amount = "{{$currentOfferAmount}}"; //current offered amount corresponding to app_prgm_limit_id

    let sub_total_amount = "{{$subTotalAmount}}"; //Sub total amount by app_prgm_limit_id

    let sub_total_balance = limit_amt - (sub_total_amount - current_offer_amount);

    unsetError('input[name=prgm_limit_amt]');
    unsetError('input[name=sub_limit]'); 
    unsetError('input[name=tenor]');
    // unsetError('select[name=equipment_type_id]');
    unsetError('select[name=facility_type_id]');
    unsetError('input[name=security_deposit]');
    unsetError('input[name=margin]');
    unsetError('input[name=irr]');
    // unsetError('input[name=security_deposit_type]');
    unsetError('select[name=rental_frequency]');
    unsetError('select[name=rental_frequency_type]');
    // unsetError('select[name=security_deposit_of]');
    // unsetError('input[name*=ptpq_from]');
    // unsetError('input[name*=ptpq_to]');
    // unsetError('input[name*=ptpq_rate]');
    // unsetError('input[name=ruby_sheet_xirr]');
    // unsetError('input[name=cash_flow_xirr]');
    unsetError('input[name=processing_fee]');
    unsetError('#check_block');
    unsetError('#radio_block');
    unsetError('select[name=asset_type_id]');
    unsetError('input[name=interest_rate]');

    let flag = true;
    let prgm_limit_amt = $('input[name=prgm_limit_amt]').val();
    let sub_limit = $('input[name=sub_limit]').val();
    let tenor = $('input[name=tenor]').val();
    // let equipment_type_id = $('select[name=equipment_type_id]').val();
    let facility_type_id = $('select[name=facility_type_id]').val();
    let security_deposit = $('input[name=security_deposit]').val();
    let margin = $('input[name=margin]').val();
    let offer_irr = $('input[name=irr]').val();
    // let security_deposit_of = $('select[name=security_deposit_of]').val();
    let rental_frequency = $('select[name=rental_frequency]').val();
    let rental_frequency_type = $('select[name=rental_frequency_type]').val();
    //let ptpq_from = $('input[name=ptpq_from]').val().trim();
    //let ptpq_to = $('input[name=ptpq_to]').val().trim();
    //let ptpq_rate = $('input[name=ptpq_rate]').val().trim();
    // let ruby_sheet_xirr = $('input[name=ruby_sheet_xirr]').val().trim();
    // let cash_flow_xirr = $('input[name=cash_flow_xirr]').val().trim();
    let processing_fee = $('input[name=processing_fee]').val().trim();
    let addl_security = $('input[name*=addl_security]').is(':checked');
    let comment = $('textarea[name=comment]').val().trim();
    // let security_deposit_type = $('input[name=security_deposit_type]:checked').val();

    // let is_invoice_processingfee = $('select[name=is_invoice_processingfee]').find(':selected').val();
    // let invoice_processingfee_type = $('select[name=invoice_processingfee_type]').find(':selected').val();
    // let invoice_processingfee_value = $('input[name=invoice_processingfee_value]').val();
    let asset_type_id = $('select[name=asset_type_id]').val();
    let interest_rate = $('input[name=interest_rate]').val();

    if(interest_rate == '' || isNaN(interest_rate)){
        setError('input[name=interest_rate]', 'Please fill interest rate');
        flag = false;
    }else if(interest_rate != '' && parseFloat(interest_rate) > 100){
        setError('input[name=interest_rate]', 'Interest rate can not be greater than 100 percent');
        flag = false;
    }

    if(prgm_limit_amt.length == 0 || parseInt(prgm_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=prgm_limit_amt]', 'Please fill loan offer amount');
        flag = false;
    }

    if(sub_limit.length == 0 || parseInt(sub_limit.replace(/,/g, '')) == 0){
        setError('input[name=sub_limit]', 'Please fill Limit');
        flag = false;
    }else if((parseInt(sub_limit.replace(/,/g, '')) > sub_total_balance) && sub_total_balance == 0){
        setError('input[name=sub_limit]', 'Your limit has been expired');
        flag = false;
    }else if((parseInt(sub_limit.replace(/,/g, '')) > sub_total_balance)){
        setError('input[name=sub_limit]', 'Limit can\'t exceed from ('+sub_total_balance+') balance limit amount');
        flag = false;
    }

    if(tenor == ''){
        setError('input[name=tenor]', 'Please flll tenor');
        flag = false;
    }

    // if(equipment_type_id == ''){
    //     setError('select[name=equipment_type_id]', 'Please select equipment type');
    //     flag = false;
    // }

    if(asset_type_id == ''){
        setError('select[name=asset_type_id]', 'Please select asset type');
        flag = false;
    }

    if(facility_type_id == ''){
        setError('select[name=facility_type_id]', 'Please select facility type');
        flag = false;
    }

    // if(typeof security_deposit_type == 'undefined'){
    //     setError('#radio_block', 'Please select security deposit type');
    //     flag = false;
    // }

    // if((parseInt(sub_limit.replace(/,/g, '')) < security_deposit)){
    //     setError('input[name=security_deposit]', 'Security deposit can\'t exceed from ('+sub_limit+') balance limit amount');
    //     flag = false;
    // }      

    if(security_deposit == '' || isNaN(security_deposit)){
        setError('input[name=security_deposit]', 'Please fill security deposit');
        flag = false;
    }else if(parseFloat(security_deposit) > 100){
        setError('input[name=security_deposit]', 'Security deposit can not be greater than 100 percent');
        flag = false;
    }
    // else if(security_deposit_type == 2 && parseFloat(security_deposit) > 100){
    //     setError('input[name=security_deposit]', 'Security deposit can not be greater than 100 percent');
    //     flag = false;
    // }else if((security_deposit_type == 1) && (parseInt(security_deposit) != security_deposit)){
    //     setError('input[name=security_deposit]', 'Please fill correct security deposit amount');
    //     flag = false;
    // }
       
    if(margin == '' || isNaN(margin)){
        setError('input[name=margin]', 'Please fill margin money');
        flag = false;
    }else if(parseFloat(margin) > 100){
        setError('input[name=margin]', 'Margin money can not be greater than 100 percent');
        flag = false;
    }

    if(offer_irr == '' || isNaN(offer_irr)){
        setError('input[name=irr]', 'Please fill IRR');
        flag = false;
    }else if(parseFloat(offer_irr) > 100){
        setError('input[name=irr]', 'IRR can not be greater than 100 percent');
        flag = false;
    }
    // if(security_deposit_of == ''){
    //     setError('select[name=security_deposit_of]', 'Please select security deposit type');
    //     flag = false;
    // }

    if(rental_frequency == ''){
        setError('select[name=rental_frequency]', 'Please select rental frequency');
        flag = false;
    }

    if(rental_frequency_type == ''){
        setError('select[name=rental_frequency_type]', 'Please select frequency type');
        flag = false;
    }
    
    // if(is_invoice_processingfee == 1){
    //     if (invoice_processingfee_type == '') {
    //         setError('input[name=invoice_processingfee_type]', 'Please select charge type');
    //         flag = false;
    //     }
    //     if (invoice_processingfee_value == '') {
    //         setError('input[name=invoice_processingfee_value]', 'Please fill charge value');
    //         flag = false;
    //     }
    //     if(invoice_processingfee_type == 2 && invoice_processingfee_value >= 50) {
    //         setError('input[name=invoice_processingfee_value]', 'Invoice processing fee can not be greater than 50%');
    //         flag = false;
    //     }
    // }
    let data = [];

    // if(tenor != '' && rental_frequency != ''){
    //     $('input[name*=ptpq_from]').each(function(i,val){
    //         let ttlcount = $('input[name*=ptpq_from]').length;
    //         let rf = {1:12, 2:6, 3:3, 4:1};
    //         let ptpq_from = $('input[name*=ptpq_from]').eq(i).val().trim();
    //         let ptpq_to = $('input[name*=ptpq_to]').eq(i).val().trim();
    //         let ptpq_rate = $('input[name*=ptpq_rate]').eq(i).val().trim();
    //         let obj = {
    //             'from':ptpq_from,
    //             'to':ptpq_to,
    //             'rate':ptpq_rate
    //         };
    //         data.push(obj);

    //         if(ptpq_from == '' || isNaN(ptpq_from)){
    //             setError('input[name*=ptpq_from]:eq('+i+')', 'Please fill FROM PTP');
    //             flag = false;
    //         }else if(i == 0){
    //             if(ptpq_from != 1){
    //                 setError('input[name*=ptpq_from]:eq('+i+')', 'From PTP should starts from 1');
    //                 flag = false;
    //             }
    //         }else if(ptpq_from -1 != data[i-1]['to']){
    //             setError('input[name*=ptpq_from]:eq('+i+')', 'Please fill correct FROM PTP');
    //             flag = false;
    //         }

    //         if(ptpq_to == '' || isNaN(ptpq_to)){
    //             setError('input[name*=ptpq_to]:eq('+i+')', 'Please fill TO PTP');
    //             flag = false;
    //         }else if((i == ttlcount-1) && (parseInt(ptpq_to) < parseInt(ptpq_from) || parseInt(ptpq_to) != Math.ceil(tenor/rf[rental_frequency])  )){
    //             setError('input[name*=ptpq_to]:eq('+i+')', 'To PTP should equal to Tenor/Rental Frequncy');
    //             flag = false;
    //         }else if(parseInt(ptpq_to) < parseInt(ptpq_from) || parseInt(ptpq_to) > Math.ceil(tenor/rf[rental_frequency])){
    //             setError('input[name*=ptpq_to]:eq('+i+')', 'To PTP should not greater than Tenor/TO PTP');
    //             flag = false;
    //         }

    //         if(ptpq_rate == '' || isNaN(ptpq_rate)){
    //             setError('input[name*=ptpq_rate]:eq('+i+')', 'Please fill PTP rate');
    //             flag = false;
    //         }
    //     });
    // }

    // if(ruby_sheet_xirr == '' || isNaN(ruby_sheet_xirr)){
    //     setError('input[name=ruby_sheet_xirr]', 'Please fill Ruby Sheet XIRR');
    //     flag = false;
    // }else if(parseFloat(ruby_sheet_xirr) > 100){
    //     setError('input[name=ruby_sheet_xirr]', 'Ruby Sheet XIRR can not be greater than 100 percent');
    //     flag = false;
    // }

    // if(cash_flow_xirr == '' || isNaN(cash_flow_xirr)){
    //     setError('input[name=cash_flow_xirr]', 'Please fill Cash Flow XIRR');
    //     flag = false;
    // }else if(parseFloat(cash_flow_xirr) > 100){
    //     setError('input[name=cash_flow_xirr]', 'Cash Flow XIRR can not be greater than 100 percent');
    //     flag = false;
    // }

    if(processing_fee == '' || isNaN(processing_fee)){
        setError('input[name=processing_fee]', 'Please fill processing fee');
        flag = false;
    }else if(parseFloat(processing_fee) > 100){
        setError('input[name=processing_fee]', 'Processing fee can not be greater than 100 percent');
        flag = false;
    }

    /*if(addl_security == ''){
        setError('#check_block', 'Please check atleast one security');
        flag = false;
    }*/

    if($('input[name*=addl_security]').is(':checked')){
        if(comment == ''){
            setError('textarea[name=comment]', 'Please fill security comment');
            flag = false;
        }else{
            // TAKE REST
        }
    }

    if(flag){
        return true;
    }else{
        return false;
    }
  }

  $(document).ready(function(){
    /*$('#other_sec').on('change', function(){
        unsetError('textarea[name=comment]');
        if($('#other_sec').is(':checked')){
            $('textarea[name=comment]').css('display', 'inline');
        }else{
            $('textarea[name=comment]').css('display', 'none');
            $('textarea[name=comment]').val('');
        }
    });*/

    // $('input[name=security_deposit_type]').on('change', function(){
    //     let sdt = $('input[name=security_deposit_type]:checked').val();
    //     if(sdt == 1){
    //         $('#sdt').text('Amount');
    //         $('input[name=security_deposit]').val('');
    //         $('.fa-change').removeClass('fa-percent').addClass('fa-inr');
    //         $('input[name=security_deposit]').attr('Placeholder', 'Deposit Amount');
    //     }else{
    //         $('#sdt').text('Percent');
    //         $('input[name=security_deposit]').val('');
    //         $('.fa-change').removeClass('fa-inr').addClass('fa-percent');
    //         $('input[name=security_deposit]').attr('Placeholder', 'Deposit Percent');
    //     }
    // });

  })

  $(document).on('click', '.add-ptpq-block', function(){
    let ptpq_block = '<div class="row mt10">'+
            '<div class="col-md-3">'+
                '<input type="text" name="ptpq_from[]" class="form-control" value="" placeholder="From Period" maxlength="3" onkeyup="this.value=this.value.replace(/[^\\d]/,\'\')">'+
            '</div>'+
            '<div class="col-md-3">'+
                '<input type="text" name="ptpq_to[]" class="form-control" value="" placeholder="To Period" maxlength="3" onkeyup="this.value=this.value.replace(/[^\\d]/,\'\')">'+
            '</div>'+
            '<div class="col-md-4 INR">'+
                '<a href="javascript:void(0);" class="verify-owner-no" style="top: 0;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                '<input type="text" name="ptpq_rate[]" class="form-control" value="" placeholder="PTPQ Rate" maxlength="6">'+
            '</div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-ptpq-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#ptpq-block').append(ptpq_block);
  });

  $(document).on('click', '.remove-ptpq-block', function(){
    $(this).parent('div').parent('div').remove();
  });
  
  $(document).on('change', '#invoice_processingfee', function(){
    let selected_val = $(this).find('option:selected').val();
    let selector1 = $('#invoice_processingfee_type_div');
    let selector2 = $('#invoice_processingfee_value_div');

    if(selected_val == 1){
        $(selector1).show();
        $(selector2).show();
    }else{
        $(selector1).hide();
        $(selector2).hide();
    }
  })
  
  $(document).on('change', '#invoice_processingfee_type', function(){
    $('#invoice_processingfee_value').val('');
  })

  $(document).on('change', '.show-hide', function(){
    let selected_val = $(this).find('option:selected').val();
    let selector = $(this).data('block_id');

    if(selected_val == 1){
        $(selector).show();
    }else{
        $(selector).hide();
        $(selector+'>div:not(:first)').remove();
    }
  });

  $(document).on('click', '.add-personal-guarantee-block', function(){
    let guarantorOption = guarantorDropdown(bizOwners);
    let personal_guarantee_block = '<div class="row mt10">'+
            '<div class="col-md-2">'+
                '<select name="pg[pg_name_of_guarantor_id][]" class="form-control">'+guarantorOption+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="pg[pg_time_for_perfecting_security_id][]" class="form-control">'+
                    '<option value="">Select time for perfecting security</option>'+
                    '<option value="1">Before Disbusrement</option>'+
                    '<option value="2">With in 30 days from date of first disbusrement</option>'+
                    '<option value="3">With in 60 days from date of first disbsurement</option>'+
                    '<option value="4">With in 90 days from date of first disbursement </option>'+
                    '<option value="5">With in 120 days from date of first disbursement</option>'+
                    '<option value="6">with in 180 days from date of first disbursement</option>'+
                    '<option value="7">with in 360 days from date of first disbsurement</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="pg[pg_residential_address][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="pg[pg_net_worth][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="pg[pg_comments][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-personal-guarantee-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
        $('#personal-guarantee-block').append(personal_guarantee_block);
    });

    $(document).on('click', '.remove-personal-guarantee-block', function(){
        $(this).parent('div').parent('div').remove();
    });

    function guarantorDropdown(bizOwners){
        let $html='<option value="">Select Guarantor</option>';
        $.each(bizOwners,function(i,bizOwner){
            $html += '<option value="'+bizOwner.biz_owner_id+'">'+bizOwner.first_name+'</option>';
        })
        return $html;
    }
</script>
@endsection