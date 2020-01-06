@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit_offer')}}" target="_top" onsubmit="return checkValidations()">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id">
    
    <div class="row">
    <div class="col-md-12">
      <div class="form-group row INR ">
        <label for="txtPassword" class="col-md-4"><b>Loan Offer:</b></label> 
        <div class="col-md-8">
        <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="prgm_limit_amt" class="form-control" value="{{isset($offerData->programLimit->limit_amt)? $offerData->programLimit->limit_amt: $limit_amt}}" placeholder="Loan Offer " maxlength="15" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
        <span class="s_value"><i class="fa fa-inr"></i>10,00,000 - <i class="fa fa-inr"></i>50,00,000</span>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Interest(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="interest_rate" class="form-control" value="{{isset($offerData->interest_rate)? $offerData->interest_rate: ''}}" placeholder="Interest Rate" maxlength="2">
        <span class="s_value">10%-12%</span>
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
        <label for="txtPassword" class="col-md-4"><b>Old Invoice Tenor(Days):</b></label> 
        <div class="col-md-8">
        <input type="text" name="tenor_old_invoice" class="form-control" value="{{isset($offerData->tenor_old_invoice)? $offerData->tenor_old_invoice: ''}}" placeholder="Tenor for old invoice" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Margin(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="margin" class="form-control" value="{{isset($offerData->margin)? $offerData->margin: ''}}" placeholder="Margin" maxlength="2">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Overdue Interest(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="overdue_interest_rate" class="form-control" value="{{isset($offerData->overdue_interest_rate)? $offerData->overdue_interest_rate: ''}}" placeholder="Overdue Interest Rate" maxlength="2">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Adhoc Interest(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="adhoc_interest_rate" class="form-control" value="{{isset($offerData->adhoc_interest_rate)? $offerData->adhoc_interest_rate: ''}}" placeholder="Adhoc Interest Rate" maxlength="2">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Grace Period(Days):</b></label> 
        <div class="col-md-8">
        <input type="text" name="grace_period" class="form-control" value="{{isset($offerData->grace_period)? $offerData->grace_period: ''}}" placeholder="Grace Period" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  INR">
        <label for="txtPassword" class="col-md-4"><b>Processing Fee:</b></label> 
        <div class="col-md-8">
        <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="processing_fee" class="form-control" value="{{isset($offerData->processing_fee)? $offerData->processing_fee: ''}}" placeholder="Processing Fee" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  INR">
        <label for="txtPassword" class="col-md-4"><b>Check Bounce Fee:</b></label> 
        <div class="col-md-8">
        <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="check_bounce_fee" class="form-control" value="{{isset($offerData->check_bounce_fee)? $offerData->check_bounce_fee: ''}}" placeholder="Check Bounce Fee" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Comment:</b></label> 
        <div class="col-md-8">
          <textarea class="form-control" name="comment" rows="3" col="3" placeholder="Comment">{{isset($offerData->comment)? $offerData->comment: ''}}</textarea>
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
  function checkValidations(){
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
    }

    if(interest_rate == ''){
        setError('input[name=interest_rate]', 'Please fill intereset rate');
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

    if(margin == ''){
        setError('input[name=margin]', 'Please fill margin');
        flag = false;
    }

    if(overdue_interest_rate == ''){
        setError('input[name=overdue_interest_rate]', 'Please fill Overdue intereset rate');
        flag = false;
    }

    if(adhoc_interest_rate == ''){
        setError('input[name=adhoc_interest_rate]', 'Please fill adhoc interest rate');
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