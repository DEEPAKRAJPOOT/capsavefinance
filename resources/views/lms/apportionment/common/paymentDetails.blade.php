<div class="row">
    <div class="col-md-4 Lh-3"><b>Transaction Date:</b> {{ $payment['date_of_payment'] }}</div>
    <div class="col-md-4 Lh-3"><b>Transaction No:</b> {{ $payment['transactionno'] }}</div>
    <div class="col-md-4 Lh-3"><b>Mode of Payment:</b> {{$payment['paymentmode']  }}</div>
    <div class="col-md-6 Lh-3"><b>Repayment Amount:</b> {{ $payment['amount'] }}</div>
    <div class="col-md-6 Lh-3"><b>Unapplied Amount:</b><span id='unappliledAmt'></span></div>
</div>