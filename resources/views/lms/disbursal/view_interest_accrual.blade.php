@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">
   
    <table class="table table-striped cell-border no-footer"  cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
        <tbody> 
            <tr>
                <td><b>Principal Amount</b></td>
                <td>{{ $disbursal->principal_amount}}</td>
                <td><b>Disburse Amount</b></td>
                <td>{{ $disbursal->disburse_amount}}</td>
            </tr>
            <tr>
                <td><b>Interest Rate:</b></td>
                <td>@if($disbursal->interest_rate>0) {{$disbursal->interest_rate}}% @endif</td>
                <td><b>Overdue Interest Rate:</b></td>
                <td>@if($disbursal->overdue_interest_rate>0){{$disbursal->overdue_interest_rate}}% @endif</td>
            </tr>
            <tr>
                <td><b>Penal days:</b></td>
                <td>{{$disbursal->penal_days}}</td>
                <td><b>Penal Amount:</b></td>
                <td>{{$disbursal->penalty_amount}}</td>
            </tr>
            <tr>
                <td><b>Accrued Interest till date</b></td>
                <td>{{$disbursal->accured_interest}}</td>
                <td><p>Grace Period</p></td>
                <td>@if($disbursal->grace_period>0) {{$disbursal->grace_period}} @if($disbursal->grace_period>1) Days @else Day @endif @endif</td>
            </tr>
            <tr>
                <td><b>Outstanding Amount:</b></td>
           
                <td>{{ (($disbursal->disburse_amount + $disbursal->total_interest) - $disbursal->settlement_amount) }}</td>
                <td><b>Total Outstanding Amount:</b></td>
                <td>{{ ($disbursal->disburse_amount + $disbursal->total_interest - $disbursal->settlement_amount + $disbursal->penalty_amount) }}</td>
            </tr>
        </tbody>
    </table>


<table id="disbursalList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
        <thead style="height: 10px !important; overflow: scroll;">
            <tr role="row">
                <th>Date</th>
                <th>Principal Amount</th>
                {{-- <th>Interest Rate</th>
                <th>Overdue Interest Rate</th> --}}
                <th>Accrued Interest</th>                    
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                @php $total_accrued_interest = 0;  @endphp
                @foreach($data as $item)
                    <tr role="row" @if($item->overdue_interest_rate) style="background-color: #f57d7d3d"@endif>
                        <td>{{ $item->interest_date }}</td>
                        <td class="text-right">{{ $item->principal_amount }}</td>
                        {{-- <td class="text-right">{{ $item->interest_rate }}</td>
                        <td class="text-right">{{ $item->overdue_interest_rate }}</td> --}}
                        <td class="text-right">{{ $item->accrued_interest }}</td>                    
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
