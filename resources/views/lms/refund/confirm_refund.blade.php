@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="card card-color mb-0">
	<div class="card-header">
		<a class="card-title ">
			Are you sure you want to refund?
		</a>
	</div>
</div>
<form id="manualDisburse" method="POST" action="{{ Route('lms_send_refund') }}" target="_top">
	@csrf
	<input type="hidden" value="" name="disbursal_ids" id="disbursal_ids">  
	<input type="hidden" name="refund_type" value="{{ request()->get('refund_type') }}">
	@if(request()->get('refund_type') == 2)
	<div class="row">
		<div class="col-md-12">
			<div class="form-group ">
				<div class="row mt10">
					<div class="col-md-3">
						<label ><b>Transaction Id</b></label>
						<input type="text" name="trans_id" class="form-control" value="" placeholder="Transaction Id">
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

	<input type="submit" id="submitManualDisburse" value="Submit" class="btn btn-success btn-sm ml-2">
</form>
 
@endsection

@section('jscript')

<script>
$(document).ready(function(){
	console.log(parent.$('#disbursal_ids').val());
	$('#disbursal_ids').val(parent.$('#disbursal_ids').val());
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