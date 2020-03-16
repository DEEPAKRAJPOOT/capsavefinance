@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="card card-color mb-0">
	<div class="card-header">
		<a class="card-title ">
			Are you sure you want to disburse checked invoices?
		</a>
	</div>
</div>
<form id="manualDisburse" method="POST" action="{{ Route('send_to_bank') }}" target="_top">
	@csrf
	<input type="hidden" value="" name="invoiceids" id="invoiceids">  
	<input type="hidden" value="" name="user_ids" id="user_ids">  
	<input type="hidden" name="disburse_type" value="{{ request()->get('disburse_type') }}">
	@if(request()->get('disburse_type') == 2)
	<div class="row">
		<div class="col-md-12">
			<div class="form-group ">
				<div class="row mt10">
					<div class="col-md-12">
						<div class="form-group">
							<label for="txtCreditPeriod">Disburse Date <span class="error_message_label">*</span> </label>
							<input type="text" id="disburse_date" name="disburse_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate">
						</div>
					</div>
					<div class="col-md-3">
						<label ><b>Transaction Id</b></label>
						<input type="text" name="trans_id" class="form-control" value="" placeholder="Transaction Id">
					</div>
					<!-- <div class="col-md-3">
						<label ><b>Utr No</b></label>
						<input type="text" name="utr_no" class="form-control" value="" placeholder="Transaction Id">
					</div> -->
					<div class="col-md-3 mt10">
						<label for="txtPassword"><b>Remarks</b></label>
						<textarea type="text" name="remarks" value="" class="form-control" placeholder="Remark" ></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
	<input type="submit" id="submitManualDisburse" value="Submit" class="btn btn-success btn-sm ml-2">
</form>
 
@endsection

@section('jscript')
<script>
$(document).ready(function(){
	$('#invoiceids').val(parent.$('#invoice_ids').val());
	$('#user_ids').val(parent.$('#user_ids').val());
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