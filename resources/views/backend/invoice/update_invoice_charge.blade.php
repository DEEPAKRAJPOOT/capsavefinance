@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="InvoiceChrg"  action="{{Route('update_invoice_chrg')}}" method="post" target="_top">
    @csrf   
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $invoiceId }}">
            
            <div class="form-group">
                <label for="txtCreditPeriod">Charge Type
                    <span class="mandatory">*</span>
                </label>
                <select type="text" class="form-control" id="chrg_type" name="chrg_type"> 
                @php 

                if(!isset($chargeData->chrg_type) && $offerData->invoice_processingfee_type == 1) {
                    $typeFlag = 1;
                } else if (isset($chargeData->chrg_type) && $chargeData->chrg_type == 1 ) {
                    $typeFlag = 1;
                } else if(!isset($chargeData->chrg_type) && $offerData->invoice_processingfee_type == 2) {
                    $typeFlag = 2;
                } else if (isset($chargeData->chrg_type) && $chargeData->chrg_type == 2 ) {
                    $typeFlag = 2;
                }

                @endphp

                    <option value="1" {{ $typeFlag == 1 ? 'selected' : '' }}>Fixed</option>
                    <option value="2" {{ $typeFlag == 2 ? 'selected' : '' }}>Percentage</option>
                </select> 

            </div>
            <div class="form-group">
                <label for="txtCreditPeriod">Amount/Percentage
                    <span class="mandatory">*</span>
                </label>
                @php 

                if(!isset($chargeData->chrg_value) && $offerData->invoice_processingfee_value) {
                    $valueAmt = $offerData->invoice_processingfee_value;
                } else if (isset($chargeData->chrg_value) && $chargeData->chrg_value) {
                    $valueAmt = $chargeData->chrg_value;
                }

                @endphp
                <input type="text" class="form-control" id="chrg_value" name="chrg_value" placeholder="Enter Amount/Percentage" value="{{ (isset($valueAmt) && $valueAmt) ? $valueAmt : '' }}">

            </div>
            <div class="form-group">
                <label for="txtCreditPeriod">Processing Fee
                    <span class="mandatory">*</span>
                </label>
                <select type="text" class="form-control" id="is_active" name="is_active"> 
                    <option value="1" {{ (isset($chargeData->is_active) && $chargeData->is_active == 1)  ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ (isset($chargeData->is_active) && $chargeData->is_active == 0)  ? 'selected' : '' }}>Inactive</option>
                </select> 
            </div>
        </div>



    </div>
    <span class="model7msg error"></span>           
    <input type="submit" id="UpdateInvoiceChrg" class="btn btn-success float-right btn-sm mt-3" value="Submit"> 
</form>
 
@endsection

@section('jscript')
<script>

$(document).ready(function () {

	$('#InvoiceChrg').validate({ // initialize the plugin
		
		rules: {
			'chrg_type' : {
				required : true,
			},
			'chrg_value' : {
				required : true,
			}
		},
		messages: {
			'chrg_type': {
				required: "Charge type required.",
			},
			'chrg_value': {
				required: "Charge Value required.",
			}
		}
	});

	$('#InvoiceChrg').validate();

	$("#UpdateInvoiceChrg").click(function() {
		if($('#InvoiceChrg').valid()) {
			$('form#InvoiceChrg').submit();
			$("#UpdateInvoiceChrg").attr("disabled","disabled");
		}  
	});            

	});
</script>
@endsection