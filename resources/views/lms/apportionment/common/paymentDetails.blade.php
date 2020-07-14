<div class="row">
    <div class="col-md-4 Lh-3"><b>Payment Date:</b> {{ Carbon\Carbon::parse($payment['date_of_payment'])->format('d-m-Y') }}</div>
    @if($payment['transactionno'])<div class="col-md-4 Lh-3"><b>Transaction No:</b> {{ $payment['transactionno'] }}</div>@endif
    <div class="col-md-4 Lh-3"><b>Mode of Payment:</b> {{$payment['paymentmode']  }}</div>
    <div class="col-md-4 Lh-3"><b>Repayment Amount:</b> ₹  {{ number_format($payment['amount'],2) }}</div>
    <div class="col-md-4 Lh-3"><b>Unapplied Amount:</b><span id='unappliledAmt'>@isset($unAppliedAmt)₹ {{ number_format($unAppliedAmt,2) }}@endisset</span></div>
    {{-- <div class="col-md-4 Lh-3"><b>Transaction Date:</b> {{ Carbon\Carbon::now()->format('d-m-Y') }}</div> --}}
</div>