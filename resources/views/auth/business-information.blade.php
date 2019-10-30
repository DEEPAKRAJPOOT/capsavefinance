@extends('layouts.guest')
@section('content')

<div class="step-form pt-5">
	<div class="container">
		<ul id="progressbar">
			<li class="count-active">
				<div class="count-heading">Business Information </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/business-information.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="assets{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading"> Authorized Signatory KYC </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/kyc.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading">Business Documents </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/business-document.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading"> Associate Buyers </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/buyers.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading"> Associate Logistics </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/logistics.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
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
					<form id="business_information_form" method="POST" action="business-information-save">
						@csrf
						<div class=" form-fields">
							<div class="form-sections">
								<div class="col-md-12">
									<h3>Business Details

									</h3>
								</div>
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtCreditPeriod">GST Number
													<span class="mandatory">*</span>
												</label>
												<a href="javascript:void(0);" class="verify-owner-no" >Verify</a>
												<input type="text" name="biz_gst_number" value="{{old('biz_gst_number')}}" class="form-control" placeholder="Enter GST Number">
												@error('biz_gst_number')
				                                    <span class="text-danger error">{{ $message }}</span>
				                                @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtSupplierName">Business PAN
													<span class="mandatory">*</span>
												</label>
												<a href="javascript:void(0);" class="verify-owner-no" >Verify</a>
												<input type="text" name="biz_pan_number" value="{{old('biz_pan_number')}}" class="form-control" tabindex="3" placeholder="Enter Business PAN">
												@error('biz_pan_number')
				                                    <span class="text-danger error">{{ $message }}</span>
				                                @enderror
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Business Name
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="biz_entity_name" value="{{old('biz_entity_name')}}" class="form-control" tabindex="1" placeholder="Enter Business Name">
												@error('biz_entity_name')
				                                    <span class="text-danger error">{{ $message }}</span>
				                                @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Type of Industry
													<span class="mandatory">*</span>
												</label>
												<select class="form-control" name="biz_type_id">
													<option value=""> Select Industry</option>
													<option value="1"> Test 1 </option>
													<option value="2"> Test 2 </option>
													<option value="3"> Test 3 </option>
												</select>
												@error('biz_type_id')
				                                    <span class="text-danger error">{{ $message }}</span>
				                                @enderror
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Business Email
													<span class="mandatory">*</span>
												</label>
												<input type="Email" name="biz_email" value="{{old('biz_email')}}" class="form-control" tabindex="1" placeholder="Enter Business Email">
												@error('biz_email')
				                                    <span class="text-danger error">{{ $message }}</span>
				                                @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Mobile
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="biz_mobile" value="{{old('biz_mobile')}}" class="form-control" tabindex="1" placeholder="Enter Mobile No.">
												@error('biz_mobile')
				                                    <span class="text-danger error">{{ $message }}</span>
				                                @enderror
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Landline
												</label>
												<input type="text" name="biz_landline" value="{{old('biz_landline')}}" class="form-control" tabindex="1" placeholder="Enter Landline No.">
												@error('biz_landline')
				                                    <span class="text-danger error">{{ $message }}</span>
				                                @enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Type of Business Entity
													<span class="mandatory">*</span>
												</label>
												<select class="form-control" name="entity_type_id">
													<option value=""> Select Business Entity</option>
													<option value="1"> Test 1 </option>
													<option value="2"> Test 2 </option>
													<option value="3"> Test 3 </option>
												</select>
												@error('entity_type_id')
				                                    <span class="text-danger error">{{ $message }}</span>
				                                @enderror
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">CIN
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="biz_cin" value="{{old('biz_cin')}}" class="form-control" tabindex="1" placeholder="Enter CIN">
												@error('biz_cin')
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
										<div class="col-md-12">
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
															<option value="1"> Test 1 </option>
															<option value="2"> Test 2 </option>
															<option value="3"> Test 3 </option>
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
												<div class="sameas"><input type="checkbox" name="address_same"> <span> Same as Business Address</span></div>
											</h3>
										</div>
										<div class="col-md-12">
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
															<option value="1"> Test 1 </option>
															<option value="2"> Test 2 </option>
															<option value="3"> Test 3 </option>
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