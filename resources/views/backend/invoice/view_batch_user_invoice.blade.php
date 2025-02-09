@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive" id="fi_list">
            <table id="fiList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                <thead>
                    <tr role="row">
                        <th>Invoice No</th>
                        <th>Disbursed Date </th>
                        <th>Invoice Amt.</th>
                        <th>Margin(%) </th>
                        <th>Disburse/Principal Amt.</th>
                        <th>Actual Disburse /Principal Amt.</th>
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
                               
							<td> {{ $invoice->invoice_no }}</td>
							<td> {{ ($invoice->invoice_disbursed->disbursal) ? \Helpers::convertDateTimeFormat($invoice->invoice_disbursed->disbursal->disburse_date, 'Y-m-d h:i:s', 'Y-m-d') : ''}}</td>
							<td> <i class="fa fa-inr"></i> {{ number_format($invoice->invoice_approve_amount, 2) }} </td>
							<td> </i>{{ $margin }} %</td>
							<td> <i class="fa fa-inr"></i> {{ number_format($invoice->invoice_approve_amount - (($invoice->invoice_approve_amount*$margin)/100), 2) }} </td>
							<td><i class="fa fa-inr"></i>
							@php
								$interest = 0;
								$tenor = $invoice->invoice_disbursed->tenor_days;
								$fundedAmount = $invoice->invoice_approve_amount - (($invoice->invoice_approve_amount*$invoice->invoice_disbursed->margin)/100);
				    			if( ($invoice->program->interest_borne_by == 2) && ($invoice->program_offer->payment_frequency == 1 || empty($invoice->program_offer->payment_frequency)) ) {
						            $interest = $invoice->invoice_disbursed->total_interest;
						        }           
								$disburseAmount = round($fundedAmount - $interest, 2);
							@endphp

							{{ number_format($disburseAmount, 2) }}
							</td>
							<td> <span class="badge badge-primary">
							@switch($invoice->program_offer->payment_frequency)
								@case(1)
									UPFRONT
									@break
								@case(2)
									MONTHLY
									@break
								@case(3)
								    REAR END
									@break
							@endswitch</span></td>
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