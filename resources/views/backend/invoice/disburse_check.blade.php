@extends('layouts.backend.admin_popup_layout')
@section('content')
@php 
$finalDisburseAmt = 0;
@endphp
@foreach($customersDisbursalList as $customer)

@php 
$disburseAmount = 0;
$totalMargin = 0;
$totalInterest = 0;
$interest = 0;
$apps = $customer->app;
foreach ($apps as $app) {
	foreach ($app->invoices as $inv) {
		$invoice = $inv->toArray();
		$margin = $invoice['program_offer']['margin'];
		$interestRate = $invoice['program_offer']['interest_rate']/100;


		$now = strtotime((isset($invoice['invoice_due_date'])) ? $invoice['invoice_due_date'] : ''); 
        $your_date = strtotime((isset($invoice['invoice_date'])) ? $invoice['invoice_date'] : '');
        $datediff = abs($now - $your_date);
        $tenor = round($datediff / (60 * 60 * 24));



		$tMargin = (($invoice['invoice_approve_amount']*$margin)/100);
		$fundedAmount =  $invoice['invoice_approve_amount'] - $tMargin ;
		if($invoice['program_offer']['payment_frequency'] == 1) {
			$interest = $fundedAmount * $tenor * ($interestRate / 360) ;                
        }
		$finalDisburseAmt += round($fundedAmount - $interest, 2);
	}
}
@endphp


@endforeach

<div class="row">
	<div class="col-12 row">

		<div class="col-4">
			<div class="form-group">
				<label for="marginAmount"># No of Cust.</label>
				<input type="text" name="" class="form-control" readonly="true" value="{{ $customersDisbursalList->count() }}">
			</div>
		</div>
		<div class="col-4">
			<div class="form-group">
				<label for="nonFactoredAmount"># Amount Disburse</label>
				<input type="text" name="" id="nonFactoredAmt" class="form-control" readonly="true" value="{{ number_format((float)$finalDisburseAmt, 2, '.', '') }}">
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-6">
		<form id="manualDisburse" method="POST" action="{{ Route('disburse_offline') }}" target="_top">
			<input type="hidden" value="{{ $invoiceIds }}" name="invoice_ids" id="invoice_ids">
			@csrf
			<div class="col-6">
				<div class="form-group">
					<label for="txtCreditPeriod">Disburse Date <span class="error_message_label">*</span> </label>
					<input type="text" id="disburse_date" name="disburse_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate" required="">
					 @if(Session::has('error'))
					 <div class="error">{{ Session::get('error') }}</div>
					  
					@endif
				</div>
			</div>
			<div class="col-6">
				<input type="submit" id="submitManualDisburse" value="Disburse Offline" class="btn btn-success btn-sm ml-2">
			</div>
		</form>
	</div>
	<!-- <div class="col-6 row">
		<div class="col-6"></div>
		<div class="col-3">
			<form id="manualDisburse" method="POST" action="{{ Route('disburse_online') }}">
				@csrf
				<input type="hidden" value="{{ $invoiceIds }}" name="invoice_ids" id="invoice_ids">
				
				<input type="submit" id="submitManualDisburse" value="Disburse Online" class="btn btn-success btn-sm ml-2 disabled">
			</form>
		</div>
	</div> -->

</div>
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
									<th width="15%">Total Invoice</th>
									<th width="15%">Total Invoice Amt.</th>
									<th width="15%">Total Disburse Amt.</th>
									<th width="15%">Total Margin</th>
									<th width="15%">Total Interest</th>
									<th width="30%">Total Actual Disburse Amt.</th>
								</tr>
							</thead>
							<tbody>
							@php 
							$finalDisburseAmt = 0;
							@endphp
								@foreach($customersDisbursalList as $customer)
								<tr role="row" class="odd">
									<td> {{ $customer->customer_id }}</td>
									<td> CAPS000{{ $customer->app_id }} </td>
									@php
									if ($customer->user->is_buyer == 2) {
										$benName = (isset($customer->user->anchor_bank_details->acc_name)) ? $customer->user->anchor_bank_details->acc_name : '';
										$displayName = $benName ? '<span><b>Anchor:&nbsp;</b>'.$benName.'</span>' : '';

									} else {
										$benName =  (isset($customer->bank_details->acc_name)) ? $customer->bank_details->acc_name : '';
										$displayName = $benName ? '<span><b>Supplier:&nbsp;</b>'.$benName.'</span>' : '';

									}
									@endphp
									<td> {!! $displayName !!}</td>
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
									$apps = $customer->app;
									foreach ($apps as $app) {
										$totalInvCount = $app->invoices->count();
									}
									@endphp
									<td> {{ $totalInvCount }}</td>
									@php
									$invoiceTotal = 0;
									$apps = $customer->app->toArray();
									foreach ($apps as $app) {
										$invoiceTotal += array_sum(array_column($app['invoices'], 'invoice_approve_amount'));
									}
									@endphp

									<td> <i class="fa fa-inr"></i> {{ number_format((float)$invoiceTotal, 2, '.', '') }}</td>
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

									<td> <i class="fa fa-inr"></i> {{ number_format((float)$fundedAmount, 2, '.', '') }}</td>

									@php 
									$disburseAmount = 0;
									$totalMargin = 0;
									$totalInterest = 0;
									$interest = 0;
									$apps = $customer->app;
									foreach ($apps as $app) {
										foreach ($app->invoices as $inv) {
											$invoice = $inv->toArray();
											$margin = $invoice['program_offer']['margin'];
											$interestRate = $invoice['program_offer']['interest_rate']/100;


											$now = strtotime((isset($invoice['invoice_due_date'])) ? $invoice['invoice_due_date'] : ''); 
									        $your_date = strtotime((isset($invoice['invoice_date'])) ? $invoice['invoice_date'] : '');
									        $datediff = abs($now - $your_date);
									        $tenor = round($datediff / (60 * 60 * 24));



											$tMargin = (($invoice['invoice_approve_amount']*$margin)/100);
											$fundedAmount =  $invoice['invoice_approve_amount'] - $tMargin ;
											if($invoice['program_offer']['payment_frequency'] == 1) {
    											$interest = $fundedAmount * $tenor * ($interestRate / 360) ;                
						                    }
											$disburseAmount += round($fundedAmount - $interest, 2);
											$totalMargin += round($tMargin, 2);
											$totalInterest += round($interest, 2);
										}
									}
									@endphp
									<td> <i class="fa fa-inr"></i> {{ number_format((float)$totalMargin, 2, '.', '') }}</td>
									<td> <i class="fa fa-inr"></i> {{ number_format((float)$totalInterest, 2, '.', '') }}</td>
									<td> <i class="fa fa-inr"></i> {{ number_format((float)$disburseAmount, 2, '.', '') }}</td>
									@php 

									$finalDisburseAmt +=  $disburseAmount;
									@endphp

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

<script type="text/javascript">
	$(document).ready(function () {
	    parent.$('.modal-dialog').addClass('viewCiblReportModal .modal-lg').removeClass('modal-dialog modal-lg');
	});
	$(document).ready(function () {
        $('#manualDisburse').validate({ // initialize the plugin
            
            rules: {
                'disburse_date' : {
                    required : true,
                }
            },
            messages: {
                'disburse_date': {
                    required: "Disburse date is required.",
                }
            }
        });

        $('#manualDisburse').validate();

        $("#submitManualDisburse").click(function(){
            if($('#manualDisburse').valid()){
                $('form#manualDisburse').submit();
                $("#submitManualDisburse").attr("disabled","disabled");
            }  
        });            

    });

</script>
@endsection