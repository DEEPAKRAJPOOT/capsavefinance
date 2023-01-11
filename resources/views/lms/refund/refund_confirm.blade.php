@extends('layouts.backend.admin_popup_layout')
@section('content')
@php 
	$finalDisburseAmt = 0;
	$cust = [];
	if(isset($data)){
		foreach($data as $customer){
			$finalDisburseAmt += round($customer->refund_amount, 2);
			$cust[$customer->payment->user_id] = 1;
		}
	}
	$totalCustomer = count($cust);
@endphp
<form id="manualDisburse" method="POST" action="{{ Route('refund_offline') }}" target="_top">
<div class="row">
		<input type="hidden" value="{{ $transIds ?? '' }}" name="transaction_ids" id="transaction_ids">
		@csrf
		<div class="col-4">
			<div class="form-group">
				<label for="marginAmount"># No of Cust.</label>
				<input type="text" name="" class="form-control" readonly="true" value="{{ $totalCustomer }}">
			</div>
		</div>
		<div class="col-4">
			<div class="form-group">
				<label for="nonFactoredAmount"># Amount Refund</label>
				<input type="text" name="" id="nonFactoredAmt" class="form-control" readonly="true" value="{{ $finalDisburseAmt }}">
			</div>
		</div>
		<div class="col-4">
			<div class="form-group">
				<label for="nonFactoredAmount">Refund Date</label>
				<input type="text" id="disburse_date" name="disburse_date" class="form-control date_of_birth datepicker-dis-fdate" required="">
			</div>
		</div>
		@if(Session::has('error'))<div class="error">{{ Session::get('error') }}</div>@endif
	</div>
	<div class="row">
		<div class="col-12">
			<div class="form-group">
				<input type="submit" id="submitManualDisburse" value="Refund Offline" class="btn btn-success btn-sm ml-2">
				<button class="btn btn-secondary btn-sm ml-2" disabled>Disbursed Online</button>
				{{-- <input type="submit" id="submitOnlineDisburse" value="Disburse Online" class="btn btn-success btn-sm ml-2"> --}}
			</div>
		</div>
	</div>
</form>
@endsection
@section('jscript')
<script type="text/javascript">
	$('.datepicker-dis-fdate').datetimepicker({
        useCurrent:true,
        format: 'dd-mm-yyyy',
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
                    required: "Refund date is required.",
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