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
					<form>
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
												<input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter GST Number" required="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtSupplierName">Business PAN
													<span class="mandatory">*</span>
												</label>
												<a href="javascript:void(0);" class="verify-owner-no" >Verify</a>
												<input type="text" name="name" id="name" value="" class="form-control" tabindex="3" placeholder="Enter Business PAN" required="">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Business Name
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Business Name" required="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Type of Industry
													<span class="mandatory">*</span>
												</label>

												<select class="form-control">
													<option> Select Industry</option>
													<option> Test 1 </option>
													<option> Test 2 </option>
													<option> Test 3 </option>


												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Business Email
													<span class="mandatory">*</span>
												</label>
												<input type="Email" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Business Email" required="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">Mobile
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Mobile No." required="">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="txtEmail">Landline
												</label>
												<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Landline No.">
											</div>
										</div>
										<div class="col-md-6">

											<div class="form-group password-input">
												<label for="txtPassword">Type of Business Entity
													<span class="mandatory">*</span>
												</label>
												<select class="form-control">
													<option> Select Business Entity</option>
													<option> Test 1 </option>
													<option> Test 2 </option>
													<option> Test 3 </option>


												</select>
											</div>
										</div>
									</div>
									<div class="row">

										<div class="col-md-6">
											<div class="form-group password-input">
												<label for="txtPassword">CIN
													<span class="mandatory">*</span>
												</label>
												<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter CIN" required="">
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
														<input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter Your Address" required="">
													</div>
												</div>

											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="txtEmail">City
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter City Name" required="">
													</div>
												</div>

											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group password-input">
														<label for="txtPassword">State
															<span class="mandatory">*</span>
														</label>

														<select class="form-control">
															<option> Select State</option>
															<option> Test 1 </option>
															<option> Test 2 </option>
															<option> Test 3 </option>


														</select>
													</div>
												</div>

											</div>
											<div class="row">

												<div class="col-md-12">

													<div class="form-group password-input">
														<label for="txtPassword">Pin Code
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Pin Code" required="">
													</div>
												</div>
											</div>



										</div>

									</div>
									<div class="col-md-6">
										<div class="col-md-12 ">
											<h3 class="full-width">Correspondence Address
												<div class="sameas"><input type="checkbox"> <span> Same as Business Address
													</span></div>

											</h3>
										</div>

										<div class="col-md-12">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="txtCreditPeriod">Address
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter Your Address" required="">
													</div>
												</div>

											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="txtEmail">City
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter City Name" required="">
													</div>
												</div>

											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group password-input">
														<label for="txtPassword">State
															<span class="mandatory">*</span>
														</label>

														<select class="form-control">
															<option> Select State</option>
															<option> Test 1 </option>
															<option> Test 2 </option>
															<option> Test 3 </option>


														</select>
													</div>
												</div>

											</div>
											<div class="row">

												<div class="col-md-12">

													<div class="form-group password-input">
														<label for="txtPassword">Pin Code
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Pin Code" required="">
													</div>
												</div>
											</div>



										</div>

									</div>


								</div>
								<div class="d-flex btn-section ">
									<div class="col-md-4 ml-auto text-right"><input type="button" value="Save and Continue" class="btn btn-primary" onclick="window.location.href='authorized-signatory-kyc.php'"> </div>
								</div>

							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

@endsection