@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
 	<form id="documetsForm" name="documetsForm" method="POST" action="{{route('save_documents')}}" target="_top">
			  @csrf

	  	<div class="row">
			<div class="form-group col-12">
			  <label for="chrg_name"><strong>Document Name</strong></label>
			  <input type="text" class="form-control" id="doc_name" name="doc_name" placeholder="Enter Document Name" maxlength="250">
			</div>
			
	  	</div>
	  	<div class="row">
		  	<div class="col-6 form-group">
				<label for="txtCreditPeriod"><strong>Product Type</strong>
					<span class="mandatory">*</span></label>  <br>
				 	<select class="form-control multi-select-checkbox clsRequired mt-2" name="product_ids[]" multiple="multiple">
				 	@foreach($products as $product)
				 		<option value="{{ $product->id }}" {{ ($product->id == 1) ? 'selected' : '' }}>{{ $product->product_name }}</option>
			 		@endforeach
				 	</select>
				                          
			</div>
			<div class="form-group col-6">
				 <label for="chrg_type"><strong>Is RCU</strong></label><br />
				 <div class="form-check-inline ">
				   <label class="form-check-label fnt">
				   <input type="radio" class="form-check-input" checked name="is_rcu" value="1">Enabled
				   </label>
				</div>
				<div class="form-check-inline">
				   <label class="form-check-label fnt">
				   <input type="radio" class="form-check-input" name="is_rcu" value="2">Disabled
				   </label>
				</div>
			</div>
		</div> 
		<div class="row">
		 	<div class="form-group col-6">
				 <label for="chrg_type"><strong>Document Stage</strong></label>
				 <select class="form-control" name="doc_type_id" id="doc_type_id">
					  <option value="" selected>Select</option>
					  <option value="1">On-boarding</option>
					  <option value="2">Pre-Sanction</option>
					  <option value="3">Post-Sanction</option>
				  </select>
			</div>
			<div class="form-group col-6">
				 <label for="chrg_type"><strong>Status</strong></label><br />
				 <select class="form-control" name="is_active" id="is_active">
					  <option value="" selected>Select</option>
					  <option value="1">Active</option>
					  <option value="2">In-Active</option>
				  </select>
			</div>
		</div>
	  <div class="row">
		 <div class="form-group col-md-12 text-right">
			 <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit"/>
		</div>
	  </div>
   </form>
</div>
@endsection
@section('additional_css')
<link rel="stylesheet" href="{{ url('backend/assets/css/bootstrap-multiselect.css') }}" />
@endsection
@section('jscript')

<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>

<script type="text/javascript">

  	$('.multi-select-checkbox').multiselect({
        maxHeight: 400,
        enableFiltering: false,
        selectAll: true,
    });

	$(document).ready(function () {
		$('#documetsForm').validate({ // initialize the plugin
			rules: {
				'doc_name' : {
					required : true,
				},
				'is_rcu' : {
					required : true,
				},
				'doc_type_id' : {
					required : true,
				},
				'is_active' : {
					required : true,
				},
			},
			messages: {
				'doc_name': {
					required: "Please enter document name.",
				},
				'is_rcu': {
					required: "Please enter document",
				},
				'doc_type_id': {
					required: "Please select document stage",
				},
				'is_active': {
					required: "Please select status",
				},
			}
		});
	});
</script>
@endsection