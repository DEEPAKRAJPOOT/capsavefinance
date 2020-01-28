@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="card card-color mb-0">
	<div class="card-header">
		<a class="card-title ">
			Are you sure you want to disburse checked invoices?
		</a>
	</div>
</div>
<form method="POST" action="{{ Route('send_to_bank') }}" target="_top">
	@csrf
	<input type="hidden" value="" name="invoiceids" id="invoiceids">  
	<input type="hidden" name="disburse_type" value="{{ request()->get('disburse_type') }}">
	@if(request()->get('disburse_type') == 2)
	<div class="row">
		<div class="col-md-12">
          	<div class="form-group ">
				<div class="row mt10">
                    <div class="col-md-3">
                        <label ><b>Transaction Id</b></label>
                        <input type="text" name="trans_id" class="form-control" value="" placeholder="Transaction Id" maxlength="5">
                    </div>
                    <div class="col-md-3 mt10">
                        <label for="txtPassword"><b>Remarks</b></label>
                        <textarea type="text" name="remarks" value="" class="form-control" placeholder="Remark" ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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