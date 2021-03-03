@extends('layouts.backend.admin-layout')

@section('content')
<div class="content-wrapper">
	<div class="row">
		@if(isset($data['anchorData']))
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 mb-4">
			<div class="card card-statistics">
				<div class="card-body m-0">
					<div class="clearfix">
						<div class="float-left">
							<h4 class="text-primary">
							<i class="fa fa-book highlight-icon"></i>
						</h4>
						</div>
						<div class="float-right">
							<h4 class="bold-text">Anchor Detail</h4>
						</div>
					</div>
				   
					<table class="table text-center text-muted m-0 mt-2">
						<thead>
							<tr>
								<th>Total Anchor Limit </th>
								<th>Consumed limit </th>
								<th>Remaining Limit </th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> {{ \Helpers::formatCurrency($data['anchorData']->totalLimit) }}</td>
								<td> {{ \Helpers::formatCurrency($data['anchorData']->utilizedLimit) }}</td>
								<td> {{ \Helpers::formatCurrency($data['anchorData']->remainingLimit) }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif
		@if(isset($data['lenderAnchorData']))
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 mb-4">
			<div class="card card-statistics">
				<div class="card-body m-0">
					<div class="clearfix">
						<div class="float-left">
							<h4 class="text-primary">
							<i class="fa fa-book highlight-icon"></i>
						</h4>
						</div>
						<div class="float-right">
							<h4 class="bold-text">Anchor Detail</h4>
						</div>
					</div>
				   
					<table class="table text-center text-muted m-0 mt-2">
						<thead>
							<tr>
								<th>Total Anchor(s) </th>
								<th>Active Anchor(s)</th>
								<th>Inactive Anchor(s)</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> {{ count($data['lenderAnchorData']) }}</td>
								<td> {{ count($data['lenderAnchorData']->where('is_active', 1)) }}</td>
								<td> {{ count($data['lenderAnchorData']->where('is_active', 0)) }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif
		@if(isset($data['anchorUserData']))
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 mb-4">
			<div class="card card-statistics">
				<div class="card-body m-0">
					<div class="clearfix">
						<div class="float-left">
							<h4 class="text-primary">
							  <i class="fa fa-users highlight-icon-small"></i>
							</h4>
						</div>
						<div class="float-right">
							<h4 class="bold-text">Customer Detail </h4>
						</div>
					</div>
					<table class="table text-center text-muted m-0 mt-2">
						<thead>
							<tr>
								<th>Total Number of Supplier</th>
								<th>Registered Supplier</th>
								<th>Unregistered Supplier</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> {{ count($data['anchorUserData']) }}</td>
								<td> {{ count($data['anchorUserData']->where("is_registered", 1)) }}</td>
								<td> {{ count($data['anchorUserData']->where("is_registered", 0)) }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif
		@if(isset($data['prgmData']))
		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
			<div class="card card-statistics">
				<div class="card-body m-0">
					<div class="clearfix">
						<div class="float-left">
							<h4 class="text-primary">
							  <i class="fa fa-credit-card highlight-icon"> </i>
							</h4>
						</div>
						<div class="float-right">
							<h4 class="bold-text">Program Detail </h4>
						</div>
					</div>
					<table class="table text-center text-muted m-0 mt-2">
						<thead>
							<tr>
								<th>Sub-Program Name </th>
								<th>Program Type </th>
								<th>Sub-Program Limit </th>
								<th>Consumed limit </th>
								<th>Remaining Limit </th>
							</tr>
						</thead>
						<tbody>
							@foreach($data['prgmData'] as $key => $value)
							<tr>
								<td>{{ $value->prgm_name }}</td>
								<td>{{ ($value->prgm_type == 1 ? 'Vendor Finance' : 'Channel Finance') }}</td>
								<td> {{ \Helpers::formatCurrency($value->anchor_sub_limit) }}</td>
								<td> {{ \Helpers::formatCurrency(\Helpers::getPrgmBalLimit($value->prgm_id)) }}</td>
								<td> {{ \Helpers::formatCurrency($value->anchor_sub_limit - \Helpers::getPrgmBalLimit($value->prgm_id)) }}</td>
								
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif
		@if(isset($data['anchorAppData']))
		<div class="col-xl-12 mb-4">
			<div class="card card-statistics">
				<div class="card-body m-0">
					<div class="clearfix">
						<div class="float-left">
							<h4 class="text-primary">
							  <i class="fa fa-address-card highlight-icon"></i>
							</h4>
						</div>
						<div class="float-right">
							<h4 class="bold-text">Application Status</h4>
						</div>
					</div>
					<table class="table text-center text-muted m-0 mt-2">
						<thead>
							<tr>
								<th>Incomplete </th>
								<th>Complete </th>
								<th>Offer Generated </th>
								<th>Limit Approved </th>
								<th>Sanction Letter Generated </th>
								<!-- <th>Sanction Letter Approved </th> -->
								<th>Sanctioned </th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> {{ count($data['anchorAppData']->where('curr_status_id', config('common.mst_status_id')['APP_INCOMPLETE'])) }}</td>
								<td> {{ count($data['anchorAppData']->where('curr_status_id', config('common.mst_status_id')['COMPLETED'])) }}</td>
								<td> {{ count($data['anchorAppData']->where('curr_status_id', config('common.mst_status_id')['OFFER_GENERATED'])) }}</td>
								<td> {{ count($data['anchorAppData']->where('curr_status_id', config('common.mst_status_id')['OFFER_ACCEPTED'])) }}</td>
								<td> {{ count($data['anchorAppData']->where('curr_status_id', config('common.mst_status_id')['SANCTION_LETTER_GENERATED'])) }}</td>
								<!-- <td> {{ count($data['anchorAppData']->where('curr_status_id', config('common.mst_status_id')['SANCTION_LETTER_APPROVED'])) }}</td> -->
								<td> {{ count($data['anchorAppData']->where('curr_status_id', config('common.mst_status_id')['APP_SANCTIONED'])) }}</td>

							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif
		@if(isset($data['anchorInvoiceData']))

		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
			<div class="card card-statistics">
				<div class="card-body m-0">
					<div class="clearfix">
						<div class="float-left">
							<h4 class="text-primary">
							  <i class="fa fa-university highlight-icon"></i>
							</h4>
						</div>
						<div class="float-right">
							<h4 class="bold-text"> Bill Status</h4>
						</div>
					</div>
					<table class="table text-center text-muted m-0 mt-2">
						<thead>
							<tr>
								<th>Selection Pending </th>
								<th>Pending </th>
								<th>Approved </th>
								<th>Disbursement Queue  </th>
								<th>Sent To Bank </th>
								<th>Failed Disbursement </th>
								<th>Disbursed</th>
								<th>Payment Settled</th>
								<th>Rejected</th>
								<th>Exception Cases</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['SELECTION_PENDING'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['PENDING'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['APPROVED'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['DISBURSMENT_QUE'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['SENT_TO_BANK'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['FAILED_DISBURSMENT'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['DISBURSED'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['PAYMENT_SETTLED'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['REJECT'])) }}</td>
								<td> {{ count($data['anchorInvoiceData']->where('status_id', config('lms.mst_status_invoice')['EXCEPTION_CASE'])) }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif
	</div>
</div>
@endsection