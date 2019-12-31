@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit_offer')}}" target="_top">
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
        <input type="text" name="prgm_limit_amt" class="form-control" value="{{$offerData->prgm_limit_amt}}" placeholder="Loan Offer " maxlength="15">
        <span class="s_value"><i class="fa fa-inr"></i>10,00,000 - <i class="fa fa-inr"></i>50,00,000</span>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Interest(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="interest_rate" class="form-control" value="{{$offerData->interest_rate}}" placeholder="Interest Rate ">
        <span class="s_value">10%-12%</span>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Invoice Tenor(Days):</b></label> 
        <div class="col-md-8">
        <input type="text" name="tenor" class="form-control" value="{{$offerData->tenor}}" placeholder="Tenor">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Old Invoice Tenor(Days):</b></label> 
        <div class="col-md-8">
        <input type="text" name="tenor_old_invoice" class="form-control" value="{{$offerData->tenor_old_invoice}}" placeholder="Tenor for old invoice">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Margin(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="margin" class="form-control" value="{{$offerData->margin}}" placeholder="Margin">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Overdue Interest(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="overdue_interest_rate" class="form-control" value="{{$offerData->overdue_interest_rate}}" placeholder="Overdue Interest Rate">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Adhoc Interest(%):</b></label> 
        <div class="col-md-8">
        <input type="text" name="adhoc_interest_rate" class="form-control" value="{{$offerData->adhoc_interest_rate}}" placeholder="Adhoc Interest Rate">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Grace Period(Days):</b></label> 
        <div class="col-md-8">
        <input type="text" name="grace_period" class="form-control" value="{{$offerData->grace_period}}" placeholder="Grace Period">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  INR">
        <label for="txtPassword" class="col-md-4"><b>Processing Fee:</b></label> 
        <div class="col-md-8">
        <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="processing_fee" class="form-control" value="{{$offerData->processing_fee}}" placeholder="Processing Fee">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  INR">
        <label for="txtPassword" class="col-md-4"><b>Check Bounce Fee:</b></label> 
        <div class="col-md-8">
        <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="check_bounce_fee" class="form-control" value="{{$offerData->check_bounce_fee}}" placeholder="Check Bounce Fee">
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row  ">
        <label for="txtPassword" class="col-md-4"><b>Comment:</b></label> 
        <div class="col-md-8">
          <textarea class="form-control" name="comment" rows="3" col="3" placeholder="Comment">{{$offerData->comment}}</textarea>
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
$(document).ready(function(){
  $(parent.$('.address_id:checked')).each(function(i,ele){
    let current_id = $(ele).val();
    let org_ids = $('#address_ids').val();
    let address_ids = org_ids+'#'+current_id;
    $('#address_ids').val(address_ids);
  });
});
</script>
@endsection