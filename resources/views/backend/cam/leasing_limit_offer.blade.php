@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit_offer')}}" target="_top" onsubmit="return checkLeasingValidations()">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id">
    <input type="hidden" value="{{request()->get('prgm_offer_id')}}" name="offer_id">
    
    <div class="row">
        <div class="col-md-6">
          <div class="form-group ">
            <label for="txtPassword" ><b>Product</b></label> 
            <input type="text" class="form-control" value="Leasing" placeholder="Facility Type" maxlength="15" disabled>
          </div>
        </div>

        @php
        $programBalanceLimit = $programLimit - $programOfferedAmount + $currentOfferAmount;
        $balanceLimit = $totalLimit - $totalOfferedAmount + $currentOfferAmount;
        $actualBalance = ($programBalanceLimit < $balanceLimit)? $programBalanceLimit: $balanceLimit;
        @endphp

        <div class="col-md-6">
          <div class="form-group INR">
            <label for="txtPassword" ><b>Limit</b></label> 
            <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
            <span class="float-right text-success">Total Balance: <i class="fa fa-inr"></i>{{($balanceLimit > 0)? $balanceLimit: 0}}</span>
            <input type="text" name="prgm_limit_amt" class="form-control number_format" value="{{isset($limitData->limit_amt)? number_format($limitData->limit_amt): ''}}" placeholder="Limit" maxlength="15" readonly>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group ">
            <label for="txtPassword" ><b>Facility Type</b></label> 
            <select class="form-control" name="facility_type_id">
                <option value="">Select Facility Type</option>
                @foreach($facilityTypeList as $key => $facilityType)
                <option value="{{$key}}" {{ (isset($offerData->facility_type_id) && $offerData->facility_type_id == $key) ? 'selected' : ''}}>{{$facilityType}}</option>
                @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group INR">
            <label for="txtPassword" ><b>Limit of the Equipment</b></label>
            <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a> 
            <input type="text" name="sub_limit" class="form-control number_format" value="{{isset($offerData->prgm_limit_amt)? number_format($offerData->prgm_limit_amt): ''}}" placeholder="Limit of the Equipment" maxlength="15">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group ">
            <label for="txtPassword" ><b>Tenor (Months)</b></label> 
            <input type="text" name="tenor" class="form-control" value="{{isset($offerData->tenor)? $offerData->tenor: ''}}" placeholder="Tenor" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group ">
            <label for="txtPassword" ><b>Equipment Type</b></label> 
            <select class="form-control" name="equipment_type_id">
                <option value="">Select Equipment type</option>
                @foreach($equips as $key => $equip)
                <option value="{{$key}}" {{(isset($offerData->equipment_type_id) && $offerData->equipment_type_id == $key)? 'selected': ''}}>{{$equip}}</option>
                @endforeach
            </select>
          </div>
        </div>
    
        <div class="col-md-12" style="display: {{((isset($offerData->facility_type_id) && $offerData->facility_type_id != 3)? 'block': 'none')}};">
            <div class="form-group ">
                <label for="txtPassword" ><b>Security Deposit</b></label> 
                <br/>
                <div id="radio_block">
                    <label class="radio-inline"><input type="radio" name="security_deposit_type" value="1" {{isset($offerData->security_deposit_type)? (($offerData->security_deposit_type == 1)? 'checked': '') : ''}}> Flat</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label class="radio-inline"><input type="radio" name="security_deposit_type" value="2" {{isset($offerData->security_deposit_type)? (($offerData->security_deposit_type == 2)? 'checked': '') : ''}}> Percent</label>
                </div>
            </div>
        </div>

        <div class="col-md-6" style="display: {{((isset($offerData->facility_type_id) && $offerData->facility_type_id != 3)? 'block': 'none')}};">
            <div class="form-group INR">
                <label for="txtPassword"><b>Deposit <span id="sdt">{{isset($offerData->security_deposit_type)? (($offerData->security_deposit_type == 1)? 'Amount': 'Percent') : 'Amount'}}</span></b></label>
                <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa-change fa {{isset($offerData->security_deposit_type)? (($offerData->security_deposit_type == 1)? 'fa-inr': 'fa-percent') : 'fa-inr'}}" aria-hidden="true"></i></a> 
                <input type="text" name="security_deposit" class="form-control" value="{{isset($offerData->security_deposit)? (($offerData->security_deposit_type == 1)? (int)$offerData->security_deposit: $offerData->security_deposit): ''}}" placeholder="Deposit {{isset($offerData->security_deposit_type)? (($offerData->security_deposit_type == 1)? 'Amount': 'Percent') : 'Amount'}}" maxlength="5">
            </div>
        </div>

        <div class="col-md-6" style="display: {{((isset($offerData->facility_type_id) && $offerData->facility_type_id == 3)? 'block': 'none')}};">
          <div class="form-group">
            <label for="txtPassword"><b>Discounting (%)</b></label>
            <input type="text" name="discounting" class="form-control" value="{{isset($offerData->discounting)? $offerData->discounting: ''}}" placeholder="Discounting" maxlength="6">
          </div>
        </div>

        <div class="col-md-6" style="display: {{((isset($offerData->facility_type_id) && $offerData->facility_type_id != 3)? 'block': 'none')}};">
            <div class="form-group">
                <label for="txtPassword"><b>Deposit Type</b></label> 
                <select name="security_deposit_of" class="form-control">
                    <option value="">Select Deposit Type</option>
                    <option value="4" {{isset($offerData->security_deposit_of)? (($offerData->security_deposit_of == 4)? 'selected': ''):''}}>Sanction</option>
                    <option value="3" {{isset($offerData->security_deposit_of)? (($offerData->security_deposit_of == 3)? 'selected': ''):''}}>Asset Base Value</option>
                    <option value="2" {{isset($offerData->security_deposit_of)? (($offerData->security_deposit_of == 2)? 'selected': ''):''}}>Asset Value</option>
                    <option value="1" {{isset($offerData->security_deposit_of)? (($offerData->security_deposit_of == 1)? 'selected': ''):''}}>Loan Amount</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="txtPassword"><b>Rental Frequency</b></label> 
                <select name="rental_frequency" class="form-control">
                    <option value="">Select Rental Frequency</option>
                    <option value="4" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 4)? 'selected': ''):''}}>Monthly</option>
                    <option value="3" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 3)? 'selected': ''):''}}>Quarterly</option>
                    <option value="2" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 2)? 'selected': ''):''}}>Bi-Yearly</option>
                    <option value="1" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 1)? 'selected': ''):''}}>Yearly</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtPassword" ><b>Frequency Type</b></label> 
                <select name="rental_frequency_type" class="form-control">
                    <option value="">Select Frequency Type</option>
                    <option value="1" {{isset($offerData->rental_frequency_type)? (($offerData->rental_frequency_type == 1)? 'selected': ''):''}}>Advance</option>
                    <option value="2" {{isset($offerData->rental_frequency_type)? (($offerData->rental_frequency_type == 2)? 'selected': ''):''}}>Arrears</option>
                </select>
            </div>
        </div>
    
        <div class="col-md-12" style="display: {{((isset($offerData->facility_type_id) && $offerData->facility_type_id != 3)? 'block': 'none')}};">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;"><b>PTP Frequency</b></label>
            <div class="col-md-12" id="ptpq-block">
                @if(isset($offerData->offerPtpq) && count($offerData->offerPtpq))
                @foreach($offerData->offerPtpq as $key=>$ptpq)
                <div class="row {{($loop->first)? '': 'mt10'}}">
                    <div class="col-md-3">
                        @if($loop->first)
                            <label for="txtPassword"><b>From Period</b></label>
                        @endif
                        <input type="text" name="ptpq_from[]" class="form-control" value="{{(int)$ptpq->ptpq_from}}" placeholder="From Period" maxlength="5" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                    </div>
                    <div class="col-md-3">
                        @if($loop->first)
                            <label for="txtPassword"><b>To Period</b></label>
                        @endif
                        <input type="text" name="ptpq_to[]" class="form-control" value="{{(int)$ptpq->ptpq_to}}" placeholder="To Period" maxlength="5" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                    </div>
                    <div class="col-md-4 INR">
                        @if($loop->first)
                            <label for="txtPassword"><b>Rate</b></label>
                        @endif
                        <a href="javascript:void(0);" class="verify-owner-no" style="top: {{($loop->first)? '29px': 0}};"><i class="fa fa-inr" aria-hidden="true"></i></a>
                        <input type="text" name="ptpq_rate[]" class="form-control" value="{{$ptpq->ptpq_rate}}" placeholder="Rate" maxlength="6">
                    </div>
                    <div class="col-md-2 center">
                     @if($loop->first)
                        <i class="fa fa-2x fa-plus-circle add-ptpq-block mt-4"></i>
                     @else
                        <i class="fa fa-2x fa-times-circle remove-ptpq-block" style="color: red;"></i>
                     @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="row">
                    <div class="col-md-3">
                    <label for="txtPassword"><b>From Period</b></label>
                        <input type="text" name="ptpq_from[]" class="form-control" value="" placeholder="From Period" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                    </div>
                    <div class="col-md-3">
                    <label for="txtPassword"><b>To Period</b></label>
                        <input type="text" name="ptpq_to[]" class="form-control" value="" placeholder="To Period" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                    </div>
                    <div class="col-md-4 INR">
                    <label for="txtPassword"><b>Rate</b></label>
                        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                        <input type="text" name="ptpq_rate[]" class="form-control" value="" placeholder="Rate" maxlength="5">
                    </div>
                    <div class="col-md-2 ">
                        <i class="fa fa-2x fa-plus-circle add-ptpq-block mt-4"></i>
                    </div>
                </div>
                @endif
            </div>
          </div>
        </div>

        <div class="col-md-12" style="display: {{((isset($offerData->facility_type_id) && $offerData->facility_type_id != 3)? 'block': 'none')}};">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;"><b>XIRR (%)</b></label> 
            <div class="col-md-6">
            <label for="txtPassword"><b>Ruby Sheet</b></label>
            <input type="text" name="ruby_sheet_xirr" class="form-control" value="{{isset($offerData->ruby_sheet_xirr)? $offerData->ruby_sheet_xirr: ''}}" placeholder="Ruby Sheet" maxlength="5">
            </div>
            <div class="col-md-6">
            <label for="txtPassword"><b>Cash Flow</b></label>
            <input type="text" name="cash_flow_xirr" class="form-control" value="{{isset($offerData->cash_flow_xirr)? $offerData->cash_flow_xirr: ''}}" placeholder="Cash Flow" maxlength="5">
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="txtPassword"><b>Processing Fee (%)</b></label>
            <input type="text" name="processing_fee" class="form-control" value="{{isset($offerData->processing_fee)? $offerData->processing_fee: ''}}" placeholder="Processing Fee" maxlength="6">
          </div>
        </div>
    
        <div class="col-md-12">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12"><b>Additional Security</b></label> 
            <div id="check_block" style="width: 100%;">
                <div class="col-md-6" style="display: inline;">
                    <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="1" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '1') !== false)? 'checked': ''): '')}}> BG</label>
                    <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="2" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '2') !== false)? 'checked': ''): '')}}> FD</label>
                    <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="3" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '3') !== false)? 'checked': ''): '')}}> MF</label>
                    <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="4" name="addl_security[]" id="other_sec" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '4') !== false)? 'checked': ''): '')}}> Others</label>
                </div>
                <div class="col-md-6" style="float: right;">
                <textarea name="comment" class="form-control" maxlength="200" placeholder="Security comment">{{isset($offerData->comment)? $offerData->comment: ''}}</textarea>
                </div>
            </div>
          </div>
        </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
      </div>
    </div>   
  </form>
 
@endsection

@section('jscript')
<script>
  function checkLeasingValidations(){
    let limit_amt = "{{$limitData->limit_amt}}"; //limit from app_prgm_limit table
    let total_limit = "{{$totalLimit}}"; //total exposure limit amount
    let program_limit = "{{$programLimit}}"; //program limit
    let total_offered_amount = "{{$totalOfferedAmount}}"; //total offered amount including all product type from offer table
    let program_offered_amount = "{{$programOfferedAmount}}"; //total offered amount related to program from offer table
    let current_offer_amount = "{{$currentOfferAmount}}"; //current offered amount corresponding to app_prgm_limit_id

    let sub_total_amount = "{{$subTotalAmount}}"; //Sub total amount by app_prgm_limit_id

    let program_balance_limit = program_limit - program_offered_amount + current_offer_amount;
    let balance_limit = total_limit - total_offered_amount + current_offer_amount;
    let actual_balance = (program_balance_limit < balance_limit)? program_balance_limit: balance_limit;
    let sub_total_balance = limit_amt - (sub_total_amount - current_offer_amount);

    unsetError('input[name=prgm_limit_amt]');
    unsetError('input[name=sub_limit]'); 
    unsetError('input[name=tenor]');
    unsetError('select[name=equipment_type_id]');
    unsetError('select[name=facility_type_id]');
    unsetError('input[name=security_deposit]');
    unsetError('input[name=security_deposit_type]');
    unsetError('select[name=rental_frequency]');
    unsetError('select[name=rental_frequency_type]');
    unsetError('select[name=security_deposit_of]');
    unsetError('input[name*=ptpq_from]');
    unsetError('input[name*=ptpq_to]');
    unsetError('input[name*=ptpq_rate]');
    unsetError('input[name=ruby_sheet_xirr]');
    unsetError('input[name=cash_flow_xirr]');
    unsetError('input[name=discounting]');
    unsetError('input[name=processing_fee]');
    unsetError('#check_block');
    unsetError('#radio_block');


    let flag = true;
    let prgm_limit_amt = $('input[name=prgm_limit_amt]').val();
    let sub_limit = $('input[name=sub_limit]').val();
    let tenor = $('input[name=tenor]').val();
    let equipment_type_id = $('select[name=equipment_type_id]').val();
    let facility_type_id = $('select[name=facility_type_id]').val();
    let security_deposit = $('input[name=security_deposit]').val();
    let security_deposit_of = $('select[name=security_deposit_of]').val();
    let rental_frequency = $('select[name=rental_frequency]').val();
    let rental_frequency_type = $('select[name=rental_frequency_type]').val();
    //let ptpq_from = $('input[name=ptpq_from]').val().trim();
    //let ptpq_to = $('input[name=ptpq_to]').val().trim();
    //let ptpq_rate = $('input[name=ptpq_rate]').val().trim();
    let ruby_sheet_xirr = $('input[name=ruby_sheet_xirr]').val().trim();
    let cash_flow_xirr = $('input[name=cash_flow_xirr]').val().trim();
    let discounting = $('input[name=discounting]').val().trim();
    let processing_fee = $('input[name=processing_fee]').val().trim();
    let addl_security = $('input[name*=addl_security]').is(':checked');
    let comment = $('textarea[name=comment]').val().trim();
    let security_deposit_type = $('input[name=security_deposit_type]:checked').val();

    if(prgm_limit_amt.length == 0 || parseInt(prgm_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=prgm_limit_amt]', 'Please fill loan offer amount');
        flag = false;
    }else if((parseInt(prgm_limit_amt.replace(/,/g, '')) > balance_limit)){
        setError('input[name=prgm_limit_amt]', 'Limit amount can not exceed from balance amount');
        flag = false;
    }

    if(sub_limit.length == 0 || parseInt(sub_limit.replace(/,/g, '')) == 0){
        setError('input[name=sub_limit]', 'Please fill Limit of the Equipment');
        flag = false;
    }else if((parseInt(sub_limit.replace(/,/g, '')) > sub_total_balance) && sub_total_balance == 0){
        setError('input[name=sub_limit]', 'Your limit has been expired');
        flag = false;
    }else if((parseInt(sub_limit.replace(/,/g, '')) > sub_total_balance)){
        setError('input[name=sub_limit]', 'Limit of the Equipment can\'t exceed from ('+sub_total_balance+') balance limit amount');
        flag = false;
    }

    if(tenor == ''){
        setError('input[name=tenor]', 'Please flll tenor');
        flag = false;
    }

    if(equipment_type_id == ''){
        setError('select[name=equipment_type_id]', 'Please select equipment type');
        flag = false;
    }

    if(facility_type_id == ''){
        setError('select[name=facility_type_id]', 'Please select facility type');
        flag = false;
    }

    if(security_deposit_of == ''){
        setError('select[name=security_deposit_of]', 'Please select security deposit type');
        flag = false;
    }

    if(rental_frequency == ''){
        setError('select[name=rental_frequency]', 'Please select rental frequency');
        flag = false;
    }

    if(rental_frequency_type == ''){
        setError('select[name=rental_frequency_type]', 'Please select frequency type');
        flag = false;
    }
    let data = [];

    if(tenor != '' && rental_frequency != '' && facility_type_id != 3){
        $('input[name*=ptpq_from]').each(function(i,val){
            let ttlcount = $('input[name*=ptpq_from]').length;
            let rf = {1:12, 2:6, 3:3, 4:1};
            let ptpq_from = $('input[name*=ptpq_from]').eq(i).val().trim();
            let ptpq_to = $('input[name*=ptpq_to]').eq(i).val().trim();
            let ptpq_rate = $('input[name*=ptpq_rate]').eq(i).val().trim();
            let obj = {
                'from':ptpq_from,
                'to':ptpq_to,
                'rate':ptpq_rate
            };
            data.push(obj);

            if(ptpq_from == '' || isNaN(ptpq_from)){
                setError('input[name*=ptpq_from]:eq('+i+')', 'Please fill FROM PTP');
                flag = false;
            }else if(i == 0){
                if(ptpq_from != 1){
                    setError('input[name*=ptpq_from]:eq('+i+')', 'From PTP should starts from 1');
                    flag = false;
                }
            }else if(ptpq_from -1 != data[i-1]['to']){
                setError('input[name*=ptpq_from]:eq('+i+')', 'Please fill correct FROM PTP');
                flag = false;
            }

            if(ptpq_to == '' || isNaN(ptpq_to)){
                setError('input[name*=ptpq_to]:eq('+i+')', 'Please fill TO PTP');
                flag = false;
            }else if((i == ttlcount-1) && (parseInt(ptpq_to) < parseInt(ptpq_from) || parseInt(ptpq_to) != Math.ceil(tenor/rf[rental_frequency])  )){
                setError('input[name*=ptpq_to]:eq('+i+')', 'To PTP should equal to Tenor/Rental Frequncy');
                flag = false;
            }else if(parseInt(ptpq_to) < parseInt(ptpq_from) || parseInt(ptpq_to) > Math.ceil(tenor/rf[rental_frequency])){
                setError('input[name*=ptpq_to]:eq('+i+')', 'To PTP should not greater than Tenor/TO PTP');
                flag = false;
            }

            if(ptpq_rate == '' || isNaN(ptpq_rate)){
                setError('input[name*=ptpq_rate]:eq('+i+')', 'Please fill PTP rate');
                flag = false;
            }
        });
    }

//-------------
    if(facility_type_id != 3){
        if(typeof security_deposit_type == 'undefined'){
            setError('#radio_block', 'Please select security deposit type');
            flag = false;
        }

        if(security_deposit == '' || isNaN(security_deposit)){
            setError('input[name=security_deposit]', 'Please fill security deposit');
            flag = false;
        }else if(security_deposit_type == 2 && parseFloat(security_deposit) > 100){
            setError('input[name=security_deposit]', 'Security deposit can not be greater than 100 percent');
            flag = false;
        }else if((security_deposit_type == 1) && (parseInt(security_deposit) != security_deposit)){
            setError('input[name=security_deposit]', 'Please fill correct security deposit amount');
            flag = false;
        }

        if(ruby_sheet_xirr == '' || isNaN(ruby_sheet_xirr)){
            setError('input[name=ruby_sheet_xirr]', 'Please fill Ruby Sheet XIRR');
            flag = false;
        }else if(parseFloat(ruby_sheet_xirr) > 100){
            setError('input[name=ruby_sheet_xirr]', 'Ruby Sheet XIRR can not be greater than 100 percent');
            flag = false;
        }

        if(cash_flow_xirr == '' || isNaN(cash_flow_xirr)){
            setError('input[name=cash_flow_xirr]', 'Please fill Cash Flow XIRR');
            flag = false;
        }else if(parseFloat(cash_flow_xirr) > 100){
            setError('input[name=cash_flow_xirr]', 'Cash Flow XIRR can not be greater than 100 percent');
            flag = false;
        }
    }else{
        if(discounting == '' || isNaN(discounting)){
            setError('input[name=discounting]', 'Please fill Discounting');
            flag = false;
        }else if(parseFloat(discounting) > 100){
            setError('input[name=discounting]', 'Discounting can not be greater than 100 percent');
            flag = false;
        }

    }
//--------------

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

    $('input[name=security_deposit_type]').on('change', function(){
        let sdt = $('input[name=security_deposit_type]:checked').val();
        if(sdt == 1){
            $('#sdt').text('Amount');
            $('input[name=security_deposit]').val('');
            $('.fa-change').removeClass('fa-percent').addClass('fa-inr');
            $('input[name=security_deposit]').attr('Placeholder', 'Deposit Amount');
        }else{
            $('#sdt').text('Percent');
            $('input[name=security_deposit]').val('');
            $('.fa-change').removeClass('fa-inr').addClass('fa-percent');
            $('input[name=security_deposit]').attr('Placeholder', 'Deposit Percent');
        }
    });

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

  $('select[name=facility_type_id]').on('change', function(){
    unsetError('input[name=security_deposit]');
    unsetError('input[name=security_deposit_type]');
    unsetError('input[name=ruby_sheet_xirr]');
    unsetError('input[name=cash_flow_xirr]');
    unsetError('select[name=security_deposit_of]');
    unsetError('input[name*=ptpq_from]');
    unsetError('input[name*=ptpq_to]');
    unsetError('input[name*=ptpq_rate]');
    unsetError('input[name=discounting]');
    unsetError('#radio_block');

    let ftid = $('select[name=facility_type_id] option:selected').val();
    if(ftid == 3){
        $('input[name=discounting]').parent().parent().show();
        $('input[name=ruby_sheet_xirr]').parent().parent().parent().hide();
        $('select[name=security_deposit_of]').parent().parent().hide();
        $('input[name=security_deposit]').parent().parent().hide();
        $('#radio_block').parent().parent().hide();
        $('#ptpq-block').parent().parent().hide();
    }else{
        $('input[name=discounting]').parent().parent().hide();
        $('input[name=ruby_sheet_xirr]').parent().parent().parent().show();
        $('select[name=security_deposit_of]').parent().parent().show();
        $('input[name=security_deposit]').parent().parent().show();
        $('#radio_block').parent().parent().show();
        $('#ptpq-block').parent().parent().show();
    }
  });

</script>
@endsection