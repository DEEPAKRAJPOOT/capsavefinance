@extends('layouts.backend.admin_popup_layout')
@section('content')

		@if($userIvoices->count() != 0)
		<div class="row">
			<div id="collapseOne" class="card-body bdr pt-2 pb-2 collapse show" data-parent="#accordion" style="">
				@foreach($userIvoices as $invoice)

				@php 
					$margin = (isset($invoice->app->acceptedOffer->margin))	? $invoice->app->acceptedOffer->margin : 0;
				@endphp
				<ul class=" p-0 m-0 d-flex justify-content-between">
					<li>
					@if($status == 0)
					<input type="checkbox" class="invoice_id" value="{{ $invoice->invoice_id }}">
					@endif
					</li>
					<li>Invoice No. <br>  <b>{{ $invoice->invoice_no }}</b></li>
					<li>Invoice Date <br> <b>{{ $invoice->invoice_date }}</b></li>
					<li>Invoice Due Date <br> <b>{{ $invoice->invoice_due_date }}</b></li>
					<li>Invoice Amt. <br> <i class="fa fa-inr"></i><b>{{ $invoice->invoice_approve_amount }}</b></li>
					<li>Margin(%). <br> <i class="fa fa-inr"></i><b>{{ $margin }}</b></li>
					<li>Disburse Amt. <br> <i class="fa fa-inr"></i><b>
					{{ $invoice->invoice_approve_amount - (($invoice->invoice_approve_amount*$margin)/100) }}
					</b></li>
					<li>Actual Funded Amt. <br> <i class="fa fa-inr"></i><b>
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

					{{ $disburseAmount }}
					</b></li>
					<li>Status  <br> <span class="badge badge-warning">{{ $invoice->mstStatus->status_name }}</span></li>
				</ul>
				<hr>
				@endforeach	
			</div>
		</div>
		@else 
		 <thead class="thead-primary">
			<tr>
				<th class="text-left" colspan="4" width="10%">No invoice found.</th>
			</tr>
		</thead>
		@endif   
 
@endsection

@section('jscript')
<script>
$(document).ready(function(){
	$('.invoice_id').on('click', function() {
		let current_inv_ids = parent.$('#invoice_ids').val();
		let current_id = $(this).val();
		if($(this).is(':checked')){
			parent.$('#invoice_ids').val(current_inv_ids+','+current_id);
		}else{
			parent.$('#invoice_ids').val(current_inv_ids.replace(new RegExp(current_id, 'g'), ''));
		}
	})
	
});
</script>
@endsection