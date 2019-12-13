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
        @can('company_details')
        <li>
            <a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" >Business Information</a>
        </li>
        @endcan 
        @can('promoter_details')
        <li>
            <a href="{{ route('promoter_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">Management Details</a>
        </li>
        @endcan 
        @can('documents')
        <li>
            <a href="{{ route('documents', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Documents</a>
        </li>
        @endcan        
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
                        @php ($main = [])
                        @php ($main1 = [])
                        @if(count($ownerDetails) > 0) 
                        @foreach($ownerDetails as $key=>$row)    @php ($i++)

                        <div class="form-fields custom-promoter">
                            @csrf

                            <?php
                            array_push($main, array('panNo' => null, 'dlNo' => null, 'voterNo' => null, 'passNo' => null,'mobileNo' => null,'mobileOtpNo' => null));
                            array_push($main1, array('panNoFile' => null, 'dlNoFile' => null, 'voterNoFile' => null, 'passNoFile' => null, 'aadharFile' => null));

                            // dd($row->businessApi);
                            /* for get api response file data   */
                            foreach ($row->businessApi as $row1) {

                                if ($row1->type == 3) {
                                    $main[$key]['panNo'] = json_decode($row1->karza->req_file);
                                } else if ($row1->type == 5) {
                                    $main[$key]['dlNo'] = json_decode($row1->karza->req_file);
                                } else if ($row1->type == 4) {
                                    $main[$key]['voterNo'] = json_decode($row1->karza->req_file);
                                } else if ($row1->type == 6) {
                                    $main[$key]['passNo'] = json_decode($row1->karza->req_file);
                                }
                                else if ($row1->type == 7) {
                                    $main[$key]['mobileNo'] = json_decode($row1->karza->req_file);
                                }
                                 else if ($row1->type == 8) {
                                    $main[$key]['mobileOtpNo'] = json_decode($row1->karza->req_file);
                                }
                            }
                       
                                /* for get document file data   */
                            foreach ($row->document as $row2) {
                                if ($row2->doc_id == 2) {
                                    $main1[$key]['panNoFile'] = $row2->userFile->file_path;
                                } else if ($row2->doc_id == 31) {

                                    $main1[$key]['dlNoFile'] = $row2->userFile->file_path;
                                } else if ($row2->doc_id == 30) {

                                    $main1[$key]['voterNoFile'] = $row2->userFile->file_path;
                                } else if ($row2->doc_id == 32) {
                                    $main1[$key]['passNoFile'] = $row2->userFile->file_path;
                                } else if ($row2->doc_id == 22) {

                                    $main1[$key]['photoFile'] = $row2->userFile->file_path;
                                } else if ($row2->doc_id == 34) {

                                    $main1[$key]['aadharFile'] = $row2->userFile->file_path;
                                }
                            }
                            ?>
                            <div class="col-md-12">
                                <h5 class="card-title form-head">Management Details ({{isset($row->first_name) ? $i : '1'}}) </h5>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod" class="d-block"> Name

                                                <span class="mandatory">*</span>
                                                
                                                <span class="pull-right d-flex"><input type="checkbox" name="is_promoter[]" data-id="{{isset($row->first_name) ? $i : '1'}}" id="is_promoter{{isset($row->first_name) ? $i : '1'}}" {{($row->is_promoter==1) ?  "checked='checked'" : ''}} value="{{($row->is_promoter==1) ?  '1' : '0'}}" class="form-control is_promoter mr-2">
                                                    <span class="white-space-nowrap">Is Promoter</span></span>
                                            </label>
                                            <input type="hidden" name="ownerid[]" id="ownerid{{isset($row->first_name) ? $i : '1'}}" value="{{$row->biz_owner_id}}">   
                                            <input type="text" name="first_name[]" id="first_name{{isset($row->first_name) ? $i : '1'}}" vname="first_name1" value="{{$row->first_name}}" class="form-control first_name" placeholder="Enter First Name" >
                                        </div>
                                    </div>
                                      <div class="col-md-4">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">Shareholding (%)

                                              
                                            </label>
                                            <input type="text" readonly=readonly id="share_per{{isset($row->first_name) ? $i : '1'}}" name="share_per[]" data-id="{{isset($row->first_name) ? $i : '1'}}" maxlength="3"  value="{{$row->share_per}}" class="form-control share_per"  placeholder="Enter Shareholder" >
                                            <span class="error" id="shareCheck{{isset($row->first_name) ? $i : '1'}}"></span> 
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-4">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">DOB
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" readonly="readonly" name="date_of_birth[]" id="date_of_birth{{isset($row->first_name) ? $i : '1'}}" value="{{ ($row->date_of_birth) ? date('d/m/Y', strtotime($row->date_of_birth)) : '' }}" class="form-control date_of_birth datepicker-dis-fdate"  placeholder="Enter Date Of Birth" >
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">Gender
                                                <span class="mandatory">*</span>
                                            </label>
                                            <select class="form-control gender" name="gender[]" id="gender{{isset($row->first_name) ? $i : '1'}}">

                                                <option value=""> Select Gender</option>
                                                <option value="1" @if($row->gender==1)  selected="selected" @endif> Male </option>
                                                <option value="2" @if($row->gender==2)  selected="selected" @endif>Female </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">PAN Number
                                                <span class="mandatory">*</span>
                                                <span class="text-success" id="successpanverify{{isset($row->first_name) ? $i : '1'}}" style="display:{{ (isset($row->pan->pan_gst_hash)) ? 'inline' : 'none' }}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>
                                                <span class="text-danger" id="failurepanverify{{isset($row->first_name) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>

                                            </label>

                                            <a href="javascript:void(0);" data-id="{{isset($row->first_name) ? $i : '1'}}" id="pan_verify{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no promoter_pan_verify" style="pointer-events:{{ (isset($row->pan->pan_gst_hash)) ? 'none' : ''}}">{{ (isset($row->pan->pan_gst_hash)) ? 'Verified' : 'Verify' }}</a>
                                            <input type="text" name="pan_no[]" id="pan_no{{isset($row->first_name) ? $i : '1'}}" value="{{ (isset($row->pan->pan_gst_hash)) ? $row->pan->pan_gst_hash : '' }}" class="form-control pan_no" placeholder="Enter Pan Number" {{ (isset($row->pan->pan_gst_hash)) ? '    readonly' : '' }}>
                                            <input name="response[]" id="response{{isset($row->first_name) ? $i : '1'}}" type="hidden" value="">
                                        </div>
                                    </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtEmail">Educational Qualification

                                            </label>
                                            <input type="text" name="edu_qualification[]" id="edu_qualification{{isset($row->first_name) ? $i : '1'}}" value="{{$row->edu_qualification}}" class="form-control edu_qualification"  placeholder="Enter Education Qualification">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                                 <label for="txtEmail">Other Ownerships
                                                 </label>
                                                 <input type="text" name="other_ownership[]" id="other_ownership{{isset($row->first_name) ? $i : '1'}}" value="{{$row->other_ownership}}" class="form-control other_ownership"  placeholder="Other Ownership">
                                             </div>
                                     </div>
                                  
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Address
                                                <span class="mandatory">*</span>
                                            </label>
                                            <textarea  style="height: 35px;" class="form-control textarea address" placeholder="Enter Address" name="owner_addr[]" id="address{{isset($row->first_name) ? $i : '1'}}">{{$row->owner_addr}}</textarea>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="row">
                                    <div class="col-md-4"> 
                                    <div class="form-group INR">
                                                 <label for="txtEmail">Networth
                                                 </label><a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                 <input type="text" name="networth[]" maxlength='15' id="networth{{isset($row->first_name) ? $i : '1'}}" value="{{$row->networth}}" class="form-control networth"  placeholder="Enter Networth">
                                             </div>
                                    </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                         <label for="txtEmail">Mobile <span class="mandatory">*</span>  </label> 
                                             <input type="text" name="mobile_no[]"  {{isset($main[$j]['mobileNo']->mobile) ? 'readonly' : '' }} maxlength="10" id="mobile_no{{isset($row->first_name) ? $i : '1'}}" value="{{ isset($main[$j]['mobileNo']->mobile) ? $main[$j]['mobileNo']->mobile : '' }}" class="form-control mobileveri"  placeholder="Enter Mobile no">
                                              
                                             <span class="text-success float-left" id="v5successpanverify{{isset($row->first_name) ? $i : '1'}}"><i class="fa fa-{{isset($main[$j]['mobileNo']->mobile) ? 'check-circle' : '' }}" aria-hidden="true"></i><i> {{isset($main[$j]['mobileNo']->mobile) ? 'Verified Successfully' : '' }}</i> </span>
                                                <span class="text-danger float-left" id="v5failurepanverify{{isset($row->first_name) ? $i : '1'}}"> </span>
                                                
                                             
                                            </div>
                                        </div>
                                    <div class="col-md-3">
                                         <div class="form-group" >
                                    
                                             <a data-toggle="modal" id="pMobileVeriView{{isset($row->first_name) ? $i : '1'}}" data-target="#modalPromoter7" data-height="400px" data-width="100%" accesskey="" data-url ="{{ route('front_verify_mobile',['type' => 7,'ownerid' => $row->biz_owner_id]) }}" style="display:{{isset($main[$j]['mobileNo']->mobile) ? 'inline' : 'none'}}"> <button class="btn-upload btn-sm" type="button" title="Verify without OTP" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="7"> <i class="fa fa-eye"></i></button></a>
                                       <a class="btn btn-primary  btn-sm verify_mobile_no"  data-id="{{isset($row->first_name) ? $i : '1'}}" name="verify_mobile_no" id="verify_mobile_no{{isset($row->first_name) ? $i : '1'}}" style="color:white; margin-top:30px;bottom: 15px;top: auto;  display:{{ (isset($main[$j]['mobileNo']->mobile)) ? 'none' : ''}}" > {{ isset($main[$j]['mobileNo']->mobile) ? 'Verified' : 'Verify without OTP' }}</a>
                                      
                                       <a class="btn btn-primary btn-sm ml-2 sen_otp_to_mobile"  data-id="{{isset($row->first_name) ? $i : '1'}}" name="verify_mobile_otp_no" id="verify_mobile_otp_no{{isset($row->first_name) ? $i : '1'}}" style="color:white; margin-top:30px;bottom: 15px;top: auto; display:{{ (isset($main[$j]['mobileOtpNo']->request_id)) ? 'none' : ''}}" > {{ isset($main[$j]['mobileOtpNo']->request_id) ? 'Verified' : 'Verify with OTP' }}</a> 
                                    </div>
                                           </div>
                                    <div class="col-md-2" id="toggleOtp{{isset($row->first_name) ? $i : '1'}}" style="display:none">
                                        <div class="form-group" >
                                                
                                                <label for="txtEmail">OTP <span class="mandatory">*</span></label>  
                                                <a class="verify-owner-no verify-show verify_otp" name="verify_otp" data-id="{{isset($row->first_name) ? $i : '1'}}"> {{isset($main[$j]['mobileOtpNo']->request_id) ?  'Verified' : 'Verify' }}</a>
                                                <input type="text" name="otp_no[]"   maxlength="6" id="verify_otp_no{{isset($row->first_name) ? $i : '1'}}" value="" class="form-control mobileotpveri"  placeholder="Enter OTP" >
                                               <span class="text-success float-left" id="v6successpanverify{{isset($row->first_name) ? $i : '1'}}"> {{isset($main[$j]['mobileNo']->request_id) ? 'Verified Successfully' : '' }} </span>
                                                <span class="text-danger float-left" id="v6failurepanverify{{isset($row->first_name) ? $i : '1'}}"> </span>
                                                
                                            </div>
                                      </div>  
                                    <div class="col-md-1">
                                         <div class="form-group" >
                                             <a data-toggle="modal" id="pOtpVeriView{{isset($row->first_name) ? $i : '1'}}" data-target="#modalPromoter8" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('front_mobile_otp_view',['type'=> 8,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($main[$j]['mobileOtpNo']->request_id) ? 'inline' : 'none'}}"> <button class="btn-upload btn-sm" type="button" title="Verify without OTP" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="8"> <i class="fa fa-eye"></i></button></a>
                                       
                                      </div>
                                    </div>

                           
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Comment
                                              
                                            </label>
                                            <textarea class="form-control textarea" placeholder="Enter Comment" name="comment[]" id="comment{{isset($row->first_name) ? $i : '1'}}">{{$row->comment}}</textarea>
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
                                                                    <a href="javascript:void(0);" id='ppan{{isset($row->first_name) ? $i : '1'}}' data-id="{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no verify-show veripan" style="top:0px; pointer-events:{{ (isset($main[$j]['panNo']->requestId)) ? 'none' : ''}}">{{ isset($main[$j]['panNo']->requestId) ? 'Verified' : 'Verify' }}</a>
                                                                    <input type="text" {{isset($main[$j]['panNo']->requestId) ? "readonly='readonly'" : '' }} value="{{ isset($main[$j]['panNo']->requestId) ? $main[$j]['panNo']->requestId : '' }}"  name="veripan[]" id="veripan{{isset($row->first_name) ? $i : '1'}}"  class="form-control verifydl"  placeholder="Enter PAN Number">
                                                                    <span class="text-success float-left" id="v1successpanverify{{isset($row->first_name) ? $i : '1'}}" style="display:{{isset($main[$j]['panNo']->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>
                                                                    <span class="text-danger float-left" id="v1failurepanverify{{isset($row->first_name) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>

                                                                </div>
                                                            </td>
                                                            <td width="14%">

                                                                <div class="file-browse float-left position-seta">
                                                                    <a data-toggle="modal" id="ppanVeriView{{isset($row->first_name) ? $i : '1'}}" data-target="#modalPromoter" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('front_show_pan_data',['type'=>3,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($main[$j]['panNo']->requestId) ? 'inline' : 'none'}}"> <button class="btn-upload btn-sm" type="button" title="view Details" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="3"> <i class="fa fa-eye"></i></button>
                                                                    </a>
                                                                    <a  href="{{ isset($main1[$j]['panNoFile']) ? Storage::url($main1[$j]['panNoFile']) : '' }}" class="btn-upload   btn-sm" type="button" id="pandown{{isset($row->first_name) ? $i : '1'}}" style="display:{{ isset($main1[$j]['panNoFile']) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>



                                                                    <input type="file" class="verifyfile" name="verifyfile[]" id="verifyfile{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>

                                                            </td>
                                                            <td width="14%">

                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" class="panfile" data-id="{{isset($row->first_name) ? $i : '1'}}"  name="panfile[]" id="panfile{{isset($row->first_name) ? $i : '1'}}" onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, {{ $row->biz_owner_id }}, 2)">
                                                                    <span class="fileUpload"></span>
                                                                </div>   
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">2</td>
                                                            <td width="30%">Driving License</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);" id='ddriving{{isset($row->first_name) ? $i : '1'}}' data-id="{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no verify-show veridl" style="top:0px; pointer-events:{{ (isset($main[$j]['dlNo']->requestId)) ? 'none' : ''}}">{{ isset($main[$j]['dlNo']->requestId) ? 'Verified' : 'Verify' }}</a>
                                                                    <input type="text" {{ isset($main[$j]['dlNo']->requestId) ? "readonly='readonly'" : '' }} value="{{ isset($main[$j]['dlNo']->requestId) ? $main[$j]['dlNo']->requestId : '' }}" name="verifydl[]" id="verifydl{{isset($row->first_name) ? $i : '1'}}" class="form-control verifydl"  placeholder="Enter DL Number">

                                                                           <span class="text-success float-left" id="v2successpanverify{{isset($row->first_name) ? $i : '1'}}" style="display:{{isset($main[$j]['dlNo']->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>

                                                                    <span class="text-danger float-left" id="v2failurepanverify{{isset($row->first_name) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>

                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a data-toggle="modal" id="ddrivingVeriView{{isset($row->first_name) ? $i : '1'}}"  data-target="#modalPromoter1" data-height="400" data-width="100%" accesskey="" data-url="{{route('front_show_dl_data',['type'=>'5','ownerid' => $row->biz_owner_id ])}}" style="display:{{ (isset($main[$j]['dlNo']->requestId)) ? 'inline' : 'none'}}">  <button class="btn-upload btn-sm" type="button" title="view Details" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="5" > <i class="fa fa-eye"></i></button></a>
                                                                    <a  href="{{ isset($main1[$j]['dlNoFile']) ? Storage::url($main1[$j]['dlNoFile']) : '' }}" class="btn-upload   btn-sm" type="button" id="dldown{{isset($row->first_name) ? $i : '1'}}" style="display:{{ isset($main1[$j]['dlNoFile']) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                                    <input type="file" id="downloaddl{{isset($row->first_name) ? $i : '1'}}" name="downloaddl[]" class="downloaddl" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>

                                                            </td>
                                                            <td width="14%">

                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="dlfile[]" data-id="{{isset($row->first_name) ? $i : '1'}}"  id="dlfile{{isset($row->first_name) ? $i : '1'}}" class="dlfile"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, {{ $row->biz_owner_id }}, 31)">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">3</td>
                                                            <td width="30%">Voter ID</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">

                                                                    <a href="javascript:void(0);" id='vvoter{{isset($row->first_name) ? $i : '1'}}' data-id="{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no verify-show verivoter" style="top:0px; pointer-events:{{ (isset($main[$j]['voterNo']->requestId)) ? 'none' : ''}}">{{ isset($main[$j]['voterNo']->requestId) ? 'Verified' : 'Verify' }}</a>

                                                                    <input type="text" {{isset($main[$j]['voterNo']->requestId) ? "readonly='readonly'" : '' }} value="{{ isset($main[$j]['voterNo']->requestId) ? $main[$j]['voterNo']->requestId : '' }}" name="verifyvoter[]" id="verifyvoter{{isset($row->first_name) ? $i : '1'}}"  class="form-control verifyvoter"  placeholder="Enter Voter's Epic Number">
                                                                    <span class="text-success float-left" id="v3successpanverify{{isset($row->first_name) ? $i : '1'}}" style="display:{{isset($main[$j]['voterNo']->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>

                                                                    <span class="text-danger float-left" id="v3failurepanverify{{isset($row->first_name) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>

                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a data-toggle="modal" id="vvoterVeriView{{isset($row->first_name) ? $i : '1'}}"  data-target="#modalPromoter2" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('front_show_voter_data',['type'=>4,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($main[$j]['voterNo']->requestId) ? 'inline' : 'none'}}">   <button class="btn-upload btn-sm" type="button" title="view Details" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="4"> <i class="fa fa-eye"></i></button></a>
                                                                    <a  href="{{ isset($main1[$j]['voterNoFile']) ? Storage::url($main1[$j]['voterNoFile']) : '' }}" class="btn-upload   btn-sm" type="button" id="voterdown{{isset($row->first_name) ? $i : '1'}}" style="display:{{ isset($main1[$j]['voterNoFile']) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                                    <input type="file" name="downloadvoter[]" class="downloadvoter" id="downloadvoter{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>

                                                            </td>
                                                            <td width="14%">
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="voterfile[]" data-id="{{isset($row->first_name) ? $i : '1'}}"  class="voterfile" id="voterfile{{isset($row->first_name) ? $i : '1'}}"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, {{ $row->biz_owner_id }}, 30)">
                                                                </div>

                                                            </td>
                                                        </tr>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">4</td>
                                                            <td width="30%">Passport</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);" id='ppassport{{isset($row->first_name) ? $i : '1'}}' data-id="{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no verify-show veripass" style="top:0px; pointer-events:{{ (isset($main[$j]['passNo']->requestId)) ? 'none' : ''}}">{{ isset($main[$j]['passNo']->requestId) ? 'Verified' : 'Verify' }}</a>
                                                                    <input type="text"  {{ isset($main[$j]['passNo']->requestId) ? "readonly='readonly'" : '' }}  value="{{ isset($main[$j]['passNo']->requestId) ? $main[$j]['passNo']->requestId : '' }}" name="verifypassport[]" id="verifypassport{{isset($row->first_name) ? $i : '1'}}"  class="form-control verifypassport" placeholder="Enter File Number">

                                                                           <span class="text-success float-left" id="v4successpanverify{{isset($row->first_name) ? $i : '1'}}"  style="display:{{isset($main[$j]['passNo']->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>

                                                                    <span class="text-danger float-left" id="v4failurepanverify{{isset($row->first_name) ? $i : '1'}}"  style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>


                                                                </div>
                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <a data-toggle="modal" id="ppassportVeriView{{isset($row->first_name) ? $i : '1'}}" data-target="#modalPromoter3" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('front_show_pass_data',['type'=>6,'ownerid' => $row->biz_owner_id ])}}"  style="display:{{isset($main[$j]['passNo']->requestId) ? 'inline' : 'none'}}">     <button class="btn-upload btn-sm" type="button" title="view Details" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="6"> <i class="fa fa-eye"></i></button></a>
                                                                    <a  href="{{ isset($main1[$j]['passNoFile']) ? Storage::url($main1[$j]['passNoFile']) : '' }}" class="btn-upload   btn-sm" type="button" id="passdown{{isset($row->first_name) ? $i : '1'}}" style="display:{{ isset($main1[$j]['passNoFile']) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                                    <input type="file" name="downloadpassport[]" class="downloadpassport" id="downloadpassport{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>

                                                            </td>
                                                            <td width="14%">
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="passportfile[]" data-id="{{isset($row->first_name) ? $i : '1'}}" class="passportfile" id="passportfile{{isset($row->first_name) ? $i : '1'}}"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, {{ $row->biz_owner_id }}, 32)">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">5</td>
                                                            <td width="30%">Photo</td>
                                                            <td width="30%" >

                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">

                                                                    <a  href="{{ isset($main1[$j]['photoFile']) ? Storage::url($main1[$j]['photoFile']) : '' }}" class="btn-upload   btn-sm" type="button" id="photodown{{isset($row->first_name) ? $i : '1'}}" style="display:{{ isset($main1[$j]['photoFile']) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                                    <input type="file" class="downloadphoto"  name="downloadphoto[]" id="downloadphoto{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>

                                                            </td>
                                                            <td width="14%"> 
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" class="photofile"  name="photofile[]"  data-id="{{isset($row->first_name) ? $i : '1'}}"  id="photofile{{isset($row->first_name) ? $i : '1'}}"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, {{ $row->biz_owner_id }}, 22)">
                                                                </div>
                                                            </td>
                                                        </tr>


                                                        <tr>
                                                            <td class="text-left">5</td>
                                                            <td width="30%">Aadhar Card </td>
                                                            <td width="30%" >

                                                            </td>
                                                            <td width="14%">
                                                                <div class="file-browse float-left position-seta">

                                                                    <a  href="{{ isset($main1[$j]['aadharFile']) ? Storage::url($main1[$j]['aadharFile']) : '' }}" class="btn-upload   btn-sm" type="button" id="aadhardown{{isset($row->first_name) ? $i : '1'}}" style="display:{{ isset($main1[$j]['aadharFile']) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                                    <input type="file" class="downloadaadhar"  name="downloadaadhar[]" id="downloadaadhar{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>

                                                            </td>
                                                            <td width="14%"> 
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" class="aadharfile"  name="aadharfile[]"  data-id="{{isset($row->first_name) ? $i : '1'}}"  id="aadharfile{{isset($row->first_name) ? $i : '1'}}"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, {{ $row->biz_owner_id }}, 34)">
                                                                </div>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>

                                                <div class="modal" id="myModal{{isset($row->first_name) ? $i : '1'}}">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h5 id="dynamicTitle{{isset($row->first_name) ? $i : '1'}}"></h5>
                                                                <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                                                            </div>
                                                            <!-- Modal body -->
                                                            <div class="modal-body text-left">
                                                                <div class="table-responsive ps ps--theme_default" data-ps-id="c019a9d0-57f7-7dd4-16ba-e6ea054ce839">
                                                                    <span class="getBizApiRes" id="getBizApiRes{{isset($row->first_name) ? $i : '1'}}"></span>
                                                                    <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps__scrollbar-x"  style="left: 0px; width: 0px;"></div></div><div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;"><div class="ps__scrollbar-y"  style="top: 0px; height: 0px;"></div></div></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>	


                                <div class="modal" id="myModal">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <br/>
                                                <div class="form-group password-input">
                                                    <label for="txtPassword"><b>Select Promoter Type</b>
                                                        <span class="mandatory">*</span>
                                                    </label>
                                                    <select class="form-control ">
                                                        <option> Select</option>
                                                        <option> Co-Applicant</option>
                                                        <option>Guarantor </option>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="text-right mt-3">
                                                            <button type="button" id="btnAddMore" class="btn btn-primary">

                                                                Submit
                                                            </button>
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
                                       <!-- <input type="button" value="Back" class="btn btn-warning" onclick="window.location.href='company-details.php'">
                                        --> <input type="button" value="Save and Continue" id="submit" class="btn btn-success btn-sm">

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
                chk_user_dl_karza: "{{ URL::route('chk_user_dl_karza') }}",
                chk_user_passport_karza: "{{ URL::route('chk_user_passport_karza') }}",
                chk_user_pan_status_karza: "{{ URL::route('chk_user_pan_status_karza') }}",
                get_user_pan_response_karza: "{{ URL::route('get_user_pan_response_karza') }}",
        };
        $(document).ready(function () {

        $('#submit').on('click', function (event) {
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
        $('select.gender').each(function () {
        $(this).rules("add",
        {
        required: true
        })
        });
        $('input.pan_no').each(function () {
        $(this).rules("add",
        {
        required: true
        })
        });
        /*  $('input.share_per').each(function () {
         $(this).rules("add",
         {
         required: true,
         number: true,
         range: [0, 100]
         })
         });   */
        $('input.mobileveri').each(function () {
        $(this).rules("add",
        {
        required: true,
                number: true,
        })
        });
        /*  $('input.edu_qualification').each(function () {
         $(this).rules("add",
         {
         required: true
         })
         });
     
         $('input.other_ownership').each(function () {
         $(this).rules("add",
         {
         required: true
         })
         });
         $('input.networth').each(function () {
         $(this).rules("add",
         {
         required: true,
         number: true
         })
         });  
         */
        $('textarea.address').each(function () {
        $(this).rules("add",
        {
        required: true
        })
        });
        /* $('.privacy_chk').each(function () {
         $(this).rules("add",
         {
         required: true
         })
         }); */
        // test if form is valid 
        if ($('form#signupForm').validate().form()) {
        var panCount = 0;
        var promoCount = 0;
        var DlLength = $('input[name="dlfile[]"]').length;
        var total = 0;
        ///// for upload one in three id proff..............
        for (i = 1; i <= DlLength; i++)
        {


        var dlVal = $("#dldown" + i).attr('href');
        var vtVal = $("#voterdown" + i).attr('href');
        var adVal = $("#aadhardown" + i).attr('href');
        if (dlVal == "" && vtVal == "" && adVal == "")
        {
        alert('Please upload atleast one ID Proof in ( Driving License / Voter ID / Aadhar Card ) in Management ' + i + '');
        $("#verifydl" + i).focus();
        return false;
        }

        }

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
        $(".verify_mobile_no").each(function (k, v) {
        panCount++;
        $spantrgt = $(this).closest('div').find('.pullMsg_mob');
        var result = $(this).text();
        if (result == "Verify")
        {
        $spantrgt.html('<span class="text-danger"><i class="fa fa-check-close" aria-hidden="true"></i> <i>Mobile no is not verified.</i> </span>');
        e.preventDefault();
        return false;
        }

        });
        
        
        ///// validation for where is checked then shareholder is mandaterory/////
        $(".is_promoter").each(function (k, v) {
        promoCount++;
        var is_promoter = $("#is_promoter" + promoCount).val();
        if (is_promoter == 1)
        {
            $("#share_per"+promoCount).attr("readonly","readonly");
            var shareHolder = $("#share_per" + promoCount).val();
            if (shareHolder == '')
            {
                $("#shareCheck" + promoCount).text('This field is required.');
                e.preventDefault();
                return false;
            }
            else if (shareHolder == 0 || shareHolder > 100)
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
        else
        {
                $("#share_per"+promoCount).removeAttr("readonly");
                $("#shareCheck" + promoCount).text('');
                return true;
        }
        });
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
                var ownerNull = false;
                $(".owneridDynamic").each(function(k, v){
                var GetVal = $(this).val();
                if (GetVal == '')
                {
                return  ownerNull = true;
                }

                });
                if (ownerNull == true)
                {
                   window.location.href = "{{ route('promoter-detail', []) }}";
                }
                else
                {
                if (res.status == 1)
                {
                    window.location.href = "{{ route('document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id') ]) }}";
                }
                else {
                alert("Something went wrong, please try again !");
                }
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
        var is_promoter = $("#is_promoter" + promoCount).val();
       
        if (is_promoter == 1)
        {
               
                if (shareHolder == '')
                {
                        $("#shareCheck" + promoCount).text('This field is required.');
                        return false;
                }
                else if (shareHolder == 0 || shareHolder > 100)
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
               
               
                $("#share_per" + promoCount).val('');
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
                $(this).val(0);
                $("#share_per"+count).attr("readonly","readonly");
                $("#shareCheck"+count).text('');
                $("#share_per"+count).val('');
                return true;
        }
        else
        {
                $(this).val(1);
                $("#share_per"+count).removeAttr("readonly");
                $("#share_per"+count).val('');
                $("#shareCheck"+count).text('This field is required.');
                return true;
        }
        });
        
       ////////////////// new form create by add promoter/////////////////
       
        $(document).on('click', '#btnAddMore', function () {
        var rowcount = parseInt($("#rowcount").val());
        if (rowcount > 0)
        {
        var x = rowcount + 1;
        } else
        {
        var x = 2;
        }
        $("#rowcount").val(x);
           $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><div class='col-md-12'><button class='close clsdiv' type='button'>x</button><h5 class='card-title form-head'>Management Details (" + x + ") </h5></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' class='d-block'> Name  <span class='mandatory'>*</span> <span class='pull-right d-flex'><input type='checkbox' name='is_promoter[]' data-id='"+x+"'  id='is_promoter"+x+"'  value='0' class='form-control is_promoter mr-2'><span class='white-space-nowrap'>Is Promoter</span></span></label><input type='hidden' class='owneridDynamic' id='ownerid" + x + "'   value=''><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)</label><input type='text' id='share_per" + x + "' data-id='" + x + "' maxlength='3' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per'  placeholder='Enter Shareholder' ><span class='error' id='shareCheck" + x + "'></span></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='text' name='date_of_birth[]'  id='date_of_birth" + x + "' readonly='readonly' value='' class='form-control date_of_birth datepicker-dis-fdate'  placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span><span class='text-success' id='successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span></label><a href='javascript:void(0);' data-id='" + x + "' id='pan_verify" + x + "' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[] id='response" + x + "' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification'  placeholder='Enter Education Qualification.'></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership'  placeholder='Enter Other Ownership'></div></div><div class='col-md-8'><div class='form-group'><label for='txtCreditPeriod'>Address<span class='mandatory'>*</span></label><textarea  style='height: 35px;' class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address'"+ x +"'></textarea></div></div></div><div class='row'><div class='col-md-4'><div class='form-group INR'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'><i class='fa fa-inr' aria-hidden='true'></i></a><input type='text' maxlength='15' name='networth[]' id='networth" + x + "' value='' class='form-control networth'  placeholder='Enter Networth'></div></div><div class='col-md-4' id='toggleOtp"+ x +"' style='display:none'><div class='form-group'><label for='txtEmail'>OTP <span class='mandatory'>*</span></label><span class='text-danger' id='v5failurepanverify"+ x +"' style='display:none;'><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i> </span><a class='verify-owner-no verify-show verify_otp' name='verify_otp' data-id='"+ x +"'> Verify</a><input type='text' name='otp_no[]'   maxlength='4' id='verify_otp_no"+ x +"' value='' class='form-control mobileotpveri'  placeholder='Enter OTP'></div></div></div><div class='row'><div class='col-md-12'><div class='form-group'><label for='txtCreditPeriod'>Comment</label><textarea class='form-control textarea' placeholder='Enter Comment' name='comment[]' id='comment"+x+"'></textarea></div></div></div></div><span id='disableDocumentPart"+x+"' style='display:none'><h5 class='card-title form-head-h5 mt-3'>Document </h5><div class='row mt-2 mb-4'><div class='col-md-12'> <div class='prtm-full-block'><div class='prtm-block-content'> <div class='table-responsive ps ps--theme_default' data-ps-id='9615ce02-be28-0492-7403-d251d7f6339e'><table class='table text-center table-striped table-hover'><thead class='thead-primary'><tr><th class='text-left'>S.No</th><th>Document Name</th><th>Document ID No.</th><th>Action</th></tr></thead><tbody><tr><td class='text-left'>1</td><td width='30%'>Pan Card</td><td width='30%'><div class='col-md-12'><span class='text-success' id='v1successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v1failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppan" + x + "' data-id='" + x + "' class='verify-owner-no verify-show veripan' style='top:0px'>Verify</a><input type='text'  name='veripan[]' id='veripan" + x + "' value='' class='form-control'  placeholder='Enter PAN Number'></div></td><td width='28%'><div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details' data-id='" + x + "' data-type='3'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button><input type='file' name='verifyfile[]' class='verifyfile' id='verifyfile" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='panfile[]' data-id='" + x + "' class='panfile' id='panfile" + x + "'> </div> </td> </tr><tr> <td class='text-left'>2</td> <td width='30%'>Driving License</td> <td width='30%' ><div class='col-md-12'><span class='text-success' id='v2successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v2failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='ddriving" + x + "' data-id='" + x + "'  class='verify-owner-no verify-show veridl' style='top:0px;'>Verify</a> <input type='text' name='verifydl[]' id='verifydl" + x + "' value='' class='form-control verifydl'  placeholder='Enter DL Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "' data-type='5'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' id='downloaddl" + x + "' name='downloaddl[]' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple='' class='downloaddl'> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='dlfile[]' data-id='" + x + "' class='dlfile' id='dlfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>3</td> <td width='30%'>Voter ID</td> <td width='30%' ><div class='col-md-12'><span class='text-success' id='v3successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v3failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='vvoter" + x + "' data-id='" + x + "'  class='verify-owner-no verify-show verivoter' style='top:0px;'>Verify</a> <input type='text' name='verifyvoter[]' id='verifyvoter" + x + "' value='' class='form-control verifyvoter'  placeholder='Enter Voter's Epic Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='4'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadvoter[]' class='downloadvoter' id='downloadvoter" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  class='voterfile' name='voterfile[]' id='voterfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>4</td> <td width='30%'>Passport</td> <td width='30%' ><div class='col-md-12'> <span class='text-success' id='v4successpanverify" + x + "' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v4failurepanverify" + x + "' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppassport" + x + "' data-id='" + x + "' class='verify-owner-no verify-show veripass' style='top:0px;'>Verify</a> <input type='text' name='verifypassport[]' id='verifypassport" + x + "' value='' class='form-control verifypassport'  placeholder='Enter File Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='6'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadpassport[]' class='downloadpassport'  id='downloadpassport" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'   name='passportfile[]' class='passportfile' id='passportfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>5</td> <td width='30%'>Photo</td> <td width='30%' > </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadphoto[]' class='downloadphoto' id='downloadphoto" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  name='photofile[]' name='photofile' id='photofile" + x + "'> </div> </td> </tr> </tbody> </table> </span> <div class='ps__scrollbar-x-rail' style='left: 0px; bottom: 0px;'><div class='ps__scrollbar-x'  style='left: 0px; width: 0px;'></div></div><div class='ps__scrollbar-y-rail' style='top: 0px; right: 0px;'><div class='ps__scrollbar-y'  style='top: 0px; height: 0px;'></div></div> </div> </div> </div> </div></div> </div></div></div>"); 
           x++;
        datepickerDisFdate();
        $(".owneridDynamic").each(function(k, v){
        var GetVal = $(this).val();
        if (GetVal == '')
        {
        $("#submit").val('Save');
        }
        else
        {
        $("#submit").val('Save and Continue');
        }

        });
        });
        
        
    //////////CIN webservice for get promoter details start here//////////////////////////////////////        
        $(document).on('click', '.clsdiv', function () {
        $(this).parent().parent().remove();
        $(".owneridDynamic").each(function(k, v){
        var GetVal = $(this).val();
        if (GetVal == '')
        {
        $("#submit").val('Save');
        }
        else
        {
        $("#submit").val('Save and Continue');
        }

        });
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
        var consent = "Y";
        var key = "NX1nBICr7TNEisJ";
        var dataStore = ({'consent': consent, 'entityId': CIN});
        var jsonData = JSON.stringify(dataStore);
        jQuery.ajax({
        url: "https://api.kscan.in/v1/corp/profile",
                headers: {
                'Content-Type': "application/json",
                        'x-karza-key': key
                },
                method: 'post',
                dataType: 'json',
                data: jsonData,
                error: function (xhr, status, errorThrown) {
                console.log(xhr);
                $('.isloader').hide();
                $('#btnAddMore').trigger('click'); return false;
                },
                success: function (result) {

                $(".isloader").hide();
                obj = result.result.directors;
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
                //// $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><div class='col-md-12'><button class='close clsdiv' type='button'>x</button><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label><input type='hidden'class='owneridDynamic' id='ownerid"+x+"' name='ownerid[]' value=''><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='"+v.name+"' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' >Last Name</label><input type='text' name='last_name[]' id='last_name" + x + "' value='' class='form-control last_name' placeholder='Enter Last Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='text' name='date_of_birth[]'  id='date_of_birth" + x + "' value='"+newDate+"' class='form-control date_of_birth datepicker-dis-fdate'  placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span><span class='text-success' id='successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span></label><a href='javascript:void(0);' data-id='" + x + "' id='pan_verify" + x + "' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[] id='response" + x + "' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per'  placeholder='Enter Shareholder' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification'  placeholder='Enter Education Qualification.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership'  placeholder='Enter Other Ownership'></div></div><div class='col-md-4'><div class='form-group INR'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'><i class='fa fa-inr' aria-hidden='true'></i></a><input type='text' maxlength='15' name='networth[]' id='networth" + x + "' value='' class='form-control networth'  placeholder='Enter Networth'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address" + x + "'>"+v.address+"</textarea></div></div> <h5 class='card-title form-head-h5 mt-3'>Document </h5><div class='row mt-2 mb-4'><div class='col-md-12'> <div class='prtm-full-block'><div class='prtm-block-content'><div class='table-responsive ps ps--theme_default' data-ps-id='9615ce02-be28-0492-7403-d251d7f6339e'><table class='table text-center table-striped table-hover'><thead class='thead-primary'><tr><th class='text-left'>S.No</th><th>Document Name</th><th>Document ID No.</th><th>Action</th></tr></thead><tbody><tr><td class='text-left'>1</td><td width='30%'>Pan Card</td><td width='30%'><div class='col-md-12'><span class='text-success' id='v1successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v1failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppan"+ x +"' data-id='"+ x +"' class='verify-owner-no verify-show veripan' style='top:0px'>Verify</a><input type='text'  name='veripan[]' id='veripan"+ x +"' value='' class='form-control'  placeholder='Enter PAN Number'></div></td><td width='28%'><div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details' data-id='" + x + "' data-type='3'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button><input type='file' name='verifyfile[]' class='verifyfile' id='verifyfile" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='panfile[]' data-id='" + x + "' class='panfile' id='panfile" + x + "'> </div> </td> </tr><tr> <td class='text-left'>2</td> <td width='30%'>Driving License</td> <td width='30%' > <div class='col-md-12'><span class='text-success' id='v2successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v2failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='ddriving" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show veridl' style='top:0px;'>Verify</a> <input type='text' name='verifydl[]' id='verifydl" + x + "' value='' class='form-control verifydl'  placeholder='Enter DL Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "' data-type='5'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' id='downloaddl" + x + "' name='downloaddl[]' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple='' class='downloaddl'> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='dlfile[]' data-id='" + x + "' class='dlfile' id='dlfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>3</td> <td width='30%'>Voter ID</td> <td width='30%' > <div class='col-md-12'><span class='text-success' id='v3successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v3failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='vvoter" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show verivoter' style='top:0px;'>Verify</a> <input type='text' name='verifyvoter[]' id='verifyvoter" + x + "' value='' class='form-control verifyvoter'  placeholder='Enter Voter's Epic Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='4'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadvoter[]' class='downloadvoter' id='downloadvoter" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  class='voterfile' name='voterfile[]' id='voterfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>4</td> <td width='30%'>Passport</td> <td width='30%' > <div class='col-md-12'> <span class='text-success' id='v4successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v4failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppassport" + x + "' data-id='" + x +"' class='verify-owner-no verify-show veripass' style='top:0px;'>Verify</a> <input type='text' name='verifypassport[]' id='verifypassport" + x + "' value='' class='form-control verifypassport'  placeholder='Enter File Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='6'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadpassport[]' class='downloadpassport'  id='downloadpassport" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'   name='passportfile[]' class='passportfile' id='passportfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>5</td> <td width='30%'>Photo</td> <td width='30%' > </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadphoto[]' class='downloadphoto' id='downloadphoto" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  name='photofile[]' name='photofile' id='photofile" + x + "'> </div> </td> </tr> </tbody> </table> <div class='ps__scrollbar-x-rail' style='left: 0px; bottom: 0px;'><div class='ps__scrollbar-x'  style='left: 0px; width: 0px;'></div></div><div class='ps__scrollbar-y-rail' style='top: 0px; right: 0px;'><div class='ps__scrollbar-y'  style='top: 0px; height: 0px;'></div></div> </div> </div> </div> </div> </div> </div></div></div> ");
                x++;
                }
                count++;
                });
                var bizId = $('input[name=biz_id]').val();
                var appId = $('input[name=app_id]').val();
                var getRes = savePromoter(arr, bizId, appId);
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
        var name = $("#first_name" + count).val();
        var dob = $("#date_of_birth" + count).val();
        var consent = "Y";
        var key = "NX1nBICr7TNEisJ";
        var dataStore = ({'consent': consent, 'pan': PAN, 'name':name, 'dob':dob});
        var jsonData = JSON.stringify(dataStore);
        $('#pan_verify' + count).text('Waiting...');
        jQuery.ajax({
        url: "https://api.karza.in/v2/pan-authentication",
                /// var dataStore = {'pan': 'BVZPS1846R','name':'Omkar Milind Shirhatti','dob':'17/08/1987','_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id };

                headers: {
                'Content-Type': "application/json",
                        'x-karza-key': key,
                },
                method: 'post',
                dataType: 'json',
                data: jsonData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                $('#pan_verify' + count).text('Verify');
                },
                success: function (data) {
                var name_status = data['result']['status'];
                var request_id = data['request_id'];
                var status = data['status-code'];
                if (data['status-code'] == 101)
                {
                var MergeResonse = name_status.concat(request_id, status);
                $('#response' + count).val(MergeResonse);
                $('#pan_no' + count).attr('readonly', true);
                $('#pan_verify' + count).text('Verified')
                        $('#successpanverify' + count).show();
                $('#failurepanverify' + count).hide();
                $("#submit").attr("disabled", false);
                } else {
                $('#pan_verify' + count).text('Verify');
                $('#successpanverify' + count).hide();
                $('#failurepanverify' + count).show();
                $("#submit").attr("disabled", true);
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
        var dataStore = {'pan': PAN, 'name':name, 'dob':dob, '_token': messages.token, 'biz_id':bizId, 'ownerid':ownerid, 'app_id':app_id};
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
                $("#submit").attr("disabled", false);
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
        var dataStore = {'dl_no': dl_no, 'dob':dob, '_token': messages.token, 'biz_id':bizId, 'ownerid':ownerid, 'app_id':app_id};
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
        if (ownerid)
        {
        var ownerid = ownerid;
        }
        else
        {
        var ownerid = 0;
        }
        var dataStore = {'epic_no':voterId, '_token': messages.token, 'biz_id':bizId, 'ownerid':ownerid, 'app_id':app_id };
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
        var dataStore = {'fileNo': file, 'dob':dob, '_token': messages.token, 'biz_id':bizId, 'ownerid':ownerid, 'app_id':app_id};
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
        //$(".isloader").css('display', 'inline');
        var count    = $(this).attr('data-id');
        var  mobile_no  =  $("#mobile_no"+count).val();
        var biz_owner_id    = $("#ownerid"+count).val();
        var appId   =  $("#app_id").val();
        var otp =  $("#verify_otp_no"+count).val();
        if(otp=='')
        {
            $("#v6failurepanverify"+count).text('Please enter OTP');
            return false;
        }
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
                 ///   $(".isloader").css('display', 'none');
                   
//                    let mclass = result['status'] ? 'success' : 'danger';
//                    let micon = result['status'] ? 'circle' : 'close';
//                    var html = result['message'];
                 if (result.status==1) {
                     $("#verify_mobile_otp_no"+count).hide();
                     $("#toggleOtp"+count).hide();
                     $("#pOtpVeriView"+count).show();
//                            var request_id = result['request_id'];
//                            span_target.html('<span class="text-' + mclass + '"><i class="fa fa-check-' + micon + '" aria-hidden="true"></i> <i>' + html + '</i> </span>');
//                            span_target.css('pointer-events', 'none');
//                            $("#mobile_no"+count).attr('readonly', 'readonly');
//                            $("#verify_mobile_no"+count).css('pointer-events', 'none');
//                         ///   $("#toggleOtp"+count).hide();
//                            $('#modalOtp').show();
//                            $('#modalOtp iframe').attr({'src':'{{URL::route("mobile_verify") }}?type=otp&request_id=' + request_id, 'width':'100%'});
                    }
                   else
                   {
                        $("#v6failurepanverify"+count).html('Not Verified');      
                   }
                },
                error:function(error) {
                     $(".isloader").css('display', 'none');
                      var html = 'Some error occured.';
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
            $("#v5failurepanverify"+count).html('<i>Please enter mobile no.</i>');
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
                    request_id = result.request_id;
                    $("#toggleOtp"+count).show();
                },
                error:function(error) {
                var html = 'Some error occured.';
                $("#v6failurepanverify"+count).html(html);
         //        $("#toggleOtp"+count).hide();
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
            $("#v5failurepanverify"+count).html('<i>Please enter mobile no.</i>');
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
                        ///$("#toggleOtp"+count).show();
                        $("#verify_mobile_no"+count).text('Verified');
                        $("#mobile_no"+count).attr('readonly','readonly');
                        $("#verify_mobile_no"+count).hide();
                         request_id = result.request_id;
                        $("#verify_mobile_otp_no"+count).css('pointer-events','auto');
                        $("#pMobileVeriView"+count).show();
                       //  $('#modalMobile').show();
                          ///  $('#modalMobile iframe').attr({'src':'{{URL::route("mobile_verify") }}?type=mobile&mobile=' + mobile_no, 'width':'100%'});
                    }
                    else
                    {
                         var html = 'Some error occured.';
                         $("#v5failurepanverify"+count).html(html);
                    }
                },
                error:function(error) {
                var html = 'Some error occured.';
                $("#v5failurepanverify"+count).html(html);
         //         $("#toggleOtp"+count).hide();
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
        if ($char < 48 || $char > 57) {
            return false;
        }
            return true;
        })
    </script>
@endsection