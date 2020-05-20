@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">
   
    <table class="table table-striped cell-border no-footer"  cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
        <tbody> 
            <tr>
                <td><b>Principal Amount:</b></td>
                <td>{{ number_format((float)$disbursal->invoice->invoice_approve_amount, 2, '.', '') }}</td>
                <td><b>Disburse Amount:</b></td>
                <td>{{ $disbursal->disburse_amt}}</td>
            </tr>
            <tr>
                <td><b>Interest Rate:</b></td>
                <td>@if($currentIntRate>0) {{number_format($currentIntRate, 2, '.', '')}}% @endif</td>
                <td><b>Overdue Interest Rate:</b></td>
                <td>@if($disbursal->overdue_interest_rate>0){{$disbursal->overdue_interest_rate}}% @endif</td>
            </tr>
            <tr>
                <td><b>Tenor(in Days):</b></td>
                <td>{{$disbursal->tenor_days}}</td>
                <td><b>Margin(%):</b></td>
                <td>@if($disbursal->margin>0){{number_format($disbursal->margin, 2, '.', '')}}% @endif</td>
            </tr>
            <tr>
                <td><b>Funded Date:</b></td>
                <td>{{($disbursal->disbursal)? Carbon\Carbon::parse($disbursal->disbursal->funded_date)->format('d-m-Y'): ''}}</td>
                <td><b>Payment Due Date:</b></td>
                <td>{{($disbursal->payment_due_date)? Carbon\Carbon::parse($disbursal->payment_due_date)->format('d-m-Y'): ''}}</td>
            </tr>
            <tr>
                <td><b>Accrued Interest till date:</b></td>
                @foreach($disbursal->accruedInterest as $item)
                <td>{{ Carbon\Carbon::parse($item->interest_date)->format('d-m-Y')}}</td>
                @break
                @endforeach

                <td><b>Grace Period:</b></td>
                <td>@if($disbursal->grace_period>0) {{$disbursal->grace_period}} @if($disbursal->grace_period>1) Days @else Day @endif @endif</td>
            </tr>
            <tr>
                <td><b>Penal days:</b></td>
                <td>{{$disbursal->accruedInterestNotNull->count() }}</td>
                <td><b>Penal Amount:</b></td>
                <td>{{number_format((float)$disbursal->accruedInterest->sum('accrued_interest'), 2, '.', '')  }}</td>
            </tr>
           {{--<tr>
                <td><b>Outstanding Amount:</b></td>
           
                <td>{{ (($disbursal->disburse_amount + $disbursal->total_interest) - $disbursal->settlement_amount) }}</td>
                <td><b>Total Outstanding Amount:</b></td>
                <td>{{ ($disbursal->disburse_amount + $disbursal->total_interest - $disbursal->settlement_amount + $disbursal->penalty_amount) }}</td>
            </tr> --}}
        </tbody>
    </table>


<table id="disbursalList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
        <thead style="height: 10px !important; overflow: scroll;">
            <tr role="row">
                <th>Date</th>
                <th>Principal Amount</th>
                <th>Interest/Overdue Rate</th>
                {{--<th>Overdue Interest Rate</th> --}}
                <th>Accrued Interest</th>                    
            </tr>
        </thead>
        <tbody>
            @if ($disbursal->accruedInterest->count() > 0)
                @php $total_accrued_interest = 0;  @endphp
                @foreach($disbursal->accruedInterest as $item)
                    <tr role="row" @if($item->overdue_interest_rate) style="background-color: #f57d7d3d"@endif>
                        <td>{{  Carbon\Carbon::parse($item->interest_date)->format('d-m-Y') }}</td>
                        <td class="text-right">{{ number_format((float)$item->principal_amount, 2, '.', '') }}</td>
                        <td class="text-right">{{ ($item->interest_rate != NULL)? number_format($item->interest_rate, 2, '.', '').'%': number_format($item->overdue_interest_rate, 2, '.', '').'%' }}</td>
                        {{-- <td class="text-right">{{ $item->overdue_interest_rate }}</td> --}}
                        <td class="text-right">{{ number_format((float)$item->accrued_interest, 3, '.', '') }}</td>                    
                    </tr>   
                    {{-- @php $total_accrued_interest = $total_accrued_interest + $item->accrued_interest;  @endphp --}}
                @endforeach
                {{-- <tr role="row">
                    <td colspan="2" class="text-center"><strong>TOTAL ACCRUED INTEREST</strong></td>
                    <td class="text-right"><strong>{{ $total_accrued_interest }}</strong></td>                    
                </tr>                  --}}
            @else
            <tr role="row"><td colspan="3">No Data Found</td></tr>
            @endif
        </tbody>
</table>                    
</div>
@endsection

@section('jscript')
@endsection
