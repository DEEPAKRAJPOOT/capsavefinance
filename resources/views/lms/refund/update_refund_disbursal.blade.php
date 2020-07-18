@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="manualDisburse" method="POST" action="{{ Route('updateDisburseRefund',[ 'payment_id' => $payment_id, 'refund_req_batch_id' => $refund_req_batch_id, 'refund_req_id' => $refund_req_id ]) }}" target="_top">
	@csrf
	<div class="row">
		<div class="col-12">
			<div class="form-group ">
				<div class="row mt10">
					<div class="col-6">
						<label><b>Transaction Id</b></label>
						<input type="text" name="trans_no" class="form-control" value="" placeholder="Transaction Id">
					</div>
					<div class="col-6">
						<label><b>Refund Date</b></label>
						<input type="text" id="disburse_date" name="disburse_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate" required="">
					</div>
					<div class="col-12">
						<label><b>Remarks</b></label>
						<textarea type="text" name="remarks" value="" class="form-control" placeholder="Remark" ></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="submit" id="submitManualDisburse" value="Mark Refunded" class="btn btn-success btn-sm ml-2">
</form>
 
@endsection

@section('jscript')
<script>

$(document).ready(function () {
	$('.datepicker-dis-fdate').datetimepicker({
        useCurrent:true,
        format: 'yyyy-mm-dd',
        autoclose: true,
        minView: 2, 
        defaultDate:new Date(),
    })
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