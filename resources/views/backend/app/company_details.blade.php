@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
    <!-- partial -->
    <div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        @can('company_details')
		<li>
			<a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'user_id' => $user_id]) }}" class="active">Business Information</a>
		</li>
        @endcan 
        @can('promoter_details')
		<li>
			<a href="{{ route('promoter_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'user_id' => $user_id]) }}">Management Information</a>
		</li>
        @endcan 
        @can('documents')
		<li>
			<a href="{{ route('documents', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'user_id' => $user_id]) }}">Documents</a>
		</li>
        @endcan
	</ul>

	<div class="row grid-margin mt-3">
		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class=" form-fields">
						<h5 class="card-title form-head mt-0">Business Information</h5>
					</div>
					<form id="business_information_form" method="POST"
						action="{{ route('company_details_save') }}"
						onsubmit="return checkValidation();">
						@csrf
						<input type="hidden" name="biz_id" value="{{ $biz_id }}">
						<input type="hidden" name="app_id" value="{{ $app_id }}">
						<input type="hidden" name="pan_api_res" value="">
						<input type="hidden" name="cin_api_res" value="">
						
						<div class=" form-fields">
							<div class="form-sections">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtEmail">Company PAN <span class="mandatory">*</span> </label>
											<span class="text-success" id="pan-msg" style="">
												@if(config('proin.CONFIGURE_API'))
													<i class="fa fa-check-circle" aria-hidden="true"></i>
													<i>Verified Successfully</i> 
												@endif
											</span>
											<div class="relative">
												@if(config('proin.CONFIGURE_API'))
													@can('chk_biz_pan_to_gst')
														<a href="javascript:void(0);" class="verify-owner-no pan-verify" style="pointer-events: none;">Verified</a>
													@endcan
												@endif 
												<input type="text" name="biz_pan_number" value="{{ old('biz_pan_number', ($pan_no ?? $data['business_info']['company_pan']['pan_no'] ?? '') ) }}" class="form-control pan-validate" tabindex="1" placeholder="Enter Company Pan" maxlength="10" @if (($pan_no ?? $data['business_info']['company_pan']['pan_no'])) readonly="readonly" @endif >
											</div>
											@error('biz_pan_number')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Unique Customer Identification Code(UCIC)
												<span class="mandatory">*</span>
											</label>
											<input type="text" name="ucic_code" id="ucic_code" value="{{ $ucicCode ?? '' }}" class="form-control" placeholder="UCIC No." readonly>
											@error('ucic_code')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<span class="span_gst_select" style="display: {{ $data['business_info']['gst_no']['is_gst_manual'] != 1 ? 'block' : 'none' }}">
												<label for="txtPassword">GST Number
													<span class="mandatory">*</span>
												</label>
												<select class="form-control" name="biz_gst_number" tabindex="2" onchange="fillEntity(this.value)">
													<option value="">Select GST Number</option>
													@forelse($gstList as $gst_key => $gst_value)
														<option value="{{ $gst_value->pan_gst_hash }}" {{ (old('biz_gst_number', $data['business_info']['gst_no']['pan_gst_hash']) == $gst_value->pan_gst_hash)? 'selected':'' }}> {{ $gst_value->pan_gst_hash }}</option>
													@empty
													@endforelse
												</select>
												@error('biz_gst_number')
													<span class="text-danger error">{{ $message }}</span>
												@enderror
											</span>
											<input type="hidden" name="is_gst_manual" value="{{ old('is_gst_manual', $data['business_info']['gst_no']['is_gst_manual']) }}" />
											<span class="span_gst_text" style="display: {{ $data['business_info']['gst_no']['is_gst_manual'] == 1 ? 'block' : 'none' }}">
												<label for="txtPassword">GST Number</label>
												<input type="text" name="biz_gst_number_text" value="{{ old('biz_gst_number_text', $data['business_info']['gst_no']['pan_gst_hash']) }}" class="form-control pan-validate" tabindex="2" placeholder="Enter GST Number" maxlength="15" />
											</span>

										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtEmail">Entity Name <span class="mandatory">*</span> </label>
											<input type="text" name="biz_entity_name" value="{{ old('biz_entity_name', $data['business_info']['entity_name']) }}" class="form-control" tabindex="3" placeholder="Enter Entity Name" maxlength="100">
											@error('biz_entity_name')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											@if(config('proin.CONFIGURE_API') && $data['business_info']['gst_no']['is_gst_manual'] == 0)
											<div id="biz_cin_select">
												<label for="txtPassword">Select CIN</label>
												<select class="form-control" name="biz_cin" tabindex="4">
													<option value="">Select CIN Number</option>
													@forelse($cinList as $cin_key => $cin_value)
													<option value="{{ $cin_value->cin }}" {{ (old('biz_cin', $data['business_info']['cin_no']) == $cin_value->cin)? 'selected':'' }}> {{ $cin_value->cin }}</option>
													@empty
													@endforelse
												</select>
											</div>
											@else
											<div id="biz_cin_text">
												<label for="txtPassword">Enter CIN</label>
												<input type="text" name="biz_cin" value="{{ old('biz_cin', $data['business_info']['cin_no']) }}" class="form-control" tabindex="4" placeholder="Enter CIN Number" maxlength="21">
											</div>
											@endif
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Segment <span class="mandatory">*</span> </label>
											
											{!! Form::select('segment', [''=>trans('backend.please_select')] + $segmentList, $data['business_info']['segment'], ['id'=>'segmentId','class'=>'form-control industry_change', 'tabindex'=>'8']) !!}
											@error('segment')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Industry <span class="mandatory">*</span> </label>
											{!! Form::select('biz_type_id', [''=>trans('backend.please_select')] + $industryList, $data['business_info']['industry'], ['id'=>'biz_type_id','class'=>'form-control industry_change', 'tabindex'=>'4']) !!}
											@error('biz_type_id')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Sub Industry</label>
											{!! Form::select('entity_type_id', [''=>trans('backend.please_select')], $data['business_info']['sub_industry'], ['id'=>'entity_type_id','class'=>'form-control sub_industry', 'tabindex'=>'5']) !!}
											@error('entity_type_id')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtEmail">Business Constitution <span class="mandatory">*</span> </label>
											{!! Form::select('biz_constitution', [''=>trans('backend.please_select')] + $constitutionList, $data['business_info']['biz_constitution'], ['class'=>'form-control constitution_id', 'tabindex'=>'7']) !!}
											@error('biz_constitution')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtEmail">MSME TYPE <span class="mandatory">*</span> </label>
											{!! Form::select('msme_type', [''=>trans('backend.please_select')] + config('common.MSMETYPE'), $data['business_info']['msme_type'], ['class'=>'form-control', 'tabindex'=>'7']) !!}
											@error('msme_type')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtEmail">MSME NUMBER <span class="mandatory">*</span> </label>
											<input type="text" name="msme_no" value="{{ old('msme_no', $data['business_info']['msme_no']) }}" class="form-control" tabindex="3" placeholder="Enter MSME Number" maxlength="30">
											@error('msme_no')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Date of Incorporation <span class="mandatory">*</span> </label>
											<input type="text" name="incorporation_date" value="{{ old('incorporation_date', $data['business_info']['incorporation_date']) }}" class="form-control datepicker-dis-fdate" tabindex="5" placeholder="Enter Entity Name" autocomplete="off" readonly>
											@error('incorporation_date')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input INR">
											<label for="txtPassword">Business Turnover </label>
											<div class="relative">
												<a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a>
												<input type="text" name="biz_turnover" value="{{ old('biz_turnover', $data['business_info']['business_turnover']) }}" class="form-control number_format" tabindex="9" placeholder="Enter Business Turnover" maxlength="19">
											</div>
											@error('biz_turnover')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Share Holding % as on <span class="mandatory">*</span> </label>
											<input type="text" name="share_holding_date" value="{{ old('share_holding_date', $data['business_info']['share_holding_per']) }}" class="form-control datepicker-dis-fdate" tabindex="5" placeholder="Enter Share Holding Date" autocomplete="off" readonly>
											@error('share_holding_date')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Date of commencement of business </label>
											<input type="text" name="busi_pan_comm_date" value="{{ $data['business_info']['commencement_date'] }}" class="form-control datepicker-dis-fdate" tabindex="5" placeholder="Enter Date of commencement of business" autocomplete="off" readonly>
											@error('busi_pan_comm_date')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="label">Label 1</label>
											<input type="text" name="label[1]" value="{{ old('label.1', $data['business_info']['label']['1']) }}" class="form-control" tabindex="5" placeholder="Enter label" autocomplete="off">
											@error('label.1')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="label">Label 2</label>
											<input type="text" name="label[2]" value="{{ old('label.2',$data['business_info']['label']['2']) }}" class="form-control" tabindex="5" placeholder="Enter label" autocomplete="off">
											@error('label.2')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="label">Label 3</label>
											<input type="text" name="label[3]" value="{{ old('label.3',$data['business_info']['label']['3']) }}" class="form-control" tabindex="5" placeholder="Enter label" autocomplete="off">
											@error('label.3')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Email
												<span class="mandatory">*</span>
											</label>
											<input type="email" name="email" value="{{ old('email',($data['business_info']['email'] ?? ''))}}" class="form-control" tabindex="5" placeholder="Enter Email address" autocomplete="off">
											@error('email')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Mobile
												<span class="mandatory">*</span>
											</label>
											<input type="text" name="mobile" value="{{ old('mobile',($data['business_info']['mobile'] ?? ''))}}" class="form-control" tabindex="5" placeholder="Enter Mobile no" autocomplete="off" maxlength="10" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
											@error('mobile')
												<span class="text-danger error">{{ $message }}</span>
											@enderror
										</div>
									</div>
								</div>
							</div>

							<div class="form-sections">
								<div class="row">
									<div class="col-md-12">
										<h5 class="form-head">Product Type</h5>
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label for="txtSupplierName">Product Type <span class="mandatory">*</span></label><br/>
													<div id="check_block">
														@if(array_key_exists(1, $product_types->toArray()))
															<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;">
																<input type="checkbox" class="product-type" value="1" name="product_id[1][checkbox]" {{ (in_array(1, $data['product_type']['product_type']) || (old('product_id.1.checkbox') == '1'))? 'checked': '' }}> Supply Chain
															</label>
														@endif
														@if(array_key_exists(2, $product_types->toArray()))
															<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;">
																<input type="checkbox" class="product-type" value="2" name="product_id[2][checkbox]" {{ (in_array(2, $data['product_type']['product_type']) || (old('product_id.2.checkbox') == '2'))? 'checked': '' }}> Term Loan
															</label>
														@endif
														@if(array_key_exists(3, $product_types->toArray()))
															<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;">
																<input type="checkbox" class="product-type" value="3" name="product_id[3][checkbox]" {{ (in_array(3, $data['product_type']['product_type']) || (old('product_id.3.checkbox') == '3'))? 'checked': '' }}> Leasing
															</label>
														@endif
													</div>
													@error('product_id')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>

											<div class="col-md-4 product-type-1 {{ (in_array(1, $data['product_type']['product_type']) || (old('product_id.1.checkbox') == '1'))? '': 'hide' }}">
												<div class="form-group INR">
													<label for="txtCreditPeriod">Supply Chain Loan Amount <span class="mandatory">*</span> </label>
													<div class="relative">
														<a href="javascript:void(0);" class="remaining"> <i class="fa fa-inr" aria-hidden="true"></i></a>
														<input type="text" name="product_id[1][loan_amount]" value="{{ old('product_id.1.loan_amount', number_format( $data['product_type']['1']['loan_amount'])) }}" class="form-control number_format" tabindex="10" placeholder="Enter Supply Chain Loan Amount" maxlength="19">
													</div>
													<div id="product_type_1_loan"></div>
													@error('product_id.1.loan_amount')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>

											<div class="col-md-4 product-type-1 {{ (in_array(1, $data['product_type']['product_type']) || (old('product_id.1.checkbox') == '1'))? '': 'hide' }}">
												<div class="form-group">
													<label for="txtSupplierName">Supply Chain Tenor (Days)
													</label>
													<input type="text" name="product_id[1][tenor_days]" value="{{ old('product_id.1.tenor_days', number_format($data['product_type']['1']['tenor'])) }}" class="form-control number_format" tabindex="11" placeholder="Enter Supply Chain Tenor (Days)" maxlength="3">
													<div id="product_type_1_tenor"></div>
													@error('product_id.1.tenor_days')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>

											<div class="col-md-4 product-type-2 {{ (in_array(2, $data['product_type']['product_type']) || (old('product_id.2.checkbox') == '2'))? '': 'hide' }}">
												<div class="form-group INR">
													<label for="txtCreditPeriod">Term Loan Amount
														<span class="mandatory">*</span>
													</label>
													<div class="relative">
														<a href="javascript:void(0);" class="remaining"> <i class="fa fa-inr" aria-hidden="true"></i></a>
														<input type="text" name="product_id[2][loan_amount]" value="{{ old('product_id.2.loan_amount', number_format($data['product_type']['2']['loan_amount'])) }}" class="form-control number_format" tabindex="10" placeholder="Enter Term Loan Amount" maxlength="19">
													</div>
													<div id="product_type_2_loan"></div>
													@error('product_id.2.loan_amount')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>

											<div class="col-md-4 product-type-2 {{ (in_array(2, $data['product_type']['product_type']) || (old('product_id.2.checkbox') == '2'))? '': 'hide' }}">
												<div class="form-group">
													<label for="txtSupplierName">Term Tenor (Months) </label>
													<input type="text" name="product_id[2][tenor_days]" value="{{ old('product_id.2.tenor_days',number_format($data['product_type']['2']['tenor'])) }}" class="form-control number_format" tabindex="11" placeholder="Enter Term Tenor (Months)" maxlength="3">
													<div id="product_type_2_tenor"></div>
													@error('product_id.2.tenor_days')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>

											<div class="col-md-4 product-type-3 {{ (in_array(3, $data['product_type']['product_type']) || (old('product_id.3.checkbox') == '3'))? '': 'hide' }}">
												<div class="form-group INR">
													<label for="txtCreditPeriod">Leasing Loan Amount <span class="mandatory">*</span> </label>
													<div class="relative">
														<a href="javascript:void(0);" class="remaining">
														<i class="fa fa-inr" aria-hidden="true"></i></a>
														<input type="text" name="product_id[3][loan_amount]" value="{{ old('product_id.3.loan_amount', number_format($data['product_type']['3']['loan_amount'])) }}" class="form-control number_format" tabindex="10" placeholder="Enter Leasing Loan Amount" maxlength="19">
													</div>
													<div id="product_type_3_loan"></div>
													@error('product_id.3.loan_amount')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>

											<div class="col-md-4 product-type-3 {{ (in_array(3, $data['product_type']['product_type']) || (old('product_id.3.checkbox') == '3'))? '': 'hide' }}">
												<div class="form-group">
													<label for="txtSupplierName">Leasing Tenor (Months) </label>
													<input type="text" name="product_id[3][tenor_days]" value="{{ old('product_id.3.tenor_days', number_format($data['product_type']['3']['tenor'])) }}" class="form-control number_format" tabindex="11" placeholder="Enter Leasing Tenor (Months)" maxlength="3">
													<div id="product_type_3_tenor"></div>
													@error('product_id.3.tenor_days')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="form-sections">
								<div class="row">
									<div class="col-md-12">
										<h5 class="form-head">GST Address</h5>
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label for="txtCreditPeriod">Address <span class="mandatory gst_address">*</span> </label>
													<input type="text" name="biz_address" value="{{ old('biz_address', $data['gst_address']['address']) }}" class="form-control" tabindex="12" placeholder="Enter Your Address" maxlength="100">
													@error('biz_address')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group password-input">
													<label for="txtPassword">State <span class="mandatory gst_address">*</span> </label>
													<select class="form-control" name="biz_state" tabindex="13">
														<option value=""> Select State</option>
														@foreach($states as $key => $state)
															<option value="{{ $state->id }}" {{ (old('biz_state', $data['gst_address']['state_id']) == $state->id)? 'selected':'' }}> {{ $state->name }} </option>
														@endforeach
													</select>
													@error('biz_state')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="txtEmail">City <span class="mandatory gst_address">*</span> </label>
													<input type="text" name="biz_city" value="{{ old('biz_city', $data['gst_address']['city']) }}" class="form-control" tabindex="14" placeholder="Enter City Name" maxlength="50">
													@error('biz_city')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>
											<div class="col-md-2">

												<div class="form-group password-input">
													<label for="txtPassword">Pin Code <span class="mandatory gst_address">*</span> </label>
													<input type="text" name="biz_pin" value="{{ old('biz_pin', $data['gst_address']['pincode']) }}" class="form-control" tabindex="15" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
													@error('biz_pin')
														<span class="text-danger error">{{ $message }}</span>
													@enderror
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group password-input">
													<label for="txtPassword">Address Label
														<span class="mandatory gst_address">*</span>
													</label>
													<select class="form-control" name="location_id" tabindex="13">
														<option value=""> Address Label</option>
														@foreach($locationType as $key => $location)
															<option value="{{ $location->location_id }}" {{ (old('location_id', $data['gst_address']['address_label']) == $location->location_id)? 'selected':'' }}> {{ $location->name }} </option>
														@endforeach
													</select>
													@error('location_id')
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
										<div class="sameas"><input type="checkbox" class="mr-2" onchange="copyAddress('#collapseOne',this)"> <span> Same as GST Address </span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseOne">
											<a class="card-title"> Communication Address </a>
										</div>
										<div id="collapseOne" class="card-body collapse" data-parent="#accordion">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label for="txtCreditPeriod">Address </label>
															<input type="text" id="biz_other_address_c" name="biz_other_address[]" value="{{ old('biz_other_address.0', $data['other_address']['communication']['address']) }}" class="form-control" tabindex="16" placeholder="Enter Your Address" maxlength="100">
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group password-input">
															<label for="txtPassword">State </label>
															<select class="form-control" id="biz_other_state_c" name="biz_other_state[]" tabindex="17"> 
																<option value=""> Select State</option>
																@foreach($states as $key => $state)
																	<option value="{{ $state->id }}" {{ (old('biz_other_state.0', $data['other_address']['communication']['state_id']) == $state->id)? 'selected':'' }}> {{ $state->name }} </option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="txtEmail">City
															</label>
															<input type="text" id="biz_other_city_c" name="biz_other_city[]" value="{{ old('biz_other_city.0', $data['other_address']['communication']['city']) }}" class="form-control" tabindex="18" placeholder="Enter City Name" maxlength="50">
														</div>
													</div>
													<div class="col-md-2">

														<div class="form-group password-input">
															<label for="txtPassword">Pin Code
															</label>
															<input type="text" id="biz_other_pin_c" name="biz_other_pin[]" value="{{ old('biz_other_pin.0', $data['other_address']['communication']['pincode']) }}" class="form-control" tabindex="19" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group password-input">
															<label for="txtPassword">Address Label </label>
															<select class="form-control" id="location_other_id_c" name="location_other_id[]" tabindex="13">
																<option value=""> Address Label</option>
																@foreach($locationType as $key => $location)
																	<option value="{{ $location->location_id }}" {{ (old('location_other_id.0', $data['other_address']['communication']['address_label']) == $location->location_id)? 'selected':'' }}> {{ $location->name }} </option>
																@endforeach
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="card card-color mb-0" style="display: none;">
										<div class="sameas"><input type="checkbox" class="mr-2"
												onchange="copyAddress('#collapseTwo',this)"> <span> Same as GST Address
											</span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseTwo">
											<a class="card-title">GST Address</a>
										</div>
										<div id="collapseTwo" class="card-body collapse" data-parent="#accordion">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label for="txtCreditPeriod">Address </label>
															<input type="text" id="biz_other_address_g" name="biz_other_address[]" value="{{ old('biz_other_address.1', $data['other_address']['gst']['address']) }}" class="form-control" tabindex="20" placeholder="Enter Your Address" maxlength="100">
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group password-input">
															<label for="txtPassword">State
															</label>

															<select class="form-control" id="biz_other_state_g" name="biz_other_state[]"
																tabindex="21">
																<option value=""> Select State</option>
																@foreach($states as $key => $state)
																	<option value="{{ $state->id }}"
																		{{ (old('biz_other_state.1', $data['other_address']['gst']['state_id']) == $state->id)? 'selected':'' }}>
																		{{ $state->name }} </option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="txtEmail">City </label>
															<input type="text" id="biz_other_city_g" name="biz_other_city[]" value="{{ old('biz_other_city.1', $data['other_address']['gst']['city']) }}" class="form-control" tabindex="22" placeholder="Enter City Name" maxlength="50">
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group password-input">
															<label for="txtPassword">Pin Code </label>
															<input type="text" id="biz_other_pin_g" name="biz_other_pin[]" value="{{ old('biz_other_pin.1', $data['other_address']['gst']['pincode']) }}" class="form-control" tabindex="23" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group password-input">
															<label for="txtPassword">Address Label </label>
															<select class="form-control" id="location_other_id_g" name="location_other_id[]" tabindex="13">
																<option value=""> Address Label</option>
																@foreach($locationType as $key => $location)
																	<option value="{{ $location->location_id }}" {{ (old('location_other_id.1', $data['other_address']['gst']['address_label']) == $location->location_id)? 'selected':'' }}> {{ $location->name }} </option>
																@endforeach
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox" class="mr-2"
												onchange="copyAddress('#collapseThree', this)"> <span> Same as GST
												Address
											</span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseThree">
											<a class="card-title">Warehouse Address</a>
										</div>
										<div id="collapseThree" class="card-body collapse" data-parent="#accordion">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label for="txtCreditPeriod">Address </label>
															<input type="text" id="biz_other_address_w" name="biz_other_address[]" value="{{ old('biz_other_address.2', $data['other_address']['warehouse']['address']) }}" class="form-control" tabindex="24" placeholder="Enter Your Address" maxlength="100">
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group password-input">
															<label for="txtPassword">State
															</label>
															<select class="form-control" id="biz_other_state_w" name="biz_other_state[]" tabindex="25">
																<option value=""> Select State</option>
																@foreach($states as $key => $state)
																	<option value="{{ $state->id }}" {{ (old('biz_other_state.2', $data['other_address']['warehouse']['state_id']) == $state->id)? 'selected':'' }}> {{ $state->name }} </option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="txtEmail">City </label>
															<input type="text" id="biz_other_city_w" name="biz_other_city[]" value="{{ old('biz_other_city.2',$data['other_address']['warehouse']['city']) }}" class="form-control" tabindex="26" placeholder="Enter City Name" maxlength="50">
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group password-input">
															<label for="txtPassword">Pin Code </label>
															<input type="text" id="biz_other_pin_w" name="biz_other_pin[]" value="{{ old('biz_other_pin.2', $data['other_address']['warehouse']['pincode']) }}" class="form-control" tabindex="27" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6">
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group password-input">
															<label for="txtPassword">Address Label
															</label>
															<select class="form-control" id="location_other_id_w" name="location_other_id[]" tabindex="13">
																<option value=""> Address Label</option>
																@foreach($locationType as $key => $location)
																	<option value="{{ $location->location_id }}" {{ (old('location_other_id.2', $data['other_address']['warehouse']['address_label']) == $location->location_id)? 'selected':'' }}> {{ $location->name }} </option>
																@endforeach
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="card card-color mb-0">
										<div class="sameas"><input type="checkbox" class="mr-2" onchange="copyAddress('#collapseFour', this)"> <span> Same as GST Address </span></div>
										<div class="card-header collapsed" data-toggle="collapse" href="#collapseFour"> <a class="card-title">Factory Address</a> </div>
										<div id="collapseFour" class="card-body collapse" data-parent="#accordion">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label for="txtCreditPeriod">Address </label>
															<input type="text" name="biz_other_address[]" value="{{ old('biz_other_address.3', $data['other_address']['factory']['address']) }}" class="form-control" tabindex="28" placeholder="Enter Your Address" maxlength="100" id="biz_other_address_f">
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group password-input">
															<label for="txtPassword">State
															</label>
															<select class="form-control" id="biz_other_state_f" name="biz_other_state[]" tabindex="29">
																<option value=""> Select State</option>
																@foreach($states as $key => $state)
																	<option value="{{ $state->id }}" {{ (old('biz_other_state.3', $data['other_address']['factory']['state_id']) == $state->id)? 'selected':'' }}> {{ $state->name }} </option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="txtEmail">City </label>
															<input type="text" name="biz_other_city[]" value="{{ old('biz_other_city.3', $data['other_address']['factory']['city']) }}" class="form-control" tabindex="30" placeholder="Enter City Name" maxlength="50" id="biz_other_city_f">
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group password-input">
															<label for="txtPassword">Pin Code </label>
															<input type="text" name="biz_other_pin[]" value="{{ old('biz_other_pin.3', $data['other_address']['factory']['pincode']) }}" class="form-control" tabindex="31" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6" id="biz_other_pin_f">
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group password-input">
															<label for="txtPassword">Address Label </label>
															<select class="form-control" name="location_other_id[]" id="location_other_id_f" tabindex="29">
																<option value=""> Address Label</option>
																@foreach($locationType as $key => $location)
																	<option value="{{ $location->location_id }}" {{ (old('location_other_id.3', $data['other_address']['factory']['address_label']) == $location->location_id)? 'selected':'' }}> {{ $location->name }} </option>
																@endforeach
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							@if($viewOnly)
								@can('company_details_save')
									<div class="d-flex btn-section" style="/*display: none !important;*/">
										<div class="ml-auto text-right">
											<input type="submit" value="Save and Continue"
												class="btn btn-success btn-sm">
										</div>
									</div>
								@endcan
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
	var messages = {
		biz_pan_to_gst_karza: "{{ URL::route('chk_biz_pan_to_gst') }}",
		biz_gst_to_entity_karza: "{{ URL::route('chk_biz_gst_to_entity') }}",
		biz_entity_to_cin_karza: "{{ URL::route('chk_biz_entity_to_cin') }}",
		data_not_found: "{{ trans('error_messages.data_not_found') }}",
		get_sub_industry: "{{ URL::route('get_sub_industry') }}",
		please_select: "{{ trans('backend.please_select') }}",
		token: "{{ csrf_token() }}",
		configure_api: "{{ config('proin.CONFIGURE_API') }}"
	};

	$(document).ready(function () {
		$(".product-type").click(function () {
			var productType = $(this).val();
			var isChecked = $(this).prop("checked");

			if (isChecked) {
				$(".product-type-" + productType).removeClass('hide');
			} else {
				$(".product-type-" + productType).addClass('hide');
			}
		});
	});
</script>
<script src="{{ url('common/js/business_info.js?v=1.2') }}"></script>
<script>
	var subind = "{{ ($data['business_info']['sub_industry'] ?? '') }}";
	var segmentId = "{{ ($data['business_info']['segment'] ?? '') }}";
	handleIndustryChange($("#biz_type_id").val(),subind, segmentId);
</script>
@endsection