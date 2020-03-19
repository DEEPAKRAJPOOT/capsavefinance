@extends('layouts.backend.admin_popup_layout')
@section('content')
<input type="hidden" value="{{ $userId }}" name="user_id" id="user_id">  

<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive" id="fi_list">
            <table id="fiList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                <thead>
                    <tr role="row">
                        <th></th>
                        <th>App ID</th>
                        <th>Invoice No</th>
                        <th>Invoice Date</th>
                        <th>Invoice Due Date</th>
                        <th>Invoice Amt.</th>
                        <th>Margin(%) </th>
                        <th>Disburse Amt.</th>
                        <th>Actual Funded Amt.</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
				@if($userIvoices->count() != 0)
					@foreach($userIvoices as $invoice)
						@php 
							$margin = (isset($invoice->app->acceptedOffer->margin))	? $invoice->app->acceptedOffer->margin : 0;
						@endphp

	                    <tr role="row" class="odd">
							<td>
							@if($status == 0)
							<input type="checkbox" class="invoice_id" value="{{ $invoice->invoice_id }}">
							@endif
							</td>
							<td> {{ \Helpers::formatIdWithPrefix($invoice->app->app_id, 'APP') }}</td>
							<td> {{ $invoice->invoice_no }}</td>
							<td> {{ $invoice->invoice_date }}</td>
							<td> {{ $invoice->invoice_due_date }}</td>
							<td> <i class="fa fa-inr"></i> {{ number_format($invoice->invoice_approve_amount) }} </td>
							<td> <i class="fa fa-inr"></i>{{ $margin }} </td>
							<td> <i class="fa fa-inr"></i> {{ number_format($invoice->invoice_approve_amount - (($invoice->invoice_approve_amount*$margin)/100)) }} </td>
							<td><i class="fa fa-inr"></i>
							@php
								$interest = 0;
								$now = strtotime($invoice->invoice_due_date); // or your date as well
								$your_date = strtotime($invoice->invoice_date);
								$datediff = abs($now - $your_date);
								$tenor = round($datediff / (60 * 60 * 24));
								$fundedAmount = $invoice->invoice_approve_amount - (($invoice->invoice_approve_amount*$invoice->program_offer->margin)/100);
				    			$tInterest = $fundedAmount * $tenor * (($invoice->program_offer->interest_rate/100) / 360) ;     
				    			if($invoice->program_offer->payment_frequency == 1 || empty($invoice->program_offer->payment_frequency)) {
						            $interest = $tInterest;
						        }           
								$disburseAmount = round($fundedAmount - $interest, 2);
							@endphp

							{{ number_format($disburseAmount) }}
							</td>
							<td> <span class="badge badge-primary">{{ ($invoice->program_offer->payment_frequency == 1) ? 'UPFRONT' : 'MONTHLY'  }}</span></td>
							<td> <span class="badge badge-warning">{{ $invoice->mstStatus->status_name }}</span></td>
						</tr>
	                @endforeach
				@else 
				 <thead class="thead-primary">
					<tr>
						<th class="text-left" colspan="4" width="10%">No invoice found.</th>
					</tr>
				</thead>
				@endif
                </tbody>
            </table>
        </div>
    </div>
</div>
 
@endsection

@section('jscript')
<script>
$(document).ready(function(){
	$('.invoice_id').each(function() {
		let parent_inv_ids = parent.$('#invoice_ids').val();
		let allInvIds = parent_inv_ids.split(',');
		let curr_val = $(this).val();
		let is_checked = jQuery.inArray(curr_val, allInvIds) != -1;
		$(this).prop('checked', is_checked);
	})

	$('.invoice_id').on('click', function() {
		let current_id = $(this).val();
		if($(this).is(':checked')){
			let parent_inv_ids = parent.$('#invoice_ids').val().trim();
			let allInvIds = parent_inv_ids.split(',');
			if(!parent_inv_ids.length){
				allInvIds = [];
			}
			if(allInvIds.length != 0){
				allInvIds.push(current_id);
				allInvIds.join();
				parent.$('#invoice_ids').val(allInvIds.join());
			}else{
				parent.$('#invoice_ids').val(current_id);
			}
			
		}else{
			let parent_inv_ids = parent.$('#invoice_ids').val().trim();
			let allInvIds = parent_inv_ids.split(',');
			if(!parent_inv_ids.length){
				allInvIds = [];
			}
			allInvIds = allInvIds.filter(e => e !== current_id);
			parent.$('#invoice_ids').val(allInvIds.join());
		}
	});
});
</script>
@endsection