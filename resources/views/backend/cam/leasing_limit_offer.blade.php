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
        <input type="text" name="prgm_limit_amt" class="form-control number_format" value="{{isset($offerData->programLimit->limit_amt)? number_format($offerData->programLimit->limit_amt): number_format($limitData->limit_amt)}}" placeholder="Loan Offer" maxlength="15"><span class="float-right">Balance: <i class="fa fa-inr"></i>{{($actualBalance > 0)? $actualBalance: 0}}</span>
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
        <input type="text" name="equipment_type" class="form-control" value="{{isset($offerData->equipment_type)? $offerData->equipment_type: ''}}" placeholder="Equipment type" maxlength="80">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Security Deposit/Margin(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="security_deposit" class="form-control" value="{{isset($offerData->security_deposit)? $offerData->security_deposit: ''}}" placeholder="Security Deposit/Margin" maxlength="5">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Rental Frequency:</b></label> 
        <div class="col-md-8">
        <select name="rental_frequency" class="form-control">
            <option value="">Select rental frequency</option>
            <option value="4" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 4)? 'selected': ''):''}}>Monthly</option>
            <option value="3" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 3)? 'selected': ''):''}}>Quaterly</option>
            <option value="2" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 2)? 'selected': ''):''}}>Bi-Yearly</option>
            <option value="1" {{isset($offerData->rental_frequency)? (($offerData->rental_frequency == 1)? 'selected': ''):''}}>Yearly</option>
        </select>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>PTPQ(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="ptpq" class="form-control" value="{{isset($offerData->ptpq)? $offerData->ptpq: ''}}" placeholder="PTPQ" maxlength="5">
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>XIRR(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="xirr" class="form-control" value="{{isset($offerData->xirr)? $offerData->xirr: ''}}" placeholder="Adhoc Interest Rate" maxlength="6">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Additional Security:</b></label> 
        <div class="col-md-8">
            <div id="check_block">
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="1" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '1') !== false)? 'checked': ''): '')}}> BG</label>
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="2" name="addl_security[]" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '2') !== false)? 'checked': ''): '')}}> MF</label>
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="3" name="addl_security[]" id="other_sec" {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '3') !== false)? 'checked': ''): '')}}> Others</label>
                <input type="text" name="comment" class="" style="display: {{(isset($offerData->addl_security)? ((strpos((string)$offerData->addl_security, '3') !== false)? 'inline': 'none'): 'none')}}" value="{{isset($offerData->comment)? $offerData->comment: ''}}" placeholder="Fill other security">
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
    unsetError('input[name=equipment_type]');
    unsetError('input[name=security_deposit]');
    unsetError('select[name=rental_frequency]');
    unsetError('input[name=ptpq]');
    unsetError('input[name=xirr]');
    unsetError('#check_block');

    let flag = true;
    let prgm_limit_amt = $('input[name=prgm_limit_amt]').val();
    let tenor = $('input[name=tenor]').val();
    let equipment_type = $('input[name=equipment_type]').val();
    let security_deposit = $('input[name=security_deposit]').val();
    let rental_frequency = $('select[name=rental_frequency]').val();
    let ptpq = $('input[name=ptpq]').val().trim();
    let xirr = $('input[name=xirr]').val().trim();
    let addl_security = $('input[name*=addl_security]').is(':checked');
    let comment = $('input[name=comment]').val().trim();

    if(prgm_limit_amt.length == 0 || parseInt(prgm_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=prgm_limit_amt]', 'Please fill loan offer amount');
        flag = false;
    }else if((parseInt(prgm_limit_amt.replace(/,/g, '')) > actual_balance)){
        setError('input[name=prgm_limit_amt]', 'Limit amount can not exceed from balance amount');
        flag = false;
    }

    if(tenor == ''){
        setError('input[name=tenor]', 'Please flll tenor');
        flag = false;
    }

    if(equipment_type == ''){
        setError('input[name=equipment_type]', 'Please fill equipment type');
        flag = false;
    }

    if(security_deposit == '' || isNaN(security_deposit)){
        setError('input[name=security_deposit]', 'Please fill security deposit');
        flag = false;
    }else if(parseFloat(security_deposit) > 20){
        setError('input[name=security_deposit]', 'Please fill correct security deposit');
        flag = false;
    }

    if(rental_frequency == ''){
        setError('input[name=rental_frequency]', 'Please select rental frequency');
        flag = false;
    }

    if(ptpq == '' || isNaN(ptpq)){
        setError('input[name=ptpq]', 'Please fill PTPQ');
        flag = false;
    }else if(parseFloat(ptpq) > 10){
        setError('input[name=ptpq]', 'Please fill correct PTPQ');
        flag = false;
    }

    if(xirr == '' || isNaN(xirr)){
        setError('input[name=xirr]', 'Please fill XIRR');
        flag = false;
    }else if(parseFloat(xirr) > 20){
        setError('input[name=xirr]', 'Please fill correct XIRR');
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