@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
@if($flag == 1)
@include('layouts.backend.partials.admin_customer_links',['active' => 'invoice'])
@endif
<div class="content-wrapper">
	<div class="col-md-12 ">
		<section class="content-header">
			<div class="header-icon">
				<i class="fa fa-clipboard" aria-hidden="true"></i>
			</div>
			<div class="header-title">
				<h3 class="mt-2">Manage Invoice</h3>

				<ol class="breadcrumb">
					<li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
					<li class="active">Manage Invoice</li>
				</ol>
			</div>
			<div class="clearfix"></div>
		</section>
		<div class="row grid-margin">

			<div class="col-md-12 ">
				<div class="card">
					<div class="card-body">
						@include('layouts.backend.invoice_status_links')
						
						
						<div class="card">
							<div class="card-body">       
								<form id="manualDisburse" method="POST" action="{{ Route('download_batch_data') }}">
								@csrf
								<div class="row">
									<div class="col-md-3">
										{!!
										Form::text('customer_code',
										null,
										[
										'class' => 'form-control',
										'placeholder' => 'Search by Customer Code',
										'id'=>'customer_code'
										])
										!!}
									</div>
									<div class="col-md-3">

										{!!
					                    Form::text('selected_date',
					                    null,
					                    [
					                    'class' => 'form-control',
					                    'placeholder' => 'Date',
					                    'value' => "2013-01-08",
					                    'id'=>'selected_date'
					                    ])
					                    !!}

									</div>
									<div class="col-md-3">
										<select class="form-control" id="batch_id" name="batch_id">
											<option value="" selected="">All</option>
											@foreach($batchData as $batch)
											<option value="{{ $batch->disbursal_batch_id }}">{{ $batch->batch_id }}</option>
											@endforeach
										</select>
									</div>
									<button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button>
									<button type="submit" class="btn  btn-success btn-sm float-right ml-4">Download Excel</button>
									</div>
								</form>
									
									<div class="col-12 dataTables_wrapper mt-4">
										<div class="overflow">
											<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
												<div class="row">
													<div class="col-sm-12">
														<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
															<table id="disbursalCustomerList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
																<thead>
																	<tr role="row">
																		<th width="5%">Batch ID</th>
																		<th width="5%">Cust ID</th>
																		<th width="13%">Ben Name</th>
																		<th width="20%">Bank Detail</th>
																		<th width="15%">Total Disburse Amt.</th>
																		<th width="8%">Total Invoice </th>
                                                                       	<th> Updated At</th>                                                    <th width="4%">Action</th>
																	</tr>
																</thead>
																<tbody>

																</tbody>
															</table>
														</div>
														<div id="disbursalCustomerList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div></div>

</div>
{!!Helpers::makeIframePopup('viewBatchSendToBankInvoice','View Invoice', 'modal-lg')!!}
{!!Helpers::makeIframePopup('invoiceDisbursalTxnUpdate','Update Trasaction Id', 'modal-lg')!!}

@endsection
@section('jscript')
<style>
	.itemBackground 
	{ 
		border: 2px solid blanchedalmond;  
		 background-color:#138864;
	}
	.itemBackgroundColor 
	{ 
		color:white;
	}
</style>    
<script>

	var messages = {
		backend_get_invoice_list_bank: "{{ URL::route('backend_get_invoice_list_bank') }}",
		token: "{{ csrf_token() }}",
	};
	$('#selected_date').datetimepicker({
		useCurrent:true,
        format: 'yyyy-mm-dd',
     	// startDate: new Date(),
        autoclose: true,
        minView: 2, 
        defaultDate:new Date(),
    })
</script>
<script src="{{ asset('backend/js/ajax-js/invoice_list_send_to_bank.js') }}"></script>

@endsection
