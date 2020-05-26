@extends('layouts.backend.admin_popup_layout')
@section('additional_css')
<style>
    label{
        font-size: 12px;
        margin: 0;
    }
    .form-control{
        padding: 5px;
        font-size: 12px;
    }
    .error{
        color: #ff0000;
        font-size: 12px;
    }
    .alert{
        padding: .40rem .40rem;
        font-size: 14px;
    }
</style>
@endsection
@section('content')
<form action="{{route('apport_waiveoff_save', ['trans_id' => $TransDetail->trans_id, 'payment_id' => $payment_id, 'sanctionPageView'=>$sanctionPageView])}}" target="_top" method="post">
    @csrf
    <div class="form-inline">
        <div class="col">
            <label for="chrg_name">Trans Type</label>
            <input type="text" readonly="readonly"  class="form-control" value="{{ $TransDetail->transType->trans_name ?? ''}}">
        </div>
        <div class="col">
            <label for="chrg_name"> Customer Name</label>
            <input type="text" readonly="readonly"  class="form-control" value="{{ $TransDetail->user->f_name . ' ' . $TransDetail->user->l_name }}">
        </div>
    </div>
    @if($TransDetail->transType->chrg_master_id == 0)
    <div class="form-inline">
        <div class="col">
            <label for="chrg_name">Margin</label>
            <input type="text" readonly="readonly"  class="form-control" value="{{ $TransDetail->disburse->margin ?? 0}}">
        </div>
        <div class="col">
            <label for="chrg_name">Interest Rate</label>
            <input type="text" readonly="readonly"  class="form-control" value="{{ $TransDetail->disburse->interest_rate ?? 0}}">
        </div>
    </div>
    <div class="form-inline">    
        <div class="col">
            <label for="chrg_name">OverDue Intrest Rate</label>
            <input type="text" readonly="readonly"  class="form-control" value="{{ $TransDetail->disburse->overdue_interest_rate ?? 0}}">
        </div>
        <div class="col">
            <label for="chrg_name">Total Interest</label>
            <input type="text" readonly="readonly"  class="form-control" value="{{ $TransDetail->disburse->total_interest ?? 0}}">
        </div>
    </div>
    @endif

    @php
        $waivedOffAmtTotal = $TransDetail->outstanding;
        $waivedOffAmt = round((($TransDetail->outstanding*100)/($gst+100)),2);
        $waivedOffGst = round((($waivedOffAmt*$gst)/100),2);

    @endphp
    @if($TransDetail->gst == 1)
        <div class="form-inline">
            <div class="col">
                <label for="chrg_name"> Amount to be Waived-Off</label>
                <input type="text" class="form-control" value="{{ $waivedOffAmt }}" name="waiveoff_amount" id="waiveoff_amount">
                <span id="waiveoff_amt_error" class="error"></span>
            </div>      
        </div>
        <div class="form-inline">
            <div class="col">
                <label for="chrg_name"> GST ({{ $gst }}%)</label>
                <input type="text" class="form-control" value="{{ $waivedOffGst }}" name="waiveoff_gst" id="waiveoff_gst" readonly="true">
                <span id="waiveoff_gst_error" class="error"></span>
            </div>      
        </div>
    @endif
    <div class="form-inline">
        <div class="col">
            <label for="chrg_name"> @if($TransDetail->gst == 1)Amount that will be Waived-Off @else Amount to be waived-Off @endif</label>
            <input type="text" class="form-control" value="{{ $waivedOffAmtTotal }}" name="amount" id="txn_amount" @if($TransDetail->gst == 1)readonly="true"@endif>
            <span id="amt_error" class="error"></span>
        </div>      
    </div>
    <div class="form-inline">
        <div class="col">
            <label for="chrg_name"> Transaction Comment</label>
            <textarea class="form-control" maxlength="250" name="comment" id="comment"></textarea>
            <span id="comment_error" class="error"></span>
        </div>      
    </div>
    <div class="form-group text-right">
        <button type="submit" id="submit" class="btn btn-success btn-sm mt10">Submit</button>     
    </div>
</form>    
@endsection
@section('jscript')
<script>
    var waivedOffAmt = parseFloat('{{ $TransDetail->outstanding }}');
    var gst = parseFloat('{{ $gst }}');
    $(document).on('propertychange change click keyup input paste','#waiveoff_amount',function(){
        var waiceOffAmount = parseFloat($(this).val());
        if(isNaN(waiceOffAmount)){ waiceOffAmount = 0; }
        var waivedOffGstAmount = (waiceOffAmount*gst)/100; 
        var waivedOffTotal = waiceOffAmount+waivedOffGstAmount;  
        $("#waiveoff_gst").val(waivedOffGstAmount.toFixed(2));
        $("#txn_amount").val(waivedOffTotal.toFixed(2));
    });

    $(document).on('click', '#submit', function() {
       var enteredAmt = parseFloat($('#txn_amount').val());
       if (enteredAmt > waivedOffAmt) {
         $('#amt_error').html('Amount can not be more than ' + waivedOffAmt);
         return false;
       }
       if (!$('#comment').val()) {
         $('#comment_error').html('Comment can not empty');
         return false;
       }
    });
</script>
@endsection
