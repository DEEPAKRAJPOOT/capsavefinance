@extends('layouts.backend.admin_popup_layout')
@section('content')
<h5>Are you sure you want to disburse checked invoices ?</h5>
<form method="POST" action="{{ Route('send_to_bank') }}" target="_top">
	@csrf
	<input type="hidden" value="" name="invoiceids" id="invoiceids">  
	<input type="hidden" name="disburse_type" value="{{ request()->get('disburse_type') }}">  
	<input type="submit" value="Submit" class="btn btn-success btn-sm ml-2">
</form>
 
@endsection

@section('jscript')

<script>
$(document).ready(function(){
	$('#invoiceids').val(parent.$('#invoice_ids').val());
});

</script>
@endsection