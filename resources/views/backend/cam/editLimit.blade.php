@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_total_limit_amnt')}}" target="_top">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    
    <div class="row">
    <div class="col-md-6">
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
$(document).on('submit', function (e) {
    let prgmLimitTotal = "{{$prgmLimitTotal}}";
    let totalCredit = $('input[name=tot_limit_amt]').val().trim();
    totalCreditAssessed = totalCredit.replace(/,/g, '');
    if(parseInt(totalCreditAssessed) < parseInt(prgmLimitTotal)){
        setError('input[name=tot_limit_amt]', 'Total Credit Assessed should be greater than Total Program Limit.');
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