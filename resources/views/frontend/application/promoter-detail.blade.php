@extends('layouts.app')
@section('content')

<style>
    .upload-btn-wrapper input[type=file] {
        font-size: inherit;
        width: 75px;
        position: absolute;
        margin-left: 0px;
        height: 31px;
    }
    .setupload-btn > .error {
        position: absolute;
        top: -3px;
    }
</style>

<!-- partial -->
<div class="content-wrapper">

    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{ route('business_information_open', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" >Business Information</a>
        </li>
        <li>
            <a href="{{ route('promoter-detail', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">Management Information</a>
        </li>
        <li>
            <a href="{{ route('document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Documents</a>
        </li>      
    </ul>
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <form id="signupForm">
                    <div class="card-body">
                        @csrf
                        <input type="hidden" name="app_id" id="app_id"  value="{{ (!empty($appId)) ? $appId : '' }}" >
                        <input type="hidden" name="biz_id" id="biz_id"  value="{{ (!empty($bizId)) ? $bizId : '' }}" >   
                        <input type="hidden" id="rowcount" value="{{count($ownerDetails)}}">
                   
                        @php ($i = 0)
                        @php ($j = 0)
                        @if(count($manInfoData) > 0)
                            @foreach($manInfoData as $key => $manInfo)
                                @php ($i++)
                                @php( $manInfoDoc = $manInfoDocData['document_upload'][$manInfo['owner_id']] ?? [])

                        <div class="form-fields custom-promoter">
                            @csrf
                            <div class="col-md-12">
                                <h5 class="card-title form-head">Management Information ({{ isset($manInfo['name']) ? $i : '1'}}) </h5>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod" class="d-block"> Name

                                                <span class="mandatory">*</span>
                                                
                                                    {{--<span class="pull-right d-flex align-items-center"><input type="checkbox" name="is_promoter[]" data-id="{{isset($manInfo['name']) ? $i : '1'}}" id="is_promoter{{isset($manInfo['name']) ? $i : '1'}}" {{($row->is_promoter==1) ?  "checked='checked'" : ''}} value="{{($row->is_promoter==1) ?  '1' : '0'}}" class="is_promoter mr-2">
                                                    <span class="white-space-nowrap">Is Promoter</span></span>--}}
                                            </label>
                                            <input type="hidden" name="ownerid[]" id="ownerid{{isset($manInfo['name']) ? $i : '1'}}" value="{{ $manInfo['owner_id'] }}">   
                                            <input type="text" name="first_name[]" id="first_name{{isset($manInfo['name']) ? $i : '1'}}" vname="first_name1" value="{{ $manInfo['name'] }}" class="form-control first_name" placeholder="Enter First Name" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                      <div class="form-group password-input">
                                          <label for="txtPassword">Owner Type
                                          </label>
                                          <select class="form-control is_promoter" name="applicant_type[]" id="applicant_type{{isset($manInfo['name']) ? $i : '1'}}">

                                              <option value="" selected="selected"> Select Owner Type</option>
                                              <option value="1" @if(($manInfo['owner_type']) ==1)  selected="selected" @endif> Is Promoter </option>
                                              <option value="2" @if(($manInfo['owner_type']) ==2)  selected="selected" @endif> Key Management Person </option>
                                              <option value="3" @if(($manInfo['owner_type']) ==3)  selected="selected" @endif> Co-Borrower </option>
                                              <option value="4" @if(($manInfo['owner_type']) ==4)  selected="selected" @endif> Guarantor </option>
                                          </select>
                                      </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">Shareholding (%)</label>
                                              <input type="hidden" name="isShareCheck[]" id="isShareCheck{{isset($manInfo['name']) ? $i : '1'}}" value="{{(($manInfo['owner_type'])==1) ?  '1' : '0'}}">
                                            
                                            <input type="text"  id="share_per{{isset($manInfo['name']) ? $i : '1'}}" name="share_per[]" data-id="{{isset($manInfo['name']) ? $i : '1'}}" maxlength="6"  value="{{(($manInfo['shareholding'])=='0.00') ? '' : $manInfo['shareholding'] ?? ''}}" class="form-control share_per"  placeholder="Enter Shareholder" >
                                            <span class="error" id="shareCheck{{isset($manInfo['name']) ? $i : '1'}}"></span> 
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-4">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">DOB
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" readonly="readonly" name="date_of_birth[]" id="date_of_birth{{isset($manInfo['name']) ? $i : '1'}}" value="{{ ($manInfo['dob']) ? date('d/m/Y', strtotime($manInfo['dob'])) : '' }}" class="form-control date_of_birth datepicker-dis-fdate"  placeholder="Enter Date Of Birth" >
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">Gender
                                                <span class="mandatory">*</span>
                                            </label>
                                            <select class="form-control gender" name="gender[]" id="gender{{isset($manInfo['name']) ? $i : '1'}}">

                                                <option value=""> Select Gender</option>
                                                <option value="1" @if($manInfo['gender']==1)  selected="selected" @endif> Male </option>
                                                <option value="2" @if($manInfo['gender']==2)  selected="selected" @endif>Female </option>
                                                <option value="3" @if($manInfo['gender']==3)  selected="selected" @endif>Other </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">PAN Number
                                                <span class="mandatory">{{($is_lease==0) ? '*' : '' }}</span>
                                                <span class="text-success" id="successpanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ (isset($manInfo['is_pan_verified']) && $manInfo['is_pan_verified']) ? 'inline' : 'none' }}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>
                                                <span class="text-danger" id="failurepanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>
                                            
                                            </label>
                                             <div class="relative"> 
                                            <a href="javascript:void(0);" data-id="{{isset($manInfo['name']) ? $i : '1'}}" id="pan_verify{{isset($manInfo['name']) ? $i : '1'}}" class="verify-owner-no promoter_pan_verify" style=" pointer-events:{{isset($manInfo['is_pan_verified']) && $manInfo['is_pan_verified'] ? 'none' : '' }}">{{isset($manInfo['is_pan_verified']) && $manInfo['is_pan_verified'] ?  'Verified' : 'Verify' }}</a>
                                            <input type="text" name="pan_no[]" id="pan_no{{isset($manInfo['name']) ? $i : '1'}}" value="{{isset($manInfo['pan_no']) ? $manInfo['pan_no'] : '' }}" class="form-control pan_no" placeholder="Enter Pan Number" {{(isset($manInfo['is_pan_verified']) && $manInfo['is_pan_verified']) ? 'readonly' : '' }}>
                                           <input name="response[]" id="response{{isset($manInfo['name']) ? $i : '1'}}" type="hidden" value="">                       
                                             </div>
                                             </div>
                                    </div>
                                     
                                  {{--<div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtEmail">Educational Qualification

                                            </label>
                                            <input type="text" name="edu_qualification[]" id="edu_qualification{{isset($manInfo['name']) ? $i : '1'}}" value="{{$row->edu_qualification}}" class="form-control edu_qualification"  placeholder="Enter Education Qualification">
                                        </div>
                                    </div>--}}
                                  <div class="col-md-4">
                                        <div class="form-group">

                                            <label for="txtEmail">Designation    <span class="mandatory"></span>


                                            </label>
                                            <input type="text" name="designation[]" id="designation{{isset($manInfo['name']) ? $i : '1'}}" value="{{ $manInfo['designation'] ?? ''}}" class="form-control designation"  placeholder="Enter Designation">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                                 <label for="txtEmail">Other Ownerships
                                                 </label>
                                                 <input type="text" name="other_ownership[]" id="other_ownership{{isset($manInfo['name']) ? $i : '1'}}" value="{{ $manInfo['other_ownership'] ?? ''}}" class="form-control other_ownership"  placeholder="Other Ownership">
                                             </div>
                                     </div>
                                  
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Address
                                                <span class="mandatory">*</span>
                                            </label>
                                            <textarea  style="height: 35px;" class="form-control textarea address" placeholder="Enter Address" name="owner_addr[]" id="address{{isset($manInfo['name']) ? $i : '1'}}">{{ $manInfo['address'] ?? ''}}</textarea>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="row">
                                    <div class="col-md-4"> 
                                        <div class="form-group INR">
                                            <label for="txtEmail">Networth</label>
                                            <div class="relative">   
                                                <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                <input type="text" name="networth[]" maxlength='15' id="networth{{isset($manInfo['name']) ? $i : '1'}}" value="{{number_format($manInfo['networth'] ?? 0)}}" class="form-control networth"  placeholder="Enter Networth">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="txtEmail">Email </label>
                                        <input type="text" name="email[]"  id="email{{isset($manInfo['name']) ? $i : '1'}}" value="{{ $manInfo['email'] ?? '' }}" class="form-control email"  placeholder="Enter Email">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="txtEmail">Mobile <span class="mandatory">{{($is_lease==0) ? '*' : '' }}</span>  </label> 
                                            <input type="text" name="mobile_no[]" {{isset($manInfo['mobileNo']->mobile) ? 'readonly' : '' }} maxlength="10" id="mobile_no{{isset($manInfo['name']) ? $i : '1'}}" value="{{ isset($manInfo['mobileNo']->mobile) ? $manInfo['mobileNo']->mobile : $manInfo['mobile'] ?? '' }}" class="form-control mobileveri"  placeholder="Enter Mobile no">
                                              
                                             <span class="text-success float-left findMobileverify" id="v5successpanverify{{isset($manInfo['name']) ? $i : '1'}}"> <i class="fa fa-{{isset($manInfo['mobileNo']->mobile) ? 'check-circle' : '' }}" aria-hidden="true"></i><i>{{isset($manInfo['mobileNo']->mobile) ? 'Verified Successfully' : '' }}</i> </span>
                                                <span class="text-danger float-left" id="v5failurepanverify{{isset($manInfo['name']) ? $i : '1'}}"> </span>
                                                                                             
                                            </div>
                                        </div>
                                    <div class="col-md-3">
                                        <div class="form-group" >
                                            <label class="d-block">&nbsp;</label>
                                           <a class="btn btn-primary  btn-sm verify_mobile_no" data-id="{{isset($manInfo['name']) ? $i : '1'}}" name="verify_mobile_no" id="verify_mobile_no{{isset($manInfo['name']) ? $i : '1'}}" style="color:white;bottom: 15px;top: auto;  display:{{ isset($manInfo['mobileNo']->mobile) ? 'none' : ''}}" > {{ isset($manInfo['mobileNo']->mobile) ? 'Verified' : 'Verify without OTP' }}</a>
                                            <a class="btn btn-primary btn-sm ml-2 sen_otp_to_mobile" data-id="{{isset($manInfo['name']) ? $i : '1'}}" name="verify_mobile_otp_no" id="verify_mobile_otp_no{{isset($manInfo['name']) ? $i : '1'}}" style="color:white;bottom: 15px;top: auto; display:{{ (isset($manInfo['mobileOtpNo']['request_id'])) ? 'none' : ''}}" > {{ isset($manInfo['mobileOtpNo']['request_id']) ? 'Verified' : 'Verify with OTP' }}</a> 
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="toggleOtp{{isset($manInfo['name']) ? $i : '1'}}" style="display:none">
                                        <div class="form-group" >
                                               
                                                <label for="txtEmail">OTP <span class="mandatory">*</span></label> 
                                                <a class="verify-owner-no verify-show verify_otp" name="verify_otp" data-id="{{isset($manInfo['name']) ? $i : '1'}}"> {{isset($manInfo['mobileOtpNo']['request_id']) ?  'Verified' : 'Verify' }}</a>
                                                <input type="text" name="otp_no[]"   maxlength="6" id="verify_otp_no{{isset($manInfo['name']) ? $i : '1'}}" value="" class="form-control mobileotpveri"  placeholder="Enter OTP" >
                                               <span class="text-success float-left" id="v6successpanverify{{isset($manInfo['name']) ? $i : '1'}}"> {{isset($manInfo['mobileNo']->mobile) ? 'Verified Successfully' : '' }} </span>
                                                <span class="text-danger float-left" id="v6failurepanverify{{isset($manInfo['name']) ? $i : '1'}}"> </span>
                                                
                                            </div>
                                      </div>  
                                    <div class="col-md-3">
                                         <div class="form-group" id="pOtpVeriView{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{isset($manInfo['mobileOtpNo']['request_id']) ? 'inline' : 'none'}}">
                                             <label class="d-block" >Verified  OTP</label> 
                                           <span class="text-success float-left" id="v11successpanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{isset($manInfo['mobileOtpNo']['request_id']) ? 'inline' : 'none'}}"> <i class="fa fa-check-circle" aria-hidden="true"></i><i>Verified Successfully</i> </span>
                                               
                                      </div>
                                    </div>                           
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">CKYC Ref No
                                              
                                            </label>
                                            <input type="text" name="ckyc_ref_no[]" id="ckyc_ref_no{{isset($manInfo['name']) ? $i : '1'}}" value="{{ $manInfo['ckyc_ref_no'] ?? ''}}" class="form-control" placeholder="Enter CKYC Ref No." >
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Comment
                                              
                                            </label>
                                            <textarea class="form-control textarea" placeholder="Enter Comment" name="comment[]" id="comment{{isset($manInfo['name']) ? $i : '1'}}">{{ $manInfo['comment'] ?? ''}}</textarea>
                                        </div>
                                    </div> 
                                </div>

                                <h5 class="card-title form-head-h5 mt-3">Document </h5>	

                                <div class="row mt-2 mb-4">
                                    <div class="col-md-12">
                                        <div class="prtm-full-block">       
                                            <div class="prtm-block-content">

                                                <table class="table table-striped table-hover">
                                                    <thead class="thead-primary">
                                                        <tr>
                                                            <th class="text-left">S.No</th>
                                                            <th>Document Name</th>
                                                            <th>Document ID No.</th>
                                                            <th colspan="2">Action</th>
                                                        </tr>
                                                    </thead> 
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-left">1</td>
                                                            <td width="30%">Pan Card</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                   <a href="javascript:void(0);" id='ppan{{isset($manInfo['name']) ? $i : '1'}}' data-id="{{isset($manInfo['name']) ? $i : '1'}}" class="verify-owner-no verify-show veripan" style="top:0px; pointer-events:{{ (isset($manInfoDoc['pan_card']['is_verify']) && $manInfoDoc['pan_card']['is_verify']) ? 'none' : ''}}" data-doc_type_name="pan_card">{{ isset($manInfoDoc['pan_card']['is_verify']) && $manInfoDoc['pan_card']['is_verify'] ? 'Verified' : 'Verify' }}</a>
                                                                    <input type="text" {{isset($manInfoDoc['pan_card']['is_verify']) && $manInfoDoc['pan_card']['is_verify'] ? "readonly='readonly'" : '' }} value="{{ isset($manInfoDoc['pan_card']['id_no']) ? $manInfoDoc['pan_card']['id_no'] : ($manInfo['pan_card'] ?? '') }}"  name="veripan[]" id="veripan{{isset($manInfo['name']) ? $i : '1'}}"  class="form-control verifydl"  placeholder="Enter PAN Number">
                                                                    <span class="text-success float-left" id="v1successpanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{isset($manInfoDoc['pan_card']['is_verify']) && $manInfoDoc['pan_card']['is_verify'] ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>
                                                                    <span class="text-danger float-left" id="v1failurepanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>

                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a  href="{{ isset($manInfoDoc['pan_card']['file']['id']) ? route('frontend_download_storage_file', ['file_id' => $manInfoDoc['pan_card']['file']['id'] ]) : '' }}" class="btn-upload   btn-sm" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['pan_card']['file']['id']) ? 'inline' : 'none'}}" > <i class="fa fa-download"></i></a>

                                                                    <a  href="{{ isset($manInfoDoc['pan_card']['file']['id']) ? route('frontend_view_uploaded_doc', ['file_id' => $manInfoDoc['pan_card']['file']['id'] ]) : '' }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['pan_card']['file']['id']) ? 'inline' : 'none'}}" target="_blank"> <i class="fa fa-eye"></i></a>

                                                                    @if(isset($manInfoDoc['pan_card']['file']['id']))
                                                                        <button type="button" class="btn-upload   btn-sm" title="Disable OVD" style="display:{{ isset($manInfoDoc['pan_card']['file']['id']) ? 'inline' : 'none'}}" name="panfiles[]" id="panfiles{{isset($manInfo['name']) ? $i : '1'}}" onclick="deleteFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, {{ isset($manInfoDoc['pan_card']['file']['id']) ? $manInfoDoc['pan_card']['file']['id'] : 'null' }}, 2, 'pan_card')" ><i class="fa fa-times"></i></button>
                                                                    @endif
                                                                    
                                                                    <!-- <input type="file" class="verifyfile" name="verifyfile[]" id="verifyfile{{isset($manInfo['name']) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple=""> -->
                                                                </div>
                                                            </td>
                                                            <td width="14%">

                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                     <input type="file" class="panfile" data-id="{{isset($manInfo['name']) ? $i : '1'}}"  name="panfile[]" id="panfile{{isset($manInfo['name']) ? $i : '1'}}" onchange="uploadFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, 2, 'pan_card')">
                                                                    <span class="fileUpload"></span>
                                                                </div>   

                                                                @if(!empty($manInfoDoc['pan_card']['is_ovd_enabled']))
                                                                    <span class="d-flex align-items-center">
                                                                        <input type="checkbox" name="is_ovd[]" value="2" {{ $manInfoDoc['pan_card']['is_ovd_enabled'] == 1 ? 'checked' : 'disabled'}} class="mr-2" disabled>
                                                                        <span class="white-space-nowrap">IS OVD Enabled</span>
                                                                    </span>  
                                                                @endif
                                                                                                                                
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">2</td>
                                                            <td width="30%">Driving License</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                   <a href="javascript:void(0);" id='ddriving{{isset($manInfo['name']) ? $i : '1'}}' data-id="{{isset($manInfo['name']) ? $i : '1'}}" class="verify-owner-no verify-show veridl" style="top:0px; pointer-events:{{ (isset($manInfoDoc['driving_license']['is_verify']) && $manInfoDoc['driving_license']['is_verify']) ? 'none' : ''}}"  data-doc_type_name="driving_license">{{ isset($manInfoDoc['driving_license']['is_verify']) && $manInfoDoc['driving_license']['is_verify'] ? 'Verified' : 'Verify' }}</a>
                                                                    <input type="text" {{ isset($manInfoDoc['driving_license']['is_verify']) && $manInfoDoc['driving_license']['is_verify'] ? "readonly='readonly'" : '' }} value="{{ isset($manInfoDoc['driving_license']['id_no']) ? $manInfoDoc['driving_license']['id_no'] : ($manInfo['driving_license'] ?? '') }}" name="verifydl[]" id="verifydl{{isset($manInfo['name']) ? $i : '1'}}" class="form-control verifydl"  placeholder="Enter DL Number">

                                                                    <span class="text-success float-left" id="v2successpanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{isset($manInfoDoc['driving_license']['is_verify']) && $manInfoDoc['driving_license']['is_verify'] ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>

                                                                    <span class="text-danger float-left" id="v2failurepanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>

                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                   <a  href="{{ isset($manInfoDoc['driving_license']['file']['id']) ? route('frontend_download_storage_file', ['file_id' => $manInfoDoc['driving_license']['file']['id'] ]) : '' }}" class="btn-upload   btn-sm" type="button" id="dldown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['driving_license']['file']['id']) ? 'inline' : 'none'}}"> <i class="fa fa-download"></i></a>
                                                                   <a  href="{{ isset($manInfoDoc['driving_license']['file']['id']) ? route('frontend_view_uploaded_doc', ['file_id' => $manInfoDoc['driving_license']['file']['id'] ]) : '' }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['driving_license']['file']['id']) ? 'inline' : 'none'}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                                    <!-- <input type="file" id="downloaddl{{isset($manInfo['name']) ? $i : '1'}}" name="downloaddl[]" class="downloaddl" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple=""> -->

                                                                    @if(!empty($manInfoDoc['driving_license']['file']['id']))
                                                                        <button type="button"  class="btn-upload   btn-sm" title="Disable OVD" style="display:{{ isset($manInfoDoc['driving_license']['file']['id']) ? 'inline' : 'none'}}" name="dlfiles[]" id="dlfiles{{isset($manInfo['name']) ? $i : '1'}}" onclick="deleteFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, {{ isset($manInfoDoc['driving_license']['file']['id']) ? $manInfoDoc['driving_license']['file']['id'] : 'null' }}, 31, 'driving_license')" ><i class="fa fa-times"></i></button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td width="14%">

                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                  <button class="btn">Upload</button>
                                                                   <input type="file" name="dlfile[]" data-id="{{isset($manInfo['name']) ? $i : '1'}}"  id="dlfile{{isset($manInfo['name']) ? $i : '1'}}" class="dlfile"  onchange="uploadFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, 31, 'driving_license')">
                                                                </div>
                                                                @if(!empty($manInfoDoc['driving_license']['is_ovd_enabled']))
                                                                    <span class="d-flex align-items-center">
                                                                        <input type="checkbox" name="is_ovd[]" value="2" {{ $manInfoDoc['driving_license']['is_ovd_enabled'] == 1 ? 'checked' : 'disabled'}} class="mr-2" disabled>
                                                                        <span class="white-space-nowrap">IS OVD Enabled</span>
                                                                    </span>  
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">3</td>
                                                            <td width="30%">Voter ID</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);" id='vvoter{{isset($manInfo['name']) ? $i : '1'}}' data-id="{{isset($manInfo['name']) ? $i : '1'}}" class="verify-owner-no verify-show verivoter" style="top:0px; pointer-events:{{ (isset($manInfoDoc['voter_id']['is_verify']) && $manInfoDoc['voter_id']['is_verify']) ? 'none' : ''}}" data-doc_type_name="voter_id">{{ isset($manInfoDoc['voter_id']['is_verify']) && $manInfoDoc['voter_id']['is_verify'] ? 'Verified' : 'Verify' }}</a>
                                                                   <input type="text" {{isset($manInfoDoc['voter_id']['is_verify']) && $manInfoDoc['voter_id']['is_verify'] ? "readonly='readonly'" : '' }} value="{{ isset($manInfoDoc['voter_id']['id_no']) ? $manInfoDoc['voter_id']['id_no'] : ($manInfo['voter_id'] ?? '') }}" name="verifyvoter[]" id="verifyvoter{{isset($manInfo['name']) ? $i : '1'}}"  class="form-control verifyvoter"  placeholder="Enter Voter's Epic Number">
                                                                    <span class="text-success float-left" id="v3successpanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{isset($manInfoDoc['voter_id']['is_verify']) && $manInfoDoc['voter_id']['is_verify'] ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>

                                                                    <span class="text-danger float-left" id="v3failurepanverify{{isset($manInfo['name']) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>

                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a  href="{{ isset($manInfoDoc['voter_id']['file']['id']) ? route('frontend_download_storage_file', ['file_id' => $manInfoDoc['voter_id']['file']['id'] ]) : '' }}" class="btn-upload   btn-sm" type="button" id="voterdown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['voter_id']['file']['id']) ? 'inline' : 'none'}}"> <i class="fa fa-download"></i></a>
                                                                    <a  href="{{ isset($manInfoDoc['voter_id']['file']['id']) ? route('frontend_view_uploaded_doc', ['file_id' => $manInfoDoc['voter_id']['file']['id'] ]) : '' }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['voter_id']['file']['id']) ? 'inline' : 'none'}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                                    <!-- <input type="file" name="downloadvoter[]" class="downloadvoter" id="downloadvoter{{isset($manInfo['name']) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple=""> -->
                                                                    @if(!empty($manInfoDoc['voter_id']['file']['id']))
                                                                        <button type="button"  class="btn-upload   btn-sm" title="Disable OVD" style="display:{{ isset($manInfoDoc['voter_id']['file']['id']) ? 'inline' : 'none'}}" name="voterdowns[]" id="voterdowns{{isset($manInfo['name']) ? $i : '1'}}" onclick="deleteFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, {{ isset($manInfoDoc['voter_id']['file']['id']) ? $manInfoDoc['voter_id']['file']['id'] : 'null' }}, 30, 'voter_id')" ><i class="fa fa-times"></i></button>
                                                                    @endif                                                                    
                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                   <button class="btn">Upload</button>
                                                                   <input type="file" name="voterfile[]" data-id="{{isset($manInfo['name']) ? $i : '1'}}"  class="voterfile" id="voterfile{{isset($manInfo['name']) ? $i : '1'}}"  onchange="uploadFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, 30, 'voter_id')">
                                                                </div>
                                                                @if(!empty($manInfoDoc['voter_id']['is_ovd_enabled']))
                                                                    <span class="d-flex align-items-center">
                                                                        <input type="checkbox" name="is_ovd[]" value="2" {{ $manInfoDoc['voter_id']['is_ovd_enabled'] == 1 ? 'checked' : 'disabled'}} class="mr-2" disabled>
                                                                        <span class="white-space-nowrap">IS OVD Enabled</span>
                                                                    </span>  
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">4</td>
                                                            <td width="30%">Passport</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);" id='ppassport{{isset($manInfo['name']) ? $i : '1'}}' data-id="{{isset($manInfo['name']) ? $i : '1'}}" class="verify-owner-no verify-show veripass" style="top:0px; pointer-events:{{ (isset($manInfoDoc['passport']['is_verify']) && $manInfoDoc['passport']['is_verify']) ? 'none' : ''}}" data-doc_type_name="passport">{{ isset($manInfoDoc['passport']['is_verify']) && $manInfoDoc['passport']['is_verify'] ? 'Verified' : 'Verify' }}</a>
                                                                   <input type="text"  {{ isset($manInfoDoc['passport']['is_verify']) && $manInfoDoc['passport']['is_verify'] ? "readonly='readonly'" : '' }}  value="{{ isset($manInfoDoc['passport']['id_no']) ? $manInfoDoc['passport']['id_no'] : ($manInfo['passport'] ?? '') }}" name="verifypassport[]" id="verifypassport{{isset($manInfo['name']) ? $i : '1'}}"  class="form-control verifypassport" placeholder="Enter File Number">

                                                                    <span class="text-success float-left" id="v4successpanverify{{isset($manInfo['name']) ? $i : '1'}}"  style="display:{{isset($manInfoDoc['passport']['is_verify']) && $manInfoDoc['passport']['is_verify'] ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>

                                                                    <span class="text-danger float-left" id="v4failurepanverify{{isset($manInfo['name']) ? $i : '1'}}"  style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>


                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a  href="{{ isset($manInfoDoc['passport']['file']['id']) ? route('frontend_download_storage_file', ['file_id' => $manInfoDoc['passport']['file']['id'] ]) : '' }}" class="btn-upload   btn-sm" type="button" id="passdown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['passport']['file']['id']) ? 'inline' : 'none'}}"> <i class="fa fa-download"></i></a>
                                                                    <a  href="{{ isset($manInfoDoc['passport']['file']['id']) ? route('frontend_view_uploaded_doc', ['file_id' => $manInfoDoc['passport']['file']['id'] ]) : '' }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['passport']['file']['id']) ? 'inline' : 'none'}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                                    <!-- <input type="file" name="downloadpassport[]" class="downloadpassport" id="downloadpassport{{isset($manInfo['name']) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple=""> -->
                                                                    @if(!empty($manInfoDoc['passport']['file']['id']))
                                                                        <button type="button"  class="btn-upload   btn-sm" title="Disable OVD" style="display:{{ isset($manInfoDoc['passport']['file']['id']) ? 'inline' : 'none'}}"  name="passportfiles[]" id="passportfiles{{isset($manInfo['name']) ? $i : '1'}}" onclick="deleteFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, {{ isset($manInfoDoc['passport']['file']['id']) ? $manInfoDoc['passport']['file']['id'] : 'null' }}, 32, 'passport')" ><i class="fa fa-times"></i></button>
                                                                    @endif                                                                    
                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                     <input type="file" name="passportfile[]" data-id="{{isset($manInfo['name']) ? $i : '1'}}" class="passportfile" id="passportfile{{isset($manInfo['name']) ? $i : '1'}}"  onchange="uploadFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, 32, 'passport')">
                                                                </div>
                                                                @if(!empty($manInfoDoc['passport']['is_ovd_enabled']))
                                                                    <span class="d-flex align-items-center">
                                                                        <input type="checkbox" name="is_ovd[]" value="2" {{ $manInfoDoc['passport']['is_ovd_enabled'] == 1 ? 'checked' : 'disabled'}} class="mr-2" disabled>
                                                                        <span class="white-space-nowrap">IS OVD Enabled</span>
                                                                    </span>  
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">5</td>
                                                            <td width="30%">Photo</td>
                                                            <td width="30%">
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">

                                                                    <a  href="{{ isset($manInfoDoc['photo']['file']['id']) ? route('frontend_download_storage_file', ['file_id' => $manInfoDoc['photo']['file']['id'] ]) : '' }}" class="btn-upload   btn-sm" type="button" id="photodown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['photo']['file']['id']) ? 'inline' : 'none'}}"> <i class="fa fa-download"></i></a>
                                                                    <a  href="{{ isset($manInfoDoc['photo']['file']['id']) ? route('frontend_view_uploaded_doc', ['file_id' => $manInfoDoc['photo']['file']['id'] ]) : '' }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['photo']['file']['id']) ? 'inline' : 'none'}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                                    <!-- <input type="file" class="downloadphoto"  name="downloadphoto[]" id="downloadphoto{{isset($manInfo['name']) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple=""> -->
                                                                    @if(!empty($manInfoDoc['photo']['file']['id']))
                                                                        <button type="button"  class="btn-upload   btn-sm" title="Disable OVD" style="display:{{ isset($manInfoDoc['photo']['file']['id']) ? 'inline' : 'none'}}"  name="photofiles[]" id="photofiles{{isset($manInfo['name']) ? $i : '1'}}" onclick="deleteFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, {{ isset($manInfoDoc['photo']['file']['id']) ? $manInfoDoc['photo']['file']['id'] : 'null' }}, 22, 'photo')" ><i class="fa fa-times"></i></button>
                                                                    @endif                                                                    
                                                                </div>
                                                            </td>
                                                            <td width="14%"> 
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                   <input type="file" class="photofile"  name="photofile[]"  data-id="{{isset($manInfo['name']) ? $i : '1'}}"  id="photofile{{isset($manInfo['name']) ? $i : '1'}}"  onchange="uploadFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, 22, 'photo')">
                                                                </div>
                                                                @if(!empty($manInfoDoc['photo']['is_ovd_enabled']))
                                                                    <span class="d-flex align-items-center">
                                                                        <input type="checkbox" name="is_ovd[]" value="2" {{ $manInfoDoc['photo']['is_ovd_enabled'] == 1 ? 'checked' : 'disabled'}} class="mr-2" disabled>
                                                                        <span class="white-space-nowrap">IS OVD Enabled</span>
                                                                    </span>  
                                                                @endif   
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">6</td>
                                                            <td width="30%">Aadhar Card </td>
                                                            <td width="30%">
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a  href="{{ isset($manInfoDoc['aadhar_card']['file']['id']) ? route('frontend_download_storage_file', ['file_id' => $manInfoDoc['aadhar_card']['file']['id'] ]) : '' }}" class="btn-upload   btn-sm" type="button" id="aadhardown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['aadhar_card']['file']['id']) ? 'inline' : 'none'}}"> <i class="fa fa-download"></i></a>
                                                                    <a  href="{{ isset($manInfoDoc['aadhar_card']['file']['id']) ? route('frontend_view_uploaded_doc', ['file_id' => $manInfoDoc['aadhar_card']['file']['id'] ]) : '' }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['aadhar_card']['file']['id']) ? 'inline' : 'none'}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                                    <!-- <input type="file" class="downloadaadhar"  name="downloadaadhar[]" id="downloadaadhar{{isset($manInfo['name']) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple=""> -->
                                                                    @if(!empty($manInfoDoc['aadhar_card']['file']['id']))
                                                                        <button type="button"  class="btn-upload   btn-sm" title="Disable OVD" style="display:{{ isset($manInfoDoc['aadhar_card']['file']['id']) ? 'inline' : 'none'}}" name="downloadaadhars[]" id="downloadaadhars{{isset($manInfo['name']) ? $i : '1'}}" onclick="deleteFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, {{ isset($manInfoDoc['aadhar_card']['file']['id']) ? $manInfoDoc['aadhar_card']['file']['id'] : 'null' }}, 34, 'aadhar_card')" ><i class="fa fa-times"></i></button>
                                                                    @endif                                                                    
                                                                </div>
                                                            </td>
                                                            <td width="14%"> 
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" class="aadharfile"  name="aadharfile[]"  data-id="{{isset($manInfo['name']) ? $i : '1'}}"  id="aadharfile{{isset($manInfo['name']) ? $i : '1'}}"  onchange="uploadFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, 34, 'aadhar_card')">
                                                                </div>
                                                                @if(!empty($manInfoDoc['aadhar_card']['is_ovd_enabled']))
                                                                    <span class="d-flex align-items-center">
                                                                        <input type="checkbox" name="is_ovd[]" value="2" {{ $manInfoDoc['aadhar_card']['is_ovd_enabled'] == 1 ? 'checked' : 'disabled'}} class="mr-2" disabled>
                                                                        <span class="white-space-nowrap">IS OVD Enabled</span>
                                                                    </span>  
                                                                @endif   
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">7</td>
                                                            <td width="30%">Electricity Bill </td>
                                                            <td width="30%"></td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a  href="{{ isset($manInfoDoc['electricity_bill']['file']['id']) ? route('frontend_download_storage_file', ['file_id' => $manInfoDoc['electricity_bill']['file']['id'] ]) : '' }}" class="btn-upload   btn-sm" type="button" id="electricitydown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['electricity_bill']['file']['id']) ? 'inline' : 'none'}}"> <i class="fa fa-download"></i></a>
                                                                    <a  href="{{ isset($manInfoDoc['electricity_bill']['file']['id']) ? route('frontend_view_uploaded_doc', ['file_id' => $manInfoDoc['electricity_bill']['file']['id'] ]) : '' }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['electricity_bill']['file']['id']) ? 'inline' : 'none'}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                                    <!-- <input type="file" class="downloadelectricity"  name="downloadelectricity[]" id="downloadelectricity{{isset($manInfo['name']) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple=""> -->
                                                                    @if(!empty($manInfoDoc['electricity_bill']['file']['id']))
                                                                        <button type="button"  class="btn-upload   btn-sm" title="Disable OVD" style="display:{{ isset($manInfoDoc['electricity_bill']['file']['id']) ? 'inline' : 'none'}}" name="downloadelectricitys[]" id="downloadelectricitys{{isset($manInfo['name']) ? $i : '1'}}" onclick="deleteFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, {{ isset($manInfoDoc['electricity_bill']['file']['id']) ? $manInfoDoc['electricity_bill']['file']['id'] : 'null' }}, 37, 'electricity_bill')" ><i class="fa fa-times"></i></button>
                                                                    @endif                                                                    
                                                                </div>
                                                            </td>
                                                            <td width="14%"> 
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                   <button class="btn">Upload</button>
                                                                    <input type="file" class="electricityfile"  name="electricityfile[]"  data-id="{{isset($manInfo['name']) ? $i : '1'}}"  id="electricityfile{{isset($manInfo['name']) ? $i : '1'}}"  onchange="uploadFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, 37, 'electricity_bill')">
                                                                </div>
                                                                @if(!empty($manInfoDoc['electricity_bill']['is_ovd_enabled']))
                                                                    <span class="d-flex align-items-center">
                                                                        <input type="checkbox" name="is_ovd[]" value="2" {{ $manInfoDoc['electricity_bill']['is_ovd_enabled'] == 1 ? 'checked' : 'disabled'}} class="mr-2" disabled>
                                                                        <span class="white-space-nowrap">IS OVD Enabled</span>
                                                                    </span>  
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">8</td>
                                                            <td width="30%">Telephone Bill </td>
                                                            <td width="30%"></td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a  href="{{ isset($manInfoDoc['telephone_bill']['file']['id']) ? route('frontend_download_storage_file', ['file_id' => $manInfoDoc['telephone_bill']['file']['id'] ]) : '' }}" class="btn-upload   btn-sm" type="button" id="telephonedown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['telephone_bill']['file']['id']) ? 'inline' : 'none'}}"> <i class="fa fa-download"></i></a>
                                                                    <a  href="{{ isset($manInfoDoc['telephone_bill']['file']['id']) ? route('frontend_view_uploaded_doc', ['file_id' => $manInfoDoc['telephone_bill']['file']['id'] ]) : '' }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandown{{isset($manInfo['name']) ? $i : '1'}}" style="display:{{ isset($manInfoDoc['telephone_bill']['file']['id']) ? 'inline' : 'none'}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                                    <!-- <input type="file" class="downloadtelephone"  name="downloadtelephone[]" id="downloadtelephone{{isset($manInfo['name']) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple=""> -->
                                                                    @if(!empty($manInfoDoc['telephone_bill']['file']['id']))
                                                                        <button type="button"  class="btn-upload   btn-sm" title="Disable OVD" style="display:{{ isset($manInfoDoc['telephone_bill']['file']['id']) ? 'inline' : 'none'}}" name="downloadtelephones[]" id="downloadtelephones{{isset($manInfo['name']) ? $i : '1'}}" onclick="deleteFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, {{ isset($manInfoDoc['telephone_bill']['file']['id']) ? $manInfoDoc['telephone_bill']['file']['id'] : 'null' }}, 38, 'telephone_bill')" ><i class="fa fa-times"></i></button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td width="14%"> 
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                   <button class="btn">Upload</button>
                                                                    <input type="file" class="telephonefile"  name="telephonefile[]"  data-id="{{isset($manInfo['name']) ? $i : '1'}}"  id="telephonefile{{isset($manInfo['name']) ? $i : '1'}}"  onchange="uploadFile({{isset($manInfo['name']) ? $i : '1'}}, {{ $manInfo['owner_id'] }}, 38, 'telephone_bill')">
                                                                </div>
                                                                @if(!empty($manInfoDoc['telephone_bill']['is_ovd_enabled']))
                                                                    <span class="d-flex align-items-center">
                                                                        <input type="checkbox" name="is_ovd[]" value="2" {{ $manInfoDoc['telephone_bill']['is_ovd_enabled'] == 1 ? 'checked' : 'disabled'}} class="mr-2" disabled>
                                                                        <span class="white-space-nowrap">IS OVD Enabled</span>
                                                                    </span>  
                                                                @endif                                                                
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <div class="modal" id="myModal{{isset($manInfo['name']) ? $i : '1'}}">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h5 id="dynamicTitle{{isset($manInfo['name']) ? $i : '1'}}"></h5>
                                                                <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                                                            </div>
                                                            <!-- Modal body -->
                                                            <div class="modal-body text-left">
                                                                <div class="table-responsive ps ps--theme_default" data-ps-id="c019a9d0-57f7-7dd4-16ba-e6ea054ce839">
                                                                    <span class="getBizApiRes" id="getBizApiRes{{isset($manInfo['name']) ? $i : '1'}}"></span>
                                                                    <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps__scrollbar-x"  style="left: 0px; width: 0px;"></div></div><div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;"><div class="ps__scrollbar-y"  style="top: 0px; height: 0px;"></div></div></div>
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
                        @php ($j++)
                        @endforeach
                        @else
                        <input type="hidden" name="ownerid[]">   
                        <input type="hidden" name="is_promoter[]">   
                        <input type="hidden" name="mobile_no[]">   
                        @endif
                        <span class="form-fields-appand"></span>   
                        <div class="row">

                            <div class="col-md-12 mt-2">

                                <div class="d-flex btn-section ">
                                    <div class="ml-auto text-right">

                                        <button type="button" id="btnAddMore" class="btn btn-success btn-add btn-sm ml-auto">
                                            <i class="fa fa-plus"></i>
                                            Add Management
                                        </button>  </div>
                                </div>				

                            </div>

                           <div class="col-md-12 mt-2">



                                <div class="d-flex btn-section ">
                                    <div class="ml-auto text-right">
                                        <input type="button" value="Save" data-type="save" id="submit" class="submit btn btn-success btn-sm">
                                         <input type="button" value="Next" data-type="next" id="submit" class="submit btn btn-success btn-sm">
                                       
                                    </div>
                                </div>	
                            </div>								
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    {!!Helpers::makeIframePopup('modalPromoter','View PAN Card Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter1','View Driving License Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter2','View Voter ID  Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter3','View Passport Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter7','Mobile Verify Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter8','OTP Verify Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter9','OTP Verify Status Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter','Upload User List', 'modal-md')!!}
    {!!Helpers::makeIframePopup('modalMobile','Mobile Verification', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalOtp','OTP Verification', 'modal-lg')!!}
    
    @endsection
    @section('jscript')

    <script type="text/javascript">
        var messages = {
        promoter_document_save: "{{ URL::route('front_promoter_document_save') }}",
                data_not_found: "{{ trans('error_messages.data_not_found') }}",
                token: "{{ csrf_token() }}",
                data_not_found: "{{ trans('error_messages.data_not_found') }}",
                chk_user_voterid_karza: "{{ URL::route('chk_user_voterid_karza') }}",
                get_promoter_details_by_cin: "{{ URL::route('get_promoter_details_by_cin') }}",
                chk_user_dl_karza: "{{ URL::route('chk_user_dl_karza') }}",
                chk_user_passport_karza: "{{ URL::route('chk_user_passport_karza') }}",
                chk_user_pan_status_karza: "{{ URL::route('chk_user_pan_status_karza') }}",
                chk_user_pan_karza_add_more: "{{ URL::route('chk_user_pan_karza_add_more') }}",
                chk_user_pan_karza: "{{ URL::route('chk_user_pan_karza') }}",
                get_user_pan_response_karza: "{{ URL::route('get_user_pan_response_karza') }}",
                protmoter_document_delete: "{{ url::route('promoter_document_ovd') }}",
        };
      
      
      
      $(document).ready(function () {
       ///////////////For Amount comma Seprate///////////
        $(".networth").each(function(){
            var id   =  $(this).attr('id');
           document.getElementById(id).addEventListener('input', event =>
           event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
           return true;
        })
        
        jQuery.validator.addMethod("emailExt", function(value, element, param) {
            return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
        },'please enter a valid email');
        
        $('.submit').on('click', function (event) {
        var button = $(this).attr("data-type");
        var is_lease = '{{$is_lease}}';
        $('input.first_name').each(function () {
        $(this).rules("add",
        {
        required: true
        })
        });
        $('input.date_of_birth').each(function () {
        $(this).rules("add",
        {
        required: true
        })
        });

        $('input.email').each(function () {
            $(this).rules("add", {
                required: false,
                emailExt: true,
                maxlength: 100
            });
        });

        if(button=='next')
      { 
                $('select.gender').each(function () {
                $(this).rules("add",
                {
                required: true
                })
                });
              if(is_lease==0)
              {
                $('input.pan_no').each(function () {
                $(this).rules("add",
                {
                required: true
                })
                });

                //  $('input.designation').each(function () {
                // $(this).rules("add",
                // {
                // required: true
                // })
                // });

              }

               if(is_lease==0)
               {
                $('input.mobileveri').each(function () {
                $(this).rules("add",
                {
                    required: true,
                    number: true,
                    minlength: 10
                })
                });
               }
                $('textarea.address').each(function () {
                $(this).rules("add",
                {
                required: true
                })
                });
       } 
       else
       {
           $('select.gender').each(function () {
                $(this).rules("add",
                {
                required: false
                })
                });
              if(is_lease==0)
              {
                $('input.pan_no').each(function () {
                $(this).rules("add",
                {
                required: false
                })
                });

                $('input.mobileveri').each(function () {
                $(this).rules("add",
                {
                    required: false,
                    number: false,
                    minlength: 10
                })
                });
              }
                $('textarea.address').each(function () {
                $(this).rules("add",
                {
                required: false
                })
                });
       }
      
        if ($('form#signupForm').validate().form()) {
        var panCount = 0;
        var promoCount = 0;
        var mobileVeriCount = 0;
        var total = 0;
        var DlLength = $('input[name="dlfile[]"]').length;
        var total = 0;
       if(button=='next')
       {  
        ///// for upload one in three id proff..............
        for (i = 1; i <= DlLength; i++)
        {


        var dlVal = $("#dldown" + i).attr('href');
        var vtVal = $("#voterdown" + i).attr('href');
        var adVal = $("#aadhardown" + i).attr('href');
        var elVal = $("#electricitydown" + i).attr('href');
         var teVal = $("#telephonedown" + i).attr('href');
        
         if(is_lease==0)
         {     
            if (dlVal == "" && vtVal == "" && adVal == "" && elVal == "" && teVal == "")
            {
            alert('Please upload atleast one ID Proof in ( Driving License / Voter ID / Aadhar Card / Electricity Bill  / Telephone Bill) in Management ' + i + '');
            $("#verifydl" + i).focus();
            return false;
            }
         } 
        }
       if(is_lease==0)
       {  
        //// for pan verify///
        $(".pan_no").each(function (k, v) {
        panCount++;
        var result = $("#pan_verify" + panCount).text();
        if (result == "Verify")
        {
        $('#failurepanverify' + panCount).show();
        $('#pan_no' + panCount).focus();
        e.preventDefault();
        return false;
        }

        });
        
         //// for mobile verify///
        $(".findMobileverify").each(function (k, v) {
         mobileVeriCount++;   
         var mobileVeri =   $(this).text();
         var otp =    $("#v11successpanverify"+mobileVeriCount).text();
 
         if($.trim(mobileVeri)!="Verified Successfully" && $.trim(otp)!="Verified Successfully")
         {
             
              $("#v5failurepanverify"+mobileVeriCount).html('<i class="fa fa-close" aria-hidden="true"></i><i>Not verified</i>');
              e.preventDefault();
              return false;
         }
        
        
        });
        }
               ///// validation for where is checked then shareholder is mandaterory/////
        $(".is_promoter").each(function (k, v) {
        promoCount++;
        var is_promoter = $("#applicant_type" + promoCount).val();
        if (is_promoter == 1)
        {
           
            var shareHolder = $("#share_per" + promoCount).val();
            if (shareHolder == '')
            {
                $("#shareCheck" + promoCount).text('This field is required.');
                e.preventDefault();
                return false;
            }
            else if (shareHolder < 1 || shareHolder > 100)
            {
                $("#shareCheck" + promoCount).text('Enter correct value 1 to 100 range');
                e.preventDefault();
                return false;
            }

            else
            {
                $("#shareCheck" + promoCount).text('');
                return true;
            }

        }
       
        });
      

        ////// Combination of Shareholding (%) should  not exceed more than 100 %///////////
            $(".share_per").each(function (k, v) { 
                if($(this).val()!='')
                {
                    total += parseFloat($(this).val());
                }
            });
           
            if(total > 100)
            {

                alert('Combination of Shareholding (%) should  not exceed more than 100 %');
                e.preventDefault();
               return false;
           }
       }
        var form = $("#signupForm");
        $('.isloader').show();
        $.ajax({
        type: "POST",
                url: '{{Route('front_promoter_detail_update')}}',
                data: form.serialize(), // serializes the form's elements.
                cache: false,
                success: function (res)
                {
                 $('.isloader').hide();
               window.location.href = "{{ route('promoter-detail', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id') ]) }}";
                  
               if (res.status == 1)
               {
                    
                    if(button=='next')
                    {  
                          window.location.href = "{{ route('document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id') ]) }}"; }
                    else
                    {
                         window.location.href = "{{ route('promoter-detail', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id') ]) }}";
                    }
                }
                else {
                alert("Something went wrong, please try again !");
                }
               
                },
                error: function (error)
                {
                console.log(error);
                }

        });
        } else {
        console.log("does not validate");
        }
        })

                $('form#signupForm').validate();
        });
        function FileDetails(clicked_id) {
        // GET THE FILE INPUT.
        var fi = document.getElementById('file_' + clicked_id);
        // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
        if (fi.files.length > 0) {

        // THE TOTAL FILE COUNT.
        var x = 'filePath_' + clicked_id;
        //var x = document.getElementById(id);alert(id);
        document.getElementById(x).innerHTML = '';
        // RUN A LOOP TO CHECK EACH SELECTED FILE.
        for (var i = 0; i <= fi.files.length - 1; i++) {

        var fname = fi.files.item(i).name; // THE NAME OF THE FILE.
        var fsize = fi.files.item(i).size; // THE SIZE OF THE FILE.
        // SHOW THE EXTRACTED DETAILS OF THE FILE.
        document.getElementById(x).innerHTML =
                '<div class="file-name" id="fileId"> ' +
                fname + '' + '<button type="button"  class="close-file" onclick="myDelete()" > x' + '</button>' + '</div>';
        }
        } else {
        alert('Please select a file.');
        }
        }

        function myDelete() {
        document.getElementById("fileId").remove();
        }

    /////////////shareholder keyup for checking is_promoter is checked or not/////////////////
        $(document).on('keyup', '.share_per', function(){
        var shareHolder = $(this).val();
        var promoCount = $(this).attr('data-id');
        var is_promoter = $("#applicant_type" + promoCount).val();
       
        if (is_promoter == 1)
        {
               
                if (shareHolder == '')
                {
                        $("#shareCheck" + promoCount).text('This field is required.');
                        return false;
                }
                else if (shareHolder < 1 || shareHolder > 100)
                {
                        $("#shareCheck" + promoCount).text('Enter correct value 1 to 100 range');
                        return false;
                }
                else
                {
                        $("#shareCheck" + promoCount).text('');
                        return true;
                }



        }
        else
        {
               
                 $("#shareCheck" + promoCount).text('');
                return true;
        }
        });
        
     /////// for is promoter checking checkbox//////////////////////
      $(document).on('click', '.is_promoter', function () {
        var res = $(this).val();
        var count = $(this).attr('data-id');
      
        if (res==1)
        { 
                $("#isShareCheck"+count).val(1);
                $("#shareCheck"+count).text('');
               
                return true;
        }
        else
        {
                $("#isShareCheck"+count).val(0);
                 if($("#share_per"+count).val()=='')
                {
                  $("#shareCheck"+count).text('This field is required.');
                 }
                 return true;
        }
        });
       
        
       ////////////////// new form create by add promoter/////////////////
       
        $(document).on('click', '#btnAddMore', function () {
        var rowcount = parseInt($("#rowcount").val());
        if (rowcount > 0)
        {
          var x = rowcount + 1;
        } 
        else if(rowcount==0)
        {
             var x = 1;
        }
        else
        {
          var x = 2;
        }
        $("#rowcount").val(x);
            if(x==1)
           {
               var close  = "";
           }
           else
           {
                var close  = "<button class='close clsdiv' type='button'>x</button>";
           }
           $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><div class='col-md-12'>"+close+"<h5 class='card-title form-head'>Management Information (" + x + ") </h5></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' class='d-block'> Name  <span class='mandatory'>*</span></label><input type='hidden' class='owneridDynamic' id='ownerid" + x + "'   value=''><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-2'><div class='form-group password-input'><label for='txtPassword'>Owner Type</label><select class='form-control is_promoter' name='applicant_type[]' id='applicant_type"+x+"'><option value='' selected='selected'>Select Owner Type</option><option value='1'>Is Promoter</option><option value='2'>Key Management Person</option><option value='3'>Co-Borrower</option><option value='4'>Guarantor </option></select></div></div><div class='col-md-2'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)</label><input type='hidden' name='isShareCheck[]' id='isShareCheck"+ x +"' value='0'><input type='text'  id='share_per" + x + "' data-id='" + x + "' maxlength='6' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per'  placeholder='Enter Shareholder' ><span class='error' id='shareCheck" + x + "'></span></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='text' name='date_of_birth[]'  id='date_of_birth" + x + "' readonly='readonly' value='' class='form-control date_of_birth datepicker-dis-fdate'  placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option><option value='3'>Other </option></select></div></div><div class='col-md-4'><div class='form-group INR'><label for='txtEmail'>Networth </label><div class='relative'><a href='javascript:void(0);' class='verify-owner-no'><i class='fa fa-inr' aria-hidden='true'></i></a><input type='text' maxlength='15' name='networth[]' id='networth" + x + "' value='' class='form-control networth'  placeholder='Enter Networth'><input name='response[]' id='response" + x + "' type='hidden' value=''></div></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Designation</label><input type='text' name='designation[]'  id='designation" + x + "' value='' class='form-control designation'  placeholder='Enter Designation'></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership'  placeholder='Enter Other Ownership'></div></div><div class='col-md-8'><div class='form-group'><label for='txtCreditPeriod'>Address<span class='mandatory'>*</span></label><textarea  style='height: 35px;' class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address'"+ x +"'></textarea></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod'>CKYC Ref No</label><input type='text' name='ckyc_ref_no[]' id='ckyc_ref_no"+x+"' value='' class='form-control' placeholder='Enter CKYC Ref No.' ></div></div></div><div class='row'><div class='col-md-12'><div class='form-group'><label for='txtCreditPeriod'>Comment</label><textarea class='form-control textarea' placeholder='Enter Comment' name='comment[]' id='comment"+x+"'></textarea></div></div></div></div><span id='disableDocumentPart"+x+"' style='display:none'><h5 class='card-title form-head-h5 mt-3'>Document </h5><div class='row mt-2 mb-4'><div class='col-md-12'> <div class='prtm-full-block'><div class='prtm-block-content'> <div class='table-responsive ps ps--theme_default' data-ps-id='9615ce02-be28-0492-7403-d251d7f6339e'><table class='table text-center table-striped table-hover'><thead class='thead-primary'><tr><th class='text-left'>S.No</th><th>Document Name</th><th>Document ID No.</th><th>Action</th></tr></thead><tbody><tr><td class='text-left'>1</td><td width='30%'>Pan Card</td><td width='30%'><div class='col-md-12'><span class='text-success' id='v1successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v1failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppan" + x + "' data-id='" + x + "' class='verify-owner-no verify-show veripan' style='top:0px'>Verify</a><input type='text'  name='veripan[]' id='veripan" + x + "' value='' class='form-control'  placeholder='Enter PAN Number'></div></td><td width='28%'><div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details' data-id='" + x + "' data-type='3'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button><input type='file' name='verifyfile[]' class='verifyfile' id='verifyfile" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='panfile[]' data-id='" + x + "' class='panfile' id='panfile" + x + "'> </div> </td> </tr><tr> <td class='text-left'>2</td> <td width='30%'>Driving License</td> <td width='30%' ><div class='col-md-12'><span class='text-success' id='v2successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v2failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='ddriving" + x + "' data-id='" + x + "'  class='verify-owner-no verify-show veridl' style='top:0px;'>Verify</a> <input type='text' name='verifydl[]' id='verifydl" + x + "' value='' class='form-control verifydl'  placeholder='Enter DL Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "' data-type='5'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' id='downloaddl" + x + "' name='downloaddl[]' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple='' class='downloaddl'> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='dlfile[]' data-id='" + x + "' class='dlfile' id='dlfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>3</td> <td width='30%'>Voter ID</td> <td width='30%' ><div class='col-md-12'><span class='text-success' id='v3successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v3failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='vvoter" + x + "' data-id='" + x + "'  class='verify-owner-no verify-show verivoter' style='top:0px;'>Verify</a> <input type='text' name='verifyvoter[]' id='verifyvoter" + x + "' value='' class='form-control verifyvoter'  placeholder='Enter Voter's Epic Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='4'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadvoter[]' class='downloadvoter' id='downloadvoter" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  class='voterfile' name='voterfile[]' id='voterfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>4</td> <td width='30%'>Passport</td> <td width='30%' ><div class='col-md-12'> <span class='text-success' id='v4successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v4failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppassport" + x + "' data-id='" + x + "' class='verify-owner-no verify-show veripass' style='top:0px;'>Verify</a> <input type='text' name='verifypassport[]' id='verifypassport" + x + "' value='' class='form-control verifypassport'  placeholder='Enter File Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='6'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadpassport[]' class='downloadpassport'  id='downloadpassport" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'   name='passportfile[]' class='passportfile' id='passportfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>5</td> <td width='30%'>Photo</td> <td width='30%' > </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadphoto[]' class='downloadphoto' id='downloadphoto" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  name='photofile[]' name='photofile' id='photofile" + x + "'> </div> </td> </tr> </tbody> </table> </span> <div class='ps__scrollbar-x-rail' style='left: 0px; bottom: 0px;'><div class='ps__scrollbar-x'  style='left: 0px; width: 0px;'></div></div><div class='ps__scrollbar-y-rail' style='top: 0px; right: 0px;'><div class='ps__scrollbar-y'  style='top: 0px; height: 0px;'></div></div> </div> </div> </div> </div></div> </div></div></div>"); 
           x++;
          datepickerDisFdate();
       
        });
        
        
    //////////CIN webservice for get promoter details start here//////////////////////////////////////        
        $(document).on('click', '.clsdiv', function () {
        $(this).parent().parent().remove();
        var rowcount = parseInt($("#rowcount").val());
         if (rowcount > 0)
        {
          var x = rowcount - 1;
        }
        $("#rowcount").val(x);
       
        });
        
        
         //////////CIN webservice for get promoter details start here//////////////////////////////////////        
        
          jQuery(document).ready(function () {
        var countOwnerRow = $("#rowcount").val();
        if (countOwnerRow > 0)
        {
           return false;
        }

        $('.isloader').show();
        var CIN = '{{ (isset($cin_no)) ? $cin_no : "" }}';
        if(CIN=='')
        {
             $('.isloader').hide();
             $('#btnAddMore').trigger('click'); 
             return false;
        }
        var consent = "Y";
        var dataStore = ({'consent': consent, 'entityId': CIN,'_token': messages.token});
        var postData = dataStore;
        jQuery.ajax({
        url: messages.get_promoter_details_by_cin,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                console.log(xhr);
                $('.isloader').hide();
                $('#btnAddMore').trigger('click'); 
                return false;
                },
                success: function (result) {

                $(".isloader").hide();
                obj = result.value;
                var count = 0;
                var arr = new Array();
                var x = 0;
                $(obj).each(function (k, v) {
                var temp = {};
                var dob = v.dob;
                var dateAr = dob.split('-');
                var newDate = '';
                if (dateAr != '')
                {

                var newDate = dateAr[0] + '/' + dateAr[1] + '/' + dateAr[2];
                }

                if (k >= 0)
                {

                temp['first_name'] = v.name;
                temp['address'] = v.address;
                temp['dob'] = newDate;
                arr.push(temp);
                //// $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><div class='col-md-12'><button class='close clsdiv' type='button'>x</button><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label><input type='hidden'class='owneridDynamic' id='ownerid"+x+"' name='ownerid[]' value=''><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='"+v.name+"' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' >Last Name</label><input type='text' name='last_name[]' id='last_name" + x + "' value='' class='form-control last_name' placeholder='Enter Last Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='text' name='date_of_birth[]'  id='date_of_birth" + x + "' value='"+newDate+"' class='form-control date_of_birth datepicker-dis-fdate'  placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span><span class='text-success' id='successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span></label><a href='javascript:void(0);' data-id='" + x + "' id='pan_verify" + x + "' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[] id='response" + x + "' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per'  placeholder='Enter Shareholder' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification'  placeholder='Enter Education Qualification.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership'  placeholder='Enter Other Ownership'></div></div><div class='col-md-4'><div class='form-group INR'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'><i class='fa fa-inr' aria-hidden='true'></i></a><input type='text' maxlength='15' name='networth[]' id='networth" + x + "' value='' class='form-control networth'  placeholder='Enter Networth'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address" + x + "'>"+v.address+"</textarea></div></div> <h5 class='card-title form-head-h5 mt-3'>Document </h5><div class='row mt-2 mb-4'><div class='col-md-12'> <div class='prtm-full-block'><div class='prtm-block-content'><div class='table-responsive ps ps--theme_default' data-ps-id='9615ce02-be28-0492-7403-d251d7f6339e'><table class='table text-center table-striped table-hover'><thead class='thead-primary'><tr><th class='text-left'>S.No</th><th>Document Name</th><th>Document ID No.</th><th>Action</th></tr></thead><tbody><tr><td class='text-left'>1</td><td width='30%'>Pan Card</td><td width='30%'><div class='col-md-12'><span class='text-success' id='v1successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v1failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppan"+ x +"' data-id='"+ x +"' class='verify-owner-no verify-show veripan' style='top:0px'>Verify</a><input type='text'  name='veripan[]' id='veripan"+ x +"' value='' class='form-control'  placeholder='Enter PAN Number'></div></td><td width='28%'><div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details' data-id='" + x + "' data-type='3'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button><input type='file' name='verifyfile[]' class='verifyfile' id='verifyfile" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='panfile[]' data-id='" + x + "' class='panfile' id='panfile" + x + "'> </div> </td> </tr><tr> <td class='text-left'>2</td> <td width='30%'>Driving License</td> <td width='30%' > <div class='col-md-12'><span class='text-success' id='v2successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v2failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='ddriving" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show veridl' style='top:0px;'>Verify</a> <input type='text' name='verifydl[]' id='verifydl" + x + "' value='' class='form-control verifydl'  placeholder='Enter DL Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "' data-type='5'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' id='downloaddl" + x + "' name='downloaddl[]' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple='' class='downloaddl'> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='dlfile[]' data-id='" + x + "' class='dlfile' id='dlfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>3</td> <td width='30%'>Voter ID</td> <td width='30%' > <div class='col-md-12'><span class='text-success' id='v3successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v3failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='vvoter" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show verivoter' style='top:0px;'>Verify</a> <input type='text' name='verifyvoter[]' id='verifyvoter" + x + "' value='' class='form-control verifyvoter'  placeholder='Enter Voter's Epic Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='4'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadvoter[]' class='downloadvoter' id='downloadvoter" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  class='voterfile' name='voterfile[]' id='voterfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>4</td> <td width='30%'>Passport</td> <td width='30%' > <div class='col-md-12'> <span class='text-success' id='v4successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v4failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppassport" + x + "' data-id='" + x +"' class='verify-owner-no verify-show veripass' style='top:0px;'>Verify</a> <input type='text' name='verifypassport[]' id='verifypassport" + x + "' value='' class='form-control verifypassport'  placeholder='Enter File Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='6'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadpassport[]' class='downloadpassport'  id='downloadpassport" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'   name='passportfile[]' class='passportfile' id='passportfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>5</td> <td width='30%'>Photo</td> <td width='30%' > </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadphoto[]' class='downloadphoto' id='downloadphoto" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  name='photofile[]' name='photofile' id='photofile" + x + "'> </div> </td> </tr> </tbody> </table> <div class='ps__scrollbar-x-rail' style='left: 0px; bottom: 0px;'><div class='ps__scrollbar-x'  style='left: 0px; width: 0px;'></div></div><div class='ps__scrollbar-y-rail' style='top: 0px; right: 0px;'><div class='ps__scrollbar-y'  style='top: 0px; height: 0px;'></div></div> </div> </div> </div> </div> </div> </div></div></div> ");
                x++;
                }
                count++;
                });
                var bizId = $('input[name=biz_id]').val();
                var appId = $('input[name=app_id]').val();
                if (arr.length > 0) {
                    var getRes = savePromoter(arr, bizId, appId);
                }
                }
        });
        });
        /* save promoter details after cin number api hit */
        function  savePromoter(data, bizId, appId)
        {

        var data = {'data' : data, 'biz_id' : bizId, 'app_id' : appId};
        jQuery.ajax({
        url: "/application/front-promoter-save",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'post',
                contentType: "json",
                processData: false,
                data: JSON.stringify(data),
                success: function (data) {
                
                window.location.href = "{{ route('promoter-detail', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}";
                var promoId = 0;
                $(data.data).each(function(k, v){
                console.log(v);
                $("#ownerid" + promoId).val(v);
                promoId++;
                });
                $("#rowcount").val(k);
                }
        });
        }


   
    ///////////////Promotor web service for pan verified start here//////////////////////////
        $(document).on('click', '.promoter_pan_verify', function () {
        var count = $(this).attr('data-id');
        var PAN = $("#pan_no" + count).val();
        var bizId = $('input[name=biz_id]').val();
        var name = $("#first_name" + count).val();
        var dob = $("#date_of_birth" + count).val();
        var appId   =  $("#app_id").val();
        var ownerid = $('#ownerid' + count).val();
        var consent = "Y";
        var key = "NX1nBICr7TNEisJ";
        var dataStore = ({'consent': consent, 'pan': PAN,'app_id' : appId,'ownerid':ownerid, 'biz_id' : bizId,'_token': messages.token,'name':name, 'dob':dob});
        var postData = dataStore;
        $('#pan_verify' + count).text('Waiting...');
        jQuery.ajax({
        url: messages.chk_user_pan_karza,
                /// var dataStore = {'pan': 'BVZPS1846R','name':'Omkar Milind Shirhatti','dob':'17/08/1987','_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id };

                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                $('#pan_verify' + count).text('Verify');
                },
                success: function (data) {
              
                if (data['status'] == 1)
                {
                $("#veripan"+count).val(PAN);   
                $('#pan_no' + count).attr('readonly', true);
                $('#pan_verify' + count).css('pointer-events', 'none');
                $('#ppanStatusVeriView' + count).css('display', 'inline');
                $('#pan_verify' + count).text('Verified')
                $('#successpanverify' + count).show();
                $('#failurepanverify' + count).hide();
               /// $("#submit").attr("disabled", false);
                } else {
                $('#pan_verify' + count).text('Verify');
                $('#successpanverify' + count).hide();
                $('#failurepanverify' + count).show();
              ///  $("#submit").attr("disabled", true);
                }
                }
        });
        });
        
    ///////////////Promotor web service for pan verified for add more start here//////////////////////////
        $(document).on('click', '.promoter_pan_verify_add_more', function () {
        var count = $(this).attr('data-id');
        var PAN = $("#pan_no" + count).val();
        var bizId = $('input[name=biz_id]').val();
        var name = $("#first_name" + count).val();
        var dob = $("#date_of_birth" + count).val();
        var appId   =  $("#app_id").val();
        var ownerid = $('#ownerid' + count).val();
        var consent = "Y";
        var key = "NX1nBICr7TNEisJ";
        var dataStore = ({'consent': consent, 'pan': PAN,'app_id' : appId,'ownerid':ownerid, 'biz_id' : bizId,'_token': messages.token,'name':name, 'dob':dob});
        var postData = dataStore;
        $('#pan_verify' + count).text('Waiting...');
        jQuery.ajax({
        url: messages.chk_user_pan_karza_add_more,
                /// var dataStore = {'pan': 'BVZPS1846R','name':'Omkar Milind Shirhatti','dob':'17/08/1987','_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id };

               method: 'post',
               dataType: 'json',
               data: postData,
               error: function (xhr, status, errorThrown) {
               alert(errorThrown);
               $('#pan_verify' + count).text('Verify');
               },
               success: function (data) {
             
               if (data.status == 1)
               {
               $("#veripan"+count).val(PAN);  
               $('#response'+count).val(data.value);
               $('#pan_no'+ count).attr('readonly', true);
               $('#pan_verify'+ count).text('Verified')
               $('#successpanverify' + count).show();
               $('#failurepanverify' + count).hide();
              /// $("#submit").attr("disabled", false);
               } else {
               $('#pan_verify' + count).text('Verify');
               $('#successpanverify' + count).hide();
               $('#failurepanverify' + count).show();
             ///  $("#submit").attr("disabled", true);
               }
               }
        });
        });
        /////////////////Karja Api pan status /////////////////////////////////////

        $(document).on('click', '.veripan', function () {
        var count = $(this).attr('data-id');
        var bizId = $('input[name=biz_id]').val();
        var app_id = $('#app_id').val();
        var ownerid = $('#ownerid' + count).val();
        if (ownerid)
        {
        var ownerid = ownerid;
        }
        else
        {
        var ownerid = 0;
        }
        var PAN = $("#veripan" + count).val();
        var name = $("#first_name" + count).val();
        var dob = $("#date_of_birth" + count).val();
        var doc_type_name = $(this).data('doc_type_name') ? $(this).data('doc_type_name') : '';
        var dataStore = {'pan': PAN, 'name':name, 'dob':dob, '_token': messages.token, 'biz_id':bizId, 'ownerid':ownerid, 'app_id':app_id, doc_type_name:doc_type_name};
        var postData = dataStore;
        $('#ppan' + count).text('Waiting...');
        jQuery.ajax({

        url: messages.chk_user_pan_status_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                $('#ppan' + count).text('Verify');
                },
                success: function (data) {
                if (data['status'] == 1)
                {
                
                $('#veripan' + count).attr('readonly', true);
                $('#ppan' + count).text('Verified');
                $('#ppan' + count).css('pointer-events', 'none');
                $('#ppanVeriView' + count).css('display', 'inline');
                $('#v1successpanverify' + count).show();
                $('#v1failurepanverify' + count).hide();
               /// $("#submit").attr("disabled", false);
                } else{
                $('#ppan' + count).text('Verify');
                $('#v1successpanverify' + count).hide();
                $('#v1failurepanverify' + count).show();
                // $("#submit").attr("disabled", true);
                }


                }
        });
        });
        ///////////////////////DL api ///////////////
        $(document).on('click', '.veridl', function () {
        var count = $(this).attr('data-id');
        var bizId = $('input[name=biz_id]').val();
        var app_id = $('#app_id').val();
        var ownerid = $('#ownerid' + count).val();
        if (ownerid > 0)
        {
        var ownerid = ownerid;
        }
        else
        {
        var ownerid = 0;
        }

        var PAN = $("#verifydl" + count).val();
        var dl_no = $("#verifydl" + count).val();
        var dob = $("#date_of_birth" + count).val();
        var doc_type_name = $(this).data('doc_type_name') ? $(this).data('doc_type_name') : '';
        var dataStore = {'dl_no': dl_no, 'dob':dob, '_token': messages.token, 'biz_id':bizId, 'ownerid':ownerid, 'app_id':app_id, doc_type_name:doc_type_name};
        ////var dataStore = {'dl_no': 'MH01 20090091406','dob':'12-06-1987','_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id};

        var postData = dataStore;
        $('#ddriving' + count).text('Waiting...');
        jQuery.ajax({
        url: messages.chk_user_dl_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                $('#ddriving' + count).text('Verify');
                },
                success: function (data) {
                if (data['status'] == 1)
                {
                $('#verifydl' + count).attr('readonly', true);
                $('#ddriving' + count).text('Verified');
                $('#ddriving' + count).css('pointer-events', 'none');
                $('#ddrivingVeriView' + count).css('display', 'inline');
                $('#v2successpanverify' + count).show();
                $('#v2failurepanverify' + count).hide();
                $("#submit").attr("disabled", false);
                } else{
                $('#ddriving' + count).text('Verify');
                $('#v2successpanverify' + count).hide();
                $('#v2failurepanverify' + count).show();
                /// $("#submit").attr("disabled", true);
                }


                }
        });
        });
        /////////////////Karja Api Voter Card/////////////////////////////////////


        $(document).on('click', '.verivoter', function () {
        var count = $(this).attr('data-id');
        var voterId = $("#verifyvoter" + count).val();
        var bizId = $('input[name=biz_id]').val();
        var app_id = $('#app_id').val();
        var ownerid = $('#ownerid' + count).val();
        var doc_type_name = $(this).data('doc_type_name') ? $(this).data('doc_type_name') : '';
        if (ownerid)
        {
        var ownerid = ownerid;
        }
        else
        {
        var ownerid = 0;
        }
        var dataStore = {'epic_no':voterId, '_token': messages.token, 'biz_id':bizId, 'ownerid':ownerid, 'app_id':app_id, doc_type_name:doc_type_name };
        ///  var dataStore = {'epic_no': 'SHA4722088','_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id };
        var postData = dataStore;
        $('#vvoter' + count).text('Waiting...');
        jQuery.ajax({
        url: messages.chk_user_voterid_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                $('#vvoter' + count).text('Verify');
                },
                success: function (data) {

                if (data['status'] > 0)
                {
                $('#verifyvoter' + count).attr('readonly', true);
                $('#vvoter' + count).text('Verified');
                $('#vvoter' + count).css('pointer-events', 'none');
                $('#vvoterVeriView' + count).show();
                $('#v3successpanverify' + count).show();
                $('#v3failurepanverify' + count).hide();
                $("#submit").attr("disabled", false);
                } else{
                $('#vvoter' + count).text('Verify');
                $('#v3successpanverify' + count).hide();
                $('#v3failurepanverify' + count).show();
                /// $("#submit").attr("disabled", true);
                }


                }
        });
        });
        /////////////////Karja Api Passport Card/////////////////////////////////////


        $(document).on('click', '.veripass', function ()  {
        var count = $(this).attr('data-id');
        var voterId = $("#verifypassport" + count).val();
        var bizId = $('input[name=biz_id]').val();
        var app_id = $('#app_id').val();
        var ownerid = $('#ownerid' + count).val();
        if (ownerid)
        {
        var ownerid = ownerid;
        }
        else
        {
        var ownerid = 0;
        }
        var file = $("#verifypassport" + count).val();
        var dob = $("#date_of_birth" + count).val();
        var doc_type_name = $(this).data('doc_type_name') ? $(this).data('doc_type_name') : '';
        var dataStore = {'fileNo': file, 'dob':dob, '_token': messages.token, 'biz_id':bizId, 'ownerid':ownerid, 'app_id':app_id,doc_type_name:doc_type_name};
        //var dataStore = {'fileNo': 'BO3072344560818','dob':'17/08/1987','_token': messages.token };
        var postData = dataStore;
        $('#ppassport' + count).text('Waiting...');
        jQuery.ajax({

        url: messages.chk_user_passport_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                $('#ppassport' + count).text('Verify');
                },
                success: function (data) {
                if (data['status'] == 1)
                {

                $('#verifypassport' + count).attr('readonly', true);
                $('#ppassport' + count).text('Verified');
                $('#ppassport' + count).css('pointer-events', 'none');
                $('#ppassportVeriView' + count).css('display', 'inline');
                $('#v4successpanverify' + count).show();
                $('#v4failurepanverify' + count).hide();
                $("#submit").attr("disabled", false);
                } else{
                $('#ppassport' + count).text('Verify');
                $('#v4successpanverify' + count).hide();
                $('#v4failurepanverify' + count).show();
                ///$("#submit").attr("disabled", true);
                }


                }
        });
        });
        $(document).on('click', '.viewDocument', function(){
        var data_id = $(this).data('id');
        var data_type = $(this).data('type');
        var ownerid = $("#ownerid" + data_id).val();
        var postData = ({'ownerid':ownerid, 'type':data_type});
        if (data_type == 3) { if ($("#ppan" + data_id).html() == 'Verify')  {  $("#v1failurepanverify" + data_id).show(); return false; }   }
        else if (data_type == 5) { if ($("#ddriving" + data_id).html() == 'Verify')  {  $("#v2failurepanverify" + data_id).show(); return false; }  }
        else if (data_type == 4) { if ($("#vvoter" + data_id).html() == 'Verify')  {  $("#v3failurepanverify" + data_id).show(); return false; }  }
        else if (data_type == 6) { if ($("#ppassport" + data_id).html() == 'Verify')  {  $("#v4failurepanverify" + data_id).show(); return false; }  }


        jQuery.ajax({
        url: messages.get_user_pan_response_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (data)
                {
                if (data_type == 3) { var firstVerify = 'View PAN Card Detail'; var showalert = 'PAN Card Detail'; }
                if (data_type == 5) { var firstVerify = 'View Driving License Detail'; var showalert = 'Driving License Detail'; }
                if (data_type == 4) { var firstVerify = 'View Voter ID  Detail'; var showalert = 'Voter ID Detail'; }
                if (data_type == 6) { var firstVerify = 'View Passport Detail'; var showalert = 'Passport Detail'; }
                //else { var firstVerify  = 'Something went wrong!'; }
                if (data.status == 1)
                {
                $('#myModal' + data_id).modal('show');
                $("#getBizApiRes" + data_id).html(data.res);
                $("#dynamicTitle" + data_id).html(firstVerify);
                }
                else if (data.status == 2)
                {
                alert('Verification not found due to some stuck response from Api');
               }
                else
                {
                alert('Please verify ' + showalert);
                }

                }
        });
        });
        
    </script>
    <style>
        .error{ 
            color:red;
        }
    </style>
        <script type="text/javascript">
       appurl = '{{URL::route("verify_mobile") }}';
       otpSend = '{{URL::route("sent_otp_mobile") }}';
       otpurl = '{{URL::route("verify_otp_mobile") }}';
       _token = "{{ csrf_token() }}";
       appId = "{{ $appId }}";</script>
    <script>
        
          //////////////////////for otp verified///////////////////
          
        $(document).on('click', '.verify_otp', function () { 
        var count    = $(this).attr('data-id');
        var  mobile_no  =  $("#mobile_no"+count).val();
        var biz_owner_id    = $("#ownerid"+count).val();
        var appId   =  $("#app_id").val();
        var otp =  $("#verify_otp_no"+count).val();
       if(otp=='')
        {
            $("#v6failurepanverify"+count).html('<i>Please enter OTP</i>');
            return false;
        }
        $("#v5failurepanverify"+count).html(''); 
        data = {_token, otp,request_id, appId, biz_owner_id};
        $.ajax({
                url  : otpurl,
                type :'POST',
                data : data,
                beforeSend: function() {
                $(".isloader").show();
                },
                dataType : 'json',
                success:function(result) {
                if (result.status==1) {
                     $("#verify_mobile_otp_no"+count).hide();
                     $("#toggleOtp"+count).hide();
                     $("#pOtpVeriView"+count).show();
                     $("#v11successpanverify"+count).show();
                  }
                   else
                   {
                        $("#v6failurepanverify"+count).html('<i>Not Verified</i>');     
                   }
                },
                error:function(error) {
                      var html = '<i>Please enter correct OTP</i>';
                      $("#v6failurepanverify"+count).html(html);
                      },
                complete: function() {
                $(".isloader").hide();
                },
        })
        });
        
        
        ////////////////////send opt on mobile/////////////////
        
        $(document).on('click', '.sen_otp_to_mobile', function () {
        var count  = $(this).attr('data-id');  
        var biz_owner_id    = $("#ownerid"+count).val();
        var appId   =  $("#app_id").val();
        var  mobile_no  =  $("#mobile_no"+count).val();
        if (mobile_no=='') {
            $("#v5failurepanverify"+count).html('<i>Please enter correct mobile no.</i>');
              return false;
        }
        else if(mobile_no.length < 10)
        {
             $("#v5failurepanverify"+count).html('<i> Enter 10 digit mobile no.</i>');
              return false;
        }
        data = {_token, mobile_no, appId, biz_owner_id};
        $.ajax({
        url  : otpSend,
                type :'POST',
                data : data,
                beforeSend: function() {
                $(".isloader").show();
                },
                dataType : 'json',
                success:function(result) {
                  if(result.status==1)
                   {
                    $("#v5failurepanverify"+count).html('<i>'+result.message+'</i>');
                    request_id = result.request_id;
                    $("#toggleOtp"+count).show();
                    $("#verify_mobile_otp_no"+count).html("Resend OTP");
                }
                else
                {
                    $("#v5failurepanverify"+count).html('<i>Please enter correct mobile no.</i>');
                    
                 }
                 },
                error:function(error) {
                    var html = '<i>Please enter correct mobile no</i>';
                     $("#v6failurepanverify"+count).html(html);
              },
                complete: function() {
                    $(".isloader").hide();
                },
        })
        });
      
       //////////////////////for mobile verified///////////////////
       
        $(document).on('click', '.verify_mobile_no', function () {
        var count  = $(this).attr('data-id');  
        var appId   =  $("#app_id").val();
        var  biz_owner_id    = $("#ownerid"+count).val();
        var  mobile_no  =  $("#mobile_no"+count).val();
         if (mobile_no=='') {
            $("#v5failurepanverify"+count).html('<i>Please enter correct mobile no.</i>');
              return false;
        }
        else if(mobile_no.length < 10)
        {
             $("#v5failurepanverify"+count).html('<i> Enter 10 digit mobile no.</i>');
              return false;
        }
         $("#v5failurepanverify"+count).hide();
         $("#v5successpanverify"+count).hide();
        data = {_token, mobile_no, appId, biz_owner_id};
        $.ajax({
        url  : appurl,
                type :'POST',
                data : data,
                beforeSend: function() {
                $(".isloader").show();
                },
                dataType : 'json',
                success:function(result) {
                    $(".isloader").hide();
                    var html = result['message'];
                 
                    if (result.status==1) {
                        $(this).hide();
                        $("#v5successpanverify"+count).show();
                        $("#v5successpanverify"+count).html('<i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i>'); 
                        $("#verify_mobile_no"+count).text('Verified');
                        $("#mobile_no"+count).attr('readonly','readonly');
                        $("#verify_mobile_no"+count).hide();
                         request_id = result.request_id;
                        $("#verify_mobile_otp_no"+count).css('pointer-events','auto');
                        $("#pMobileVeriView"+count).show();
                    }
                    else
                    {
                        $("#v5failurepanverify"+count).show();
                        var html = '<i>Please enter correct mobile no.</i>';
                        $("#v5failurepanverify"+count).html(html);
                    }
                },
                error:function(error) {
                    $("#v5failurepanverify"+count).show();
                     var html = '<i>Please enter correct mobile no.</i>';
                     $("#v5failurepanverify"+count).html(html);
              },
                complete: function() {
                    $(".isloader").hide();
                },
        })
        });
        $(document).on('click', '#modalMobile .close', function() {
        $('#modalMobile').hide();
        });
        
        $(document).on('click', '#modalOtp .close', function() {
        $('#modalOtp').hide();
        });
        
        
        
           $(document).on('keypress', '.share_per', function(e){
        $char = e.keyCode || e.which;
      
        if (($char < 48 && $char!=46) || $char > 57) {
            return false;
        }
            return true;
        })
        
         $(document).on('keypress', '.networth', function(e){
        $char = e.keyCode || e.which;
        if ($char < 48 || $char > 57) {
            return false;
        }
           var id   =  $(this).attr('id');
           document.getElementById(id).addEventListener('input', event =>
           event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
           return true;
        })
        
       $(document).on('keypress', '.mobileveri', function(e){
        $char = e.keyCode || e.which;
        if ($char < 48 || $char > 57) {
            return false;
        }
            return true;
        })
    </script>
    @endsection