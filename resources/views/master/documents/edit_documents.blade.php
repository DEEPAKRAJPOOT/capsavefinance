@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
	<form id="documetsForm" name="documetsForm" method="POST" action="{{route('save_documents')}}" target="_top">
		@csrf
		<input type="hidden" name="filter_product_type" id="filter_product_type" value="">
		<input type="hidden" name="filter_doc_type_id" id ="filter_doc_type_id" value="">
		<input type="hidden" name="filter_search_keyword" id ="filter_search_keyword" value="">
		<div class="row">
			<div class="form-group col-12">
				<label for="chrg_name">Document Name</label>
				<input type="text" class="form-control" id="doc_name" name="doc_name" value="{{$document_data->doc_name}}" placeholder="Enter Document Name" maxlength="50">
				<input type="hidden" class="form-control id" name="id" maxlength="5" value="{{$document_data->id}}">
				<span class="doc-error"></span>
			</div>
		</div>
		<div class="row">
			<div class="col-6 form-group">
				<label for="txtCreditPeriod">Product Type
				<span class="mandatory">*</span>
				<select class="form-control multi-select-checkbox clsRequired" name="product_ids[]" multiple="multiple">
					@foreach($products as $product)
						<option value="{{ $product->id }}" {{ (in_array($product->id, $documentProductIds)) ? 'selected' : '' }}>{{ $product->product_name }}</option>
					@endforeach
				</select>
				</label>                            
			</div>
			<div class="form-group col-6">
				 <label for="chrg_type">Is RCU</label><br />
				 <div class="form-check-inline ">
					 <label class="form-check-label fnt">
					 <input type="radio" class="form-check-input" {{$document_data->is_rcu == 1 ? 'checked' : ($document_data->is_rcu != 2 ? 'checked' : '' )}} name="is_rcu" value="1">Enabled
					 </label>
				</div>
				<div class="form-check-inline">
					 <label class="form-check-label fnt">
					 <input type="radio" class="form-check-input" {{$document_data->is_rcu == 2 ? 'checked' : ''}} name="is_rcu" value="2">Disabled
					 </label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-6">
				<label for="chrg_type">Doc Type</label>
				<select class="form-control" name="doc_type_id" id="doc_type_id">
					<option value="" selected>Select</option>
					<option {{$document_data->doc_type_id == 1 ? 'selected' : ''}} value="1">On-boarding</option>
					<option {{$document_data->doc_type_id == 2 ? 'selected' : ''}} value="2">Pre-Sanction</option>
					<option {{$document_data->doc_type_id == 3 ? 'selected' : ''}} value="3">Post-Sanction</option>
                                        <option {{$document_data->doc_type_id == 4 ? 'selected' : ''}} value="4">Pre-Offer</option>
				</select>
			</div>
			<div class="form-group col-6">
				<label for="chrg_type">Status</label><br />
				<select class="form-control" name="is_active" id="is_active">
					<option value="" selected>Select</option>
					 <option {{$document_data->is_active == 1 ? 'selected' : ''}} value="1">Active</option>
					<option {{$document_data->is_active == 2 ? 'selected' : ''}} value="2">In-Active</option>
				</select>
			</div>
		</div>
	<div class="row">
		 <div class="form-group col-12 text-right">
				 <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit"/>
		</div>
	</div>
	</form>
</div>
@endsection
@section('jscript')
@section('additional_css')
<link rel="stylesheet" href="{{ url('backend/assets/css/bootstrap-multiselect.css') }}" />
@endsection
@section('jscript')

<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>

<script type="text/javascript">

    var messages = {
        //get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };

		$(document).ready(function(){
				$('#filter_product_type').val(parent.$('#product_type').val());
				$('#filter_doc_type_id').val(parent.$('#doc_type_id').val());
				$('#filter_search_keyword').val(parent.$('#search_keyword').val());
		});

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
						required: "Please enter Document Name",
					},
					'is_rcu': {
						required: "Please enter Document Description",
					},
					'doc_type_id': {
						required: "Please select Document type",
					},
					'is_active': {
						required: "Please Select Status of Document",
					},
				}
			});
		});

	$(document).ready(function () {
	// Check GSTIN is already present or not
		$("#doc_name").on('keypress change blur keyup keydown', function() {
			var doc_name = $(this).val();
			var id = $('.id').val();
			$.ajax({
				type: 'post',
				url: "{{url('check_document_name_exist_edit_ajax')}}",
				data: { 
					doc_name: doc_name,
					id: id,
					_token : messages.token,
				},
				success: function(resp) {
					if(resp == 'false') {
						$('.doc-error').html('<font style="color:red; font-weight: 900;">Document already present.</font>')
					} else {
						$('.doc-error').html('')
					}               
				},
				error: function() {
					alert('Error');
				}
			});
		});		
	});
</script>
@endsection