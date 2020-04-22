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
<form action="{{route('apport_reversal_save', ['trans_id' => $TransDetail->trans_id, 'payment_id' => $payment_id])}}" target="_top" method="post">
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
    <div class="form-inline">
        <div class="col">
            <label for="chrg_name"> Transaction Amount</label>
            <input type="text" class="form-control" value="{{ $TransDetail->getOutstandingAttribute()}}" name="amount" id="txn_amount">
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
    var totalTxnAmt = '{{ $TransDetail->amount}}';
    $(document).on('click', '#submit', function() {
       var enteredAmt = parseFloat($('#txn_amount').val());
       if (enteredAmt > totalTxnAmt) {
         $('#amt_error').html('Amount can not be more than ' + totalTxnAmt);
         return false;
       }
       if (!$('#comment').val()) {
         $('#comment_error').html('Comment can not empty');
         return false;
       }
    })
</script>
@endsection
