@extends('layouts.app')

@section('content')
    <!-- partial -->
    <div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
		<li>
             <a href="javascript:void(0);" class="active">Business Information</a>
		</li>
		<li>
			<a href="javascript:void(0);">Management Details</a>
		</li>
		<li>
			<a href="javascript:void(0);">Documents</a>
		</li>
	</ul>

<div class="row grid-margin mt-3">
	<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class=" form-fields row">
					<div class="col-md-12">
						<h5 class="card-title form-head mt-0">Business Details</h5>
					</div>	
				</div>	
				<form id="business_information_form" method="POST" action="{{route('business_information_save')}}" onsubmit="return checkValidation();">
				@csrf
				<input type="hidden" name="biz_cin" value="">
				<input type="hidden" name="pan_api_res" value="">
				<div class=" form-fields">
					<div class="form-sections row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="txtEmail">Company PAN
											<span class="mandatory">*</span>
										</label>
										<span class="text-success" id="pan-msg" style="display: none;">
											<i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i>
										</span>
										<a href="javascript:void(0);" class="verify-owner-no pan-verify" style="">Verify</a>
										<input type="text" name="biz_pan_number" value="{{old('biz_pan_number')}}" class="form-control pan-validate" tabindex="1" placeholder="Enter Company Pan" maxlength="10" >
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
										<select class="form-control" name="biz_gst_number" tabindex="2" onchange="fillEntity(this.value)" >
											</select>
											<!-- <input type="text" name="biz_gst_number" value="{{old('biz_gst_number')}}" class="form-control" tabindex="1" placeholder="Enter GST Number"> -->
											@error('biz_gst_number')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="txtEmail">Entity Name
											<span class="mandatory">*</span>
										</label>
										<input type="text" name="biz_entity_name" value="{{old('biz_entity_name')}}" class="form-control" tabindex="3" placeholder="Enter Entity Name" maxlength="100" >
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
											<select class="form-control" name="biz_type_id" tabindex="4" >
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
									
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Date of Incorporation
												<span class="mandatory">*</span>
											</label>
											<input type="text" name="incorporation_date" value="{{old('incorporation_date')}}" class="form-control datepicker-dis-fdate" tabindex="5" autocomplete="off" readonly>
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
											<select class="form-control" name="biz_constitution" tabindex="6" >
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
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Nature of Business
												<span class="mandatory">*</span>
											</label>
											<select class="form-control" name="entity_type_id" tabindex="7" >
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

									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Segment
												<span class="mandatory">*</span>
											</label>
											<select class="form-control" name="segment" tabindex="8" >
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

									<div class="col-md-4">
										<div class="form-group password-input INR">
											<label for="txtPassword">Business Turnover
											</label> <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
											<input type="text" name="biz_turnover" value="{{old('biz_turnover')}}" class="form-control number_format" tabindex="9" placeholder="Enter Business Turnover" maxlength="19">
											@error('biz_turnover')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-12" >
										<div class="form-group">
											<label for="txtSupplierName">Product Type
											</label><br/>
											<div id="check_block">
											<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input  {{ (old('product_id.1.checkbox') == '1')? 'checked': ''}} class="product-type" type="checkbox" value="1" name="product_id[1][checkbox]"> Supply Chain</label>
											<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input {{ (old('product_id.2.checkbox') == '2')? 'checked': ''}} class="product-type" type="checkbox" value="2" name="product_id[2][checkbox]"> Term Loan</label>
											<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input {{ (old('product_id.3.checkbox') == '3')? 'checked': ''}} class="product-type" type="checkbox" value="3" name="product_id[3][checkbox]"> Leasing</label>
											</div>
											@error('product_id')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
								</div>
								<div class="row {{ (old('product_id.1.checkbox') == '1')? '': 'hide'}}" id="product-type-1">
									<div class="col-md-4">Supply Chain</div>
									<div class="col-md-4">
										<div class="form-group INR">
											<label for="txtCreditPeriod">Applied Loan Amount
												<span class="mandatory">*</span>
											</label>
											<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
											<input type="text" name="product_id[1][loan_amount]" value="{{old('product_id.1.loan_amount')}}" class="form-control number_format" tabindex="10" placeholder="Enter Applied Loan Amount" maxlength="19">
											@error('product_id.1.loan_amount')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtSupplierName">Tranche Tenor (Days)
											</label>
											<input type="text" name="product_id[1][tenor_days]" value="{{old('product_id.1.tenor_days')}}" class="form-control number_format" tabindex="11" placeholder="Enter Tranche Tenor" maxlength="3">
											@error('product_id.1.tenor_days')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
								</div>
								<div class="row {{ (old('product_id.2.checkbox') == '2')? '': 'hide'}}" id="product-type-2">
									<div class="col-md-4">Term Loan</div>
									<div class="col-md-4">
										<div class="form-group INR">
											<label for="txtCreditPeriod">Applied Loan Amount
												<span class="mandatory">*</span>
											</label>
											<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
											<input type="text" name="product_id[2][loan_amount]" value="{{old('product_id.2.loan_amount')}}" class="form-control number_format" tabindex="10" placeholder="Enter Applied Loan Amount" maxlength="19">
											@error('product_id.2.loan_amount')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtSupplierName">Tranche Tenor (Days)
											</label>
											<input type="text" name="product_id.2.tenor_days" value="{{old('product_id.2.tenor_days')}}" class="form-control number_format" tabindex="11" placeholder="Enter Tranche Tenor" maxlength="3">
											@error('product_id.2.tenor_days')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
								</div>
								<div class="row {{ (old('product_id.3.checkbox') == '3')? '': 'hide'}}" id="product-type-3">
									<div class="col-md-4">Leasing</div>
									<div class="col-md-4">
										<div class="form-group INR">
											<label for="txtCreditPeriod">Applied Loan Amount
												<span class="mandatory">*</span>
											</label>
											<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
											<input type="text" name="product_id[3][loan_amount]" value="{{old('product_id.3.loan_amount')}}" class="form-control number_format" tabindex="10" placeholder="Enter Applied Loan Amount" maxlength="19">
											@error('product_id.3.loan_amount')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtSupplierName">Tranche Tenor (Days)
											</label>
											<input type="text" name="product_id[3][tenor_days]" value="{{old('product_id.3.tenor_days')}}" class="form-control number_format" tabindex="11" placeholder="Enter Tranche Tenor" maxlength="3">
											@error('product_id.3.tenor_days')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
								</div>

								{{-- <div class="row">
									<div class="col-md-4">
										<div class="form-group INR">
											<label for="txtCreditPeriod">Applied Loan Amount
												<span class="mandatory">*</span>
											</label>
											<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
											<input type="text" name="loan_amount" value="{{old('loan_amount')}}" class="form-control number_format" tabindex="10" placeholder="Enter Applied Loan Amount" maxlength="19" >
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
											<input type="text" name="tenor_days" value="{{old('tenor_days')}}" class="form-control number_format" tabindex="11" placeholder="Enter Tranche Tenor" maxlength="3">
											@error('tenor_days')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtSupplierName">Product Type
												<span class="mandatory">*</span>
											</label><br/>
											<div id="check_block">
											@foreach($product_types as $key=>$product_type)
												<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="{{$key}}" name="product_id[]"> {{$product_type}}</label>
											@endforeach
											</div>
											@error('product_id')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
								</div> --}}
							</div>
						</div>

						<div class="form-sections">
							<div class="row">
								<div class="col-md-12">
										<h5 class="form-head">GST Address</h5>
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label for="txtCreditPeriod">Address
														<span class="mandatory">*</span>
													</label>
													<input type="text" name="biz_address" value="{{old('biz_address')}}" class="form-control" tabindex="12" placeholder="Enter Your Address" maxlength="100" >
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
													<select class="form-control" name="biz_state" tabindex="13" >
                                                        <option value=""> Select State</option>
                                                        @foreach($states as $key => $state)
                                                        <option value="{{$state->id}}" {{(old('biz_state') == $state->id)? 'selected':''}}> {{$state->name}} </option>
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
													<input type="text" name="biz_city" value="{{old('biz_city')}}" class="form-control" tabindex="14" placeholder="Enter City Name" maxlength="50" >
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
													<input type="text" name="biz_pin" value="{{old('biz_pin')}}" class="form-control" tabindex="15" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6" >
						                            @error('biz_pin')
                                                        <span class="text-danger error">{{ $message }}</span>
                                                    @enderror
												</div>
											</div>
										</div>	
								</div>
							</div>
						</div>	
						<div class="form-sections row">	
							<div class="col-md-12">
								<h5 class="form-head">Other Addresses</h5>
							</div>	
						</div>			
						<div class="form-sections">
								<div id="accordion" class="accordion">
									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox" class="mr-2" onchange="copyAddress('#collapseOne',this)"> <span> Same as GST Address
										</span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseOne">
											<a class="card-title">
												Communication Address
											</a>
										</div>
										<div id="collapseOne" class="card-body collapse" data-parent="#accordion">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label for="txtCreditPeriod">Address
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_address[]" value="{{old('biz_other_address.0')}}" class="form-control" tabindex="16" placeholder="Enter Your Address" maxlength="100">
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
		                                                        <option value="{{$state->id}}" {{(old('biz_other_state.0') == $state->id)? 'selected':''}}> {{$state->name}} </option>
		                                                        @endforeach
		                                                    </select>
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="txtEmail">City
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_city[]" value="{{old('biz_other_city.0')}}" class="form-control" tabindex="18" placeholder="Enter City Name" maxlength="50">
														</div>
													</div>
													<div class="col-md-2">

														<div class="form-group password-input">
															<label for="txtPassword">Pin Code
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_pin[]" value="{{old('biz_other_pin.0')}}" class="form-control" tabindex="19" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="card card-color mb-0" style="display: none;">
										<div class="sameas"><input type="checkbox" class="mr-2" onchange="copyAddress('#collapseTwo',this)"> <span> Same as GST Address
										</span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseTwo">
											<a class="card-title">GST Address</a>
										</div>
										<div id="collapseTwo" class="card-body collapse" data-parent="#accordion">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label for="txtCreditPeriod">Address
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_address[]" value="{{old('biz_other_address.1')}}" class="form-control" tabindex="20" placeholder="Enter Your Address" maxlength="100">
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
		                                                        <option value="{{$state->id}}" {{(old('biz_other_state.1') == $state->id)? 'selected':''}}> {{$state->name}} </option>
		                                                        @endforeach
		                                                    </select>
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="txtEmail">City
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_city[]" value="{{old('biz_other_city.1')}}" class="form-control" tabindex="22" placeholder="Enter City Name" maxlength="50">
														</div>
													</div>

													<div class="col-md-2">
														<div class="form-group password-input">
															<label for="txtPassword">Pin Code
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_pin[]" value="{{old('biz_other_pin.1')}}" class="form-control" tabindex="23" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox" class="mr-2" onchange="copyAddress('#collapseThree', this)"> <span> Same as GST Address
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
															<input type="text" name="biz_other_address[]" value="{{old('biz_other_address.2')}}" class="form-control" tabindex="24" placeholder="Enter Your Address" maxlength="100">
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
		                                                        <option value="{{$state->id}}" {{(old('biz_other_state.2') == $state->id)? 'selected':''}}> {{$state->name}} </option>
		                                                        @endforeach
		                                                    </select>
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="txtEmail">City
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_city[]" value="{{old('biz_other_city.2')}}" class="form-control" tabindex="26" placeholder="Enter City Name" maxlength="50">
														</div>
													</div>

													<div class="col-md-2">
														<div class="form-group password-input">
															<label for="txtPassword">Pin Code
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_pin[]" value="{{old('biz_other_pin.2')}}" class="form-control" tabindex="27" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox" class="mr-2" onchange="copyAddress('#collapseFour', this)"> <span> Same as GST Address
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
															<input type="text" name="biz_other_address[]" value="{{old('biz_other_address.3')}}" class="form-control" tabindex="28" placeholder="Enter Your Address" maxlength="100">
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
		                                                        <option value="{{$state->id}}" {{(old('biz_other_state.3') == $state->id)? 'selected':''}}> {{$state->name}} </option>
		                                                        @endforeach
		                                                    </select>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="txtEmail">City
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_city[]" value="{{old('biz_other_city.3')}}" class="form-control" tabindex="30" placeholder="Enter City Name" maxlength="50">
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group password-input">
															<label for="txtPassword">Pin Code
																<!-- <span class="mandatory">*</span> -->
															</label>
															<input type="text" name="biz_other_pin[]" value="{{old('biz_other_pin.3')}}" class="form-control" tabindex="31" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
							</div>
						</div>
						<div class="d-flex btn-section">
							<div class="ml-auto text-right">
								<input type="submit" value="Save and Continue" class="btn btn-success btn-sm">
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

@section('jscript')
<script>
var messages = {
	biz_pan_to_gst_karza: "{{ URL::route('chk_biz_pan_to_gst') }}",
	biz_gst_to_entity_karza: "{{ URL::route('chk_biz_gst_to_entity') }}",
	biz_entity_to_cin_karza: "{{ URL::route('chk_biz_entity_to_cin') }}",
	data_not_found: "{{ trans('error_messages.data_not_found') }}",
	token: "{{ csrf_token() }}"
};

$(document).ready(function () {
	$(".product-type"). click(function(){
		var productType = $(this).val();
		var isChecked  = $(this).prop("checked");

		if(isChecked){
			$("#product-type-"+productType).removeClass('hide');
		}else{
			$("#product-type-"+productType).addClass('hide');
			$("input[name='product_id["+productType+"][loan_amount]']").val('');
			$("input[name='product_id["+productType+"][tenor_days]']").val('');
		}
	});
});
</script>
<!-- <script src="{{url('common/js/business_information.js?v=1')}}"></script> -->
<script src="{{url('common/js/business_info.js?v=1.1')}}"></script>
@endsection