@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
	<form method="POST" action="" onsubmit="">
	<div class=" form-fields">
		<div class="form-sections">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="marginAmount">Margin Amount</label>
						<input type="text" name="" id="marginAmt" class="form-control" readonly="true">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="nonFactoredAmount">Non Factored Amount</label>
						<input type="text" name="" id="nonFactoredAmt" class="form-control" readonly="true">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group password-input">
						<label for="txtPassword">Interest Amount</label>
						<input type="text" name="" id="interestAmt" class="form-control" readonly="true">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group password-input">
						<label for="txtPassword">Comment</label>
						<textarea name="" id="" class="form-control" cols="30" rows="5"></textarea>
					</div>
				</div>
			</div>
			<div class="d-flex btn-section">
				<div class="ml-auto text-right">
					<input type="submit" value="Save" class="btn btn-success btn-sm">
				</div>
			</div>
		</div>									
	</div>
	</form>
</div>

<div class="header-title">
	<h5>Interest</h5>
</div>
	
<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
						<table id="interestRefundList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
							<thead>
								<tr role="row">
									<th>#</th>
									<th>Customer ID</th>
									<th>Trans Date</th>
									<th>Invoice No</th>
									<th>Amount</th>
									<th>Balance Amount</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody class="chechBoxContainter" id="chechBoxContainter">

							</tbody>
						</table>
					</div>
					<div id="interestRefundList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="header-title">
	<h5>Non Factored Amount</h5>
</div>
	
<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
						<table id="nonFactoredRefundList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
							<thead>
								<tr role="row">
									<th>#</th>
									<th>Customer ID</th>
									<th>Trans Date</th>
									<th>Amount</th>
									<th>Balance Amount</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
					<div id="nonFactoredRefundList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="header-title">
	<h5>Margin</h5>
</div>
	
<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
						<table id="marginList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
							<thead id="selectField">
								<tr role="row">
									<th>#</th>
									<th>Customer ID</th>
									<th>Trans Date</th>
									<th>Amount</th>
									<th>Balance Amount</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<input type="text" id="yourText">
							</tbody>
						</table>
					</div>
					<div id="marginList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	// If data send via POST, with the name "cbname"
	if(isset($_POST['trans_id'])) {
	$cbarray = $_POST['trans_id'];       // gets the value of the selected checkboxes
	print_r($cbarray);       // outputs the array with data stored in $cbarray
	}
	else {
	echo 'Select the web sites you like:';
	}
?>

@endsection

@section('jscript')
<script>

    var messages = {
		action: "{{ $action }}",
		non_factored: "{{config('lms.TRANS_TYPE.NON_FACTORED_AMT')}}",
		interest_refund: "{{config('lms.TRANS_TYPE.INTEREST_REFUND')}}",
		margin: "{{config('lms.TRANS_TYPE.MARGIN')}}",
        lms_get_refund_adjust: "{{ URL::route('lms_get_refund_adjust') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>

<script src="{{ asset('backend/js/lms/refund.js') }}" type="text/javascript"></script>
<script>
var totalAmt;
		
	function disableInput(el) {
		var value = $(el).val();

		if($(el).prop("checked") == true){
			$("input[name='settledAmount["+value+"]']").prop('disabled', false);
		}
		else if($(el).prop("checked") == false){
			$("input[name='settledAmount["+value+"]']").prop('disabled', true);
		}
		calMarginAmt();
		calNonFactoredAmt();
		calInterestAmt(value);
	}

	function calMarginAmt(){
		totalAmt = 0;
		$(".transType"+messages.margin).each(function (index, element) {

			totalAmt += parseFloat(element.val());
		
		});
		$('#marginAmt').val(totalAmt);
	}

	function calNonFactoredAmt(){
		totalAmt = 0;
		$(".transType"+messages.non_factored).each(function (index, element) {
			totalAmt += parseFloat($(element).val());
		});
		$('#nonFactoredAmt').val(totalAmt);
	}

	function calInterestAmt(value){	
		totalAmt = 0;
		$(".transType"+messages.interest_refund).each(function (index, element) {

			let elName = $(element).attr('transId');

			if($("#"+elName).prop("checked") == true){

				totalAmt += parseFloat($(element).val());
			}

		});
		
		$('#interestAmt').val(totalAmt);
		
	}
	$(document).ajaxSuccess(function(event,request,settings){
		console.log('success')
		jQuery(document).ready(function ($) {
			disableInput();
		});
	});
	

</script>
@endsection