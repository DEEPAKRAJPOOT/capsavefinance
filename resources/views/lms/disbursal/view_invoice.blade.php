@extends('layouts.backend.admin_popup_layout')
@section('content')

	<form method="POST" style="width:100%;" action="{{route('save_assign_rcu')}}" target="_top" onsubmit="return checkValidation();">
		@csrf
		<input type="hidden" value="" name="document_ids" id="document_ids">
		<input type="hidden" value="{{request()->get('user_id')}}" name="user_id">
		@if($userIvoices->count() != 0)
		<div class="row">
			<div id="collapseOne" class="card-body bdr pt-2 pb-2 collapse show" data-parent="#accordion" style="">
				@foreach($userIvoices as $invoice)
				<ul class=" p-0 m-0 d-flex justify-content-between">
					<li><input type="checkbox" class="invoice_id" value="{{ $invoice->invoice_id }}"></li>
					<li>Invoice No. <br> <i class="fa fa-inr"></i> <b>{{ $invoice->invoice_id }}</b></li>
					<li>Invoice Date <br> <b>{{ $invoice->invoice_date }}</b></li>
					<li>Invoice Due Date <br> <b>{{ $invoice->invoice_due_date }}</b></li>
					<li>Invoice Amt. <br> <i class="fa fa-inr"></i><b>{{ $invoice->invoice_approve_amount }}</b></li>
					<li>Margin(%). <br> <i class="fa fa-inr"></i><b>{{ $invoice->app->acceptedOffer->margin }}</b></li>
					<li>Funded Amt. <br> <i class="fa fa-inr"></i><b>
					{{ $invoice->invoice_approve_amount - (($invoice->invoice_approve_amount*$invoice->app->acceptedOffer->margin)/100) }}
					</b></li>
					<li>Disburse Amt. <br> <i class="fa fa-inr"></i><b>
					{{ $invoice->invoice_approve_amount - (($invoice->invoice_approve_amount*$invoice->app->acceptedOffer->margin)/100) - (($invoice->invoice_approve_amount*$invoice->app->acceptedOffer->interest_rate)/100) }}
					</b></li>
					<li>Status  <br> <span class="badge badge-warning">{{ $invoice->mstStatus->status_name }}</span></li>
				</ul>
				<hr>
				@endforeach	
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
			</div>
		</div>
		@else 
		 <thead class="thead-primary">
	        <tr>
	            <th class="text-left" colspan="4" width="10%">No invoice found.</th>
	        </tr>
	    </thead>
		@endif   
	</form>
 
@endsection

@section('jscript')
<script>
$(document).ready(function(){
	$('.invoice_id').on('click', function() {
		let current_inv_ids = parent.$('#invoice_ids').val();
		let current_id = $(this).val();
		if($(this).is(':checked')){
			parent.$('#invoice_ids').val(current_inv_ids+','+current_id);
        }else{
            alert('First check at least one checkbox.');
        }
		/*let invoice_ids = $('#invoice_ids').val();
		let all_ids = invoice_ids+'#'+current_id;
		$('#invoice_ids').val(all_ids);*/
	})
	
});

function checkValidation(){
		unsetError('select[name=agency_id]');
		unsetError('select[name=to_id]');
		//unsetError('textarea[name=comment]');

		let flag = true;
		let agency_id = $('select[name=agency_id]').val();
		let to_id = $('select[name=to_id]').val();
		//let comment = $('textarea[name=comment]').val().trim();

		if(agency_id == ''){
				setError('select[name=agency_id]', 'Plese select Agency');
				flag = false;
		}

		if(to_id == ''){
				setError('select[name=to_id]', 'Plese select User');
				flag = false;
		}

		if(flag){
				return true;
		}else{
				return false;
		}
}

function fillAgencyUser(agency_id){
	let html = '<option value="">Select User</option>';
	$.each(users, function(i,user){
		if(agency_id == user.agency_id)
			html += '<option value="'+user.user_id+'">'+user.f_name+' '+user.l_name+'</option>';
	});
	$('#to_id').html(html);
}
</script>
@endsection