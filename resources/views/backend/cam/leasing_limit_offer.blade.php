@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit_offer')}}" target="_top" onsubmit="return checkLeasingValidations()">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id">
    
    <div class="row">
    <div class="col-md-12">
      <div class="form-group row">
        <label for="txtPassword" class="col-md-4"><b>Facility Type:</b></label> 
        <div class="col-md-8">
        <input type="text" class="form-control" value="Leasing" placeholder="Facility Type" maxlength="15" disabled>
        </div>
      </div>
    </div>

    @php
    $programBalanceLimit = $programLimit - $programOfferedAmount + $currentOfferAmount;
    $balanceLimit = $totalLimit - $totalOfferedAmount + $currentOfferAmount;
    $actualBalance = ($programBalanceLimit < $balanceLimit)? $programBalanceLimit: $balanceLimit;
    @endphp

    <div class="col-md-12">
      <div class="form-group row INR">
        <label for="txtPassword" class="col-md-4"><b>Limit:</b></label> 
        <div class="col-md-8">
        <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="prgm_limit_amt" class="form-control number_format" value="{{isset($offerData->programLimit->limit_amt)? number_format($offerData->programLimit->limit_amt): number_format($limitData->limit_amt)}}" placeholder="Loan Offer" maxlength="15"><span class="float-right">Balance: <i class="fa fa-inr"></i>{{($balanceLimit > 0)? $balanceLimit: 0}}</span>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Invoice Tenor(Days):</b></label> 
        <div class="col-md-8">
        <input type="text" name="tenor" class="form-control" value="{{isset($offerData->tenor)? $offerData->tenor: ''}}" placeholder="Tenor" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Equipment Type:</b></label> 
        <div class="col-md-8">
        <select class="form-control" name="equipment_type_id">
            <option value="">Select Equipment type</option>
            <option value="1" {{(isset($offerData->equipment_type_id) && $offerData->equipment_type_id == 1)? 'selected': ''}}>Equipment type 1</option>
            <option value="2" {{(isset($offerData->equipment_type_id) && $offerData->equipment_type_id == 2)? 'selected': ''}}>Equipment type 2</option>
            <option value="3" {{(isset($offerData->equipment_type_id) && $offerData->equipment_type_id == 3)? 'selected': ''}}>Equipment type 3</option>
            <option value="4" {{(isset($offerData->equipment_type_id) && $offerData->equipment_type_id == 4)? 'selected': ''}}>Equipment type 4</option>
        </select>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Security Deposit:</b></label> 
        <div class="col-md-8">
        <label class="radio-inline"><input type="radio" name="security_deposit_type" value="1" checked> Flat</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <label class="radio-inline"><input type="radio" name="security_deposit_type" value="2"> Percent</label>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b></b></label> 
        <div class="col-md-4">
        <input type="text" name="security_deposit" class="form-control" value="{{isset($offerData->security_deposit)? (($offerData->security_deposit_type == 1)? (int)$offerData->security_deposit: $offerData->security_deposit): ''}}" placeholder="Security Deposit" maxlength="5">
        </div>
        <div class="col-md-4">
        <select name="security_deposit_of" class="form-control">
            <option value="">Select Deposit Type</option>
            <option value="4" {{isset($offerData->security_deposit_of)? (($offerData->security_deposit_of == 4)? 'selected': ''):''}}>Sanction</option>
            <option value="3" {{isset($offerData->security_deposit_of)? (($offerData->security_deposit_of == 3)? 'selected': ''):''}}>Asset Base Value</option>
            <option value="2" {{isset($offerData->security_deposit_of)? (($offerData->security_deposit_of == 2)? 'selected': ''):''}}>Asset Value</option>
            <option value="1" {{isset($offerData->security_deposit_of)? (($offerData->security_deposit_of == 1)? 'selected': ''):''}}>Loan Amount</option>
        </select>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Rental Frequency:</b></label> 
        <div class="col-md-4">
            <select name="rental_frequency" class="form-control">
                <option value="">Select Rental Frequency</option>
                <option value="4" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 4)? 'selected': ''):''}}>Monthly</option>
                <option value="3" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 3)? 'selected': ''):''}}>Quaterly</option>
                <option value="2" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 2)? 'selected': ''):''}}>Bi-Yearly</option>
                <option value="1" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 1)? 'selected': ''):''}}>Yearly</option>
            </select>
        </div>
        <div class="col-md-4">
            <select name="rental_frequency_type" class="form-control">
                <option value="">Select Frequency Type</option>
                <option value="1" {{isset($offerData->rental_frequency_type)? (($offerData->rental_frequency_type == 1)? 'selected': ''):''}}>Advance</option>
                <option value="2" {{isset($offerData->rental_frequency_type)? (($offerData->rental_frequency_type == 2)? 'selected': ''):''}}>Arrears</option>
            </select>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>PTPQ:</b></label> 
        <div class="col-md-4">
        <input type="text" name="ptpq_from" class="form-control" value="{{isset($offerData->ptpq_from)? (int)$offerData->ptpq_from: ''}}" placeholder="From Period" maxlength="5" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
        </div>
        <div class="col-md-4">
        <input type="text" name="ptpq_to" class="form-control" value="{{isset($offerData->ptpq_to)? (int)$offerData->ptpq_to: ''}}" placeholder="To Period" maxlength="5" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
        </div>
        <!-- <div class="col-md-2">
        <input type="text" name="ptpq_rate" class="form-control" value="{{isset($offerData->ptpq_rate)? $offerData->ptpq_rate: ''}}" placeholder="PTPQ Rate" maxlength="5">
        </div> -->
      </div>
    </div>

    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>XIRR(%):</b></label> 
        <div class="col-md-4">
        <input type="text" name="ruby_sheet_xirr" class="form-control" value="{{isset($offerData->ruby_sheet_xirr)? $offerData->ruby_sheet_xirr: ''}}" placeholder="Ruby Sheet XIRR" maxlength="5">
        </div>
        <div class="col-md-4">
        <input type="text" name="cash_flow_xirr" class="form-control" value="{{isset($offerData->cash_flow_xirr)? $offerData->cash_flow_xirr: ''}}" placeholder="Cash Flow XIRR" maxlength="5">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Additional Security:</b></label> 
        <div class="col-md-8">
            <div id="check_block">
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="1" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '1') !== false)? 'checked': ''): '')}}> BG</label>
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="2" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '2') !== false)? 'checked': ''): '')}}> FD</label>
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="3" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '3') !== false)? 'checked': ''): '')}}> MF</label>
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="4" name="addl_security[]" id="other_sec" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '4') !== false)? 'checked': ''): '')}}> Others</label>
                <input type="text" name="comment" class="" style="display: {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '4') !== false)? 'inline': 'none'): 'none')}}" value="{{isset($offerData->comment)? $offerData->comment: ''}}" placeholder="Fill other security" maxlength="200">
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
    let total_limit = "{{$totalLimit}}"; //total exposure limit amount
    let program_limit = "{{$programLimit}}"; //program limit
    let total_offered_amount = "{{$totalOfferedAmount}}"; //total offered amount including all product type from offer table
    let program_offered_amount = "{{$programOfferedAmount}}"; //total offered amount related to program from offer table
    let current_offer_amount = "{{$currentOfferAmount}}"; //current offered amount corresponding to app_prgm_limit_id

    let program_balance_limit = program_limit - program_offered_amount + current_offer_amount;
    let balance_limit = total_limit - total_offered_amount + current_offer_amount;
    let actual_balance = (program_balance_limit < balance_limit)? program_balance_limit: balance_limit;

    unsetError('input[name=prgm_limit_amt]');
    unsetError('input[name=tenor]');
    unsetError('select[name=equipment_type_id]');
    unsetError('input[name=security_deposit]');
    unsetError('select[name=rental_frequency]');
    unsetError('select[name=rental_frequency_type]');
    unsetError('select[name=security_deposit_of]');
    unsetError('input[name=ptpq_from]');
    unsetError('input[name=ptpq_to]');
    //unsetError('input[name=ptpq_rate]');
    unsetError('input[name=ruby_sheet_xirr]');
    unsetError('input[name=cash_flow_xirr]');
    unsetError('#check_block');

    let flag = true;
    let prgm_limit_amt = $('input[name=prgm_limit_amt]').val();
    let tenor = $('input[name=tenor]').val();
    let equipment_type_id = $('select[name=equipment_type_id]').val();
    let security_deposit = $('input[name=security_deposit]').val();
    let security_deposit_of = $('select[name=security_deposit_of]').val();
    let rental_frequency = $('select[name=rental_frequency]').val();
    let rental_frequency_type = $('select[name=rental_frequency_type]').val();
    let ptpq_from = $('input[name=ptpq_from]').val().trim();
    let ptpq_to = $('input[name=ptpq_to]').val().trim();
    //let ptpq_rate = $('input[name=ptpq_rate]').val().trim();
    let ruby_sheet_xirr = $('input[name=ruby_sheet_xirr]').val().trim();
    let cash_flow_xirr = $('input[name=cash_flow_xirr]').val().trim();
    let addl_security = $('input[name*=addl_security]').is(':checked');
    let comment = $('input[name=comment]').val().trim();
    let security_deposit_type = $('input[name=security_deposit_type]:checked').val();

    if(prgm_limit_amt.length == 0 || parseInt(prgm_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=prgm_limit_amt]', 'Please fill loan offer amount');
        flag = false;
    }else if((parseInt(prgm_limit_amt.replace(/,/g, '')) > balance_limit)){
        setError('input[name=prgm_limit_amt]', 'Limit amount can not exceed from balance amount');
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

    if(security_deposit == '' || isNaN(security_deposit)){
        setError('input[name=security_deposit]', 'Please fill security deposit');
        flag = false;
    }else if(security_deposit_type == 2 && parseFloat(security_deposit) > 100){
        setError('input[name=security_deposit]', 'Please fill correct security deposit percent');
        flag = false;
    }else if((security_deposit_type == 1) && (parseInt(security_deposit) != security_deposit)){
        setError('input[name=security_deposit]', 'Please fill correct security deposit amount');
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

    if(ptpq_from == '' || isNaN(ptpq_from)){
        setError('input[name=ptpq_from]', 'Please fill from PTPQ');
        flag = false;
    }

    if(ptpq_to == '' || isNaN(ptpq_to)){
        setError('input[name=ptpq_to]', 'Please fill to PTPQ');
        flag = false;
    }

    /*if(ptpq_rate == '' || isNaN(ptpq_rate)){
        setError('input[name=ptpq_rate]', 'Please fill PTPQ rate');
        flag = false;
    }*/

    if(ruby_sheet_xirr == '' || isNaN(ruby_sheet_xirr)){
        setError('input[name=ruby_sheet_xirr]', 'Please fill Ruby Sheet XIRR');
        flag = false;
    }else if(parseFloat(ruby_sheet_xirr) > 100){
        setError('input[name=ruby_sheet_xirr]', 'Please fill correct Ruby Sheet XIRR');
        flag = false;
    }

    if(cash_flow_xirr == '' || isNaN(cash_flow_xirr)){
        setError('input[name=cash_flow_xirr]', 'Please fill Cash Flow XIRR');
        flag = false;
    }else if(parseFloat(cash_flow_xirr) > 100){
        setError('input[name=cash_flow_xirr]', 'Please fill correct Cash Flow XIRR');
        flag = false;
    }

    if(addl_security == ''){
        setError('#check_block', 'Please check atleast one security');
        flag = false;
    }else if($('#other_sec').is(':checked')){
        if(comment == ''){
            setError('#check_block', 'Please fill other security');
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
    $('#other_sec').on('change', function(){
        if($('#other_sec').is(':checked')){
            $('input[name=comment]').css('display', 'inline');
        }else{
            $('input[name=comment]').css('display', 'none');
            $('input[name=comment]').val('');
        }
    })
  })
</script>
@endsection