@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="card card-color mb-0">
	<div class="card-header">
		<a class="card-title ">
			Are you sure you want to disburse checked invoices?
		</a>
	</div>
</div>
<div class="row">	
	<div class="col-3">
		<form id="manualDisburse" method="POST" action="{{ Route('disburse_offline') }}">
			@csrf
			<input type="hidden" value="{{ $invoiceIds }}" name="invoice_ids" id="invoice_ids">
			
			<input type="submit" id="submitManualDisburse" value="Export CSV (OFFLINE)" class="btn btn-success btn-sm ml-2">
		</form>
	</div>
	<div class="col-3">
		<form id="manualDisburse" method="POST" action="{{ Route('disburse_online') }}">
			@csrf
			<input type="hidden" value="{{ $invoiceIds }}" name="invoice_ids" id="invoice_ids">
			
			<input type="submit" id="submitManualDisburse" value="Send To Bank (ONLINE)" class="btn btn-success btn-sm ml-2">
		</form>
	</div>
</div>
@endsection