@extends('layouts.guest')
@section('content')



<div class="step-form pt-5">
	<div class="container">
		<ul id="progressbar">
			<li class="count-active">
				<div class="count-heading">Business Information </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('frontend/assets/images/business-information.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('frontend/assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading"> Promoter Details </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('frontend/assets/images/kyc.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('frontend/assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading">KYC </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('frontend/assets/images/business-document.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('frontend/assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
		</ul>
	</div>

	<div class="container">
		<div class="mt-4">
			<div class="form-heading p-3">
				<h2>Business Information
					<small> ( Please fill the Business Information )

					</small>
				</h2>
			</div>
			<div class="col-md-12 form-design ">
				<div id="reg-box">
					<form id="business_information_form" method="POST" action="business-information-save" onsubmit="return checkValidation();">
						@csrf
						<input type="hidden" name="biz_cin" value="">
						<input type="hidden" name="pan_api_res" value="">
						<div class=" form-fields">
							<div class="form-sections">
								<div class="col-md-12">
									<h3>Business Details</h3>
								</div>
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group INR">
												<label for="txtCreditPeriod">Applied Loan Amount
													<span class="mandatory">*</span>
												</label>
												<a href="javascript:void(0);" class="verify-owner-no">INR</a>
												<input type="text" name="loan_amount" value="{{old('loan_amount')}}" class="form-control" placeholder="Enter Applied Loan Amount">
												@error('loan_amount')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtSupplierName">Tranche Tenor (Days)
												</label>
												<input type="number" name="tenor_days" value="{{old('tenor_days')}}" class="form-control" tabindex="3" placeholder="Enter Tranche Tenor (1 - 120)">
												@error('tenor_days')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Entity Name
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="biz_entity_name" value="{{old('biz_entity_name')}}" class="form-control" tabindex="1" placeholder="Enter Entity Name">
												@error('biz_entity_name')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Date of Incorporation
													<span class="mandatory">*</span>
												</label>
												<input type="date" name="incorporation_date" value="{{old('incorporation_date')}}" class="form-control" tabindex="1">
												@error('incorporation_date')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Business Constitution
													<span class="mandatory">*</span>
												</label>
												<select class="form-control" name="biz_constitution">
													<option value=""> Select Business Constitution</option>
													<option value="1" {{(old('biz_constitution') == 1)? 'selected':''}}> Business Constitution 1 </option>
													<option value="2" {{(old('biz_constitution') == 2)? 'selected':''}}> Business Constitution 2 </option>
													<option value="3" {{(old('biz_constitution') == 3)? 'selected':''}}> Business Constitution 3 </option>
												</select>
												@error('biz_constitution')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input INR">
												<label for="txtPassword">Business Turnover
												</label> <a href="javascript:void(0);" class="verify-owner-no">INR</a>
												<input type="text" name="biz_turnover" value="{{old('biz_turnover')}}" class="form-control" tabindex="1" placeholder="Enter Business Turnover">
												@error('biz_turnover')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Company PAN
													<span class="mandatory">*</span>
												</label>
												<a href="javascript:void(0);" class="verify-owner-no pan-verify">Verify</a>
												<input type="text" name="biz_pan_number" value="{{old('biz_pan_number')}}" class="form-control" tabindex="1" placeholder="Enter Company PAN">
												@error('biz_pan_number')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">GST Number
													<span class="mandatory">*</span>
												</label>
												<!-- <a href="javascript:void(0);" class="verify-owner-no gst-verify">Verify</a> -->
												<select class="form-control" name="biz_gst_number" onchange="fillEntity(this.value)">
												</select>
												<!-- <input type="text" name="biz_gst_number" value="{{old('biz_gst_number')}}" class="form-control" tabindex="1" placeholder="Enter GST Number"> -->
												@error('biz_gst_number')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Nature of Business
													<span class="mandatory">*</span>
												</label>
												<select class="form-control" name="entity_type_id">
													<option value=""> Select Nature of Business</option>
													<option value="1" {{(old('entity_type_id') == 1)? 'selected':''}}> Nature of Business 1 </option>
													<option value="2" {{(old('entity_type_id') == 2)? 'selected':''}}> Nature of Business 2 </option>
													<option value="3" {{(old('entity_type_id') == 3)? 'selected':''}}> Nature of Business 3 </option>
												</select>
												@error('entity_type_id')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Industry
													<span class="mandatory">*</span>
												</label>
												<select class="form-control" name="biz_type_id">
													<option value=""> Select Industry</option>
													<option value="1" {{(old('biz_type_id') == 1)? 'selected':''}}> Industry 1 </option>
													<option value="2" {{(old('biz_type_id') == 2)? 'selected':''}}> Industry 2 </option>
													<option value="3" {{(old('biz_type_id') == 3)? 'selected':''}}> Industry 3 </option>
												</select>
												@error('biz_type_id')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Segment
													<span class="mandatory">*</span>
												</label>
												<select class="form-control" name="segment">
													<option value=""> Select Segment</option>
													<option value="1" {{(old('segment') == 1)? 'selected':''}}> Segment 1 </option>
													<option value="2" {{(old('segment') == 2)? 'selected':''}}> Segment 2 </option>
													<option value="3" {{(old('segment') == 3)? 'selected':''}}> Segment 3 </option>
												</select>
												@error('segment')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
											</div>
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="form-sections">
								<div class="row">
									<div class="col-md-6">
										<div class="col-md-12">
											<h3>Business Address
											</h3>
										</div>
										<div class="col-md-12 address-block">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="txtCreditPeriod">Address
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="biz_address" value="{{old('biz_address')}}" class="form-control" placeholder="Enter Your Address">
														@error('biz_address')
						                                    <span class="text-danger error">{{ $message }}</span>
						                                @enderror
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="txtEmail">City
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="biz_city" value="{{old('biz_city')}}" class="form-control" tabindex="1" placeholder="Enter City Name">
														@error('biz_city')
						                                    <span class="text-danger error">{{ $message }}</span>
						                                @enderror
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group password-input">
														<label for="txtPassword">State
															<span class="mandatory">*</span>
														</label>
														<select class="form-control" name="biz_state">
															<option value=""> Select State</option>
															<option value="1" {{(old('biz_state') == 1)? 'selected':''}}> State 1 </option>
															<option value="2" {{(old('biz_state') == 2)? 'selected':''}}> State 2 </option>
															<option value="3" {{(old('biz_state') == 3)? 'selected':''}}> State 3 </option>
														</select>
														@error('biz_state')
						                                    <span class="text-danger error">{{ $message }}</span>
						                                @enderror
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group password-input">
														<label for="txtPassword">Pin Code
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="biz_pin" value="{{old('biz_pin')}}" class="form-control" tabindex="1" placeholder="Enter Pin Code">
														@error('biz_pin')
						                                    <span class="text-danger error">{{ $message }}</span>
						                                @enderror
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="col-md-12 ">
											<h3 class="full-width">Correspondence Address
												<div class="sameas"><input type="checkbox" name="address_same" onchange="copyAddress()"> <span> Same as Business Address</span></div>
											</h3>
										</div>
										<div class="col-md-12 copy-address-block">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="txtCreditPeriod">Address
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="biz_corres_address" value="{{old('biz_corres_address')}}" class="form-control" placeholder="Enter Your Address">
														@error('biz_corres_address')
						                                    <span class="text-danger error">{{ $message }}</span>
						                                @enderror
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="txtEmail">City
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="biz_corres_city" value="{{old('biz_corres_city')}}" class="form-control" tabindex="1" placeholder="Enter City Name">
														@error('biz_corres_city')
						                                    <span class="text-danger error">{{ $message }}</span>
						                                @enderror
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group password-input">
														<label for="txtPassword">State
															<span class="mandatory">*</span>
														</label>
														<select class="form-control" name="biz_corres_state">
															<option value=""> Select State</option>
															<option value="1" {{(old('biz_corres_state') == 1)? 'selected':''}}> State 1 </option>
															<option value="2" {{(old('biz_corres_state') == 2)? 'selected':''}}> State 2 </option>
															<option value="3" {{(old('biz_corres_state') == 3)? 'selected':''}}> State 3 </option>
														</select>
														@error('biz_corres_state')
						                                    <span class="text-danger error">{{ $message }}</span>
						                                @enderror
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group password-input">
														<label for="txtPassword">Pin Code
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="biz_corres_pin" value="{{old('biz_corres_pin')}}" class="form-control" tabindex="1" placeholder="Enter Pin Code">
														@error('biz_corres_pin')
						                                    <span class="text-danger error">{{ $message }}</span>
						                                @enderror
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="d-flex btn-section ">
									<div class="col-md-4 ml-auto text-right">
										<input type="submit" value="Save and Continue" class="btn btn-primary">
									</div>
								</div>

							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
	function copyAddress(){
		if($('input[name=address_same]').is(':checked')){
			$('.copy-address-block input[name=biz_corres_address]').val($('.address-block input[name=biz_address]').val());
			$('.copy-address-block input[name=biz_corres_city]').val($('.address-block input[name=biz_city]').val());
			$('.copy-address-block select[name=biz_corres_state]').val($('.address-block select[name=biz_state]').val());
			$('.copy-address-block input[name=biz_corres_pin]').val($('.address-block input[name=biz_pin]').val());
		}else{
			$('.copy-address-block input[name=biz_corres_address]').val('');
			$('.copy-address-block input[name=biz_corres_city]').val('');
			$('.copy-address-block select[name=biz_corres_state]').val('');
			$('.copy-address-block input[name=biz_corres_pin]').val('');
		}
	}

	$(document).ready(function(){
		$('.pan-verify').on('click',function(){
			let pan_no = $('input[name=biz_pan_number]').val().trim();
			if(pan_no.length != 10){
				$('input[name=biz_pan_number] +span').remove();
				$('input[name=biz_pan_number]').after('<span class="text-danger error">Enter valid PAN Number</span>');
				return false;
			}
			$('.isloader').show();
			$.ajax({
				url: "https://gst.karza.in/uat/v1/search",
				type: "POST",
				data: JSON.stringify({"consent": "Y","pan": pan_no}),
				dataType:'json',
				headers:{"Content-Type": "application/json", "x-karza-key": "h3JOdjfOvay7J8SF"},
				error:function (xhr, status, errorThrown) {
					$('.isloader').hide();
        			alert(errorThrown);
    			},
				success: function(res){
				    if(res['statusCode'] == 101){
				    	$('.pan-verify').text('Verified');
				    	$('.pan-verify').css('pointer-events','none');
				    	$('input[name=biz_pan_number]').attr('readonly',true);
				    	$('input[name=biz_pan_number] +span').remove();
				    	fillGSTinput(res.result);
				    }else{
				    	alert('No GST associated with the entered PAN.');
				    }
				    $('.isloader').hide();
				  }
			});
		})

		/*$('.gst-verify').on('click',function(){
			let gst_no = $('input[name=biz_gst_number]').val().trim();
				$('input[name=biz_gst_number] +span').remove()
			if(gst_no.length != 15){
				$('input[name=biz_gst_number]').after('<span class="text-danger error">Enter valid GST Number</span>');
				return false;
			}
			$.ajax({
				url: "https://gst.karza.in/uat/v1/gstdetailed",
				type: "POST",
				data: JSON.stringify({"consent": "Y","gstin": gst_no}),
				dataType:'json',
				headers:{"Content-Type": "application/json", "x-karza-key": "h3JOdjfOvay7J8SF"},
				error:function (xhr, status, errorThrown) {
        			alert(errorThrown);
    			},
				success: function(res){
				    if(res['status-code'] == 101){
				    	$('.gst-verify').text('Verified');
				    	$('.gst-verify').css('pointer-events','none');
				    	$('input[name=biz_gst_number]').attr('readonly',true);
				    	$('input[name=biz_gst_number] +span').remove();
				    }else{
				    	alert('Something went wrong, Try again later');
				    }
				}
			});
		})*/
	})

	function fillGSTinput(datas){
		let res ='';
		let option_html = '<option val="">Select GST Number</option>';
		$(datas).each(function(i,data){
			if(data.authStatus == 'Active'){
				res += data.gstinId+',';
				option_html += '<option val="'+data.gstinId+'">'+data.gstinId+'</option>';
			}
		})
		$('select[name=biz_gst_number]').html(option_html);
		$('input[name=pan_api_res]').val(res);
		//$('#business_information_form input[type=submit]').prop("disabled", false);
	}

	function fillEntity(gstinId){
		$('.isloader').show();
		$.ajax({
				url: "https://gst.karza.in/uat/v1/gst-verification",
				type: "POST",
				data: JSON.stringify({"consent": "Y","gstin": gstinId}),
				dataType:'json',
				headers:{"Content-Type": "application/json", "x-karza-key": "h3JOdjfOvay7J8SF"},
				error:function (xhr, status, errorThrown) {
					$('.isloader').hide();
        			alert(errorThrown);
    			},
				success: function(res){
				    if(res['statusCode'] == 101){
				    	$('input[name=biz_entity_name]').val(res.result.lgnm);
				    	getCIN(res.result.lgnm);
				    	//for cin number https://testapi.karza.in/v2/compsearch-lite
				    	// $('.gst-verify').text('Verified');
				    	// $('.gst-verify').css('pointer-events','none');
				    	// $('input[name=biz_gst_number]').attr('readonly',true);
				    	// $('input[name=biz_gst_number] +span').remove();
				    }else{
				    	alert('Something went wrong, Try again later');
				    }
				    $('.isloader').hide();
				}
			});
	}

	function getCIN(entityName){
		$.ajax({
			url: "https://testapi.karza.in/v2/compsearch-lite",
			type: "POST",
			data: JSON.stringify({"consent": "Y","companyName": entityName}),
			dataType:'json',
			headers:{"Content-Type": "application/json", "x-karza-key": "h3JOdjfOvay7J8SF"},
			error:function (xhr, status, errorThrown) {
    			alert(errorThrown);
			},
			success: function(res){
			    if(res['status-code'] == 101){
			    	$('input[name=biz_cin]').val(res.result[0].cin);
			    }else{
			    	alert('Something went wrong, Try again later');
			    }
			}
		});
	}

	function checkValidation(){
		if($('.pan-verify').text() == 'Verify' || $('biz_cin').val() == ''){
			alert('Please fill and verify Business PAN First');
			return false;
		}else if($('biz_cin').val()  == ''){
			alert('Service unavailable!');
			return false;
		}else{
			return true;
		}
	}
</script>
@endsection