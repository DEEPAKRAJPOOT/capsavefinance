@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit_offer')}}" target="_top" onsubmit="return true">
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


    <div class="col-md-12">
      <div class="form-group row INR">
        <label for="txtPassword" class="col-md-4"><b>Limit:</b></label> 
        <div class="col-md-8">
        <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="prgm_limit_amt" class="form-control number_format" value="{{isset($offerData->programLimit->limit_amt)? number_format($offerData->programLimit->limit_amt): number_format($limit_amt)}}" placeholder="Loan Offer" maxlength="15">
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
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="1" name="addl_security[]"> BG</label>
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="2" name="addl_security[]"> MF</label>
                <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="3" name="addl_security[]"> Others</label>
                <input type="text" name="comment" class="form-control" style="display: none" value="{{isset($offerData->coment)? $offerData->comment: ''}}">
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
    let tot_limit_amt = "{{$totalLimit}}";
    let prgm_limit = 0;
    let offered_limit = "{{$offeredLimit}}";
    let balance_limit = prgm_limit - offered_limit;

    unsetError('input[name=prgm_limit_amt]');
    unsetError('input[name=interest_rate]');
    unsetError('input[name=tenor]');
    unsetError('input[name=tenor_old_invoice]');
    unsetError('input[name=margin]');
    unsetError('input[name=overdue_interest_rate]');
    unsetError('input[name=adhoc_interest_rate]');
    unsetError('input[name=grace_period]');
    unsetError('input[name=processing_fee]');
    unsetError('input[name=check_bounce_fee]');

    let flag = true;
    let prgm_limit_amt = $('input[name=prgm_limit_amt]').val();
    let interest_rate = $('input[name=interest_rate]').val();
    let tenor = $('input[name=tenor]').val();
    let tenor_old_invoice = $('input[name=tenor_old_invoice]').val().trim();
    let margin = $('input[name=margin]').val().trim();
    let overdue_interest_rate = $('input[name=overdue_interest_rate]').val().trim();
    let adhoc_interest_rate = $('input[name=adhoc_interest_rate]').val().trim();
    let grace_period = $('input[name=grace_period]').val().trim();
    let processing_fee = $('input[name=processing_fee]').val().trim();
    let check_bounce_fee = $('input[name=check_bounce_fee]').val().trim();

    if(prgm_limit_amt.length == 0 || parseInt(prgm_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=prgm_limit_amt]', 'Please fill loan offer amount');
        flag = false;
    }else if((parseInt(prgm_limit_amt.replace(/,/g, '')) > parseInt(tot_limit_amt.replace(/,/g, ''))) || (parseInt(prgm_limit_amt.replace(/,/g, '')) > balance_limit)){
        setError('input[name=prgm_limit_amt]', 'Limit amount can not exceed from Balance/Total limit');
        flag = false;
    }

    if(interest_rate == '' || isNaN(interest_rate)){
        setError('input[name=interest_rate]', 'Please fill intereset rate');
        flag = false;
    }else if(parseFloat(interest_rate) > 20){
        setError('input[name=interest_rate]', 'Please fill correct intereset rate');
        flag = false;
    }

    if(tenor == ''){
        setError('input[name=tenor]', 'Please flll tenor');
        flag = false;
    }

    if(tenor_old_invoice == ''){
        setError('input[name=tenor_old_invoice]', 'Please fill old tenor invoice');
        flag = false;
    }

    if(margin == '' || isNaN(margin)){
        setError('input[name=margin]', 'Please fill margin');
        flag = false;
    }else if(parseFloat(margin) > 10){
        setError('input[name=margin]', 'Please fill correct margin rate');
        flag = false;
    }

    if(overdue_interest_rate == '' || isNaN(overdue_interest_rate)){
        setError('input[name=overdue_interest_rate]', 'Please fill Overdue intereset rate');
        flag = false;
    }else if(parseFloat(overdue_interest_rate) > 20){
        setError('input[name=overdue_interest_rate]', 'Please fill correct overdue interest rate');
        flag = false;
    }

    if(adhoc_interest_rate == '' || isNaN(adhoc_interest_rate)){
        setError('input[name=adhoc_interest_rate]', 'Please fill adhoc interest rate');
        flag = false;
    }else if(parseFloat(adhoc_interest_rate) > 20){
        setError('input[name=adhoc_interest_rate]', 'Please fill correct adhoc interest rate');
        flag = false;
    }

    if(grace_period == ''){
        setError('input[name=grace_period]', 'Please fill grace period');
        flag = false;
    }

    if(processing_fee == ''){
        setError('input[name=processing_fee]', 'Please fill processing fee');
        flag = false;
    }

    if(check_bounce_fee == ''){
        setError('input[name=check_bounce_fee]', 'Please fill check bounce fee');
        flag = false;
    }

    if(flag){
        return true;
    }else{
        return false;
    }
  }
</script>
@endsection