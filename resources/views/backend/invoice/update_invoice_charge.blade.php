@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="InvoiceChrg"  action="{{Route('update_invoice_chrg')}}" method="post" target="_top">
    @csrf   
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $invoiceId }}">
            <input type="hidden" name="invoice_gst_chrg_value" id="invoice_gst_chrg_value" value="{{ $gstChrgValue ?? ''}}">
            <table class="table table-striped cell-border no-footer"  cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                <tbody> 
                    <tr>
                        <td><b>Invoice Approved Amount:</b></td>
                        <td>{{ number_format($invoiceData->invoice_approve_amount, 2) }}</td>
                        <td><b>Margin(%):</b></td>
                        <td>{{ number_format($marginAmt,2)}}</td>
                    </tr>
                    <tr>
                        <td><b>Principle Amount:</b></td>
                        <td>{{ number_format(($invoiceData->invoice_approve_amount - $marginAmt), 2) }}</td>
                        <td><b>GST Percentage:</b></td>
                        <td>{{ $getPercentage['tax_value'] ?? ''}}</td>
                    </tr>
                </tbody>
            </table>
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

                    <option value="1" {{ isset($typeFlag) && $typeFlag == 1 ? 'selected' : '' }}>Fixed</option>
                    <option value="2" {{ isset($typeFlag) && $typeFlag == 2 ? 'selected' : '' }}>Percentage</option>
                </select> 

            </div>
            <div class="form-group">
                <label for="txtCreditPeriod">Charge Amount/Percentage
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
                <label for="txtCreditPeriod">Processing Fee Amount
                    <span class="mandatory">*</span>
                </label>
                <input type="text" class="form-control" id="processing_fee_amount" name="processing_fee_amount" placeholder="GST Charge Amount" value="{{ $processingFee ?? ''}}" disabled="">

            </div>
            @if($chrgData->is_gst_applicable == 1)
            <div class="form-group">
                <label for="txtCreditPeriod">Processing Fee with GST
                    <span class="mandatory">*</span>
                </label>
                <input type="text" class="form-control" id="gst_chrg_value" name="gst_chrg_value" placeholder="GST Charge Amount" value="{{ $gstChrgValue ?? ''}}" disabled="">

            </div>
            @endif
            {{--
            <div class="form-group">
                <label for="txtCreditPeriod">Processing Fee
                    <span class="mandatory">*</span>
                </label>
                <select type="text" class="form-control" id="is_active" name="is_active"> 
                    <option value="1" {{ (isset($chargeData->is_active) && $chargeData->is_active == 1)  ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ (isset($chargeData->is_active) && $chargeData->is_active == 0)  ? 'selected' : '' }}>Inactive</option>
                </select> 
            </div>
            --}}
        </div>



    </div>
    <span class="model7msg error"></span>           
    <input type="submit" id="UpdateInvoiceChrg" class="btn btn-success float-right btn-sm mt-3" value="Submit"> 
</form>
 
@endsection

@section('jscript')
<script>
 var messages = {
        change_gst_amount: "{{ URL::route('backend_get_invoice_processing_gst_amount') }}",
        token: "{{ csrf_token() }}",
    };


   
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
$('#chrg_value').change(function () {
    var chrg_value = $(this).val();
    var chrg_type = $('#chrg_type').val();
    var invoice_id = $('#invoice_id').val();
    var postData = ({'chrg_value':chrg_value,'chrg_type':chrg_type,'invoice_id': invoice_id, '_token': messages.token});

    jQuery.ajax({
        url: messages.change_gst_amount,
        method: 'post',
        dataType: 'json',
        data: postData,
        error: function (xhr, status, errorThrown) {
            alert(errorThrown);
        },
        success: function (data) {
            // console.log(data.gstChrgValue);
            $('#gst_chrg_value').val(data.gstChrgValue);
            $('#invoice_gst_chrg_value').val(data.gstChrgValue);
            $('#processing_fee_amount').val(data.processingFee);
        }
    });
});

</script>
@endsection