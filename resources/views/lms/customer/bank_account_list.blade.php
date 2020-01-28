@extends('layouts.backend.admin-layout')

@section('content')

@include('layouts.backend.partials.admin_customer_links',['active'=>'bank'])
<div class="content-wrapper">
	<div class="row grid-margin mt-3">
		<div class="  col-md-12  ">
			<section class="content-header">
				<div class="header-icon">
					<i class="fa fa-clipboard" aria-hidden="true"></i>
				</div>
				<div class="header-title">
					<h3 class="mt-2">Bank Account</h3>

					<ol class="breadcrumb">
						<li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
						<li class="active">Bank Account</li>
					</ol>
				</div>
				<div class="clearfix"></div>
			</section>
			<div class="row">
				<div class="col-sm-12">
					<div class="head-sec">
						@can('add_bank_account')
						<a data-toggle="modal" 
						   title="Add Bank" 
						   data-height="450px" 
						   data-width="100%" 
						   data-target="#add_bank_account"
						   id="register" 
						   data-url="{{ route('add_bank_account', ['user_id' => request()->get('user_id')]) }}" >
							<button class="btn  btn-success btn-sm float-right mb-3" type="button">
								+ Add Bank
							</button>
						</a>
						@endcan

					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12">

							<table class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
								<thead>
									<tr role="row">
										<th>Acc. Holder Name </th>
										<th>Acc. Number</th>
										<th>Bank Name</th>
										<th>IFSC Code</th>
										<th>Branch Name </th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($bankAccounts  as $account) 
										<tr>
											<td>{{ $account->acc_name }}</td>
											<td>{{ $account->acc_no }}</td>
											<td>{{ $account->bank->bank_name }}</td>
											<td>{{ $account->ifsc_code }}</td>
											<td>{{ $account->branch_name }}</td>
											<td>{!! ($account->is_active == 1) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-warning current-status">InActive</span>' !!}</td>
											@php

											$checked = ($account->is_default == 1) ? 'checked' : null;
											$act = '';
											if($account->is_active){
											  $act .= '    <input type="checkbox"  ' . $checked . ' data-rel = "' . \Crypt::encrypt($account->bank_account_id) . '"  class="make_default" name="add"><label for="add">Default</label> ';
											}
										  
											if (Helpers::checkPermission('add_bank_account')) {
												$act .= '<a data-toggle="modal"  data-height="450px" 
										   data-width="100%" 
										   data-target="#add_bank_account"
										   data-url="' . route('add_bank_account', ['bank_account_id' => $account->bank_account_id, 'user_id' => request()->get('user_id')]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Bank Account"><i class="fa fa-edit"></i></a>';
											}
											@endphp
											<td>{!! $act !!}</td>
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
</div>
@endsection
{!!Helpers::makeIframePopup('add_bank_account','Add Bank', 'modal-lg')!!}
@section('additional_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection
@section('jscript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script>
var messages = {
	data_not_found: "{{ trans('error_messages.data_not_found') }}",
	token: "{{ csrf_token() }}",
	set_default_account : "{{ URL::route('set_default_account') }}",
};
</script>
<script>
	try {
		jQuery(document).ready(function ($) {
			$(document).on('click', '.make_default', function () {
				$this = $(this);
				var currentValue = ($(this).prop('checked')) ? 1 : 0;
				var acc_id = $(this).data('rel');
				$.confirm({
					title: 'Confirm!',
					content: 'Are you sure to Make Default?',
					buttons: {
						Yes: {
							action: function () {
								jQuery.ajax({
									url: messages.set_default_account,
									data: {bank_account_id: acc_id, _token: messages.token , value: currentValue },
									'type': 'POST',
									beforeSend: function () {
									   $('.isloader').show();
								   },
									success: function (data) {
										$('.isloader').hide();
										location.reload();
									}
								});
							}

						},
						Cancel: {
							action: function () {
								$this.prop('checked', false);
							}
						},
					},

				});
			});


		});
	} catch (e) {
		if (typeof console !== 'undefined') {
			console.log(e);
		}
	}
</script>
@endsection