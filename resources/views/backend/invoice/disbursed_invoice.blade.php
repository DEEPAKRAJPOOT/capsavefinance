@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
@if($flag == 1)
@include('layouts.backend.partials.admin_customer_links',['active'=>'invoice'])
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
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item ">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_invoice') active @endif"  href="{{Route('backend_get_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Pending</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_invoice') active @endif"  href="{{Route('backend_get_invoice')}}">Pending</a>
								@endif
							</li>
							<li class="nav-item">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_approve_invoice') active @endif"  href="{{Route('backend_get_approve_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Approved</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_approve_invoice') active @endif"  href="{{Route('backend_get_approve_invoice')}}">Approved</a>
								@endif
							</li>
							<li class="nav-item">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed_invoice') active @endif"  href="{{Route('backend_get_disbursed_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Disbursement Queue</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed_invoice') active @endif"  href="{{Route('backend_get_disbursed_invoice')}}">Disbursement Queue</a>
								@endif
							</li>

							<li class="nav-item">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_sent_to_bank') active @endif" href="{{Route('backend_get_sent_to_bank',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Sent to Bank</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_sent_to_bank') active @endif" href="{{Route('backend_get_sent_to_bank')}}">Sent to Bank</a>
								@endif
							</li>
							<li class="nav-item">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_failed_disbursment') active @endif" href="{{Route('backend_get_failed_disbursment',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Failed Disbursement</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_failed_disbursment') active @endif" href="{{Route('backend_get_failed_disbursment')}}">Failed Disbursement</a>
								@endif
							</li>
							<li class="nav-item">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed') active @endif" href="{{Route('backend_get_disbursed',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Disbursed</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed') active @endif" href="{{Route('backend_get_disbursed')}}">Disbursed</a>
								@endif
							</li>
							<li class="nav-item">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_repaid_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Repaid</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_repaid_invoice')}}">Repaid</a>
								@endif
							</li>
							<li class="nav-item">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_reject_invoice') active @endif" href="{{Route('backend_get_reject_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Reject</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_reject_invoice') active @endif" href="{{Route('backend_get_reject_invoice')}}">Reject</a>
								@endif
							</li>

							<li class="nav-item">
								@if($flag == 1)
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_exception_cases') active @endif" href="{{Route('backend_get_exception_cases',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Exception Cases</a>
								@else
								<a class="nav-link @if(Route::currentRouteName()=='backend_get_exception_cases') active @endif" href="{{Route('backend_get_exception_cases')}}">Exception Cases</a>
								@endif
							</li>

						</ul>
						<div class="tab-content">
							<div id="menu1" class=" active tab-pane "><br>
								<div class="card">
									<div class="card-body">
										<div class="row">
											<div class="col-md-2">				 
												<input type="hidden" name="route" value="{{Route::currentRouteName()}}">                                
												<select class="form-control form-control-sm changeBiz searchbtn"  name="search_biz" id="search_biz">
													<option value="">Select Application  </option>
													@foreach($get_bus as $row)
													<option value="{{{$row->business->biz_id}}}">{{{$row->business->biz_entity_name}}} </option>
													@endforeach
												</select>
												<span id="anchorMsg" class="error"></span>
											</div>
											<div class="col-md-2">				 
												<select class="form-control form-control-sm changeAnchor searchbtn"  name="search_anchor">
													<option value="">Select Anchor  </option>
													@foreach($anchor_list as $row)
													@php if(isset($row->anchorOne->anchor_id)) { @endphp
													<option value="{{{$row->anchorOne->anchor_id}}}">{{{$row->anchorOne->comp_name}}}  </option>
													@php } @endphp
													@endforeach
												</select>
											</div>
											<div class="col-md-2">		    

												<select readonly="readonly" class="form-control form-control-sm searchbtn" id="supplier_id" name="search_supplier">

												</select>
											</div>    
											<div class="col-md-4 ml-auto text-right" id="buttonDiv">
												<a data-url="{{ route('disburse_confirm', ['disburse_type' => 2 ]) }}" data-height="330px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2 disburseClickBtn" >Send To Bank</a>

												<a data-toggle="modal" data-target="#disburseInvoice" data-url ="" data-height="330px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2" id="openDisburseInvoice" style="display: none;" >Disburse Trigger</a>
											</div>
											<input type="hidden" value="" name="invoice_ids" id="invoice_ids">  

										</div>
										<div class="row">
											<div class="col-12 dataTables_wrapper mt-4">
												<div class="overflow">
													<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
														<div class="row">
															<div class="col-sm-12">
																<table id="invoiceListDisbursedQue" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
																	<thead>
																		<tr role="row">
																			<th><input type="checkbox" id="chkAll"></th> 
																			<th>Invoice No</th> 
																			<th>Anchor Name</th>
																			<th>Customer Name</th>
																			<th>Invoice Date</th>
																			<th>Invoice Due Date</th>
																			<th>Tenor</th>
																			<th>Invoice  Amount</th>
																			<th>Invoice Approve Amount</th>
																			<th>Status</th>

																		</tr>
																	</thead>
																	<tbody>

																	</tbody>
																</table>
																<div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
				</div>
			</div>
		</div>
	</div>
</div>
<div id="loadDiv1">
	<input type="hidden" id="loadUrl1" value=""> 
</div>
{!!Helpers::makeIframePopup('disburseInvoice','Disburse Invoices', 'modal-lg')!!}

@endsection
@section('jscript')
<script src="{{ asset('backend/js/ajax-js/invoice_list_disbursment_que.js') }}"></script>
<script src="{{ asset('backend/js/invoice-disburse.js') }}"></script>

@php 
$operation_status = session()->get('operation_status', false);
@endphp
@if( $operation_status == config('common.YES')) 
  <script>
      try {
          var p = window.parent;
          p.jQuery('#disburseInvoice').modal('hide');
          window.parent.location.reload();
      } catch (e) {
          if (typeof console !== 'undefined') {
              console.log(e);
          }
      }
  </script>
@endif

<script>

	var messages = {
		backend_get_invoice_list_disbursed_que: "{{ URL::route('backend_get_invoice_list_disbursed_que') }}",
		upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
		get_program_supplier: "{{ URL::route('get_program_supplier') }}",
		data_not_found: "{{ trans('error_messages.data_not_found') }}",
		front_program_list: "{{ URL::route('front_program_list') }}",
		front_supplier_list: "{{ URL::route('front_supplier_list') }}",
		update_invoice_approve: "{{ URL::route('update_invoice_approve') }}",
		invoice_document_save: "{{ URL::route('invoice_document_save') }}",
		update_bulk_invoice: "{{ URL::route('update_bulk_invoice') }}",
		token: "{{ csrf_token() }}",
		appp_id: "{{ $app_id }}",
	};


	$(document).ready(function () {
		$("#program_bulk_id").append("<option value=''>No data found</option>");

		/////// jquery validate on submit button/////////////////////
		$('#submit').on('click', function (e) {

			if ($('form#signupForm').validate().form()) {
				$("#anchor_bulk_id").rules("add", {
					required: true,
					messages: {
						required: "Please enter Anchor name",
					}
				});

				$("#supplier_id").rules("add", {
					required: true,
					messages: {
						required: "Please Select Supplier Name",
					}
				});
				$("#program_bulk_id").rules("add", {
					required: true,
					messages: {
						required: "Please Select Product Program Name",
					}
				});

				$("#customFile").rules("add", {
					required: true,
					messages: {
						required: "Please upload Invoice Copy",
					}
				});


			} else {
				alert();
			}
		});
	});


	//////////////// for checked & unchecked////////////////
	$(document).on('click', '#chkAll', function () {
		var isChecked = $("#chkAll").is(':checked');
		if (isChecked)
		{
			$('input:checkbox').attr('checked', 'checked');
		} else
		{
			$('input:checkbox').removeAttr('checked');
		}
	});

	//////////////////// onchange anchor  id get data /////////////////

	$("#supplier_id").append("<option value=''>Select customer</option>");
	$(document).on('change', '.changeAnchor', function () {
		var anchor_id = $(this).val();
		$("#supplier_id").empty();
		var postData = ({'anchor_id': anchor_id, '_token': messages.token});
		jQuery.ajax({
			url: messages.get_program_supplier,
			method: 'post',
			dataType: 'json',
			data: postData,
			error: function (xhr, status, errorThrown) {
				alert(errorThrown);

			},
			success: function (data) {

				if (data.status == 1)
				{
					var obj1 = data.userList;

					///////////////////// for suppllier array///////////////  

					if (obj1.length > 0)
					{
						$("#supplier_id").append("<option value=''> Select Supplier </option>");
						$(obj1).each(function (i, v) {

							$("#supplier_id").append("<option value='" + v.app.user.user_id + "'>" + v.app.user.f_name + "</option>");

						});
					} else
					{
						$("#supplier_id").append("<option value=''>No data found</option>");

					}


				}

			}
		});
	});


	/////////// for pop up//////////////////


	//////////////////// onchange anchor  id get data /////////////////
	$(document).on('change', '.changeBulkAnchor', function () {

		var anchor_id = $(this).val();
		if (anchor_id == '')
		{
			$("#pro_limit").empty();
			$("#pro_limit_hide").empty();
		}
		$("#program_bulk_id").empty();
		$("#anc_limit").empty();
		var postData = ({'anchor_id': anchor_id, '_token': messages.token});
		jQuery.ajax({
			url: messages.front_program_list,
			method: 'post',
			dataType: 'json',
			data: postData,
			error: function (xhr, status, errorThrown) {
				alert(errorThrown);

			},
			success: function (data) {
				if (data.status == 1)
				{

					var obj1 = data.get_program;
					var obj2 = data.limit;

					$("#anc_limit").html('Limit : <span class="fa fa-inr"></span>  ' + obj2.anchor_limit + '');


					$("#program_bulk_id").append("<option value=''>Please Select</option>");
					$(obj1).each(function (i, v) {

						$("#program_bulk_id").append("<option value='" + v.program.prgm_id + "'>" + v.program.prgm_name + "</option>");
					});



				} else
				{

					$("#program_bulk_id").append("<option value=''>No data found</option>");


				}

			}
		});
	});

	//////////////////// onchange anchor  id get data /////////////////
	$(document).on('change', '.changeBulkSupplier', function () {

		var program_id = $(this).val();
		$("#supplier_bulk_id").empty();
		$("#pro_limit").empty();
		$("#pro_limit_hide").empty();
		var postData = ({'program_id': program_id, '_token': messages.token});
		jQuery.ajax({
			url: messages.front_supplier_list,
			method: 'post',
			dataType: 'json',
			data: postData,
			error: function (xhr, status, errorThrown) {
				alert(errorThrown);

			},
			success: function (data) {
				if (data.status == 1)
				{

					var obj1 = data.get_supplier;
					var obj2 = data.limit;

					$("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  ' + obj2.anchor_limit + '');
					$("#pro_limit_hide").val(obj2.anchor_limit);
					$("#supplier_bulk_id").append("<option value=''>Please Select</option>");
					$(obj1).each(function (i, v) {

						$("#supplier_bulk_id").append("<option value='" + v.app.user.user_id + "'>" + v.app.user.f_name + "</option>");
					});

				} else
				{

					$("#supplier_bulk_id").append("<option value=''>No data found</option>");


				}

			}
		});
	});

///////////////////////////////////////// change invoice amount////////////////
	$(document).on('click', '#UpdateInvoiceAmount', function () {

		var amount = parseFloat($("#invoice_amount").val());
		var approveAmount = parseFloat($("#invoice_approve_amount").val());
		if (approveAmount > amount)
		{
			$(".model7msg").show();
			$(".model7msg").html('Invoice Approve Amount should not greater amount');
			return false;
		} else
		{
			$(".model7msg").hide();
			return true;
		}
	});
</script>

@endsection
