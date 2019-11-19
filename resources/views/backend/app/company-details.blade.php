@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
    <!-- partial -->
    <div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
		<li>
			<a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">Company Details</a>
		</li>
		<li>
			<a href="#">Promoter Details</a>
		</li>
		<li>
			<a href="#">Documents</a>
		</li>
		<!--<li>
			<a href="buyers.php">Buyers </a>
		</li>-->
		<!-- <li>
			<a href="third-party.php">Third party</a>
		</li> -->
	</ul>



<div class="row grid-margin mt-3">
	<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class=" form-fields">
					<div class="col-md-12">
						<h5 class="card-title form-head-h5">Business Details</h5>
					</div>	
				</div>	
				<div class=" form-fields">
					<div class="form-sections">

						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group INR">
										<label for="txtCreditPeriod">Applied Loan Amount
											<span class="mandatory">*</span>
										</label>
										<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
										<input type="text" name="loan_amount" value="{{old('loan_amount', $business_info->app->loan_amt)}}" class="form-control" placeholder="Enter Applied Loan Amount">
										<p class="float-right inr-box"><i>Enter amount in lakhs</i>
										</p></div>

									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="txtSupplierName">Tranche Tenor (Days)
											</label>
											<input type="number" name="tenor_days" value="{{old('tenor_days')}}" class="form-control" tabindex="3" placeholder="Enter Tranche Tenor (1 - 120)">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="txtEmail">Entity Name
												<span class="mandatory">*</span>
											</label>
											<input type="text" name="biz_entity_name" value="{{old('biz_entity_name', $business_info->biz_entity_name)}}" class="form-control" tabindex="1" placeholder="Enter Entity Name">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group password-input">
											<label for="txtPassword">Date of Incorporation
												<span class="mandatory">*</span>
											</label>
											<input type="date" name="incorporation_date" value="{{old('incorporation_date', $business_info->date_of_in_corp)}}" class="form-control" tabindex="1" placeholder="Enter Entity Name">
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
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group password-input INR">
											<label for="txtPassword">Business Turnover
											</label> <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
											<input type="text" name="biz_turnover" value="{{old('biz_turnover', $business_info->turnover_amt)}}" class="form-control" tabindex="1" placeholder="Enter Business Turnover">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="txtEmail">Company Pan
												<span class="mandatory">*</span>
											</label>
											<span class="alert-verify float-right">Enter Valid PAN No.</span>
											<a href="javascript:void(0);" class="verify-owner-no verify-show">Verified</a>
											<input type="text" name="biz_pan_number" value="{{old('biz_pan_number', $business_info->pan->pan_gst_hash)}}" class="form-control" tabindex="1" placeholder="Enter Company Pan">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group password-input">
											<label for="txtPassword">GST Number
												<span class="mandatory">*</span>
											</label>
											<!--<a href="javascript:void(0);" class="verify-owner-no">Verify</a>-->
											<select class="form-control" name="biz_gst_number">
												<option value="">Select GST Number</option>
												@forelse($business_info->gsts as $gst_key => $gst_value)
													<option val="{{$gst_value->pan_gst_hash}}">{{$gst_value->pan_gst_hash}}</option>
												@empty
												@endforelse
											</select>
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
												<option value="1" {{(old('entity_type_id', $business_info->entity_type_id) == 1)? 'selected':''}}> Nature of Business 1 </option>
												<option value="2" {{(old('entity_type_id', $business_info->entity_type_id) == 2)? 'selected':''}}> Nature of Business 2 </option>
												<option value="3" {{(old('entity_type_id', $business_info->entity_type_id) == 3)? 'selected':''}}> Nature of Business 3 </option>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group password-input">
											<label for="txtPassword">Industry
												<span class="mandatory">*</span>
											</label>
											<select class="form-control" name="biz_type_id">
												<option value=""> Select Industry</option>
												<option value="1" {{(old('biz_type_id', $business_info->nature_of_biz) == 1)? 'selected':''}}> Industry 1 </option>
												<option value="2" {{(old('biz_type_id', $business_info->nature_of_biz) == 2)? 'selected':''}}> Industry 2 </option>
												<option value="3" {{(old('biz_type_id', $business_info->nature_of_biz) == 3)? 'selected':''}}> Industry 3 </option>
											</select>
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
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="form-sections">
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-12">
										<h5 class="form-head-h5">Registered Address</h5>
									</div>

									<div class="col-md-12">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label for="txtCreditPeriod">Address
														<span class="mandatory">*</span>
													</label>
													<input type="text" name="biz_address" value="{{old('biz_address', $business_info->address[0]->addr_1)}}" class="form-control" placeholder="Enter Your Address">
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-4">
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
											<div class="col-md-4">
												<div class="form-group">
													<label for="txtEmail">City
														<span class="mandatory">*</span>
													</label>
													<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter City Name" required="">
												</div>
											</div>
											<div class="col-md-4">

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
						</div>	
						<div class="form-sections">	
							<div class="col-md-12">
								<h5 class="form-head-h5">Other Addresses</h5>
							</div>	
						</div>			

						<div class="form-sections">
							<div class="col-md-12">
								<div id="accordion" class="accordion">

									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox"> <span> Same as Registered Address
										</span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseOne">
											<a class="card-title">
												Communication Address
											</a>

										</div>
										<div id="collapseOne" class="card-body collapse" data-parent="#accordion">
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


													<div class="col-md-4">
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


													<div class="col-md-4">
														<div class="form-group">
															<label for="txtEmail">City
																<span class="mandatory">*</span>
															</label>
															<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter City Name" required="">
														</div>
													</div>
													<div class="col-md-4">

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

									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox"> <span> Same as Registered Address
										</span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseOne24">
											<a class="card-title">
												GST   Address
											</a>

										</div>
										<div id="collapseOne24" class="card-body collapse" data-parent="#accordion">
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

													<div class="col-md-4">
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


													<div class="col-md-4">
														<div class="form-group">
															<label for="txtEmail">City
																<span class="mandatory">*</span>
															</label>
															<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter City Name" required="">
														</div>
													</div>

													<div class="col-md-4">
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

									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox"> <span> Same as Registered Address
										</span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseOne2">
											<a class="card-title">
												Warehouse Address
											</a>

										</div>
										<div id="collapseOne2" class="card-body collapse" data-parent="#accordion">
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


													<div class="col-md-4">
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

													<div class="col-md-4">
														<div class="form-group">
															<label for="txtEmail">City
																<span class="mandatory">*</span>
															</label>
															<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter City Name" required="">
														</div>
													</div>


													<div class="col-md-4">
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
									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox"> <span> Same as Registered Address
										</span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseOne3">
											<a class="card-title">
												Factory Address
											</a>
										</div>
										<div id="collapseOne3" class="card-body collapse" data-parent="#accordion">
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
													<div class="col-md-4">
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
													<div class="col-md-4">
														<div class="form-group">
															<label for="txtEmail">City
																<span class="mandatory">*</span>
															</label>
															<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter City Name" required="">
														</div>
													</div>
													<div class="col-md-4">
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
								</div>	
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
@endsection