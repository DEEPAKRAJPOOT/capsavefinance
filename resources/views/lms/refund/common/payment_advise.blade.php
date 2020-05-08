<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
    <table id="interestRefundList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
        <thead>
            <tr role="row">
                <th>Trans Date</th>
                <th>Value Date</th>
                <th>Tran Type</th>
                <th>Invoice No</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        @php 
            $balanceAmount = $repayment->amount;
        @endphp
            <tr>
                <td>{{date('d-m-Y',strtotime($repayment->date_of_payment))}}</td>
                <td>{{date('d-M-Y',strtotime($repayment->created_at))}}</td>
                <td>{{ $repayment->paymentname }}</td>
                <td></td>
                <td></td>
                <td>{{ number_format($repayment->amount,2) }}</td>
                <td>{{ number_format($repayment->amount,2) }}</td>
            </tr>
            @foreach($repaymentTrails as $repay)
                @php 
                    if($repay->entry_type=='1')
                        $balanceAmount -= $repay->amount;
                    elseif($repay->entry_type=='0')
                        $balanceAmount += $repay->amount;
                @endphp
                <tr role="row" >
                    <td> {{date('d-m-Y',strtotime($repay->trans_date))}}</td>
                    <td> {{date('d-m-Y',strtotime($repay->created_at))}}</td>
                    <td> {{$repay->TransName}}</td>
                    <td>
                        @if($repay->invoice_disbursed_id && $repay->invoiceDisbursed->invoice_id)
                            {{$repay->invoiceDisbursed->invoice->invoice_no}}
                        @endif 
                    </td>
                    <td>
                        @if($repay->entry_type=='1')
                            {{ number_format($repay->amount,2) }}
                        @endif
                    </td>
                    <td>
                        @if($repay->entry_type=='0')
                            {{ number_format($repay->amount,2) }}
                        @endif
                    </td>
                    <td>
                        {{ number_format($balanceAmount,2) }}
                    </td>
                </tr>
            @endforeach
            <tr role="row" >
                <td colspan="8" style="min-height: 15px"></td>
            </tr>
            <tr role="row" >
                <td colspan="6">Total Factored</td>
                <td>{{ number_format($repayment->amount,2) }}</td>
            </tr>
            <tr role="row">
                <td colspan="6">Non Factored</td>
                <td>{{ number_format($nonFactoredAmount,2) }}</td>
            </tr>
            <tr role="row" >
                <td colspan="6">Overdue Interest</td>
                <td>{{ number_format($interestOverdue,2) }}</td>
            </tr>
            <tr role="row" >
                <td colspan="6"> Interest Refund</td>
                <td>{{ number_format($interestRefund,2) }}</td>
            </tr>
            <tr role="row" >
                <td colspan="6">Margin Released</td>
                <td>{{ number_format($marginTotal,2) }}</td>
            </tr>
            <tr role="row" >
                <td colspan="6" style="font-weight:bold; font-size: 15px"><b>Total Refundable Amount</b></td>
                <td>{{  number_format($refundableAmount,2) }}</td>
            </tr>
    </table>
</div>