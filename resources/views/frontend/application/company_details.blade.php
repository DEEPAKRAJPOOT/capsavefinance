@extends('layouts.app')

@section('content')
    <!-- partial -->
<div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{ route('business_information_open', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">Company Details</a>
        </li>
        <li>
            <a href="{{ route('promoter-detail', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Promoter Details</a>
        </li>
        <li>
            <a href="{{ route('document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Documents</a>
        </li>
    </ul>


    <div class="row grid-margin mt-3">
	<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class="form-fields">
					<h5 class="card-title form-head mt-0">Business Details</h5>
				</div>	
				<form id="business_information_form" method="POST" action="{{route('business_information_save',['biz_id'=>request()->get('biz_id'), 'app_id'=>request()->get('app_id')])}}" onsubmit="return checkValidation();">
				@csrf
				<input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
				<input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">
				<input type="hidden" name="biz_cin" value="">
				<input type="hidden" name="pan_api_res" value="">
				<div class=" form-fields">
					<div class="form-sections">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="txtEmail">Company Pan
											<span class="mandatory">*</span>
										</label>
										<span class="text-success" id="pan-msg" style="">
											<i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i>
										</span>
										<a href="javascript:void(0);" class="verify-owner-no pan-verify" style="pointer-events: none;">Verified</a>
										<input type="text" name="biz_pan_number" value="{{old('biz_pan_number', $business_info->pan->pan_gst_hash)}}" class="form-control" tabindex="1" placeholder="Enter Company Pan" minlength="10" maxlength="10" required readonly>
										@error('biz_pan_number')
							                <span class="text-danger error">{{ $message }}</span>
							            @enderror
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group password-input">
										<label for="txtPassword">GST Number
											<span class="mandatory">*</span>
										</label>
										<!--<a href="javascript:void(0);" class="verify-owner-no">Verify</a>-->
										<select class="form-control" name="biz_gst_number" tabindex="2" onchange="fillEntity(this.value)" required>
											<option value="">Select GST Number</option>
											@forelse($business_info->gsts as $gst_key => $gst_value)
												<option value="{{$gst_value->pan_gst_hash}}" {{(old('biz_gst_number', Helpers::customIsset($business_info->gst, 'pan_gst_hash')) == $gst_value->pan_gst_hash)? 'selected':''}}>{{$gst_value->pan_gst_hash}}</option>
											@empty
											@endforelse
										</select>
										@error('biz_gst_number')
							                <span class="text-danger error">{{ $message }}</span>
							            @enderror
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="txtEmail">Entity Name
											<span class="mandatory">*</span>
										</label>
										<input type="text" name="biz_entity_name" value="{{old('biz_entity_name', $business_info->biz_entity_name)}}" class="form-control" tabindex="3" placeholder="Enter Entity Name" maxlength="100" required>
										@error('biz_entity_name')
							                <span class="text-danger error">{{ $message }}</span>
							            @enderror
									</div>
								</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Industry
												<span class="mandatory">*</span>
											</label>
											<select class="form-control" name="biz_type_id" tabindex="4" required>
												<option value=""> Select Industry</option>
												<option value="1" {{(old('biz_type_id', $business_info->nature_of_biz) == 1)? 'selected':''}}> Industry 1 </option>
												<option value="2" {{(old('biz_type_id', $business_info->nature_of_biz) == 2)? 'selected':''}}> Industry 2 </option>
												<option value="3" {{(old('biz_type_id', $business_info->nature_of_biz) == 3)? 'selected':''}}> Industry 3 </option>
											</select>
											@error('biz_type_id')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Date of Incorporation
												<span class="mandatory">*</span>
											</label>
											<input type="text" name="incorporation_date" value="{{old('incorporation_date', \Carbon\Carbon::parse($business_info->date_of_in_corp)->format('d/m/Y'))}}" class="form-control datepicker-dis-fdate" tabindex="5" placeholder="Enter Entity Name" autocomplete="off" readonly required>
											@error('incorporation_date')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtEmail">Business Constitution
												<span class="mandatory">*</span>
											</label>
											<select class="form-control" name="biz_constitution" tabindex="6" required>
													<option value=""> Select Business Constitution</option>
													<option value="1" {{(old('biz_constitution', $business_info->biz_constitution) == 1)? 'selected':''}}> Business Constitution 1 </option>
													<option value="2" {{(old('biz_constitution', $business_info->biz_constitution) == 2)? 'selected':''}}> Business Constitution 2 </option>
													<option value="3" {{(old('biz_constitution', $business_info->biz_constitution) == 3)? 'selected':''}}> Business Constitution 3 </option>
												</select>
												@error('biz_constitution')
									                <span class="text-danger error">{{ $message }}</span>
									            @enderror
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Nature of Business
												<span class="mandatory">*</span>
											</label>
											<select class="form-control" name="entity_type_id" tabindex="7" required="">
												<option value=""> Select Nature of Business</option>
												<option value="1" {{(old('entity_type_id', $business_info->entity_type_id) == 1)? 'selected':''}}> Nature of Business 1 </option>
												<option value="2" {{(old('entity_type_id', $business_info->entity_type_id) == 2)? 'selected':''}}> Nature of Business 2 </option>
												<option value="3" {{(old('entity_type_id', $business_info->entity_type_id) == 3)? 'selected':''}}> Nature of Business 3 </option>
											</select>
											@error('entity_type_id')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Segment
												<span class="mandatory">*</span>
											</label>
											<select class="form-control" name="segment" tabindex="8" required>
												<option value=""> Select Segment</option>
												<option value="1" {{(old('segment', $business_info->biz_segment) == 1)? 'selected':''}}> Segment 1 </option>
												<option value="2" {{(old('segment', $business_info->biz_segment) == 2)? 'selected':''}}> Segment 2 </option>
												<option value="3" {{(old('segment', $business_info->biz_segment) == 3)? 'selected':''}}> Segment 3 </option>
											</select>
											@error('segment')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group password-input INR">
											<label for="txtPassword">Business Turnover
											</label> <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
											<input type="text" name="biz_turnover" value="{{old('biz_turnover', number_format($business_info->turnover_amt))}}" class="form-control number_format" tabindex="9" placeholder="Enter Business Turnover" maxlength="19">
											@error('biz_turnover')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group INR">
											<label for="txtCreditPeriod">Applied Loan Amount
												<span class="mandatory">*</span>
											</label>
											<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
											<input type="text" name="loan_amount" value="{{old('loan_amount', number_format($business_info->app->loan_amt))}}" class="form-control number_format" tabindex="10" placeholder="Enter Applied Loan Amount" maxlength="19" required>
											<!-- <p class="float-right inr-box"><i>Enter amount in lakhs</i></p> -->
											@error('loan_amount')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtSupplierName">Tranche Tenor (Days)
											</label>
											<input type="text" name="tenor_days" value="{{old('tenor_days', $business_info->tenor_days)}}" class="form-control number_format" tabindex="11" placeholder="Enter Tranche Tenor (1 - 120)" maxlength="3">
											@error('tenor_days')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
								</div>
						</div>
						<div class="form-sections">
							<div class="row">
								<div class="col-md-12">
									<h5 class="form-head">Registered Address</h5>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="txtCreditPeriod">Address
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="biz_address" value="{{old('biz_address', $business_info->address[0]->addr_1)}}" class="form-control" tabindex="12" placeholder="Enter Your Address" maxlength="100" required>
												@error('biz_address')
                                                    <span class="text-danger error">{{ $message }}</span>
                                                @enderror
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group password-input">
												<label for="txtPassword">State
													<span class="mandatory">*</span>
												</label>
												<select class="form-control" name="biz_state" tabindex="13" required>
                                                    <option value=""> Select State</option>
                                                    @foreach($states as $key => $state)
                                                    <option value="{{$state->id}}" {{(old('biz_state', $business_info->address[0]->state_name) == $state->id)? 'selected':''}}> {{$state->name}} </option>
                                                    @endforeach
                                                </select>
                                                @error('biz_state')
                                                    <span class="text-danger error">{{ $message }}</span>
                                                @enderror
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="txtEmail">City
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="biz_city" value="{{old('biz_city', $business_info->address[0]->city_name)}}" class="form-control" tabindex="14" placeholder="Enter City Name" maxlength="50" required>
												@error('biz_city')
                                                    <span class="text-danger error">{{ $message }}</span>
                                                @enderror
											</div>
										</div>
										<div class="col-md-2">

											<div class="form-group password-input">
												<label for="txtPassword">Pin Code
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="biz_pin" value="{{old('biz_pin', $business_info->address[0]->pin_code)}}" class="form-control" tabindex="15" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" minlength="6" maxlength="6" required>
												@error('biz_pin')
                                                    <span class="text-danger error">{{ $message }}</span>
                                                @enderror
											</div>
										</div>
									</div>	
								</div>
							</div>
						</div>	
						<div class="form-sections">	
							<h5 class="form-head">Other Addresses</h5>
						</div>			
						<div class="form-sections">
							<div id="accordion" class="accordion">
								<div class="card card-color mb-0">
									<div class="sameas"><input type="checkbox"  class="mr-2" onchange="copyAddress('#collapseOne',this)"> <span> Same as Registered Address
									</span></div>
									<div class="card-header collapsed" data-toggle="collapse" href="#collapseOne">
										<a class="card-title">Communication Address</a>
									</div>
									<div id="collapseOne" class="card-body collapse" data-parent="#accordion">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtCreditPeriod">Address
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_address[]" value="{{old('biz_other_address.0', $business_info->address[1]->addr_1)}}" class="form-control" tabindex="16" placeholder="Enter Your Address" maxlength="100">
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group password-input">
														<label for="txtPassword">State
															<!-- <span class="mandatory">*</span> -->
														</label>
														<select class="form-control" name="biz_other_state[]" tabindex="17">
	                                                        <option value=""> Select State</option>
	                                                        @foreach($states as $key => $state)
	                                                        <option value="{{$state->id}}" {{(old('biz_other_state.0', $business_info->address[1]->state_name) == $state->id)? 'selected':''}}> {{$state->name}} </option>
	                                                        @endforeach
	                                                    </select>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label for="txtEmail">City
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_city[]" value="{{old('biz_other_city.0',$business_info->address[1]->city_name)}}" class="form-control" tabindex="18" placeholder="Enter City Name" maxlength="50">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group password-input">
														<label for="txtPassword">Pin Code
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_pin[]" value="{{old('biz_other_pin.0', $business_info->address[1]->pin_code)}}" class="form-control" tabindex="19" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="card card-color mb-0">
									<div class="sameas"><input type="checkbox"  class="mr-2" onchange="copyAddress('#collapseTwo',this)"> <span> Same as Registered Address
									</span></div>
									<div class="card-header collapsed" data-toggle="collapse" href="#collapseTwo">
										<a class="card-title">GST   Address</a>
									</div>
									<div id="collapseTwo" class="card-body collapse" data-parent="#accordion">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtCreditPeriod">Address
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_address[]" value="{{old('biz_other_address.1', $business_info->address[2]->addr_1)}}" class="form-control" tabindex="20" placeholder="Enter Your Address" maxlength="100">
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group password-input">
														<label for="txtPassword">State
															<!-- <span class="mandatory">*</span> -->
														</label>

														<select class="form-control" name="biz_other_state[]" tabindex="21">
	                                                        <option value=""> Select State</option>
	                                                        @foreach($states as $key => $state)
	                                                        <option value="{{$state->id}}" {{(old('biz_other_state.1', $business_info->address[2]->state_name) == $state->id)? 'selected':''}}> {{$state->name}} </option>
	                                                        @endforeach
	                                                    </select>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label for="txtEmail">City
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_city[]" value="{{old('biz_other_city.1',$business_info->address[2]->city_name)}}" class="form-control" tabindex="22" placeholder="Enter City Name" maxlength="50">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group password-input">
														<label for="txtPassword">Pin Code
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_pin[]" value="{{old('biz_other_pin.1', $business_info->address[2]->pin_code)}}" class="form-control" tabindex="23" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="card card-color mb-0">
									<div class="sameas"><input type="checkbox"  class="mr-2" onchange="copyAddress('#collapseThree', this)"> <span> Same as Registered Address
									</span></div>
									<div class="card-header collapsed" data-toggle="collapse" href="#collapseThree">
										<a class="card-title">Warehouse Address</a>
									</div>
									<div id="collapseThree" class="card-body collapse" data-parent="#accordion">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtCreditPeriod">Address
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_address[]" value="{{old('biz_other_address.2', $business_info->address[3]->addr_1)}}" class="form-control" tabindex="24" placeholder="Enter Your Address" maxlength="100">
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group password-input">
														<label for="txtPassword">State
															<!-- <span class="mandatory">*</span> -->
														</label>
														<select class="form-control" name="biz_other_state[]" tabindex="25">
	                                                        <option value=""> Select State</option>
	                                                        @foreach($states as $key => $state)
	                                                        <option value="{{$state->id}}" {{(old('biz_other_state.2', $business_info->address[3]->state_name) == $state->id)? 'selected':''}}> {{$state->name}} </option>
	                                                        @endforeach
	                                                    </select>
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group">
														<label for="txtEmail">City
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_city[]" value="{{old('biz_other_city.2',$business_info->address[3]->city_name)}}" class="form-control" tabindex="26" placeholder="Enter City Name" maxlength="50">
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group password-input">
														<label for="txtPassword">Pin Code
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_pin[]" value="{{old('biz_other_pin.2', $business_info->address[3]->pin_code)}}" class="form-control" tabindex="27" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card card-color mb-0">
									<div class="sameas"><input type="checkbox" class="mr-2" onchange="copyAddress('#collapseFour', this)"> <span> Same as Registered Address
									</span></div>
									<div class="card-header collapsed" data-toggle="collapse" href="#collapseFour">
										<a class="card-title">Factory Address</a>
									</div>
									<div id="collapseFour" class="card-body collapse" data-parent="#accordion">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtCreditPeriod">Address
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_address[]" value="{{old('biz_other_address.3', $business_info->address[4]->addr_1)}}" class="form-control" tabindex="28" placeholder="Enter Your Address" maxlength="100">
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group password-input">
														<label for="txtPassword">State
															<!-- <span class="mandatory">*</span> -->
														</label>
														<select class="form-control" name="biz_other_state[]" tabindex="29">
	                                                        <option value=""> Select State</option>
	                                                        @foreach($states as $key => $state)
	                                                        <option value="{{$state->id}}" {{(old('biz_other_state.3', $business_info->address[4]->state_name) == $state->id)? 'selected':''}}> {{$state->name}} </option>
	                                                        @endforeach
	                                                    </select>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label for="txtEmail">City
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_city[]" value="{{old('biz_other_city.3',$business_info->address[4]->city_name)}}" class="form-control" tabindex="30" placeholder="Enter City Name" maxlength="50">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group password-input">
														<label for="txtPassword">Pin Code
															<!-- <span class="mandatory">*</span> -->
														</label>
														<input type="text" name="biz_other_pin[]" value="{{old('biz_other_pin.3', $business_info->address[4]->pin_code)}}" class="form-control" tabindex="31" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>	
						</div>
						@if($business_info->app->status != 1)
						<div class="d-flex btn-section">
							<div class="ml-auto text-right">
								<input type="submit" value="Update and Continue" class="btn btn-primary">
							</div>
						</div>
						@endif
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('jscript')
<script>
	function copyAddress(id,th){
		console.log(id);
		if($(th).is(':checked')){
			$(id+' input[name*=biz_other_address]').val($('input[name=biz_address]').val());
			$(id+' input[name*=biz_other_city]').val($('input[name=biz_city]').val());
			$(id+' select[name*=biz_other_state]').val($('select[name=biz_state]').val());
			$(id+' input[name*=biz_other_pin]').val($('input[name=biz_pin]').val());
		}else{
			$(id+' input[name*=biz_other_address]').val('');
			$(id+' input[name*=biz_other_city]').val('');
			$(id+' select[name*=biz_other_state]').val('');
			$(id+' input[name*=biz_other_pin]').val('');
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
				    	//$('#pan-msg').show();
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
		let option_html = '<option value="">Select GST Number</option>';
		$(datas).each(function(i,data){
			if(data.authStatus == 'Active'){
				res += data.gstinId+',';
				option_html += '<option value="'+data.gstinId+'">'+data.gstinId+'</option>';
			}
		})
		$('select[name=biz_gst_number]').html(option_html);
		$('input[name=pan_api_res]').val(res);
		//$('#business_information_form input[type=submit]').prop("disabled", false);
	}

	function fillEntity(gstinId){
		if(gstinId == ''){
			return false;
		}
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
				    	fillRegisteredAddress(res.result.pradr.adr);
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

	function fillRegisteredAddress(addr_str){
		try {
			let addr_array = addr_str.split(',');
			let pin = addr_array.pop().replace(/pin:/,'').trim();
			let state = addr_array.pop().trim();
			let city = addr_array.pop().trim();
			let address = addr_array.toString().trim();
			$('input[name=biz_address]').val(address);
			$('select[name=biz_state] option:contains('+state+')').attr('selected', true);
			$('input[name=biz_city]').val(city);
			$('input[name=biz_pin]').val(pin);
			//return {'address': address, 'city': city, 'state': state, 'pin': pin};
		}
		catch(err) {
		  console.error(err);
		}
	}
</script>
@endsection