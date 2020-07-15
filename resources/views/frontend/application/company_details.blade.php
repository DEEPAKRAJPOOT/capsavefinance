@extends('layouts.app')

@section('content')
    <!-- partial -->
<div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{ route('business_information_open', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">Business Information</a>
        </li>
        <li>
            <a href="{{ route('promoter-detail', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'edit' => 1]) }}">Management Information</a>
        </li>
        <li>
            <a href="{{ route('document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'edit' => 1]) }}">Documents</a>
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
				<!-- <input type="hidden" name="biz_cin" value=""> -->
				<input type="hidden" name="pan_api_res" value="">
				<input type="hidden" name="cin_api_res" value="">
				<div class=" form-fields">
						<div class="form-sections">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="txtEmail">Company PAN
											<span class="mandatory">*</span>
										</label>
										<span class="text-success" id="pan-msg" style="">
											<i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i>
										</span>
										<a href="javascript:void(0);" class="verify-owner-no pan-verify" style="pointer-events: none;">Verified</a>
										<input type="text" name="biz_pan_number" value="{{old('biz_pan_number', $business_info->pan->pan_gst_hash)}}" class="form-control pan-validate" tabindex="1" placeholder="Enter Company Pan" maxlength="10" readonly>
										@error('biz_pan_number')
											<span class="text-danger error">{{ $message }}</span>
										@enderror
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group password-input">
										
										<span class="span_gst_select" style="display: {{$business_info->is_gst_manual!=1 ? 'block' : 'none' }}">
										<label for="txtPassword">GST Number
											<span class="mandatory">*</span>
										</label>
										<!--<a href="javascript:void(0);" class="verify-owner-no">Verify</a>-->
										<select class="form-control" name="biz_gst_number" tabindex="2" onchange="fillEntity(this.value)" >
											<option value="">Select GST Number</option>
											@forelse($business_info->gsts as $gst_key => $gst_value)
												<option value="{{$gst_value->pan_gst_hash}}" {{(old('biz_gst_number', Helpers::customIsset($business_info->gst, 'pan_gst_hash')) == $gst_value->pan_gst_hash)? 'selected':''}}>{{$gst_value->pan_gst_hash}}</option>
											@empty
											@endforelse
										</select>
										@error('biz_gst_number')
											<span class="text-danger error">{{ $message }}</span>
										@enderror
										</span>
										<input type="hidden" name="is_gst_manual" value="{{old('is_gst_manual', $business_info->is_gst_manual)}}"/>
										<span class="span_gst_text" style="display: {{$business_info->is_gst_manual==1 ? 'block' : 'none' }}">
											<label for="txtPassword">GST Number</label>
											<input type="text" name="biz_gst_number_text" value="{{old('biz_gst_number_text', $business_info->gst['pan_gst_hash'])}}" class="form-control pan-validate" tabindex="2" placeholder="Enter GST Number" maxlength="15" />
										</span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="txtEmail">Entity Name
											<span class="mandatory">*</span>
										</label>
										<input type="text" name="biz_entity_name" value="{{old('biz_entity_name', $business_info->biz_entity_name)}}" class="form-control" tabindex="3" placeholder="Enter Entity Name" maxlength="100" >
										@error('biz_entity_name')
											<span class="text-danger error">{{ $message }}</span>
										@enderror
									</div>
								</div>
							</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group password-input" style="display: {{$business_info->is_gst_manual!=1 ? 'block' : 'none' }}">
											<label for="txtPassword">Select CIN
													<span class="mandatory mandatory-biz-cin" @if (isset($business_info->cins) && count($business_info->cins) == 0) style="display:none;"  @endif>*</span>
											</label>

											<select class="form-control" name="biz_cin" tabindex="2">
												<option value="">Select CIN Number</option>
												@forelse($business_info->cins as $cin_key => $cin_value)
													<option value="{{$cin_value->cin}}" {{(old('biz_cin', Helpers::customIsset($business_info->cin, 'cin')) == $cin_value->cin)? 'selected':''}}>{{$cin_value->cin}}</option>
												@empty
												@endforelse
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Segment
												<span class="mandatory">*</span>
											</label>
											<!-- <select class="form-control" name="segment" tabindex="8" id="segmentId" >
												<option value=""> Select Segment</option>
												<option value="1" {{(old('segment', $business_info->biz_segment) == 1)? 'selected':''}}> Segment 1 </option>
												<option value="2" {{(old('segment', $business_info->biz_segment) == 2)? 'selected':''}}> Segment 2 </option>
												<option value="3" {{(old('segment', $business_info->biz_segment) == 3)? 'selected':''}}> Segment 3 </option>
											</select> -->

											{!! Form::select('segment', [''=>trans('backend.please_select')] + $segmentList, $business_info->biz_segment, ['id'=>'segmentId','class'=>'form-control industry_change', 'tabindex'=>'8']) !!}

											
											@error('segment')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Industry
												<span class="mandatory">*</span>
											</label>
											{!! Form::select('biz_type_id', [''=>trans('backend.please_select')] + $industryList, $business_info->nature_of_biz, ['id'=>'biz_type_id','class'=>'form-control industry_change', 'tabindex'=>'4']) !!}	
											@error('biz_type_id')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>
																	
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Sub Industry</label>
											{!! Form::select('entity_type_id', [''=>trans('backend.please_select')], $business_info->entity_type_id, ['id'=>'entity_type_id','class'=>'form-control sub_industry', 'tabindex'=>'5']) !!}
											@error('entity_type_id')
								                <span class="text-danger error">{{ $message }}</span>
								            @enderror
										</div>
									</div>		
									<div class="col-md-4">
										<div class="form-group">
											<label for="txtEmail">Business Constitution
												<span class="mandatory">*</span>
											</label>
												{!! Form::select('biz_constitution', [''=>trans('backend.please_select')] + $constitutionList, $business_info->biz_constitution, ['class'=>'form-control constitution_id', 'tabindex'=>'7']) !!}
												@error('biz_constitution')
													<span class="text-danger error">{{ $message }}</span>
												@enderror
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Date of Incorporation
												<span class="mandatory">*</span>
											</label>
											<input type="text" name="incorporation_date" value="{{old('incorporation_date', \Carbon\Carbon::parse($business_info->date_of_in_corp)->format('d/m/Y'))}}" class="form-control datepicker-dis-fdate" tabindex="5" placeholder="Enter Entity Name" autocomplete="off" readonly >
											@error('incorporation_date')
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
									
									<div class="col-md-4">
										<div class="form-group password-input">
											<label for="txtPassword">Share Holding % as on
												<span class="mandatory">*</span>
											</label>
											<input type="text" name="share_holding_date" value="{{old('share_holding_date', \Carbon\Carbon::parse($business_info->share_holding_date)->format('d/m/Y'))}}" class="form-control datepicker-dis-fdate" tabindex="5" placeholder="Enter Share Holding Date" autocomplete="off" readonly >
											@error('share_holding_date')
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
												<label for="txtSupplierName">Product Type <span class="mandatory">*</span>
												</label><br/>
												<div id="check_block">
													@if(array_key_exists(1, $product_types->toArray()))
													<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" class="product-type" value="1" name="product_id[1][checkbox]" {{ (array_key_exists(1, $product_ids) || (old('product_id.1.checkbox') == '1'))? 'checked': ''}} > Supply Chain</label>
													@endif
													@if(array_key_exists(2, $product_types->toArray()))
													<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" class="product-type" value="2" name="product_id[2][checkbox]" {{ (array_key_exists(2, $product_ids) || (old('product_id.2.checkbox') == '2'))? 'checked': ''}} > Term Loan</label>
													@endif
													@if(array_key_exists(3, $product_types->toArray()))
													<label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" class="product-type" value="3" name="product_id[3][checkbox]" {{ (array_key_exists(3, $product_ids) || (old('product_id.3.checkbox') == '3'))? 'checked': ''}} > Leasing</label>
													@endif
												</div>
												@error('product_id')
													<span class="text-danger error">{{ $message }}</span>
												@enderror
											</div>
										</div>
										
										<div class="col-md-4 product-type-1 {{ (array_key_exists(1, $product_ids) || (old('product_id.1.checkbox') == '1'))? '': 'hide'}}">
											<div class="form-group INR">
												<label for="txtCreditPeriod">Supply Chain Loan Amount
													<span class="mandatory">*</span>
												</label>
												<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
												<input type="text" name="product_id[1][loan_amount]" value="@if(array_key_exists(1, $product_ids)){{ old('product_id.1.loan_amount', number_format($product_ids['1']['loan_amount'])) }}@else{{ old('product_id.1.loan_amount','')}}@endif" class="form-control number_format" tabindex="10" placeholder="Enter Supply Chain Loan Amount" maxlength="19" >
												<!-- <p class="float-right inr-box"><i>Enter amount in lakhs</i></p> -->
												<div id="product_type_1_loan"></div>
												@error('product_id.1.loan_amount')
													<span class="text-danger error">{{ $message }}</span>
												@enderror
											</div>
										</div>
	
										<div class="col-md-4 product-type-1 {{ (array_key_exists(1, $product_ids) || (old('product_id.1.checkbox') == '1'))? '': 'hide'}}">
											<div class="form-group">
												<label for="txtSupplierName">Supply Chain Tenor (Days)
												</label>
												<input type="text" name="product_id[1][tenor_days]" value="@if(array_key_exists(1, $product_ids)){{ old('product_id.1.tenor_days', number_format($product_ids[1]['tenor_days'])) }}@else{{ old('product_id.1.tenor_days', '') }} @endif" class="form-control number_format" tabindex="11" placeholder="Enter Supply Chain Tenor (Days)" maxlength="3">
												<div id="product_type_1_tenor"></div>
												@error('product_id.1.tenor_days')
													<span class="text-danger error">{{ $message }}</span>
												@enderror
											</div>
										</div>
	
										<div class="col-md-4 product-type-2 {{ (array_key_exists(2, $product_ids) || (old('product_id.2.checkbox') == '2'))? '': 'hide'}}">
											<div class="form-group INR">
												<label for="txtCreditPeriod">Term Loan Amount
													<span class="mandatory">*</span>
												</label>
												<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
												<input type="text" name="product_id[2][loan_amount]" value="@if(array_key_exists(2, $product_ids)){{ old('product_id.2.loan_amount', number_format($product_ids[2]['loan_amount'])) }}@else{{ old('product_id.2.loan_amount','')}}@endif" class="form-control number_format" tabindex="10" placeholder="Enter Term Loan Amount" maxlength="19" >
												<!-- <p class="float-right inr-box"><i>Enter amount in lakhs</i></p> -->
												<div id="product_type_2_loan"></div>
												@error('product_id.2.loan_amount')
													<span class="text-danger error">{{ $message }}</span>
												@enderror
											</div>
										</div>
	
										<div class="col-md-4 product-type-2 {{ (array_key_exists(2, $product_ids) || (old('product_id.2.checkbox') == '2'))? '': 'hide'}}">
											<div class="form-group">
												<label for="txtSupplierName">Term Tenor (Months)
												</label>
												<input type="text" name="product_id[2][tenor_days]" value="@if(array_key_exists(2, $product_ids)){{ old('product_id.2.tenor_days',number_format($product_ids[2]['tenor_days']))}}@else{{ old('product_id.2.tenor_days', '') }} @endif" class="form-control number_format" tabindex="11" placeholder="Enter Term Tenor (Months)" maxlength="3">
												<div id="product_type_2_tenor"></div>
												@error('product_id.2.tenor_days')
													<span class="text-danger error">{{ $message }}</span>
												@enderror
											</div>
										</div>
	
										<div class="col-md-4 product-type-3 {{ (array_key_exists(3, $product_ids) || (old('product_id.3.checkbox') == '3'))? '': 'hide'}}">
											<div class="form-group INR">
												<label for="txtCreditPeriod">Leasing Loan Amount
													<span class="mandatory">*</span>
												</label>
												<a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
												<input type="text" name="product_id[3][loan_amount]" value="@if (array_key_exists(3, $product_ids)){{ old('product_id.3.loan_amount', number_format($product_ids[3]['loan_amount']))}}@else{{ old('product_id.3.loan_amount', '') }}@endif" class="form-control number_format" tabindex="10" placeholder="Enter Leasing Loan Amount" maxlength="19" >
												<!-- <p class="float-right inr-box"><i>Enter amount in lakhs</i></p> -->
												<div id="product_type_3_loan"></div>
												@error('product_id.3.loan_amount')
													<span class="text-danger error">{{ $message }}</span>
												@enderror
											</div>
										</div>

	
										<div class="col-md-4 product-type-3 {{ (array_key_exists(3, $product_ids) || (old('product_id.3.checkbox') == '3'))? '': 'hide'}}">
											<div class="form-group">
												<label for="txtSupplierName">Leasing Tenor (Months)
												</label>
												<input type="text" name="product_id[3][tenor_days]" value="@if (array_key_exists(3, $product_ids)){{ old('product_id.3.tenor_days', number_format($product_ids[3]['tenor_days'])) }}@else{{ old('product_id.3.tenor_days', '') }}@endif" class="form-control number_format" tabindex="11" placeholder="Enter Leasing Tenor (Months)" maxlength="3">
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
												<label for="txtCreditPeriod">Address
													<span class="mandatory gst_address">*</span>
												</label>
												<input type="text" name="biz_address" value="{{old('biz_address', $business_info->address[0]->addr_1)}}" class="form-control" tabindex="12" placeholder="Enter Your Address" maxlength="100" >
												@error('biz_address')
                                                    <span class="text-danger error">{{ $message }}</span>
                                                @enderror
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group password-input">
												<label for="txtPassword">State
													<span class="mandatory gst_address">*</span>
												</label>
												<select class="form-control" name="biz_state" tabindex="13" >
                                                    <option value=""> Select State</option>
                                                    @foreach($states as $key => $state)
                                                    <option value="{{$state->id}}" {{(old('biz_state', $business_info->address[0]->state_id) == $state->id)? 'selected':''}}> {{$state->name}} </option>
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
													<span class="mandatory gst_address">*</span>
												</label>
												<input type="text" name="biz_city" value="{{old('biz_city', $business_info->address[0]->city_name)}}" class="form-control" tabindex="14" placeholder="Enter City Name" maxlength="50" >
												@error('biz_city')
                                                    <span class="text-danger error">{{ $message }}</span>
                                                @enderror
											</div>
										</div>
										<div class="col-md-2">

											<div class="form-group password-input">
												<label for="txtPassword">Pin Code
													<span class="mandatory gst_address">*</span>
												</label>
												<input type="text" name="biz_pin" value="{{old('biz_pin', $business_info->address[0]->pin_code)}}" class="form-control" tabindex="15" placeholder="Enter Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6" >
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
									<div class="sameas"><input type="checkbox"  class="mr-2" onchange="copyAddress('#collapseOne',this)"> <span> Same as GST Address
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
	                                                        <option value="{{$state->id}}" {{(old('biz_other_state.0', $business_info->address[1]->state_id) == $state->id)? 'selected':''}}> {{$state->name}} </option>
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

								<div class="card card-color mb-0" style="display: none;">
									<div class="sameas"><input type="checkbox"  class="mr-2" onchange="copyAddress('#collapseTwo',this)"> <span> Same as GST Address
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
	                                                        <option value="{{$state->id}}" {{(old('biz_other_state.1', $business_info->address[2]->state_id) == $state->id)? 'selected':''}}> {{$state->name}} </option>
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
									<div class="sameas"><input type="checkbox"  class="mr-2" onchange="copyAddress('#collapseThree', this)"> <span> Same as GST Address
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
	                                                        <option value="{{$state->id}}" {{(old('biz_other_state.2', $business_info->address[3]->state_id) == $state->id)? 'selected':''}}> {{$state->name}} </option>
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
	                                                        <option value="{{$state->id}}" {{(old('biz_other_state.3', $business_info->address[4]->state_id) == $state->id)? 'selected':''}}> {{$state->name}} </option>
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
								<input type="submit" value="Update and Continue" class="btn btn-success btn-sm">
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
var messages = {
	biz_pan_to_gst_karza: "{{ URL::route('chk_biz_pan_to_gst') }}",
	biz_gst_to_entity_karza: "{{ URL::route('chk_biz_gst_to_entity') }}",
	biz_entity_to_cin_karza: "{{ URL::route('chk_biz_entity_to_cin') }}",
	data_not_found: "{{ trans('error_messages.data_not_found') }}",
	get_sub_industry: "{{ URL::route('get_sub_industry') }}",
	please_select: "{{ trans('backend.please_select') }}",
	token: "{{ csrf_token() }}"
};
$(document).ready(function () {
	$(".product-type"). click(function(){
		var productType = $(this).val();
		var isChecked  = $(this).prop("checked");

		if(isChecked){
			$(".product-type-"+productType).removeClass('hide');
		}else{
			$(".product-type-"+productType).addClass('hide');
		}
	});
});
</script>
<!-- <script src="{{url('common/js/company_details.js?v=1')}}"></script> -->
<script src="{{url('common/js/business_info.js?v=1.1')}}"></script>
<script>
var subind={{$business_info->entity_type_id}};
var segmentId={{$business_info->biz_segment}};
handleIndustryChange($("#biz_type_id").val(),subind, segmentId);
</script>
@endsection