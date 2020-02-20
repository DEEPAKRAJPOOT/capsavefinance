@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit_offer')}}" target="_top" onsubmit="return checkSupplyValidations()">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id">
    
    <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Product</b></label> 
        <input type="text" class="form-control" value="Supply Chain" placeholder="Facility Type" maxlength="15" disabled>
      </div>
    </div>

    @php
    $currentOfferAmount = $offerData->prgm_limit_amt ?? 0;
    @endphp

    <div class="col-md-6">
      <div class="form-group INR">
        <label for="txtPassword" ><b>Limit</b></label> 
        <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" class="form-control number_format" value="{{isset($limitData->limit_amt)? number_format($limitData->limit_amt): ''}}" placeholder="Limit" maxlength="15" readonly>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Anchor</b></label> 
            <select name="anchor_id" id="anchor_id" class="form-control">
                <option value="">Select Anchor</option>
                @foreach($anchors as $key=>$anchor)
                <option value="{{$anchor->anchor_id}}">{{$anchor->comp_name}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Program</b></label> 
            <select name="prgm_id" id="program_id" class="form-control">
            </select>
        </div>
    </div>

    <div class="col-md-6">
      <div class="form-group INR">
        <label for="txtPassword"><b>Loan Offer</b></label>
        <span class="text-success limit"></span>
        <span class="float-right text-success">Balance: <i class="fa fa-inr"></i>{{(int)$limitData->limit_amt - (int)$subTotalAmount + (int)$currentOfferAmount}}</span>
        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr"></i></a>
        <input type="text" name="prgm_limit_amt" class="form-control number_format" value="{{isset($offerData->prgm_limit_amt)? number_format($offerData->prgm_limit_amt): ''}}" placeholder="Loan Offer" maxlength="15">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Interest(%)</b></label>
        <span class="float-right text-success limit"></span>
        <input type="text" name="interest_rate" class="form-control" value="{{isset($offerData->interest_rate)? $offerData->interest_rate: ''}}" placeholder="Interest Rate" maxlength="5">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Invoice Tenor(Days)</b></label> 
        <input type="text" name="tenor" class="form-control" value="{{isset($offerData->tenor)? $offerData->tenor: ''}}" placeholder="Tenor" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Old Invoice Tenor(Days)</b></label> 
        <input type="text" name="tenor_old_invoice" class="form-control" value="{{isset($offerData->tenor_old_invoice)? $offerData->tenor_old_invoice: ''}}" placeholder="Tenor for old invoice" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Margin(%)</b></label> 
        <input type="text" name="margin" class="form-control" value="{{isset($offerData->margin)? $offerData->margin: ''}}" placeholder="Margin" maxlength="5">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Overdue Interest(%)</b></label> 
        <input type="text" name="overdue_interest_rate" class="form-control" value="{{isset($offerData->overdue_interest_rate)? $offerData->overdue_interest_rate: ''}}" placeholder="Overdue Interest Rate" maxlength="5">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Adhoc Interest(%)</b></label> 
        <input type="text" name="adhoc_interest_rate" class="form-control" value="{{isset($offerData->adhoc_interest_rate)? $offerData->adhoc_interest_rate: ''}}" placeholder="Adhoc Interest Rate" maxlength="5">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Grace Period(Days)</b></label> 
        <input type="text" name="grace_period" class="form-control" value="{{isset($offerData->grace_period)? $offerData->grace_period: ''}}" placeholder="Grace Period" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
            <label for="txtPassword"><b>Processing Fee (%)</b></label>
            <input type="text" name="processing_fee" class="form-control" value="{{isset($offerData->processing_fee)? $offerData->processing_fee: ''}}" placeholder="Processing Fee" maxlength="6">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group INR">
        <label for="txtPassword"><b>Check Bounce Fee</b></label> 
        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="check_bounce_fee" class="form-control number_format" value="{{isset($offerData->check_bounce_fee)? number_format($offerData->check_bounce_fee): ''}}" placeholder="Check Bounce Fee" maxlength="6">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Comment</b></label> 
        <textarea class="form-control" name="comment" rows="3" col="3" placeholder="Comment" maxlength="250">{{isset($offerData->comment)? $offerData->comment: ''}}</textarea>
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
    var messages = {
        "get_program_balance_limit" : "{{route('ajax_get_program_balance_limit')}}",
        "token" : "{{ csrf_token() }}"  
    };
    var current_offer_amt = "{{$currentOfferAmount}}";
    var prgm_consumed_limit = 0;
    var anchorPrgms = {!! json_encode($anchorPrgms) !!};
    var anchor_id = {{$offerData->anchor_id ?? 0}};
    var program_id = {{$offerData->prgm_id ?? 0}};
    $(document).ready(function(){
        fillPrograms(anchor_id, anchorPrgms)
    })

    $('#anchor_id').on('change',function(){
        unsetError('input[name=prgm_limit_amt]');
        unsetError('input[name=interest_rate]');
        let anchor_id = $('#anchor_id').val();
        setLimit('input[name=prgm_limit_amt]', '');
        setLimit('input[name=interest_rate]', '');
        fillPrograms(anchor_id, anchorPrgms);
    });

    function fillPrograms(anchor_id, programs){
        let html = '<option value="" data-sub_limit="0" data-min_rate="0" data-max_rate="0" data-min_limit="0" data-max_limit="0">Select Program</option>';
        $.each(programs, function(i,program){
            if(program.prgm_name != null && program.anchor_id == anchor_id)
                html += '<option value="'+program.prgm_id+'" data-sub_limit="'+program.anchor_sub_limit+'" data-min_rate="'+program.min_interest_rate+'"  data-max_rate="'+program.max_interest_rate+'" data-min_limit="'+program.min_loan_size+'" data-max_limit="'+program.max_loan_size+'" '+((program.prgm_id == program_id)? "selected": "")+'>'+program.prgm_name+'</option>';
        });
        $('#program_id').html(html);
    }

    $('#program_id').on('change',function(){
        unsetError('input[name=prgm_limit_amt]');
        unsetError('input[name=interest_rate]');
        let program_min_rate = $('#program_id option:selected').data('min_rate');
        let program_max_rate = $('#program_id option:selected').data('max_rate');
        let program_min_limit = parseInt($('#program_id option:selected').data('min_limit'));
        let program_max_limit = parseInt($('#program_id option:selected').data('max_limit'));
        let program_id = $('#program_id').val();
        setLimit('input[name=prgm_limit_amt]', '');
        setLimit('input[name=interest_rate]', '');

        if(program_id == ''){
            unsetError('select[name=prgm_id]');
            setLimit('input[name=prgm_limit_amt]', '');
            setLimit('input[name=interest_rate]', '');
            return;
        }else{
            unsetError('select[name=prgm_id]');
            setLimit('input[name=prgm_limit_amt]', '(<i class="fa fa-inr" aria-hidden="true"></i> '+program_min_limit+'-<i class="fa fa-inr" aria-hidden="true"></i> '+program_max_limit+')');
            setLimit('input[name=interest_rate]', '('+program_min_rate+'%-'+program_max_rate+'%)');
        }
        let token = "{{ csrf_token() }}";
        $('.isloader').show();
        $.ajax({
            'url':messages.get_program_balance_limit,
            'type':"POST",
            'data':{"_token" : messages.token, "program_id" : program_id},
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success:function(res){
                res = JSON.parse(res);
                prgm_consumed_limit = parseInt(res) - current_offer_amt;
                $('.isloader').hide();
            }
        })
    });

  function checkSupplyValidations(){
    var limitObj={
        'prgm_min_rate':$('#program_id option:selected').data('min_rate'),
        'prgm_max_rate':$('#program_id option:selected').data('max_rate'),
        'prgm_min_limit':$('#program_id option:selected').data('min_limit'),
        'prgm_max_limit':$('#program_id option:selected').data('max_limit'),
        'limit_balance_amt':"{{(int)$limitData->limit_amt - (int)$subTotalAmount + (int)$currentOfferAmount}}",
        'prgm_balance_limit':$('#program_id option:selected').data('sub_limit') - prgm_consumed_limit,
    };
    
    unsetError('select[name=anchor_id]');
    unsetError('select[name=prgm_id]');
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
    let anchor_id = $('select[name=anchor_id]').val();
    let prgm_id = $('select[name=prgm_id]').val();
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

    if(anchor_id == ''){
        setError('select[name=anchor_id]', 'Please select Anchor');
        flag = false;
    }

    if(prgm_id == ''){
        setError('select[name=prgm_id]', 'Please select Program');
        flag = false;
    }

    if(prgm_limit_amt.length == 0 || parseInt(prgm_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=prgm_limit_amt]', 'Please fill loan offer amount');
        flag = false;
    }else if(anchor_id !='' && prgm_id != ''){
        if((parseInt(prgm_limit_amt.replace(/,/g, '')) < parseInt(limitObj.prgm_min_limit)) ||(parseInt(prgm_limit_amt.replace(/,/g, '')) > parseInt(limitObj.prgm_max_limit))){
            setError('input[name=prgm_limit_amt]', 'Limit amount should be ('+parseInt(limitObj.prgm_min_limit)+'-'+parseInt(limitObj.prgm_max_limit)+') program range');
            flag = false;
        }else if(parseInt(prgm_limit_amt.replace(/,/g, '')) > parseInt(limitObj.prgm_balance_limit)){
            setError('input[name=prgm_limit_amt]', 'Limit amount should be less than ('+limitObj.prgm_balance_limit+') program balance limit');
            flag = false;
        }else{
            //TAKE REST
        }
    }

    if(interest_rate == '' || isNaN(interest_rate)){
        setError('input[name=interest_rate]', 'Please fill intereset rate');
        flag = false;
    }else if(anchor_id !='' && prgm_id != ''){
        if(parseFloat(interest_rate) > 100){
            setError('input[name=interest_rate]', 'Please fill correct intereset rate');
            flag = false;
        }else if((parseFloat(interest_rate) < parseFloat(limitObj.prgm_min_rate)) || parseFloat(interest_rate) > parseFloat(limitObj.prgm_min_rate)){
            setError('input[name=interest_rate]', 'Interest rate should be ('+limitObj.prgm_min_rate+'% - '+limitObj.prgm_max_rate+'%) range');
            flag = false;
        }else{
            //TAKE REST
        }
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
    }else if(parseFloat(margin) > 100){
        setError('input[name=margin]', 'Please fill correct margin rate');
        flag = false;
    }

    if(overdue_interest_rate == '' || isNaN(overdue_interest_rate)){
        setError('input[name=overdue_interest_rate]', 'Please fill Overdue intereset rate');
        flag = false;
    }else if(parseFloat(overdue_interest_rate) > 100){
        setError('input[name=overdue_interest_rate]', 'Please fill correct overdue interest rate');
        flag = false;
    }

    if(adhoc_interest_rate == '' || isNaN(adhoc_interest_rate)){
        setError('input[name=adhoc_interest_rate]', 'Please fill adhoc interest rate');
        flag = false;
    }else if(parseFloat(adhoc_interest_rate) > 100){
        setError('input[name=adhoc_interest_rate]', 'Please fill correct adhoc interest rate');
        flag = false;
    }

    if(grace_period == ''){
        setError('input[name=grace_period]', 'Please fill grace period');
        flag = false;
    }

    if(processing_fee == '' || isNaN(processing_fee)){
        setError('input[name=processing_fee]', 'Please fill processing fee');
        flag = false;
    }else if(parseFloat(processing_fee) > 100){
        setError('input[name=processing_fee]', 'Processing fee can not be greater than 100 percent');
        flag = false;
    }

    if(check_bounce_fee == ''){
        setError('input[name=check_bounce_fee]', 'Please fill check bounce fee');
        flag = false;
    }

    if(flag){
        return false;
    }else{
        return false;
    }
  }
</script>
@endsection