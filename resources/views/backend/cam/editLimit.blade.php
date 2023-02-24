@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_total_limit_amnt')}}" target="_top">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    {{-- <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id"> --}}
    
    <div class="row">
    {{-- <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Product Type:</b></label> 
        <input type="text" name="prgm_limit_amt" class="form-control" value="{{isset($currentPrgmLimitData->product)? $currentPrgmLimitData->product->product_name : ''}}" disabled>
      </div>
    </div>
    
    @php
    $currentPrgmLimit = isset($currentPrgmLimitData->limit_amt)? $currentPrgmLimitData->limit_amt: 0;
    $balanceLimit = $totalLimit - $totalPrgmLimit + $currentPrgmLimit;
    @endphp --}}
    
    <div class="col-md-6">
      {{-- <div class="form-group INR">
        <label for="txtPassword"><b>Total Credit Assessed</b></label>
        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="total_limit_amt" class="form-control number_format" value="{{isset($currentPrgmLimitData->limit_amt)? number_format($currentPrgmLimitData->limit_amt): ''}}" placeholder="Product Limit amount" maxlength="15">
      </div> --}}
      <div class="form-group INR">
        <label>Total Credit Assessed</label>
        <div class="relative">
        <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" class="form-control number_format" id="tot_limit_amt" name="tot_limit_amt" value="{{ isset($limitData->tot_limit_amt)? number_format($limitData->tot_limit_amt): '' }}" maxlength="15" placeholder="Total Exposure">
        </div>
    </div>
    </div>

    </div>
    <div class="row">
    @can('update_total_limit_amnt')
      <div class="col-md-12">
        <button type="submit" id="submit" class="btn btn-success btn-sm float-right">Submit</button>
      </div>
      @endcan
    </div>   
  </form>
  
@endsection

@section('jscript')
<script>
//   function checkLimitValidation(){
//     let prgmLimitTotal = "{{$prgmLimitTotal}}";
//     let limitData = "{{$limitData}}";
//     console.log(prgmLimitTotal);
//     unsetError('input[name=tot_limit_amt]');

//     let flag = true;
//     let limit_amt = $('input[name=tot_limit_amt]').val().trim();

//     if(tot_limit_amt.length == 0 || parseInt(tot_limit_amt) == 0){
//         setError('input[name=limit_amt]', 'Please fill Product Limit amount');
//         flag = false;
//     }else if(parseInt(tot_limit_amt) < parseInt(prgmLimitTotal.replace(/,/g, ''))){
//         setError('input[name=tot_limit_amt]', 'Total Credit Assessed should be greater than total prgm limit');
//         flag = false;
//     }

//     if(flag){
//         return true;
//     }else{
//         return false;
//     }
//   }
$(document).on('click', '#submit', function (e) {
    let prgmLimitTotal = "{{$prgmLimitTotal}}";
    let totalCredit = $('input[name=tot_limit_amt]').val().trim();
    let flag = true;
    totalCreditAssessed = totalCredit.replace(/,/g, '');
    if(parseInt(totalCreditAssessed) < parseInt(prgmLimitTotal)){
        setError('input[name=tot_limit_amt]', 'Total Credit Assessed should be greater than total prgm limit');
        flag = false;
    }

    if(flag){
        return true;
    }else{
        return false;
    }

});
</script>
@endsection