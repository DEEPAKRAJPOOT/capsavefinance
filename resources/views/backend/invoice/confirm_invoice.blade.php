@extends('layouts.backend.admin_popup_layout')
@section('content')

<input type="hidden" value="{{ $invoiceIds }}" name="invoice_ids" id="invoice_ids">

<div class="col-12 dataTables_wrapper mt-4">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
						<table id="disbursalCustomerList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
							<thead>
								<tr role="row">
									<th width="4%">Cust ID</th>
									<th width="4%">App ID</th>
									<th width="10%">Ben Name</th>
									<th width="20%">Bank Detail</th>
									<th width="15%">Total Invoice Amt.</th>
									<th width="15%">Total Disburse Amt.</th>
									<th width="30%">Total Actual Funded Amt.</th>
								</tr>
							</thead>
							<tbody>
								@foreach($customersDisbursalList as $customer)
								<tr role="row" class="odd">
									<td> {{ $customer->customer_id }}</td>
									<td> CAPS000{{ $customer->app_id }} </td>
									@php
									if ($customer->user->is_buyer == 2) {
										$benName = (isset($customer->user->anchor_bank_details->acc_name)) ? $customer->user->anchor_bank_details->acc_name : '';
									} else {
										$benName =  (isset($customer->bank_details->acc_name)) ? $customer->bank_details->acc_name : '';
									}
									@endphp
									<td> {{ $benName }}</td>
									@php
									if ($customer->user->is_buyer == 2) {
										$bank_name = (isset($customer->user->anchor_bank_details->bank->bank_name)) ? $customer->user->anchor_bank_details->bank->bank_name : '';
									} else {
										$bank_name = (isset($customer->bank_details->bank->bank_name)) ? $customer->bank_details->bank->bank_name : '';
									}


									if ($customer->user->is_buyer == 2) {
										$ifsc_code = (isset($customer->user->anchor_bank_details->ifsc_code)) ? $customer->user->anchor_bank_details->ifsc_code : '';
									} else {
										$ifsc_code = (isset($customer->bank_details->ifsc_code)) ? $customer->bank_details->ifsc_code : '';
									}

									if ($customer->user->is_buyer == 2) {
										$benAcc = (isset($customer->user->anchor_bank_details->acc_no)) ? $customer->user->anchor_bank_details->acc_no : '';
									} else {
										$benAcc = (isset($customer->bank_details->acc_no)) ? $customer->bank_details->acc_no : '';
									}

									$account = '';
									$account .= $bank_name ? '<span><b>Bank:&nbsp;</b>'.$bank_name.'</span>' : '';
									$account .= $ifsc_code ? '<br><span><b>IFSC:&nbsp;</b>'.$ifsc_code.'</span>' : '';
									$account .= $benAcc ? '<br><span><b>Acc. #:&nbsp;</b>'.$benAcc.'</span>' : '';
									@endphp

									<td> {!! $account !!}</td>

									@php
									$invoiceTotal = 0;
									$apps = $customer->app->toArray();
									foreach ($apps as $app) {
										$invoiceTotal += array_sum(array_column($app['invoices'], 'invoice_approve_amount'));
									}
									@endphp

									<td> <i class="fa fa-inr"></i> {{ number_format($invoiceTotal) }}</td>
									@php
									$fundedAmount = 0;
									$apps = $customer->app;
									foreach ($apps as $app) {
										foreach ($app->invoices as $inv) {
											$invoice = $inv->toArray();
											$margin = $invoice['program_offer']['margin'];
											$fundedAmount += $invoice['invoice_approve_amount'] - (($invoice['invoice_approve_amount']*$margin)/100);
										}
									}
									@endphp

									<td> <i class="fa fa-inr"></i> {{ number_format($fundedAmount) }}</td>

									@php 
									$disburseAmount = 0;
									$interest = 0;
									$apps = $customer->app;
									foreach ($apps as $app) {
										foreach ($app->invoices as $inv) {
											$invoice = $inv->toArray();
											$margin = $invoice['program_offer']['margin'];
											$fundedAmount =  $invoice['invoice_approve_amount'] - (($invoice['invoice_approve_amount']*$margin)/100);
											
											$now = strtotime((isset($invoice['invoice_due_date'])) ? $invoice['invoice_due_date'] : '');
									        $your_date = strtotime((isset($invoice['invoice_date'])) ? $invoice['invoice_date'] : '');
									        $datediff = abs($now - $your_date);

									        $tenorDays = round($datediff / (60 * 60 * 24));

									        $tInterest = $fundedAmount * $tenorDays * ($invoice['program_offer']['interest_rate'] / 360) ;                

											if($invoice['program_offer']['payment_frequency'] == 1 || empty($invoice['program_offer']['payment_frequency'])) {
												$interest = $tInterest;
											}
											$disburseAmount += round($fundedAmount - $interest, 2);
										}
									}
									@endphp
									<td> <i class="fa fa-inr"></i> {{ number_format($disburseAmount) }}</td>


								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 
@endsection

@section('jscript')
<script>
$(document).ready(function(){
	$('#invoiceids').val(parent.$('#invoice_ids').val());
});

$(document).ready(function () {
		$('#manualDisburse').validate({ // initialize the plugin
			
			rules: {
				'trans_id' : {
					required : true,
				},
				'utr_no' : {
					required : true,
				},
				'remarks' : {
					required : true,
				}
			},
			messages: {
				'trans_id': {
					required: "Transaction Id required.",
				}
				,'utr_no': {
					required: "UTR Number required.",
				},
				'remarks': {
					required: "Remark required.",
				}
			}
		});

		$('#manualDisburse').validate();

		$("#savedocument").click(function() {
			if($('#manualDisburse').valid()) {
				$('form#manualDisburse').submit();
				$("#submitManualDisburse").attr("disabled","disabled");
			}  
		});            

	});
</script>
@endsection