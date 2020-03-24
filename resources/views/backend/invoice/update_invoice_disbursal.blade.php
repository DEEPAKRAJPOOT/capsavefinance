@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="manualDisburse" method="POST" action="{{ Route('updateDisburseInvoice') }}" target="_top">
	@csrf
	<input type="hidden" value="{{ $user_id }}" name="user_id">  
	<input type="hidden" value="{{ $disbursal_batch_id }}" name="disbursal_batch_id">  
	<div class="row">
		<div class="col-12">
			<div class="form-group ">
				<div class="row mt10">
					<div class="col-6">
						<label ><b>Transaction Id</b></label>
						<input type="text" name="trans_id" class="form-control" value="" placeholder="Transaction Id">
					</div>
					<div class="col-6">
						<label for="txtPassword"><b>Remarks</b></label>
						<textarea type="text" name="remarks" value="" class="form-control" placeholder="Remark" ></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="submit" id="submitManualDisburse" value="Mark Disburse" class="btn btn-success btn-sm ml-2">
</form>
 
@endsection

@section('jscript')
<script>

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