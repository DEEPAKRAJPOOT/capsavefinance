@extends('layouts.popup_layout')
@section('content')
<form style="width: 100%" method="POST" action="{{ Route('backend_add_nach_detail') }}" enctype="multipart/form-data" target="_top" id="submitForm">
	@csrf
	<div class="modal-body text-left">
		<div class="row">

			<div class="col-12">
			   <div class="form-group">
				  <label for="bank">Select Customer</label>
				  <select class="form-control" name="customer_id" id="customer_id">
					 <option selected diabled value=''>Select Bank</option>
						@foreach($users as $key => $value)
						  <option value="{{ $value->user_id }}">{{ $value->f_name.' '.$value->l_name }} ( {{ $value->lms_user->customer_id }} )</option>
						@endforeach
				  </select>
			   </div>
			</div>
			<div class="col-12">
			   <div class="form-group">
				  <label for="bank">Select Bank</label>
				  <select class="form-control" name="bank_account_id" id="bank_account_id">
					 <option selected diabled value=''>Select Bank</option>
						
				  </select>
			   </div>
			</div>
		</div>

		<button type="submit" class="btn btn-success float-right btn-sm" id="savedocument" >Submit</button>  
	</div>
</form>
 
@endsection

@section('jscript')

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript"> 
var messages = {
	token: "{{ csrf_token() }}",
	backend_ajax_nach_user: "{{ URL::route('backend_ajax_nach_user') }}",
		  
   };
 $(document).ready(function () {
  /////// jquery validate on submit button/////////////////////
	$('#submitForm').validate({ // initialize the plugin
			
		rules: {
			'customer_id': {
				required: true,
			},
			'bank_account_id': {
				required: true,
			}
		},
		messages: {
			'customer_id': {
				required: "Please select customer",
			},
			'bank_account_id': {
				required: "Please select Bank",
			}
		}
	});

	$('#submitForm').validate();

	$("#savedocument").click(function(){
		if($('#submitForm').valid()){
			$('form#submitForm').submit();
			$("#savedocument").attr("disabled","disabled");
		}  
	});       
  
  //////////////////// onchange anchor  id get data /////////////////
  	$(document).on('change','#customer_id',function(){
	  	var customer_id =  $(this).val(); 
	  	$("#bank_account_id").empty();
	  	var postData =  ({'customer_id':customer_id, '_token':messages.token});
	   	jQuery.ajax({
		url: messages.backend_ajax_nach_user,
				method: 'post',
				dataType: 'json',
				data: postData,
				error: function (xhr, status, errorThrown) {
				alert(errorThrown);
				
				},
				success: function (data) {
					if(data.status==1)
					{
						console.log(data.BankList);
						var obj1  = data.BankList;
					 
						if(obj1.length > 0)
						{
							$(obj1).each(function(i,v){
								$("#bank_account_id").append("<option value='"+v.bank_account_id+"'>"+v.acc_no+" ("+v.acc_name+")</option>");  
							});
						}
						else
						{
							$("#bank_account_id").append("<option value=''>No data found</option>");  
						}
					}
					else
					{
						console.log("some error occured.");
					}
				  
				}
		});  
  	});
});
  </script>
@endsection
 