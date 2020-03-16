@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit')}}" target="_top" onsubmit="return checkLimitValidation()">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id">
    
    <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Product Type:</b></label> 
        <input type="text" name="prgm_limit_amt" class="form-control" value="{{isset($currentPrgmLimitData->product)? $currentPrgmLimitData->product->product_name : ''}}" disabled>
      </div>
    </div>
    
    @php
    $currentPrgmLimit = isset($currentPrgmLimitData->limit_amt)? $currentPrgmLimitData->limit_amt: 0;
    $balanceLimit = $totalLimit - $totalPrgmLimit + $currentPrgmLimit;
    @endphp
    
    <div class="col-md-6">
      <div class="form-group INR">
        <label for="txtPassword"><b>Product Limit</b></label>
        <span class="float-right text-success">Balance: <i class="fa fa-inr"></i>{{($balanceLimit > 0)? $balanceLimit: 0}}</span>
        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="limit_amt" class="form-control number_format" value="{{isset($currentPrgmLimitData->limit_amt)? number_format($currentPrgmLimitData->limit_amt): ''}}" placeholder="Product Limit amount" maxlength="15">
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
  function checkLimitValidation(){
    let balance_limit = "{{$balanceLimit}}";
    let totalOfferedAmount = "{{$totalOfferedAmount}}";

    unsetError('input[name=limit_amt]');

    let flag = true;
    let limit_amt = $('input[name=limit_amt]').val().trim();

    if(limit_amt.length == 0 || parseInt(limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=limit_amt]', 'Please fill Product Limit amount');
        flag = false;
    }else if(balance_limit == 0){
        setError('input[name=limit_amt]', 'Your Product limit has been expired');
        flag = false;
    }else if(parseInt(limit_amt.replace(/,/g, '')) > parseInt(balance_limit)){
        setError('input[name=limit_amt]', 'Product Limit amount can not exceed from balance amount');
        flag = false;
    }else if(parseInt(limit_amt.replace(/,/g, '')) < parseInt(totalOfferedAmount)){
        setError('input[name=limit_amt]', 'Product Limit amount can not be less than applied offer amount');
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