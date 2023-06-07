<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
<style>
    .footer{
        width: 104% !important;
        margin-left: -27px !important;
        position: inherit !important;
    }
</style>
@extends('layouts.backend.admin-layout')

@section('content')

    <div class="content-wrapper">
	@include('master.ucic.tab_nav')

    <div class="row grid-margin mt-3">
	<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
		<div class="card">
			<div class="card-body">
            <div class="data">
               <!--
                  <h2 class="sub-title bg mb-4"><span class=" mt-2">Company CIBIL</span> <button  class="btn btn-primary  btn-sm float-right"> Upload Document</button></h2>
                  -->
               <h2 class="sub-title bg"   style="margin-bottom: 0px;">Company</h2>
               <div id="pullMsgCommercial"></div>
               @if(!empty($bizData))
               <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                  <div class="row ">
                     <div class="col-sm-12">
                        <table id="cibil-table" class="table table-striped  no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="cibil-table_info" style="width: 100%;">
                           <thead>
                              <tr role="row">
                                 <th>S No.</th>
                                 <th>Entity Name</th>
                                 <th>PAN No.</th>
                                 <th>Email Id</th>
                                 <th>Mobile No.</th>
                                 <th>MSME No.</th>
                                 <th>UCIC ID</th>
                                 <th>CKYC Applicable</th>
                                 <th>Action</th>
                                 <th></th>
                              </tr>
                           </thead>
                        
                           
                     
                              @php
                              $i = 0;
                              $arr = $bizData;
                              $i++;
                              $searchLegalApiLog = App\Inv\Repositories\Models\UserCkycApiLog::getLegalEntityckycsearchLog($user_id,1);
                              $downloadLegalApiLog = App\Inv\Repositories\Models\UserCkycApiLog::getLegalEntityckycsearchLog($user_id,2);
                              
                              
                        @endphp
                              
                        
                            <tbody>                    
                                <tr role="row" class="odd">
                                    <td>{{$i}}</td>
                                    <td>{{$arr->biz_entity_name}}</td>
                                    <td>{{$arr->pan_gst_hash}}</td>
                                    <td>{{$arr->email}}</td>
                                    <td>{{$arr->mobile_no}}</td>
                                    <td>{{$arr->msme_no}}</td>
                                    <td>{{$ucic->ucic_code??'N/A'}}</td>
                                    <td>
                                    <select  class="form-control" name="is_applicable" tabindex="9" id="business_ckyc_applicable_{{$arr->biz_id}}" onchange='apply_ckyc("{{$arr->user_id}}","{{$arr->biz_id}}","{{$ucic->user_ucic_id}}",0,this)' @if($consentData !== null) disabled @endif>
                                       <option value="">Select Ckyc Applicable</option>
                                       <option value="1" @if(!is_null($companyCkycReport) && $companyCkycReport->ckyc_applicable == '1') selected @endif>Yes</option>
                                       <option value="0" @if(!is_null($companyCkycReport) && $companyCkycReport->ckyc_applicable == '0') selected @endif>No</option>
                                    </select>
                                    
                                    </td>
                                    <td> 
                                       @if($consentData !== null)
                                          @if($consentData['status'] == '1')
                                          @can('ckyc_pull_request')
                                          @if(is_null($searchLegalApiLog))
                                           <a id="pull_button" class="btn btn-success btn-sm"  supplier="49" id="cibilScoreBtn{{$arr->biz_id}}" href="{{ route('ckyc_pull_request', ['user_id' => $arr->user_id, 'biz_id'=> $arr->biz_id,'userUcicId'=>$ucic->user_ucic_id]) }}" onclick="showloader(this)">Pull</a> 
                                           @endif
                                           @if(!is_null($searchLegalApiLog))
                                           <a id="pull_button" class="btn btn-success btn-sm"  supplier="49" id="cibilScoreBtn{{$arr->biz_id}}" href="{{ route('ckyc_pull_request', ['user_id' => $arr->user_id, 'biz_id'=> $arr->biz_id,'userUcicId'=>$ucic->user_ucic_id]) }}" onclick="showloader(this)">Re-Pull</a> 
                                           @endif
                                          @endcan  
                                          @elseif($consentData['status'] == '0')
                                          @can('ckyc_otp_consent')
                                            <div class="btn-group"><label style="background-color:#0cb70c; color:white;margin-top: 7px;" class="badge badge-warning">OTP consent pending&nbsp; &nbsp;</label></div>
                                            <a class="btn btn-success btn-sm" supplier="49"  onclick="return confirm('Are you sure you want to re-send OTP consent?')" href="{{ route('ckyc_otp_consent', ['user_id' => $arr->user_id, 'biz_id'=> $arr->biz_id,'ckyc_consent_id'=>$consentData['ckyc_consent_id'],'userUcicId'=>$ucic->user_ucic_id]) }}" onclick="showloader(this)"> Re-send OTP Consent</a>
                                          @endcan
                                          @can('ckyc_manual_consent')
                                            <a data-toggle="modal" class="btn btn-success btn-sm" supplier="49" style="margin-right: -50px;" id="businessManual" data-target="#modalManualConsent" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('ckyc_manual_consent',['user_id' => $arr->user_id, 'biz_id'=> $arr->biz_id,'ckyc_consent_id'=>$consentData['ckyc_consent_id'],'userUcicId'=>$ucic->user_ucic_id])}}" >Manual Consent</a>
                                          @endcan
                                          
                                          @endif
                                        @else  
                                          @can('ckyc_otp_consent')
                                            <a class="btn btn-success btn-sm" supplier="49"  onclick="return confirm('Are you sure you want to send OTP consent?')" href="{{ route('ckyc_otp_consent', ['user_id' => $arr->user_id, 'biz_id'=> $arr->biz_id,'userUcicId'=>$ucic->user_ucic_id]) }}" id="business_otp_consent_{{$arr->biz_id}}" onclick="showloader(this)" @if(is_null($companyCkycReport)) style="pointer-events: none;background-color: cadetblue;" @elseif($companyCkycReport->ckyc_applicable == '0') style="pointer-events: none;background-color: cadetblue;" @endif> OTP Consent</a>
                                          @endcan  
                                            @can('ckyc_manual_consent')
                                            <a data-toggle="modal" class="btn btn-success btn-sm" supplier="49"  id="business_manual_consent_{{$arr->biz_id}}" data-target="#modalManualConsent" data-height="300px" data-width="100%" accesskey=""data-url ="{{route('ckyc_manual_consent',['user_id' => $arr->user_id, 'biz_id'=> $arr->biz_id,'userUcicId'=>$ucic->user_ucic_id])}}" @if(is_null($companyCkycReport)) style="pointer-events: none;background-color: cadetblue;" @elseif($companyCkycReport->ckyc_applicable == '0') style="pointer-events: none;background-color: cadetblue;" @endif >Manual Consent</a>
                                            @endcan
                                            
                                        @endif
                                        
                                    </td>
                                    <td ><a  supplier="49" data-placement="top"  data-toggle="collapse" href="#collapseparent{{$i}}"><i class="fa fa-plus" ></i></a></td>
                                </tr>
                              </tbody>
                            </table>
                            </div>
                            </div>
                            <div class="row ">
                <div id="accordionparent{{$i}}" class="accordion d-table col-sm-12">
                    <div class="card card-color mb-0">
                        <div id="collapseparent{{$i}}" class="card-body collapse p-0 " data-parent="#accordionparent{{$i}}">

                            <table class="table  overview-table" id="documentTable" cellpadding="0" cellspacing="0" border="1">
                                <tbody>
                                    @if($consentData !== null)
                                        @if($consentData['status'] == '1')
                                           @if($consentData['consent_type'] == '1')
                                            <tr>
                                                <td width="20%"><b>CKYC Consent Type:</b></td>
                                                <td width="20%">Manual Consent Received</td>
                                                <td width="20%"><b>Consent File:</b></td>
                                                <td width="20%">
                                                <a  href="{{ route('download_storage_file', ['file_id' =>$consentData->file_id ]) }}" class="btn-upload   btn-sm" type="button" id="pandownconsent" > <i class="fa fa-download"></i></a>

                                                <a  href="{{route('view_uploaded_doc', ['file_id' =>$consentData->file_id ]) }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandowneyeconsent" target="_blank"> <i class="fa fa-eye"></i></a>
                                                </td>
                                                <td width="20%"><b>Comment:</b></td>
                                                <td width="20%">{{$consentData->comment}}</td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td width="20%"><b>CKYC Consent Type:</b></td>
                                                <td width="20%">OTP Consent Received</td>
                                                <td width="20%"></td>
                                                <td width="20%"></td>
                                                <td width="20%"></td>
                                                <td width="20%"></td>
                                            </tr>
                                           @endif 
                                        @endif
                                    @endif   
                                    
                                @if(!is_null($searchLegalApiLog) && !is_null($searchLegalApiLog->getCKYCdownloadData))
                                @php 
                                    if(empty($searchLegalApiLog->res_data) && !empty($searchLegalApiLog->res_file_id)){
                                        $entitysearchRes = App\Http\Controllers\Backend\CkycController::getCKYCResponse($searchLegalApiLog->res_file_id);
                                        $legalEntitysearchRes = json_decode($entitysearchRes,true);
                                        
                                    }else{
                                        $legalEntitysearchRes = json_decode($searchLegalApiLog->res_data,true);
                                    }
                                    
                                @endphp
                                    
                                    <tr>
                                        <td width="20%"><b>CKYC ID:</b></td>
                                        <td width="20%">{{$searchLegalApiLog->getCKYCdownloadData->ckyc_no}}</td>
                                        <td width="20%"><b>Full Name:</b></td>
                                        @if((isset($legalEntitysearchRes['result']['nonIndividual']) && !empty($legalEntitysearchRes['result']['nonIndividual'])))
                                        <td width="20%">{{$legalEntitysearchRes['result']['nonIndividual'][0]['name']}}</td>
                                        @elseif(isset($legalEntitysearchRes['result']['individual']) && !empty($legalEntitysearchRes['result']['individual']))
                                        <td width="20%">{{$legalEntitysearchRes['result']['individual'][0]['name']}}</td>
                                        @else
                                        <td width="20%">{{$legalEntitysearchRes['result']['name']}}</td>
                                        @endif
                                        <td width="20%"><b>Constitution Type:</b></td>
                                        @if((isset($legalEntitysearchRes['result']['nonIndividual']) && !empty($legalEntitysearchRes['result']['nonIndividual'])))
                                        <td width="20%">{{$legalEntitysearchRes['result']['nonIndividual'][0]['type']}}</td>
                                        @elseif((isset($legalEntitysearchRes['result']['individual']) && !empty($legalEntitysearchRes['result']['individual'])))
                                        <td width="20%">{{$legalEntitysearchRes['result']['individual'][0]['type']??'N/A'}}</td>
                                        @else
                                        <td width="20%">{{$legalEntitysearchRes['result']['type']}}</td>
                                        @endif
                                        
                                    </tr> 
                                    <tr>
                                       <td width="20%"><b>Place of Incorporation:</b></td>
                                       @if(isset($legalEntitysearchRes['result']['nonIndividual']) && !empty($legalEntitysearchRes['result']['nonIndividual']))
                                       <td width="20%">{{(isset($legalEntitysearchRes['result']['nonIndividual'][0]['placeOfIncorporation']) && !is_null($legalEntitysearchRes['result']['nonIndividual'][0]['placeOfIncorporation']))?$legalEntitysearchRes['result']['nonIndividual'][0]['placeOfIncorporation']:'N/A'}}</td>
                                       @elseif((isset($legalEntitysearchRes['result']['individual']) && !empty($legalEntitysearchRes['result']['individual'])))
                                        <td width="20%">{{$legalEntitysearchRes['result']['individual'][0]['placeOfIncorporation']??'N/A'}}</td>
                                        @else
                                          <td width="20%">{{(isset($legalEntitysearchRes['result']['placeOfIncorporation']) && !is_null($legalEntitysearchRes['result']['placeOfIncorporation']))?$legalEntitysearchRes['result']['placeOfIncorporation']:'N/A'}}</td>
                                       @endif
                                       
                                       <td width="20%"><b>Age:</b></td>
                                       @if(isset($legalEntitysearchRes['result']['nonIndividual']) && !empty($legalEntitysearchRes['result']['nonIndividual']))
                                       <td width="20%">{{(isset($indvsearchRes['result']['nonIndividual'][0]['age']) && !is_null($indvsearchRes['result']['nonIndividual'][0]['age']))?$indvsearchRes['result']['nonIndividual'][0]['age']:'N/A'}}</td>
                                       @elseif((isset($legalEntitysearchRes['result']['individual']) && !empty($legalEntitysearchRes['result']['individual'])))
                                        <td width="20%">{{$legalEntitysearchRes['result']['individual'][0]['age']??'N/A'}}</td>
                                        @else
                                          <td width="20%">{{(isset($legalEntitysearchRes['result']['age']) && !is_null($legalEntitysearchRes['result']['age']))?$legalEntitysearchRes['result']['age']:'N/A'}} Yrs</td>
                                       @endif
                                       <td width="20%"><b>KYC Date:</b></td>
                                       @if(isset($legalEntitysearchRes['result']['nonIndividual']) && !empty($legalEntitysearchRes['result']['nonIndividual']))
                                          <td width="20%">{{(isset($indvsearchRes['result']['nonIndividual'][0]['kycDate']) && !is_null($indvsearchRes['result']['nonIndividual'][0]['kycDate']))?$indvsearchRes['result']['nonIndividual'][0]['kycDate']:'N/A'}}</td>
                                        @elseif((isset($legalEntitysearchRes['result']['individual']) && !empty($legalEntitysearchRes['result']['individual'])))
                                        <td width="20%">{{$legalEntitysearchRes['result']['individual'][0]['kycDate']??'N/A'}}</td>
                                       @else
                                         <td width="20%">{{(isset($legalEntitysearchRes['result']['kycDate']) && !is_null($legalEntitysearchRes['result']['kycDate']))?$legalEntitysearchRes['result']['kycDate']:'N/A'}}</td>
                                       @endif
                                       
                                    </tr> 
                                    
                                        @if(!is_null($downloadLegalApiLog))
                                            @php 
                                                 if(empty($downloadLegalApiLog->res_data) && !empty($downloadLegalApiLog->res_file_id)){
                                                    $entitydownloadRes = App\Http\Controllers\Backend\CkycController::getCKYCResponse($downloadLegalApiLog->res_file_id);
                                                    $legalEntitydownloadRes = json_decode($entitydownloadRes,true);
                                                    
                                                }else{
                                                    $legalEntitydownloadRes = json_decode($downloadLegalApiLog->res_data,true);
                                                } 

                                                 
                                                
                                            @endphp  
                                             @if(isset($legalEntitydownloadRes['result']['personalDetails'])) 
                                             
                                           <tr>
                                           <td width="20%"><b>PAN No.</b></td>
                                           <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['pan']}}</td>
                                           <td width="20%"><b>DOB:</b></td>
                                           <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['dob']}}</td>
                                           <td width="20%"><b>Email ID:</b></td>
                                           @if(isset($legalEntitydownloadRes['result']['personalDetails']['email']) && !empty($legalEntitydownloadRes['result']['personalDetails']['email']))
                                           <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['email'][0]}}</td>
                                           @else
                                            <td width="20%">N/A</td>
                                            @endif
                                           </tr>
                                           
                                           <tr>
                                               <td width="20%"><b>Gender:</b></td>
                                               @if(isset($legalEntitydownloadRes['result']['personalDetails']['gender']))
                                               <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['gender']}}</td>
                                               @else
                                                <td width="20%">N/A</td>
                                                @endif
                                               <td width="20%"><b>GST:</b></td>
                                               <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['gstNumber']??'N/A'}}</td>
                                               <td width="20%"><b>Mobile Code:</b></td>
                                               @if(isset($legalEntitydownloadRes['result']['personalDetails']['mobNumber']) && !empty($legalEntitydownloadRes['result']['personalDetails']['mobNumber']))
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['mobNumber'][0]['code']}}</td>
                                               @else
                                               <td width="20%">N/A</td>
                                               @endif
                                           </tr>
                                            <tr>
                                           
                                               <td width="20%"><b>Mobile No:</b></td>
                                               @if(isset($legalEntitydownloadRes['result']['personalDetails']['mobNumber']) && !empty($legalEntitydownloadRes['result']['personalDetails']['mobNumber']))
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['mobNumber'][0]['mobNum']}}</td>
                                               @else
                                               <td width="20%">N/A</td>
                                               @endif
                                               
                                                <td width="20%"><b>Country of Incorporation:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['countryOfIncorporation']?$legalEntitydownloadRes['result']['personalDetails']['countryOfIncorporation']:'N/A'}}</td>
                                                <td width="20%"><b>Permanent Address:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['permanentAddress']['line1']}}<br> {{$legalEntitydownloadRes['result']['personalDetails']['permanentAddress']['line2']}} <br> {{$legalEntitydownloadRes['result']['personalDetails']['permanentAddress']['line3']}}</td>
                                            </tr> 
                                            <tr>
                                               
                                                <td width="20%"><b>City:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['permanentAddress']['city']}}</td>
                                                <td width="20%"><b>District:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['permanentAddress']['district']}}</td>
                                                <td width="20%"><b>State:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['permanentAddress']['state']}}</td>
                                            </tr> 
                                            <tr>
                                                
                                                <td width="20%"><b>Pincode:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['permanentAddress']['pincode']}}</td>
                                                <td width="20%"><b>Country:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['permanentAddress']['country']}}</td>
                                                <td width="20%"><b>Permanent Corresponding Address same:</b></td>
                                               <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['permCorresSame']?' Yes':' No'}}</td>
                                            </tr>
                                            
                                            <tr>
                                                
                                               <td width="20%"><b>Correspondence Address:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['correspondence']['line1']}}</br> {{$legalEntitydownloadRes['result']['personalDetails']['correspondence']['line2']}} </br> {{$legalEntitydownloadRes['result']['personalDetails']['correspondence']['line3']}}</td>
                                                <td width="20%"><b>City:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['correspondence']['city']}}</td>
                                                <td width="20%"><b>District:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['correspondence']['district']}}</td>
                                            </tr>
                                            <tr>
                                                
                                            <td width="20%"><b>State:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['correspondence']['state']}}</td>
                                                <td width="20%"><b>Country:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['correspondence']['country']}}</td>
                                                <td width="20%"><b>Pincode:</b></td>
                                                <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['correspondence']['pincode']}}</td>
                                            </tr>
                                            
                                            <tr>
                                            
                                            <td width="20%"><b>Declaration Date:</b></td>
                                            <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['declarationDate']}}</td>
                                            <td width="20%"><b>ID verification done:</b></td>
                                            <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['identityVerificationDone']??'N/A'}}</td>
                                            <td width="20%"><b>Organization Name:</b></td>
                                            <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['organisationName']}}</td>
                                            </tr> 
                                            <tr>
                                            <td width="20%"><b>Organization Code:</b></td>
                                            <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['organisationCode']}}</td>
                                              <td width="20%"><b>Declaration Place:</b></td>
                                              <td width="20%">{{$legalEntitydownloadRes['result']['personalDetails']['declarationPlace']}}</td>
                                              <td width="20%"><b>STD Code:</b></td>
                                              <td width="20%">{{($legalEntitydownloadRes['result']['personalDetails']['stdCode'] != null) ? $legalEntitydownloadRes['result']['personalDetails']['stdCode'] : 'N/A'}}</td>
                                            </tr>
                                            <tr>
                                            <td width="20%"><b>Type Of Doc Submited:</b></td>
                                            <td width="20%">{{($legalEntitydownloadRes['result']['personalDetails']['typeOfDocSubmitted'] != null) ? $legalEntitydownloadRes['result']['personalDetails']['typeOfDocSubmitted'] : 'N/A'}}</td>
                                              <td width="20%"><b>Office STD Code:</b></td>
                                              <td width="20%">{{($legalEntitydownloadRes['result']['personalDetails']['officeStdCode'] != null) ? $legalEntitydownloadRes['result']['personalDetails']['officeStdCode'] : 'N/A'}}</td>
                                              <td width="20%"><b>Office Telephone Number:</b></td>
                                              <td width="20%">{{($legalEntitydownloadRes['result']['personalDetails']['officeTelephoneNumber'] != null) ? $legalEntitydownloadRes['result']['personalDetails']['officeTelephoneNumber'] : 'N/A'}}</td>
                                            </tr>
                                            <tr>
                                            <td width="20%"><b>KYC Updated On:</b></td>
                                            @if(isset($legalEntitysearchRes['result']['nonIndividual']))
                                               <td width="20%">{{(isset($legalEntitysearchRes['result']['nonIndividual'][0]['updatedOn']) && !is_null($legalEntitysearchRes['result']['nonIndividual'][0]['updatedOn']))?$legalEntitysearchRes['result']['nonIndividual'][0]['updatedOn']:'N/A'}}</td>
                                            @else
                                               <td width="20%">{{(isset($legalEntitysearchRes['result']['updatedOn']) && !is_null($legalEntitysearchRes['result']['updatedOn']))?$legalEntitysearchRes['result']['updatedOn']:'N/A'}}</td>
                                            @endif
                                            
                                            <td width="20%"><b>Date of commencement</b></td>
                                            <td width="20%">{{(isset($legalEntitydownloadRes['result']['personalDetails']['dateOfCommencement']) && $legalEntitydownloadRes['result']['personalDetails']['dateOfCommencement'] != null) ? $legalEntitydownloadRes['result']['personalDetails']['dateOfCommencement'] : 'N/A'}}</td>
                                            <td width="20%"><b>Proof of address</b></td>
                                            <td width="20%">{{($legalEntitydownloadRes['result']['personalDetails']['proofAddress'] != null) ? $legalEntitydownloadRes['result']['personalDetails']['proofAddress'] : 'N/A'}}</td>
                                            </tr>
                                            <tr>
                                            <td width="20%"><b>Fax No:</b></td>
                                            <td width="20%">{{(isset($legalEntitydownloadRes['result']['personalDetails']['faxNo']) && !is_null($legalEntitydownloadRes['result']['personalDetails']['faxNo']))?$legalEntitydownloadRes['result']['personalDetails']['faxNo']:'N/A'}}</td>
                                            <td width="20%"></td>
                                            <td width="20%"></td>
                                            <td width="20%"></td>
                                            <td width="20%"></td>
                                            </tr>
                                            @endif
                                            
                                            @if(!is_null($downloadLegalApiLog->collectAllCkycDoc))
                                                @php $i=1; @endphp
                                                @foreach($downloadLegalApiLog->collectAllCkycDoc as $allCkycDoc)
                                                @php 
                                                @endphp
                                                 <tr>
                                                 <td width="20%"><b>{{ucfirst($allCkycDoc->file_type)}}:</b></td>
                                                 <td width="20%">
                                                 <a  href="{{ route('download_s3_file', ['file_id' =>$allCkycDoc->file_id ]) }}" class="btn-upload   btn-sm" type="button" id="pandown{{$i}}" > <i class="fa fa-download"></i></a>

                                                 <a  href="{{ Storage::url($allCkycDoc->file->file_path) }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandowneye{{$i}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                 </td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                </tr>
                                                @php $i++; @endphp
                                                @endforeach
                                            @endif
                                            @php $i=1;@endphp
                                            
                                            @if(!empty($legalEntitydownloadRes['result']['relatedPersonDetails']))
                                              @foreach($legalEntitydownloadRes['result']['relatedPersonDetails'] as $relatedPersonDetail)
                                                <tr><td><h6>Related Person Details {{$i}}</h6></td></tr>
                                                <tr>
                                                    <td><b>Relation Type:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['relationType']))?$relatedPersonDetail['relationType']:'N/A'}}</td>
                                                    <td><b>Add Delete Flag:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['addDeleteFlag']))?$relatedPersonDetail['addDeleteFlag']:'N/A'}}</td>
                                                    <td><b>CKYC Id:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['ckycId']))?$relatedPersonDetail['ckycId']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Full Name:</b></td>
                                                    <td>{{$relatedPersonDetail['personalInfo']['prefix']}} {{  $relatedPersonDetail['personalInfo']['firstName'] }} {{  $relatedPersonDetail['personalInfo']['lastName'] }}</td>
                                                    <td><b>DOB:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['dob']))?$relatedPersonDetail['dob']:'N/A'}}</td>
                                                    <td><b>Gender:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['gender']))?$relatedPersonDetail['gender']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Form60 Submitted:</b></td>
                                                    <td>{{($relatedPersonDetail['form60Submitted'])?'Yes':'No'}}</td>
                                                    <td><b>Maiden Name:</b></td>
                                                    @if(!is_null($relatedPersonDetail['maidenInfo']['maidenPrefix']) && !is_null($relatedPersonDetail['maidenInfo']['maidenFirstName']) && !is_null($relatedPersonDetail['maidenInfo']['maidenLastName']))
                                                    <td>{{$relatedPersonDetail['maidenInfo']['maidenPrefix']}} {{$relatedPersonDetail['maidenInfo']['maidenFirstName']}} {{$relatedPersonDetail['maidenInfo']['maidenLastName']}}</td>
                                                    @else
                                                    <td>N/A</td>
                                                    @endif
                                                    <td><b>Father Spouse Name:</b></td>
                                                    @if(!is_null($relatedPersonDetail['fatherSpouseInfo']['fatherSpousePrefix']) && !is_null($relatedPersonDetail['fatherSpouseInfo']['fatherSpouseFirstName']) && !is_null($relatedPersonDetail['fatherSpouseInfo']['fatherSpouseLastName']))
                                                    <td>{{$relatedPersonDetail['fatherSpouseInfo']['fatherSpousePrefix']}} {{$relatedPersonDetail['fatherSpouseInfo']['fatherSpouseFirstName']}} {{$relatedPersonDetail['fatherSpouseInfo']['fatherSpouseLastName']}}</td>
                                                    @else
                                                    <td>N/A</td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    <td><b>Mother Name:</b></td>
                                                    @if(!is_null($relatedPersonDetail['motherInfo']['motherPrefix']) && !is_null($relatedPersonDetail['motherInfo']['motherFirstName']) && !is_null($relatedPersonDetail['motherInfo']['motherLastName']))
                                                    <td>{{$relatedPersonDetail['motherInfo']['motherPrefix']}} {{$relatedPersonDetail['motherInfo']['motherFirstName']}} {{$relatedPersonDetail['motherInfo']['motherLastName']}}</td>
                                                    @else
                                                    <td>N/A</td>
                                                    @endif
                                                    <td><b>Permanent Address:</b></td>
                                                    @if(!is_null($relatedPersonDetail['permanentAddress']['line1']) && !is_null($relatedPersonDetail['permanentAddress']['line2']) && !is_null($relatedPersonDetail['permanentAddress']['line3']))
                                                    <td>{{$relatedPersonDetail['permanentAddress']['line1']}} {{$relatedPersonDetail['permanentAddress']['line2']}} {{$relatedPersonDetail['permanentAddress']['line3']}}</td>
                                                    @else
                                                    <td>N/A</td>
                                                    @endif
                                                    <td><b>City:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['permanentAddress']['city']))?$relatedPersonDetail['permanentAddress']['city']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>State:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['permanentAddress']['state']))?$relatedPersonDetail['permanentAddress']['state']:'N/A'}}</td>
                                                    <td><b>Country:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['permanentAddress']['country']))?$relatedPersonDetail['permanentAddress']['country']:'N/A'}}</td>
                                                    <td><b>Pincode:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['permanentAddress']['pincode']))?$relatedPersonDetail['permanentAddress']['pincode']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Permanent Corresponding Address same:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['permCorresSame']))?($relatedPersonDetail['permCorresSame']?'Yes':'No'):'N/A'}}</td>
                                                    <td><b>Correspondence Address:</b></td>
                                                    @if(!is_null($relatedPersonDetail['correspondence']['line1']) && !is_null($relatedPersonDetail['correspondence']['line2']) && !is_null($relatedPersonDetail['correspondence']['line3']))
                                                    <td>{{$relatedPersonDetail['correspondence']['line1']}} {{$relatedPersonDetail['correspondence']['line2']}} {{$relatedPersonDetail['correspondence']['line3']}}</td>
                                                    @else
                                                    <td>N/A</td>
                                                    @endif
                                                    <td><b>City:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['correspondence']['city']))?$relatedPersonDetail['correspondence']['city']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>State:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['correspondence']['state']))?$relatedPersonDetail['correspondence']['state']:'N/A'}}</td>
                                                    <td><b>Country:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['correspondence']['country']))?$relatedPersonDetail['correspondence']['country']:'N/A'}}</td>
                                                    <td><b>Pincode:</b></td>
                                                    <td>{{(!is_null($relatedPersonDetail['correspondence']['pincode']))?$relatedPersonDetail['correspondence']['pincode']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                   <td><b>Stdcode:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['stdCode']))?$relatedPersonDetail['stdCode']:'N/A'}}</td>
                                                   <td><b>Telephone Number:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['telephoneNumber']))?$relatedPersonDetail['telephoneNumber']:'N/A'}}</td>
                                                   <td><b>Office Std Code:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['officeStdCode']))?$relatedPersonDetail['officeStdCode']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                   <td><b>Office Telephone Number:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['officeTelephoneNumber']))?$relatedPersonDetail['officeTelephoneNumber']:'N/A'}}</td>
                                                   <td><b>Mobile Code:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['mobileCode']))?$relatedPersonDetail['mobileCode']:'N/A'}}</td>
                                                   <td><b>Mobile Number:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['mobileNumber']))?$relatedPersonDetail['mobileNumber']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                   <td><b>Email:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['email']))?$relatedPersonDetail['email']:'N/A'}}</td>
                                                   <td><b>Remarks:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['remarks']))?$relatedPersonDetail['remarks']:'N/A'}}</td>
                                                   <td><b>Declaration Place:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['declarationPlace']))?$relatedPersonDetail['declarationPlace']:'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                   <td><b>Type Of Doc Submitted:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['typeOfDocSubmitted']))?$relatedPersonDetail['typeOfDocSubmitted']:'N/A'}}</td>
                                                   <td><b>Organization Name:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['organisationName']))?$relatedPersonDetail['organisationName']:'N/A'}}</td>
                                                   <td><b>Organization Code:</b></td>
                                                   <td>{{(!is_null($relatedPersonDetail['organisationCode']))?$relatedPersonDetail['organisationCode']:'N/A'}}</td>
                                                </tr>
                                                @php $i++; @endphp
                                              @endforeach
                                            @endif
                                         @endif 
                                     @else

                                    <tr>
                                        <td style="border:none">CKYC Data not Available ! </td> 
                                    </tr>

                                 @endif
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                           
                            
              
               <!-- </div> -->
              @else
              <div class="row ">
                <div class="col-sm-12">
                    <span><b>Company Data Not Found !</b></span>
                </div>
                </div>
               @endif
            </div>
              <div class="data mt-4">
                <h2 class="sub-title bg "    style="margin-bottom: 0px;">Director / Proprietor / Owner / Partner</h2>
                <div id="pullMsg"></div>
                @if(!empty($arrCompanyOwnersData))
                @php
                            $j = 0;
                            @endphp
                            @foreach($arrCompanyOwnersData as $arr)
                            @php
                            
                                $j++;
                            @endphp
                <div class="row ">
                     <div class="col-sm-12">
                        <table id="cibil-table" class="table table-striped  no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="cibil-table_info" style="width: 100%;">
                           <thead>
                            <tr role="row">
                                <th>S No.</th>
                                <th >Name</th>
                                <th >AppId</th>
                                <th>PAN No.</th>
                                <th>Email Id</th>
                                <th>CKYC Applicable</th>
                                <th>Action</th>
                                <th></th>
                            </tr>
                           </thead>
                        
                           <tbody>
                              <tr role="row" class="odd">
                                 <td>{{$j}}</td>
                                 <td>{{$arr->first_name." ".$arr->last_name}}</td>
                                 <td>CAPAI{{$arr->app_id}}</td>
                                 <td>{{$arr->pan_number??'N/A'}}</td>
                                 <td id="cibilScore{{$arr->biz_owner_id}}">{{$arr->email??'N/A'}}</td>
                                 <td>
                                     <select class="form-control" name="is_applicable" tabindex="9" id="individual_ckyc_applicable_{{$arr->biz_owner_id}}" onchange='apply_ckyc("{{$arr["user_id"]}}","{{$arr->biz_id}}","{{$ucic->user_ucic_id}}","{{$arr["biz_owner_id"]}}",this)' @if($arr->ckyc_consent_id !== null) disabled @endif>
                                       <option value="">Select Ckyc Applicable</option>
                                       <option value="1" @if($arr->ckyc_applicable == '1') selected @endif>Yes</option>
                                       <option value="0" @if($arr->ckyc_applicable == '0') selected @endif>No</option>
                                     </select>
                                </td>    
                                <td> 
                                @php 
                                    $searchApiLog = App\Inv\Repositories\Models\UserCkycApiLog::getindividualckycsearchLog($arr->biz_owner_id,1);
                                    $downloadApiLog = App\Inv\Repositories\Models\UserCkycApiLog::getindividualckycsearchLog($arr->biz_owner_id,2);
                                @endphp
                                    <!-- <button class="btn btn-success btn-sm" supplier="49" style="margin-right: 23px;"> Pull</button> -->
                                    @if($arr->ckyc_consent_id !== null)
                                          @if($arr->status == '1')
                                          @can('ckyc_pull_request')
                                          @if(is_null($searchApiLog))
                                          <a class="btn btn-success btn-sm"  supplier="49" id="cibilScoreBtn{{$arr->biz_id}}" style="margin-right: -110px;" href="{{ route('ckyc_pull_request', ['user_id' => $userData->user_id, 'biz_id'=> $arr->biz_id, 'biz_owner_id'=>$arr['biz_owner_id'],'userUcicId'=>$ucic->user_ucic_id]) }}" onclick="showloader(this)">Pull</a> 
                                          @endif
                                          @if(!is_null($searchApiLog))
                                           <a id="pull_button" class="btn btn-success btn-sm"  supplier="49" id="cibilScoreBtn{{$arr->biz_id}}" href="{{ route('ckyc_pull_request', ['user_id' => $arr->user_id, 'biz_id'=> $arr->biz_id,'userUcicId'=>$ucic->user_ucic_id, 'biz_owner_id'=>$arr['biz_owner_id']]) }}" onclick="showloader(this)">Re-Pull</a> 
                                           @endif
                                          @endcan  
                                          @elseif($arr->status == '0')
                                          
                                          <div class="btn-group"><label style="background-color:#0cb70c; color:white;margin-top: 7px;" class="badge badge-warning">OTP consent pending&nbsp; &nbsp;</label></div>
                                            @can('ckyc_otp_consent')
                                            <a class="btn btn-success btn-sm " supplier="49"  onclick="return confirm('Are you sure you want to re-send OTP consent?')" href="{{ route('ckyc_otp_consent', ['user_id' => $userData->user_id, 'biz_id'=> $arr->biz_id,'biz_owner_id'=>$arr['biz_owner_id'],'userUcicId'=>$ucic->user_ucic_id]) }}" > Re-send OTP Consent</a>
                                            @endcan
                                            @can('ckyc_manual_consent')
                                            <a data-toggle="modal" class="btn btn-success btn-sm" supplier="49" style="    margin-right: -140px;"  data-target="#modalManualConsent" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('ckyc_manual_consent',['user_id' => $userData->user_id, 'biz_id'=> $arr->biz_id,'ckyc_consent_id'=>$arr->ckyc_consent_id,'biz_owner_id'=>$arr['biz_owner_id'],'userUcicId'=>$ucic->user_ucic_id])}}">Manual Consent</a>
                                            @endcan
                                            
                                          @endif
                                        @else  
                                        @can('ckyc_otp_consent')
                                        <a class="btn btn-success btn-sm consent_btn" supplier="49"  onclick="return confirm('Are you sure you want to send OTP consent?')" href="{{ route('ckyc_otp_consent', ['user_id' => $userData->user_id, 'biz_id'=> $arr->biz_id,'biz_owner_id'=>$arr['biz_owner_id'],'userUcicId'=>$ucic->user_ucic_id]) }}" id="{{$arr['biz_owner_id']}}_otp_consent" @if($arr->ckyc_applicable == '0' || is_null($arr->ckyc_applicable)) style="pointer-events: none;background-color: cadetblue;" @endif> OTP Consent</a>
                                        @endcan
                                        @can('ckyc_manual_consent')
                                        <a data-toggle="modal" class="btn btn-success btn-sm" supplier="49" @if($arr->ckyc_applicable == '0' || is_null($arr->ckyc_applicable)) style="    margin-right: -74px;pointer-events: none;background-color: cadetblue;" @else style="    margin-right: -74px;" @endif   data-target="#modalManualConsent" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('ckyc_manual_consent',['user_id' => $userData->user_id, 'biz_id'=> $arr->biz_id,'biz_owner_id'=>$arr['biz_owner_id'],'userUcicId'=>$ucic->user_ucic_id])}}" id="{{$arr['biz_owner_id']}}_manual_consent">Manual Consent</a>
                                        @endcan
                                        
                                        @endif
                              </td>
                              <td>
                              <a  supplier="49" data-placement="top"  data-toggle="collapse" href="#collapse{{$j}}"><i class="fa fa-plus" ></i></a>
                              </td>
                            </tr> 
                           </tbody>
                        </table>
                   </div>
                 </div>
                 <div class="row ">
                <div id="accordion{{$j}}" class="accordion d-table col-sm-12">
                    <div class="card card-color mb-0">
                        <div id="collapse{{$j}}" class="card-body collapse p-0 " data-parent="#accordion{{$j}}">

                            <table class="table  overview-table" id="documentTable" cellpadding="0" cellspacing="0" border="1">
                                <tbody>
                                @if($arr->status !== null)
                                    @if($arr->status == '1')
                                      @if($arr->consent_type == '1')
                                            <tr>
                                                <td width="20%"><b>CKYC Consent Type:</b></td>
                                                <td width="20%">Manual Consent Received</td>
                                                <td width="20%"><b>Consent File:</b></td>
                                                <td width="20%">
                                                <a  href="{{ route('download_storage_file', ['file_id' =>$arr->file_id ]) }}" class="btn-upload   btn-sm" type="button" id="pandownconsent" > <i class="fa fa-download"></i></a>

                                                <a  href="{{route('view_uploaded_doc', ['file_id' =>$arr->file_id ]) }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandowneyeconsent" target="_blank"> <i class="fa fa-eye"></i></a>
                                                </td>
                                                <td width="20%"><b>Comment:</b></td>
                                                <td width="20%">{{$arr->comment}}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td width="20%"><b>CKYC Consent Type:</b></td>
                                                <td width="20%">OTP Consent Received</td>
                                                <td width="20%"></td>
                                                <td width="20%"></td>
                                                <td width="20%"></td>
                                                <td width="20%"></td>
                                            </tr>
                                        @endif
                                    @endif
                                @endif
                                
                                @if(!is_null($searchApiLog) && !is_null($searchApiLog->getCKYCdownloadData))
                                @php 
                                if(empty($searchApiLog->res_data) && !empty($searchApiLog->res_file_id)){
                                        $indvsearchRes = App\Http\Controllers\Backend\CkycController::getCKYCResponse($searchApiLog->res_file_id);
                                        $indvsearchRes = json_decode($indvsearchRes,true);
                                        
                                    }else{
                                        $indvsearchRes = json_decode($searchApiLog->res_data,true);
                                    }
                                @endphp
                                    <tr>
                                        <td width="20%"><b>CKYC ID:</b></td>
                                        <td width="20%">{{$searchApiLog->getCKYCdownloadData->ckyc_no}}</td>
                                        <td width="20%"><b>Full Name:</b></td>
                                        <td width="20%">@if(isset($indvsearchRes['result']['individual']) && !empty($indvsearchRes['result']['individual'])){{$indvsearchRes['result']['individual'][0]['name']}} @elseif(isset($indvsearchRes['result']['nonIndividual']) && !empty($indvsearchRes['result']['nonIndividual'])){{$indvsearchRes['result']['nonIndividual'][0]['name']}}@else {{$indvsearchRes['result']['name']}} @endif</td>
                                        <td width="20%"><b>Constitution Type:</b></td>
                                        <td width="20%">@if(isset($indvsearchRes['result']['individual']) && !empty($indvsearchRes['result']['individual'])){{$indvsearchRes['result']['individual'][0]['type']}} @elseif(isset($indvsearchRes['result']['nonIndividual']) && !empty($indvsearchRes['result']['nonIndividual'])){{$indvsearchRes['result']['nonIndividual'][0]['type']}}@else {{$indvsearchRes['result']['type']}} @endif</td>

                                    </tr> 
                                    <tr>
                                       <td width="20%"><b>Father's Name:</b></td>
                                       @if(isset($indvsearchRes['result']['individual']) && !empty($indvsearchRes['result']['individual']))
                                       <td width="20%">{{(isset($indvsearchRes['result']['individual'][0]['fatherName']) && !is_null($indvsearchRes['result']['individual'][0]['fatherName']))?$indvsearchRes['result']['individual'][0]['fatherName']:'N/A'}}</td>
                                       @elseif(isset($indvsearchRes['result']['nonIndividual']) && !empty($indvsearchRes['result']['nonIndividual']))
                                       <td width="20%">{{(isset($indvsearchRes['result']['nonIndividual'][0]['fatherName']) && !is_null($indvsearchRes['result']['nonIndividual'][0]['fatherName']))?$indvsearchRes['result']['nonIndividual'][0]['fatherName']:'N/A'}}</td>
                                       @else
                                       <td width="20%">{{(isset($indvsearchRes['result']['fatherName']) && !is_null($indvsearchRes['result']['fatherName']))?$indvsearchRes['result']['fatherName']:'N/A'}}</td>
                                       @endif
                                       <td width="20%"><b>Age:</b></td>
                                       @if(isset($indvsearchRes['result']['individual']) && !empty($indvsearchRes['result']['individual']))

                                       <td width="20%">{{(isset($indvsearchRes['result']['individual'][0]['age']) && !is_null($indvsearchRes['result']['individual'][0]['age']))?$indvsearchRes['result']['individual'][0]['age']:'N/A'}}</td>

                                       @elseif(isset($indvsearchRes['result']['nonIndividual']) && !empty($indvsearchRes['result']['nonIndividual']))

                                       <td width="20%">{{(isset($indvsearchRes['result']['nonIndividual'][0]['age']) && !is_null($indvsearchRes['result']['nonIndividual'][0]['age']))?$indvsearchRes['result']['nonIndividual'][0]['age']:'N/A'}}</td>

                                       @else
                                       <td width="20%">{{(isset($indvsearchRes['result']['age']) && !is_null($indvsearchRes['result']['age']))?$indvsearchRes['result']['age']:'N/A'}}</td>
                                       @endif
                                       
                                       <td width="20%"><b>KYC Date:</b></td>
                                       @if(isset($indvsearchRes['result']['individual']) && !empty($indvsearchRes['result']['individual']))
                                       <td width="20%">{{(isset($indvsearchRes['result']['individual'][0]['kycDate']) && !is_null($indvsearchRes['result']['individual'][0]['kycDate']))?$indvsearchRes['result']['individual'][0]['kycDate']:'N/A'}}</td>

                                       @elseif(isset($indvsearchRes['result']['nonIndividual']) && !empty($indvsearchRes['result']['nonIndividual']))

                                       <td width="20%">{{(isset($indvsearchRes['result']['nonIndividual'][0]['kycDate']) && !is_null($indvsearchRes['result']['nonIndividual'][0]['kycDate']))?$indvsearchRes['result']['nonIndividual'][0]['kycDate']:'N/A'}}</td>

                                       
                                       @else
                                       <td width="20%">{{(isset($indvsearchRes['result']['kycDate']) && !is_null($indvsearchRes['result']['kycDate']))?$indvsearchRes['result']['kycDate']:'N/A'}}</td>
                                       @endif
                                       
                                    </tr> 
                                    
                                        @if(!is_null($downloadApiLog))
                                            @php
                                                 if(empty($downloadApiLog->res_data) && !empty($downloadApiLog->res_file_id)){
                                                    $indvdownloadRes = App\Http\Controllers\Backend\CkycController::getCKYCResponse($downloadApiLog->res_file_id);
                                                    $indvdownloadRes = json_decode($indvdownloadRes,true);
                                                    
                                                }else{
                                                    $indvdownloadRes = json_decode($downloadApiLog->res_data,true);
                                                }
                                               
                                            @endphp 
                                            @if(isset($indvdownloadRes['result']['personalDetails']))   
                                            <tr>
                                           <td width="20%"><b>PAN No.</b></td>
                                           <td width="20%">{{$indvdownloadRes['result']['personalDetails']['pan']}}</td>
                                           <td width="20%"><b>DOB:</b></td>
                                           <td width="20%">{{$indvdownloadRes['result']['personalDetails']['dob']}}</td>
                                           <td width="20%"><b>Email ID:</b></td>
                                           <td width="20%">{{$indvdownloadRes['result']['personalDetails']['email']}}</td>
                                           </tr>
                                           <tr>
                                               <td width="20%"><b>Gender:</b></td>
                                               <td width="20%">{{$indvdownloadRes['result']['personalDetails']['gender']}}</td>
                                               <td width="20%"><b>Account Holder Type:</b></td>
                                               <td width="20%">{{$indvdownloadRes['result']['personalDetails']['accountHolderType']}}</td>
                                               <td width="20%"><b>Mobile Code:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['mobileCode']??'N/A'}}</td>
                                           </tr>
                                            <tr>
                                                
                                               <td width="20%"><b>Mobile No:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['mobileNumber']}}</td>
                                                <td width="20%"><b>Mother's Name:</b></td>
                                                <td width="20%">{{(isset($indvdownloadRes['result']['personalDetails']['motherInfo']) && !empty($indvdownloadRes['result']['personalDetails']['motherInfo']))?$indvdownloadRes['result']['personalDetails']['motherInfo']['motherFullName']:'N/A'}}</td>
                                                <td width="20%"><b>Permanent Address:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['permanentAddress']['line1']}} </br> {{$indvdownloadRes['result']['personalDetails']['permanentAddress']['line2']}} </br> {{$indvdownloadRes['result']['personalDetails']['permanentAddress']['line3']}}</td>
                                            </tr> 
                                            <tr>
                                               
                                                <td width="20%"><b>City:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['permanentAddress']['city']}}</td>
                                                <td width="20%"><b>District:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['permanentAddress']['district']}}</td>
                                                <td width="20%"><b>State:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['permanentAddress']['state']}}</td>
                                            </tr> 
                                            <tr>
                                                
                                                <td width="20%"><b>Pincode:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['permanentAddress']['pincode']}}</td>
                                                <td width="20%"><b>Country:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['permanentAddress']['country']}}</td>
                                                <td width="20%"><b>Permanent Corresponding Address same:</b></td>
                                               <td width="20%">{{$indvdownloadRes['result']['personalDetails']['permCorresSame']?' Yes':' No'}}</td>
                                            </tr>
                                            
                                            <tr>
                                                
                                               <td width="20%"><b>Correspondence Address:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['correspondence']['line1']}}</br> {{$indvdownloadRes['result']['personalDetails']['correspondence']['line2']}} </br> {{$indvdownloadRes['result']['personalDetails']['correspondence']['line3']}}</td>
                                                <td width="20%"><b>City:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['correspondence']['city']}}</td>
                                                <td width="20%"><b>District:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['correspondence']['district']}}</td>
                                            </tr>
                                            <tr>
                                                
                                            <td width="20%"><b>State:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['correspondence']['state']}}</td>
                                                <td width="20%"><b>Country:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['correspondence']['country']}}</td>
                                                <td width="20%"><b>Pincode:</b></td>
                                                <td width="20%">{{$indvdownloadRes['result']['personalDetails']['correspondence']['pincode']}}</td>
                                            </tr>
                                            <tr>
                                            
                                            <td width="20%"><b>Declaration Date:</b></td>
                                            <td width="20%">{{$indvdownloadRes['result']['personalDetails']['declarationDate']}}</td>
                                            <td width="20%"><b>Declaration Place:</b></td>
                                            <td width="20%">{{$indvdownloadRes['result']['personalDetails']['declarationPlace']}}</td>
                                            <td width="20%"><b>Organization Name:</b></td>
                                            <td width="20%">{{$indvdownloadRes['result']['personalDetails']['organisationName']}}</td>
                                            </tr> 
                                            <tr>
                                            <td width="20%"><b>Organization Code:</b></td>
                                            <td width="20%">{{$indvdownloadRes['result']['personalDetails']['organisationCode']}}</td>
                                              <td width="20%"><b>STD Code:</b></td>
                                              <td width="20%">{{($indvdownloadRes['result']['personalDetails']['stdCode'] != null) ? $indvdownloadRes['result']['personalDetails']['stdCode'] : 'N/A'}}</td>
                                              <td width="20%"><b>Telephone Number:</b></td>
                                            <td width="20%">{{($indvdownloadRes['result']['personalDetails']['telephoneNumber'] != null) ? $indvdownloadRes['result']['personalDetails']['telephoneNumber'] : 'N/A'}}</td>
                                            </tr>
                                            <tr>
                                            
                                              <td width="20%"><b>Office STD Code:</b></td>
                                              <td width="20%">{{($indvdownloadRes['result']['personalDetails']['officeStdCode'] != null) ? $indvdownloadRes['result']['personalDetails']['officeStdCode'] : 'N/A'}}</td>
                                              <td width="20%"><b>Office Telephone Number:</b></td>
                                              <td width="20%">{{($indvdownloadRes['result']['personalDetails']['officeTelephoneNumber'] != null) ? $indvdownloadRes['result']['personalDetails']['officeTelephoneNumber'] : 'N/A'}}</td>
                                              <td width="20%"><b>Type of Doc submitted:</b></td>
                                              <td width="20%">{{($indvdownloadRes['result']['personalDetails']['typeOfDocSubmitted'] != null) ? $indvdownloadRes['result']['personalDetails']['typeOfDocSubmitted'] : 'N/A'}}</td>
                                            </tr> 
                                            @endif
                                            <tr>
                                            <td width="20%"><b>KYC Updated On:</b></td>
                                            @if(isset($indvsearchRes['result']['individual']) && !empty($indvsearchRes['result']['individual']))

                                            <td width="20%">{{(isset($indvsearchRes['result']['individual'][0]['updatedOn']) && !is_null($indvsearchRes['result']['individual'][0]['updatedOn']))?$indvsearchRes['result']['individual'][0]['updatedOn']:'N/A'}}</td>

                                            @elseif(isset($indvsearchRes['result']['nonIndividual']) && !empty($indvsearchRes['result']['nonIndividual']))

                                             <td width="20%">{{(isset($indvsearchRes['result']['nonIndividual'][0]['kycDate']) && !is_null($indvsearchRes['result']['nonIndividual'][0]['kycDate']))?$indvsearchRes['result']['nonIndividual'][0]['kycDate']:'N/A'}}</td>

                                            @else
                                            <td width="20%">{{(isset($indvsearchRes['result']['updatedOn']) && !is_null($indvsearchRes['result']['updatedOn']))?$indvsearchRes['result']['updatedOn']:'N/A'}}</td>
                                            @endif
                                            <td width="20%"></td>
                                            <td width="20%"></td>
                                            <td width="20%"></td>
                                            <td width="20%"></td>
                                            </tr>
                                            @if(!is_null($downloadApiLog->collectAllCkycDoc))
                                                @php $i=1; @endphp
                                                @foreach($downloadApiLog->collectAllCkycDoc as $allCkycDoc)
                                                 <tr>
                                                 <td width="20%"><b>{{ucfirst($allCkycDoc->file_type)}}:</b></td>
                                                 <td width="20%">
                                                 <a  href="{{ route('download_s3_file', ['file_id' =>$allCkycDoc->file_id ]) }}" class="btn-upload   btn-sm" type="button" id="pandown{{$i}}" > <i class="fa fa-download"></i></a>

                                                 <a  href="{{ Storage::url($allCkycDoc->file->file_path) }}" title="View File" class="btn-upload   btn-sm" target="_blank" type="button" id="pandowneye{{$i}}" target="_blank"> <i class="fa fa-eye"></i></a>
                                                 </td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                </tr>
                                                @php $i++; @endphp
                                                @endforeach
                                            @endif

                                         @endif 
                                @else

                                    <tr>
                                        <td style="border:none">CKYC Data not Available ! </td> 
                                    </tr>

                                 @endif
                                    
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
                 @endforeach 
                
              </div>
            </div>
            @else
              <div class="row ">
                <div class="col-sm-12">
                    <span><b>Owner Data Not Found !</b></span>
                </div>
                </div>
            @endif
		</div>
	</div>
</div>
</div>
{!!Helpers::makeIframePopup('modalManualConsent','Manual CKYC Consent', 'modal-md')!!}
@endsection

@section('jscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
var messages = {
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    is_accept: "{{ Session::get('is_accept') }}",
    operation_status:"{{Session::get('operation_status')}}",
    message : "{{ trans('backend_messages.user_manual_consent') }}",
    error_msg : "{{ Session::get('error_msg') }}",
    ckyc_applicable_save:"{{ route('ckyc_applicable_save') }}",
};
</script>
<script>

function apply_ckyc(user_id,biz_id,ucic_id,biz_owner_id,element){
    var is_applicable = $(element).val();
    if(is_applicable == '1' || is_applicable == '0') {
    $(".isloader").show();
  
   var element_id = $(element).attr('id');
   var data = {user_id : user_id,biz_id:biz_id,biz_owner_id:biz_owner_id,ucic_id:ucic_id,is_applicable:is_applicable, _token: messages.token};
    $.ajax({
        url : messages.ckyc_applicable_save,
        type: "POST",
        data: data,
         success: function(r){
             var resobj = JSON.parse(r);
             if(resobj.status == 200){
                var entity_type = element_id.split("_");
                if (entity_type[0] == 'business'){
                    if (is_applicable == '1'){
                        resobj.message = 'CKYC applied successfully for the Entity.';
                        $('#business_manual_consent_'+biz_id).css({ 'background-color' : '', 'pointer-events' : '' });
                        $('#business_otp_consent_'+biz_id).css({ 'background-color' : '', 'pointer-events' : '' });
                    }else if (is_applicable == '0'){
                        resobj.message = 'CKYC is NOT applicable.';
                        $('#business_manual_consent_'+biz_id).css({ 'background-color' : 'cadetblue', 'pointer-events' : 'none' });
                        $('#business_otp_consent_'+biz_id).css({ 'background-color' : 'cadetblue', 'pointer-events' : 'none' });
                    }
                }else if(entity_type[0] == 'individual'){
                    if(is_applicable == '1'){
                        resobj.message = 'CKYC applied successfully for the Management.';
                       $('#'+biz_owner_id+'_otp_consent').css({ 'background-color' : '', 'pointer-events' : '' });
                       $('#'+biz_owner_id+'_manual_consent').css({ 'background-color' : '', 'pointer-events' : '' });
                    }else if (is_applicable == '0'){
                        resobj.message = 'CKYC is NOT applicable.';
                        $('#'+biz_owner_id+'_otp_consent').css({ 'background-color' : 'cadetblue', 'pointer-events' : 'none' });
                       $('#'+biz_owner_id+'_manual_consent').css({ 'background-color' : 'cadetblue', 'pointer-events' : 'none' });
                    }
                }
                $(".isloader").hide();
                $("#iframeMessage").html('<div class="alert alert-success" role="alert">'+resobj.message+'</div>');
               
             }else{
                console.log('failure');
             }
            //$(".isloader").hide();
            //location.reload();
        }
      
    });
  } 
}

function showloader(element){
    console.log('hii');
    $(".isloader").show();
}

if(messages.operation_status == 1){
    $(".isloader").hide();
}

$(document).ready(function() {
 // $('[data-toggle="collapse"]').find('i').removeClass('fa-plus').addClass('fa-minus');
  $('[data-toggle="collapse"]').click(function(e) {
    e.preventDefault();
    var icon = $(this).find('i');
    var target = $($(this).attr('href'));
    console.log(target);
    target.slideToggle('fast', function() {
      if (target.is(':visible')) {
        icon.removeClass('fa-plus').addClass('fa-minus');
      } else {
        icon.removeClass('fa-minus').addClass('fa-plus');
      }
    });
  });
});
</script>
@endsection