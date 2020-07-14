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
						<label ><b>Transaction Id</b><span class="error_message_label">*</span></label>
						<input type="text" name="trans_id" class="form-control" value="" placeholder="Transaction Id">
					</div>
					<div class="col-6">
						<label for="txtCreditPeriod"><b>Funded Date </b><span class="error_message_label">*</span> </label>
						<input type="text" id="funded_date" name="funded_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate" required="">
					</div>
					<div class="col-6">
						<label for="txtPassword"><b>Remarks</b><span class="error_message_label">*</span></label>
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
	var date = new Date();
	date.setDate(date.getDate() - 60);
    $('#funded_date').datetimepicker('setStartDate',  date);

	$('#manualDisburse').validate({ // initialize the plugin
		
		rules: {
			'trans_id' : {
				required : true,
			},
			'funded_date' : {
				required : true,
			},
			'remarks' : {
				required : true,
			}
		},
		messages: {
			'trans_id': {
				required: "Transaction Id required.",
			},
			'funded_date': {
				required: "Funded Date required.",
			},
			'remarks': {
				required: "Remark required.",
			}
		}
	});

	$('#manualDisburse').validate();

	$("#submitManualDisburse").click(function() {
		if($('#manualDisburse').valid()) {
			$('form#manualDisburse').submit();
			$("#submitManualDisburse").attr("disabled","disabled");
		}  
	});            

	});
</script>
@endsection