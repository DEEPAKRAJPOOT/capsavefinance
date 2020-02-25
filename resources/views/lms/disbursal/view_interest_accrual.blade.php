@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">
<table id="disbursalList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
        <thead>
            <tr role="row">
                <th>Date</th>
                <th>Principal Amount</th>
                <th>Interest Rate</th>
                <th>Overdue Interest Rate</th>
                <th>Accrued Interest</th>                    
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                @php $total_accrued_interest = 0;  @endphp
                @foreach($data as $item)
                    <tr role="row">
                        <td>{{ $item->interest_date }}</td>
                        <td>{{ $item->principal_amount }}</td>
                        <td>{{ $item->interest_rate }}</td>
                        <td>{{ $item->overdue_interest_rate }}</td>
                        <td>{{ $item->accrued_interest }}</td>                    
                    </tr>   
                    @php $total_accrued_interest = $total_accrued_interest + $item->accrued_interest;  @endphp
                @endforeach
                <tr role="row">
                    <td colspan="4"><strong>TOTAL ACCRUED INTEREST</strong></td>
                    <td><strong>{{ $total_accrued_interest }}</strong></td>                    
                </tr>                 
            @else
            <tr role="row"><td colspan="5">No Data Found</td></tr>
            @endif
        </tbody>
</table>                    
</div>
@endsection

@section('jscript')
@endsection
