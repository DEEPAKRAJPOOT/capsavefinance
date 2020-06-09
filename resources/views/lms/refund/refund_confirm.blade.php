@extends('layouts.backend.admin_popup_layout')
@section('content')
@php 
$finalDisburseAmt = 0;
@endphp
@foreach($data as $customer)

	@php 
		$finalDisburseAmt += round($customer->refund_amount, 2);
		$cust[$customer->payment->user_id] = 1;
	@endphp


@endforeach
@php
	$totalCustomer = count($cust);
@endphp
<div class="row">
	<div class="col-12 row">

		<div class="col-4">
			<div class="form-group">
				<label for="marginAmount"># No of Cust.</label>
				<input type="text" name="" class="form-control" readonly="true" value="{{ $totalCustomer }}">
			</div>
		</div>
		<div class="col-4">
			<div class="form-group">
				<label for="nonFactoredAmount"># Amount Disburse</label>
				<input type="text" name="" id="nonFactoredAmt" class="form-control" readonly="true" value="{{ $finalDisburseAmt }}">
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-6">
		<form id="manualDisburse" method="POST" action="{{ Route('refund_offline') }}" target="_top">
			<input type="hidden" value="{{ $transIds }}" name="transaction_ids" id="transaction_ids">
			@csrf
			<div class="col-6">
				<div class="form-group">
					<label for="txtCreditPeriod">Disburse Date <span class="error_message_label">*</span> </label>
					<input type="text" id="disburse_date" name="disburse_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate" required="">
					 @if(Session::has('error'))
					 <div class="error">{{ Session::get('error') }}</div>
					  
					@endif
				</div>
			</div>
			<div class="col-6">
				<input type="submit" id="submitManualDisburse" value="Disburse Offline" class="btn btn-success btn-sm ml-2">
			</div>
		</form>
	</div>
	<div class="col-6 right">
		<form id="onlineDisburse" method="POST" action="{{ Route('refund_online') }}" target="_top">
			<input type="hidden" value="{{ $transIds }}" name="transaction_ids">
			@csrf
			<div class="col-6">
				<!-- <div class="form-group">
					<label for="txtCreditPeriod">Value Date <span class="error_message_label">*</span> </label>
					<input type="text" id="value_date" name="value_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate" required="">
					 @if(Session::has('error'))
					 <div class="error">{{ Session::get('error') }}</div>
					  
					@endif
				</div> -->
			</div>
			<div class="col-6">
				<input type="submit" id="submitOnlineDisburse" value="Disburse Online" class="btn btn-success btn-sm ml-2">
			</div>
		</form>
	</div>
</div>
@endsection
@section('jscript')

<script type="text/javascript">
	$('.datepicker-dis-fdate').datetimepicker({
        useCurrent:true,
        format: 'yyyy-mm-dd',
        autoclose: true,
        minView: 2, 
        defaultDate:new Date(),
    })
    $(document).ready(function () {
        $('#manualDisburse').validate({ // initialize the plugin
            
            rules: {
                'disburse_date' : {
                    required : true,
                }
            },
            messages: {
                'disburse_date': {
                    required: "Disburse date is required.",
                }
            }
        });

        $('#manualDisburse').validate();

        $("#submitManualDisburse").click(function(){
            if($('#manualDisburse').valid()){
                $('form#manualDisburse').submit();
                $("#submitManualDisburse").attr("disabled","disabled");
            }  
        });            

    });
</script>
@endsection