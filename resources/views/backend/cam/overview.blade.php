@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">

        <div class="card mt-3">
            <div class="card-body pt-3 pb-3">
                <ul class="float-left mb-0 pl-0">
                    <li><b class="bold">Application ID: {{isset($arrRequest['app_id'])? \Helpers::formatIdWithPrefix($arrRequest['app_id'], 'APP') :''}}</b> </li>
                   <!--  <li><b class="bold">Credit Head Status :</b> Reject</li> -->

                </ul>
                <!-- <a  data-toggle="modal" data-target="#changeAppDisbursStatus" data-url ="{{ route('app_status_disbursed', ['app_id' => $arrRequest['app_id'],'biz_id' => $arrRequest['biz_id']]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success pull-right  btn-sm" title="Update App Status">Disbursed</a> -->
                    @php 
                    $role_id=Helpers::getUserRole(Auth::user()->user_id);
                    @endphp
                    @if ($arrRequest['app_id'] && $role_id[0]->pivot->role_id== config('common.user_role')['OPPS_CHECKER'] && $current_status_id!=config('common.mst_status_id')['DISBURSED'] && $checkDisburseBtn=='showDisburseBtn')
                    <a  data-toggle="modal" data-target="#changeAppDisbursStatus" data-url ="{{ route('app_status_disbursed', ['app_id' => $arrRequest['app_id'],'biz_id' => $arrRequest['biz_id']]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success pull-right  btn-sm" title="Update App Status">Disbursed</a>
                    @else 
                    @if($current_status_id && $current_status_id==config('common.mst_status_id')['DISBURSED'])
                    <span class="pull-right"><span class="badge badge-success current-status"><i class="fa fa-check-circle" aria-hidden="true"></i>   Disbursed</span></span>
                    @endif
                    @endif
             
               <!-- <ul class="float-right mr-5 mb-0">

                     <li><b class="bold">Requested Loan Amount :</b> 5Lac</li>
                    <li><b class="bold">Assigned Underwriter :</b> abc</li>
 
                </ul>-->

            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body ">
             <form method="POST" id="camForm" action="{{route('cam_information_save')}}" enctype="multipart/form-data"> 
             @csrf

                <input type="hidden" name="app_id" value="{{isset($arrRequest['app_id']) ? $arrRequest['app_id'] : ''}}" />             
                <input type="hidden" name="biz_id" value="{{isset($arrRequest['biz_id']) ? $arrRequest['biz_id'] : ''}}" />             
                <input type="hidden" name="cam_report_id" value="{{isset($arrCamData->cam_report_id) ? $arrCamData->cam_report_id : ''}}" />    

                <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0" border="1">
                    <tbody>
                        <tr>
                            <td width="25%"><b>Name of Borrower</b></td>
                            <td width="25%">{{$arrBizData->biz_entity_name}}</td>
                            <td><b>Key Management Person</b></td>
                            <td> 
                                <select class="form-control" name="contact_person">
                                <option  value="">Select</option>
                                 @foreach($arrOwner as $key => $val)
                                    <option @if((isset($arrCamData->contact_person)) && $arrCamData->contact_person == $val) selected @endif value="{{$val}}"> {{$val}}</option>
                                 @endforeach   
                                </select>
                           </td>
                        </tr>

                        <tr>
                            <td width="25%"><b>PAN Number of Borrower</b></td>
                            <td width="25%">{{$arrBizData->pan->pan_gst_hash}}</td>
                            <td><b>Type of Industry</b></td>
                            <td>{{$arrBizData->industryType }}</td>
                        </tr>
                        <tr>
                            <td><b>Phone Number</b></td>
                            <td>{{$arrBizData->mobile_no}}</td>
                            <td><b>Email</b></td>
                            <td>{{$arrBizData->email}}</td>
                        </tr>
                        <tr>
                            <td><b>GST Address</b></td>
                           
                            <td>{{$arrBizData->address[0]->addr_1.' '.(isset($arrBizData->address[0]->city_name) ? $arrBizData->address[0]->city_name : '').' '. (isset($arrBizData->address[0]->state->name) ? $arrBizData->address[0]->state->name : '').' '. (isset($arrBizData->address[0]->pin_code) ? $arrBizData->address[0]->pin_code : '')}}
                            </td>

                            <td><b>Communication Address </b></td>

                            <td>{{$arrBizData->address[1]->addr_1.' '.(isset($arrBizData->address[1]->city_name) ? $arrBizData->address[1]->city_name : '').' '. (isset($arrBizData->address[1]->state->name) ? $arrBizData->address[1]->state->name : '').' '. (isset($arrBizData->address[1]->pin_code) ? $arrBizData->address[1]->pin_code : '')}}
                            </td>


                        </tr>
                        <tr>
                            <td><b>Factory Address</b></td>
                             <td>{{$arrBizData->address[4]->addr_1.' '.(isset($arrBizData->address[4]->city_name) ? $arrBizData->address[4]->city_name : '').' '. (isset($arrBizData->address[4]->state->name) ? $arrBizData->address[4]->state->name : '').' '. (isset($arrBizData->address[4]->pin_code) ? $arrBizData->address[4]->pin_code : '')}}
                            </td>

                            <td width="25%"><b>Legal Constitution </b></td>
                            <td width="25%">{{$arrBizData->legalConstitution}}</td>
                        </tr>
                        <tr>
                            <td width="25%"><b>Industry / Activity / Products</b></td>
                            <td width="25%"></td>
                            <td><b>Operational Person</b></td>
                            <td><input type="text" name="operational_person" id="operational_person" class="form-control" value="{{isset($arrCamData->operational_person) ? $arrCamData->operational_person : '' }}"></td>
                        </tr>
                        <tr>
                            <td><b>Program</b></td>
                            <td>{{$arrBizData->prgm_name}}</td>
                            <td><b>External Rating ( If any )</b></td>
                            <td style="text-align: center;">
                                <textarea class="form-control" id="external_rating_comments" rows="2" name="rating_comment"> {{isset($arrCamData->rating_comment) ? $arrCamData->rating_comment : ''}}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="data mt-4">
                    <h2 class="sub-title bg">Group Company Exposure
                      <span class="pull-right" style="font-size: 11px;">
                                        @if(isset($arrCamData->By_updated))  
                                            Updated By: {{$arrCamData->By_updated}} ({!! isset($arrGroupCompany['0']['updated_at']) ?  \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$arrGroupCompany['0']['updated_at'])->format('j F, Y') : '' !!})
                                        @endif
                                    </span>   </h2>                    
                    <div class="col-md-12 mt-4 ">
                         <div class="row">
                            <div class="col-md-2">
                                <label for="txtPassword"><b>Group Name</b></label> <span style="color: red; font-size: 20px"> * </span>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="group_company" class="form-control group-company" value="{{isset($arrCamData->group_company) ? $arrCamData->group_company : ''}}" placeholder="Group Name" @if(\Helpers::checkPermission('get_group_company') && request()->get('view_only')) @else readonly="readonly" @endif autocomplete="off" style="padding: -;position:absolute; right: 17px;" >
                            </div>
                            <label class="error" for="group_company"></label>
                            <span  class="group_nameId" style="color:red;"></span>
                            
                        </div>
                     </div>   



                    <div class="col-md-12 mt-4" id="ptpq-block">
                        @if(!empty($arrGroupCompany))
                            @foreach($arrGroupCompany as $key=>$arr)
                            <div class="row  toRemoveDiv {{($loop->first)? '': 'mt10'}}">
                                <input type="hidden" name="group_company_expo_id[]" class="form-control group_company_expo_id" value="{{$arr['group_company_expo_id'] ?? ''}}" placeholder="Group Company" />
                                <div class="col-md-4">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Borrower</b></label>
                                    @endif
                                    <input type="text" name="group_company_name[]" class="form-control" value="{{$arr['group_company_name'] ?? ''}}" placeholder="Group Company"  autocomplete="off"/>
                                </div>
                                <div class="col-md-3 INR">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Sanction Limit (In Mn)</b></label>
                                    @endif
                                    <div class="relative">
                                        <a href="javascript:void(0);" class="verify-owner-no" style="top:{{($loop->first) ? '28px;': '1px;' }}"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <input type="text" name="sanction_limit[]" class="form-control calTotalExposure float_format" value="{{($arr['sanction_limit'] > 0) ? $arr['sanction_limit'] :'' }}" placeholder="Sanction Limit (In Mn)" autocomplete="off"/>
                                     </div>
                                </div>
                                <div class="col-md-3 INR">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Outstanding Exposure (In Mn)</b></label>
                                    @endif
                                    <div class="relative">
                                        <a href="javascript:void(0);" class="verify-owner-no" style="top:{{($loop->first) ? '28px;': '1px;' }}"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <input type="text" name="outstanding_exposure[]" class="form-control  calTotalExposure float_format" value="{{($arr['outstanding_exposure'] > 0) ? $arr['outstanding_exposure']: '' }}" placeholder="Outstanding Exposure (In Mn)" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="col-md-2 center INR">
                                     @if($loop->first)
                                        <label for="txtPassword"><b>Proposed Limit (In Mn)</b></label>
                                    @endif
                                         @if($arr['proposed_exposure'] > 0 || ($loop->first))
                                             <a href="javascript:void(0);" class="verify-owner-no" style="top:{{($loop->first) ? '28px;': '1px;' }}"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                         @endif    
                                     <div class="d-flex">
                                        
                                          <input type="{{(($arr['proposed_exposure'] > 0) || ($loop->first)) ? 'text' : 'hidden' }}" name="proposed_exposure[]" maxlength="20" class="form-control  calTotalExposure float_format proposed_exposureInput"  value="{{($arr['proposed_exposure'] > 0) ? $arr['proposed_exposure'] : ''}}" placeholder="Proposed Limit (In Mn)" autocomplete="off"/>
                                        
                                           @if($loop->first)
                                                <i class="fa fa-2x fa-plus-circle add-ptpq-block ml-2"  style="color: green;"></i>
                                           @else
                                            <i class="fa fa-2x fa-times-circle remove-ptpq-block ml-2" style="color: red;"></i>
                                           @endif
                                           
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                                
                            <div class="row">
                                <input type="hidden" name="group_company_expo_id[]" class="form-control" value="" placeholder="Group Company" />
                                <div class="col-md-4 mt-4">
                                     <label for="txtPassword"><b>Borrower</b></label>
                                    <input type="text" class="form-control" name="group_company_name[]" value="{{$arrBizData->biz_entity_name}}" />
                                    
                                </div>
                                <div class="col-md-3 mt-4 INR">
                                        <label for="txtPassword"><b>Sanction Limit (In Mn)</b></label>
                                        <div class="relative">
                                            <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a> 
                                            <input type="text" name="sanction_limit[]" class="form-control float_format" value="" placeholder="Sanction Limit (In Mn)" autocomplete="off"/>
                                        </div>
                                </div>        
                                <div class="col-md-3 mt-4 INR">
                                     <label for="txtPassword"><b>Outstanding Exposure (In Mn)</b></label>
                                     <div class="relative">
                                        <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <input type="text" name="outstanding_exposure[]" class="form-control  calTotalExposure float_format" value="" placeholder="Outstanding Exposure (In Mn)" autocomplete="off"/>
                                     </div>
                                </div>
                                <div class="col-md-2 mt-4 INR">
                                     <label for="txtPassword"><b>Proposed Limit (In Mn)</b></label>
                                     <div class="relative">
                                        <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <div class="d-flex">
                                             <input type="text" name="proposed_exposure[]" maxlength="20" class="form-control  calTotalExposure float_format proposed_exposureInput"  value="" placeholder="Proposed Limit (In Mn)" />
                                              <i class="fa fa-2x fa-plus-circle add-ptpq-block ml-2"  style="color: green;"></i>
                                        </div>
                                     </div>
                                </div>
                            </div>


                        @endif
                    </div>

                    <div class="col-md-12 mt-4 mb-2" style="background: #e1f0eb; padding: 5px;">
                        <div class="row">
                            <div class="col-md-3 mt-2">
                                <label for="txtPassword"><b>Total Exposure (In Mn)</b></label>
                            </div>
                            <div class="col-md-6 "></div>
                             <div class="col-md-3 INR">
                                <div class="relative">
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control " name="total_exposure" value="{{($arrCamData &&  $arrCamData->total_exposure_amount > 0) ? $arrCamData->total_exposure_amount : ''}}" readonly />
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>  

                <div class="data mt-4">
                    <h2 class="sub-title bg"  style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Rating Rationale</h2>
                    <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                        <textarea class="form-control" id="rating_rational" name="rating_rational" rows="3" spellcheck="false" >{{isset($arrCamData->rating_rational) ? $arrCamData->rating_rational : ''}}</textarea>
                    <!-- </div> -->
                </div>

                <div class="data mt-4">
                    <h2 class="sub-title bg" style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Terms Of Facility</h2>
                    <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                        <table class="table overview-table table-bordered" cellpadding="0" cellspacing="0" border="1">
                            <tbody>
                                <tr>
                                    <td width="30%"><b>Proposed Limit</b> </td>
                                    <td id="limits" name="limits"> {!! $arrBizData->app->loan_amt ? \Helpers::formatCurreny($arrBizData->app->loan_amt) : '' !!} </td>
                                    <td><b>Exiting Limits ( If any ) </b></td>
                                    <td><span class="fa fa-inr" aria-hidden="true" style="position:absolute; margin:12px 5px; "></span><input type="text" name="t_o_f_limit" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onfocusout="checkNumber(this)" id="existing_limits" class="form-control inr number_format" maxlength="20" value="{{isset($arrCamData->t_o_f_limit) ? $arrCamData->t_o_f_limit : ''}}"></td>
                                </tr>
                                <tr>
                                    {{--<td><b>Maximum Tenor of Invoices/tranch</b></td>
                                    <td value=""></td>--}}
                                    <td><b>Purpose</b></td>
                                    <td colspan="3">
                                        <input type="text" name="t_o_f_purpose" id="purpose" class="form-control" value="{{isset($arrCamData->t_o_f_purpose) ? $arrCamData->t_o_f_purpose : ''}}" maxlength="250">
                                    </td>
                                </tr>
                                <tr>
                                    {{--<td><b>Takeout</b></td>
                                    <td>
                                        <input type="text" name="t_o_f_takeout" id="takeout" class="form-control" value="{{isset($arrCamData->t_o_f_takeout) ? $arrCamData->t_o_f_takeout : ''}}" @if ($checkDisburseBtn=='showDisburseBtn') readonly="readonly" @endif>
                                    </td>--}}
                                    <td><b>Recourse</b></td>
                                    <td colspan="3">
                                        <input type="text" name="t_o_f_recourse" id="recourse" class="form-control" maxlength="250" value="{{isset($arrCamData->t_o_f_recourse) ? $arrCamData->t_o_f_recourse : ''}}" @if ($checkDisburseBtn=='showDisburseBtn') readonly="readonly" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Security</b></td>
                                    <td>
                                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                                         <label class="form-check-label">
                                         <input type="checkbox" class="form-check-input" name="t_o_f_security_check[]" value="BG" {{isset($arrCamData->t_o_f_security_check) && in_array('BG', $arrCamData->t_o_f_security_check) ? 'checked' : ''}} onchange="showSecurityComment('BG');">BG
                                         <i class="input-helper"></i></label>
                                       </div>
                                       <div class="form-check" style="display: inline-block;">
                                         <label class="form-check-label">
                                         <input type="checkbox" class="form-check-input" name="t_o_f_security_check[]"  value="FD" {{isset($arrCamData->t_o_f_security_check) && in_array('FD', $arrCamData->t_o_f_security_check) ? 'checked' : ''}} onchange="showSecurityComment('FD');">FD
                                         <i class="input-helper"></i></label>
                                      </div>
                                      <div class="form-check" style="display: inline-block;">
                                         <label class="form-check-label">
                                         <input type="checkbox" class="form-check-input" name="t_o_f_security_check[]"  value="MF" {{isset($arrCamData->t_o_f_security_check) && in_array('MF', $arrCamData->t_o_f_security_check) ? 'checked' : ''}} onchange="showSecurityComment('MF');">MF
                                         <i class="input-helper"></i></label>
                                      </div>
                                      <div class="form-check" style="display: inline-block;">
                                         <label class="form-check-label">
                                         <input type="checkbox" class="form-check-input" id="othersCheckbox" name="t_o_f_security_check[]"  value="Others" {{isset($arrCamData->t_o_f_security_check) && in_array('Others', $arrCamData->t_o_f_security_check) ? 'checked' : ''}} onchange="showSecurityComment('Others');">Others
                                         <i class="input-helper"></i></label>
                                      </div>


                                        <input type="text" name="t_o_f_security" id="securityComment" class="form-control" value="{{isset($arrCamData->t_o_f_security) ? $arrCamData->t_o_f_security : ''}}" style="display: {{isset($arrCamData->t_o_f_security_check) && in_array('Others', $arrCamData->t_o_f_security_check) ? '' : 'none'}} ">
                                    </td>
                                    <td><b>Adhoc Limit</b></td>
                                    <td><span class="fa fa-inr" aria-hidden="true" style="position:absolute; margin:12px 5px; "></span><input type="text" name="t_o_f_adhoc_limit" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" id="adhoc_limit" class="form-control inr number_format" onfocusout="checkNumber(this)" maxlength="20" value="{{isset($arrCamData->t_o_f_adhoc_limit) ? $arrCamData->t_o_f_adhoc_limit : ''}}"></td>
                                </tr>
                                <tr>
                                    <td><b>Status of Covenants stipulated during last approval</b></td>
                                    <td colspan="3">
                                        <input type="text" name="t_o_f_covenants" id="last_approval_status_stipulated" class="form-control" value="{{isset($arrCamData->t_o_f_covenants) ? $arrCamData->t_o_f_covenants : ''}}">
                                    </td>
                                </tr>
                                 
                            </tbody>
                        </table>
                    <!-- </div> -->
                </div>

                <div class="data mt-4">
                    <h2 class="sub-title bg" style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Brief Profile of the Company</h2>
                    <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                        <textarea class="form-control" id="profile_of_company" name="t_o_f_profile_comp" rows="3" spellcheck="false" >{{isset($arrCamData->t_o_f_profile_comp) ? $arrCamData->t_o_f_profile_comp : ''}}</textarea>
                    <!-- </div> -->
                </div>

                <!-- <div class="data mt-4">
                    <h2 class="sub-title bg" style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Risk Comments</h2>
                  
                        <textarea class="form-control" id="risk_comments" name="risk_comments" rows="3" spellcheck="false">{{isset($arrCamData->risk_comments) ? $arrCamData->risk_comments : ''}}</textarea>
                    
                </div>

                <div class="data mt-4">
                    <h2 class="sub-title bg" style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Recommendation and Comments of Credit Manager</h2>
                   
                        @php 
                        $role_id=Helpers::getUserRole(Auth::user()->user_id);
                        @endphp
                        
                        <textarea @if (in_array($role_id[0]->pivot->role_id ,[config('common.user_role')['SALES'],config('common.user_role')['CPA']])) disabled="true" @endif class="form-control" id="anchor_risk_comments" rows="3" spellcheck="false" name="cm_comment">{{ isset($arrCamData->cm_comment) ? $arrCamData->cm_comment : ''}}</textarea>

                        <div class="clearfix"></div>
                   

                </div> -->
                 <div class="data mt-4">
                    <h2 class="sub-title bg" style="border: 1px solid #d1d1d1;">Contigent Liabilities & Auditors Observations </h2>
                    <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                        <div class="form-group row">
                         <label for="debt_on" class="col-sm-2 col-form-label">Date As On</label>
                         <div class="col-sm-4">
                           <input type="text" class="form-control" value="{{isset($arrCamData->debt_on) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arrCamData->debt_on)->format('d/m/Y') : '' }}" name="debt_on" id="debt_on" placeholder="Select Date">
                         </div>
                       </div>
                       <div class="form-group row">
                       <div class="col-sm-12">
                        <textarea class="form-control" id="contigent_observations" rows="3" spellcheck="false" name="contigent_observations">{{isset($arrCamData->contigent_observations) ? $arrCamData->contigent_observations : ''}}</textarea>
                       </div>
                        </div>
                        <div class="clearfix"></div>
                    <!-- </div> -->

                </div>
                <div class="data mt-4">
                    <h2 class="sub-title bg">Security Document
                      </h2>                    
                    <div class="col-md-12 mt-4" id="security-doc-block">
                        @if(!empty($arrAppSecurityDoc))
                             @php
                                 $jsonDecode = json_decode($securityDocumentListJson,true);
                             @endphp
                            @foreach($arrAppSecurityDoc as $key=>$arr)
                            @php
                                  
                                    $key =  $key+1;
                                  
                              @endphp
                            <div class="row  toRemoveDiv1 {{($loop->first)? '': 'mt10'}}" style="background-color: #e9e7e7;">
                                <input type="hidden" name="app_security_doc_id[]" class="form-control app_security_doc_id" value="{{$arr['app_security_doc_id'] ?? ''}}" id="app_security_doc_id_{{ $key }}"/>
                                <div class="col-md-2 mt-4">
                                    <label for="txtPassword"><b>Pre/Post</b></label>
                                    <div class="relative">
                                        <select class="form-control doc_type" name="doc_type[]" id="update_doc_type_{{ $key }}">
                                            <option value="">Select</option>
                                            <option value="pre" {{(isset($arr['doc_type']) && $arr['doc_type'] == 'pre') ? 'selected': '' }}>Pre</option>
                                            <option value="post" {{(isset($arr['doc_type']) && $arr['doc_type'] == 'post') ? 'selected': '' }}>Post</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label for="txtPassword"><b>Type of Document</b></label>
                                    <select class="form-control security_doc_id" name="security_doc_id[]" id="update_security_doc_id_{{ $key }}">
                                       <option value="">Select</option>
                                       @foreach ($jsonDecode as $key1 => $sectDoc)
                                           <option value="{{ $sectDoc['security_doc_id'] }}" {{(isset($arr['security_doc_id']) && $sectDoc['security_doc_id'] == $arr['security_doc_id']) ? 'selected': '' }}>{{ $sectDoc['name'] }}</option>
                                       @endforeach
                                   </select>
                               </div>
                               <div class="col-md-2 mt-4">
                                       <label for="txtPassword"><b>Description</b></label>
                                       <div class="relative">
                                           <input type="text" name="description[]" class="form-control description" value="{{$arr['description'] ?? ''}}" placeholder="Description" autocomplete="off" id="update_description_{{ $key }}"/>
                                       </div>
                               </div>        
                               <div class="col-md-2 mt-4">
                                    <label for="txtPassword"><b>Document Number</b></label>
                                    <div class="relative">
                                       <input type="text" name="document_number[]" class="form-control document_number" value="{{$arr['document_number'] ?? ''}}" placeholder="Document Number" autocomplete="off" id="update_document_number_{{ $key }}"/>
                                    </div>
                               </div>
                               <div class="col-md-2 mt-4">
                                    <label for="txtPassword"><b>Due Date</b></label>
                                    <div class="relative">
                                            <input type="text" name="due_date[]" maxlength="20" class="form-control sc-doc-date due_date" value="{{\Carbon\Carbon::createFromFormat('Y-m-d', $arr['due_date'])->format('d/m/Y') ?? ''}}" placeholder="Due Date" autocomplete="off" id="update_due_date_{{ $key }}"/>
                                    </div>
                               </div>
                               <div class="col-md-1 mt-4">
                                   <label for="txtPassword"><b>Completed</b></label>
                                   <div class="relative">
                                       <select class="form-control completed" name="completed[]" id="update_completed_{{ $key }}">
                                           <option value="">Select</option>
                                           <option value="yes" {{(isset($arr['completed']) && $arr['completed'] == 'yes') ? 'selected': '' }}>Yes</option>
                                           <option value="no" {{(isset($arr['completed']) && $arr['completed'] == 'no') ? 'selected': '' }}>No</option>
                                       </select>
                                   </div>
                              </div>
                              <div class="col-md-1 mt-4">
                                       <label for="txtPassword"><b>Exception Received</b></label>
                                       <div class="relative">
                                           <select class="form-control exception_received" name="exception_received[]" onchange="displayExceptionFields(this.value,{{ $key }});" id="update_exception_received_{{ $key }}" data-previous="{{ $key }}">
                                               <option value="">Select</option>
                                               <option value="yes" {{(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'selected': '' }}>Yes</option>
                                               <option value="no" {{(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'selected': '' }}>No</option>
                                           </select>
                                       </div>
                               </div>
                               <div class="col-md-2 mt-4 exceptionFields_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="display: none;"': '' !!}>
                                       <label for="txtPassword"><b>Exception Received From</b></label>
                                       <div class="relative">
                                       <input type="text" name="exception_received_from[]" class="form-control exception_received_from {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'required': '' !!}" value="{{$arr['exception_received_from'] ?? ''}}" placeholder="Exception Received From" autocomplete="off" id="update_exception_received_from_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="visibility: none;height: 0;"': '' !!}/>
                                       </div>
                               </div>
                               <div class="col-md-2 mt-4 exceptionFields_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="display: none;"': '' !!}>
                                       <label for="txtPassword"><b>Exception Received Date</b></label>
                                       <div class="relative">
                                       <input type="text" name="exception_received_date[]" class="form-control sc-doc-date exception_received_date {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'required': '' !!}" value ="{{(isset($arr['exception_received_date']) && $arr['exception_received_date']) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arr['exception_received_date'])->format('d/m/Y'): '' }}" placeholder="Exception Received Date" autocomplete="off" id="update_exception_received_date_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="visibility: hidden;height: 0;"': '' !!}/>
                                       </div>
                               </div>
                               <div class="col-md-2 mt-4 exceptionFields_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="display: none;"': '' !!}>
                                   <label for="txtPassword"><b>Exception Remark</b></label>
                                   <div class="relative">
                                   <input type="text" name="exception_remark[]" class="form-control exception_remark {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'required': '' !!}" value="{{$arr['exception_remark'] ?? ''}}" placeholder="Exception Remark" autocomplete="off" id="update_exception_remark_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="visibility: hidden;height: 0;"': '' !!}/>
                                   </div>
                               </div>
                               <div class="col-md-2 mt-4">
                                   <label for="txtPassword"><b>Maturity Date</b></label>
                                   <div class="relative">
                                   <input type="text" name="maturity_date[]" class="form-control sc-doc-date maturity_date" value="{{\Carbon\Carbon::createFromFormat('Y-m-d', $arr['maturity_date'])->format('d/m/Y') ?? ''}}" placeholder="Maturity Date" autocomplete="off" id="update_maturity_date_{{ $key }}"/>
                                   </div>
                               </div>
                               <div class="col-md-2 mt-4">
                                   <label for="txtPassword"><b>Renewal Reminder Days</b></label>
                                   <div class="relative">
                                   <input type="text" name="renewal_reminder_days[]" class="form-control digits renewal_reminder_days" value="{{$arr['renewal_reminder_days'] ?? ''}}" placeholder="Renewal Reminder Days" autocomplete="off" id="update_renewal_reminder_days_{{ $key }}"/>
                                   </div>
                               </div>
                               <div class="col-md-2 mt-4 INR">
                                   <label for="txtPassword"><b>Amount Expected</b></label>
                                   <div class="relative">
                                   <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                                   <input type="text" name="amount_expected[]" class="form-control number float_format amount_expected" value="{{$arr['amount_expected'] ?? ''}}" placeholder="Amount Expected" autocomplete="off" id="update_amount_expected_{{ $key }}"/>
                                   </div>
                               </div>
                               <div class="col-md-2 mt-4 INR">
                                   <label for="txtPassword"><b>Document Amount</b></label>
                                   <div class="relative">
                                   <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                                   <input type="text" name="document_amount[]" class="form-control number float_format document_amount" value="{{$arr['document_amount'] ?? ''}}" placeholder="Document Amount" autocomplete="off" id="update_document_amount_{{ $key }}"/>
                                   </div>
                               </div>
                               <div class="col-md-2 mt-4">
                                   <label for="txtPassword"><b>Doc Upload</b>
                                    @if ($arr['is_upload'] && $arr['is_upload'] == 1)
                                    <a href="{{ route('download_storage_file', ['file_id' => $arr['file_id'] ]) }}" title="Download Document"><i class="fa fa-lg fa-download ml-3" aria-hidden="true"></i></a>  
                                    @endif
                                  </label>
                                   <div class="relative">
                                    <div class="d-flex">
                                       <div class="custom-file upload-btn-cls mb-3">
                                           <input type="file" class="custom-file-input getFileName doc_file_sec" name="doc_file_sec[]" id="update_doc_file_{{ $key }}">
                                           <label class="custom-file-label" for="customFile">Choose file</label>
                                       </div>
                                       @if($loop->first)
                                        <i class="fa fa-2x fa-plus-circle add-security-doc-block ml-2"  style="color: green;"></i>
                                         @else
                                         <i class="fa fa-2x fa-times-circle remove-security-doc-block ml-2" style="color: red;"></i>
                                         @endif
                                    </div>
                                   </div>
                               </div>
                            </div>
                            @endforeach
                        @else
                                
                            <div class="row toRemoveDiv1" style="background-color: #e9e7e7;">
                                <input type="hidden" name="app_security_doc_id[]" class="form-control" value="" placeholder="Group Company" />
                                <div class="col-md-2 mt-4">
                                    <label for="txtPassword"><b>Pre/Post</b></label>
                                    <div class="relative">
                                        <select class="form-control" name="doc_type[]" id="doc_type_1">
                                            <option value="">Select</option>
                                            <option value="pre">Pre</option>
                                            <option value="post">Post</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                     <label for="txtPassword"><b>Type of Document</b></label>
                                     <select class="form-control" name="security_doc_id[]" id="security_doc_id_1">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mt-4">
                                        <label for="txtPassword"><b>Description</b></label>
                                        <div class="relative">
                                            <input type="text" name="description[]" class="form-control" value="" placeholder="Description" autocomplete="off"/>
                                        </div>
                                </div>        
                                <div class="col-md-2 mt-4">
                                     <label for="txtPassword"><b>Document Number</b></label>
                                     <div class="relative">
                                        <input type="text" name="document_number[]" class="form-control" value="" placeholder="Document Number" autocomplete="off" min="1" checkDocumentNumber="true"/>
                                     </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                     <label for="txtPassword"><b>Due Date</b></label>
                                     <div class="relative">
                                             <input type="text" name="due_date[]" maxlength="20" class="form-control sc-doc-date" value="" placeholder="Due Date" autocomplete="off"/>
                                     </div>
                                </div>
                                <div class="col-md-1 mt-4">
                                    <label for="txtPassword"><b>Completed</b></label>
                                    <div class="relative">
                                        <select class="form-control" name="completed[]">
                                            <option value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                               </div>
                               <div class="col-md-1 mt-4">
                                        <label for="txtPassword"><b>Exception Received</b></label>
                                        <div class="relative">
                                            <select class="form-control" name="exception_received[]" onchange="displayExceptionFields(this.value,1);" data-previous="1">
                                                <option value="">Select</option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                </div>
                                <div class="col-md-2 mt-4 exceptionFields_1" style="display: none;">
                                        <label for="txtPassword"><b>Exception Received From</b></label>
                                        <div class="relative">
                                        <input type="text" name="exception_received_from[]" class="form-control" value="" placeholder="Exception Received From" autocomplete="off"/>
                                        </div>
                                </div>
                                <div class="col-md-2 mt-4 exceptionFields_1" style="display: none;">
                                        <label for="txtPassword"><b>Exception Received Date</b></label>
                                        <div class="relative">
                                        <input type="text" name="exception_received_date[]" class="form-control sc-doc-date" value="" placeholder="Exception Received Date" autocomplete="off"/>
                                        </div>
                                </div>
                                <div class="col-md-2 mt-4 exceptionFields_1" style="display: none;">
                                    <label for="txtPassword"><b>Exception Remark</b></label>
                                    <div class="relative">
                                    <input type="text" name="exception_remark[]" class="form-control" value="" placeholder="Exception Remark" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label for="txtPassword"><b>Maturity Date</b></label>
                                    <div class="relative">
                                    <input type="text" name="maturity_date[]" class="form-control sc-doc-date" value="" placeholder="Maturity Date" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label for="txtPassword"><b>Renewal Reminder Days</b></label>
                                    <div class="relative">
                                    <input type="text" name="renewal_reminder_days[]" class="form-control digits" value="" placeholder="Renewal Reminder Days" autocomplete="off"  min="1" max="365"/>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4 INR">
                                    <label for="txtPassword"><b>Amount Expected</b></label>
                                    <div class="relative">
                                    <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" name="amount_expected[]" class="form-control number float_format" value="" placeholder="Amount Expected" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4 INR">
                                    <label for="txtPassword"><b>Document Amount</b></label>
                                    <div class="relative">
                                    <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" name="document_amount[]" class="form-control number float_format" value="" placeholder="Document Amount" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label for="txtPassword"><b>Doc Upload</b></label>
                                    <div class="relative">
                                        <div class="d-flex">
                                        <div class="custom-file upload-btn-cls mb-3">
                                            <input type="file" class="custom-file-input getFileName" name="doc_file_sec[]" extension="jpg,png,pdf,doc,docx"
                                            filesize = "200000000" data-msg-extension="Only support jpg,png,pdf,doc,docx type format." data-msg-filesize="maximum size for upload 20 MB.">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                        <i class="fa fa-2x fa-plus-circle add-security-doc-block ml-2"  style="color: green;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-12 mt-4 mb-2" style="padding: 5px;">
                        
                    </div>    
                </div> 
                @if(request()->get('view_only'))
                @can('cam_information_save')
                    <button class="btn btn-success pull-right  mt-3" type="Submit"> Save</button>
                @endcan
                @endif
              </form>
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('changeAppDisbursStatus','Change App Status', 'modal-md')!!}
@endsection
@section('jscript')
<script src="{{url('common/js/typehead.js')}}"></script>
<script src="{{ asset('common/js/additional-methods.min.js') }}"></script>
<script type="text/javascript">
   getAllSecurityDocumentName(1);
   $('#debt_on').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
   });

      var ckeditorOptions =  {
        filebrowserUploadUrl: "{{route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'file' ])}}",
        filebrowserUploadMethod: 'form',
        imageUploadUrl:"{{ route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'image' ]) }}",
        disallowedContent: 'img{width,height};'
      };

      CKEDITOR.replace('contigent_observations', ckeditorOptions);
      CKEDITOR.replace('risk_comments', ckeditorOptions);
      CKEDITOR.replace('anchor_risk_comments', ckeditorOptions);
      CKEDITOR.replace('profile_of_company', ckeditorOptions);
      CKEDITOR.replace('rating_rational', ckeditorOptions);

    function showSecurityComment(val){
        if($("#othersCheckbox").is(':checked')){
            $("#securityComment").show();
        }else{
            $("#securityComment").hide();
        }
    }

    var path = "{{ route('get_group_company') }}";
    

    $('input.group-company').typeahead({
        source:  function (query, process) {
            return $.get(path, { query: query }, function (data) {
                return process(data);
            });
        },
        minLength: '3'
    });

    function calTotalExposure(){
       var outstandingExposure = 0;
       var proposed = 0;
            $('input[name*=outstanding_exposure]').each(function(){
                if($.isNumeric($(this).val())){
                    outstandingExposure  = parseFloat(outstandingExposure) + parseFloat($(this).val());
                }      
            });
            $('input[name*=proposed_exposure]').each(function(){
                if($.isNumeric($(this).val())){
                    proposed  = parseFloat(proposed) + parseFloat($(this).val());
                }      
            });
        var outstandingExposure = (!isNaN(outstandingExposure))?outstandingExposure:0;
        proposed = (!isNaN(proposed))?proposed:0;
        
        $("input[name='total_exposure']").val((proposed+outstandingExposure).toFixed(2));

    }

    $(document).on('input', 'input.calTotalExposure', function(){
           calTotalExposure();
    })
    
    $(document).on('click', '.add-ptpq-block', function(){
    let ptpq_block = '<div class="row mt10 toRemoveDiv">'+
            '<div class="col-md-4">'+
                '<input type="text" name="group_company_name[]" class="form-control" value="" placeholder="Group Company" required autocomplete="off">'+
            '</div>'+
            '<div class="col-md-3 INR">'+
                '<div class="relative"><a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                '<input type="text" name="sanction_limit[]" class="form-control " value="" placeholder="Sanction Limit (In Mn)" required autocomplete="off">'+
            '</div></div>'+
            '<div class="col-md-3 INR">'+
                '<div class="relative"><a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                '<input type="text" name="outstanding_exposure[]" class="form-control  calTotalExposure" value="" placeholder="Outstanding Exposure (In Mn)" required autocomplete="off">'+
            '</div></div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-ptpq-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#ptpq-block').append(ptpq_block);
  });

  $(document).on('click', '.remove-ptpq-block', function(){
    var group_company_expo_id = $(this).closest('.toRemoveDiv').find('.group_company_expo_id').val();

    if(group_company_expo_id > 0 && group_company_expo_id != 'undefined'){
            var messages = {
                  update_group_company_exposure: "{{ URL::route('update_group_company_exposure') }}",
                  token: "{{ csrf_token() }}",
             };

             var dataStore = {'group_company_expo_id': group_company_expo_id,'_token': messages.token };
             jQuery.ajax({
                 url: messages.update_group_company_exposure,
                 method: 'post',
                 dataType: 'json',
                 data: dataStore,
                 error: function (xhr, status, errorThrown) {
                                   // alert(errorThrown);
                 },
                 success: function (data) {  
                 }
             });   
    }
        $(this).closest('.toRemoveDiv').remove();
        calTotalExposure();
  });


  $(document).on('click', '.dropdown-menu .dropdown-item ', function(argument) {
       var messages = {
              get_group_company_exposure: "{{ URL::route('get_group_company_exposure') }}",
              token: "{{ csrf_token() }}",
         };
         var groupid = $(this).find('.groupid').attr('groupid');
         var dataStore = {'groupid': groupid,'_token': messages.token };
         //var openIf = if(arr.proposed_exposure > 0){;
        // var closeIf = };
      jQuery.ajax({
             url: messages.get_group_company_exposure,
             method: 'post',
             dataType: 'json',
             data: dataStore,
             error: function (xhr, status, errorThrown) {
                               // alert(errorThrown);
             },
             success: function (data) {  
              $.each(data, function(i, arr) {
                let symbol_rs = '';
                let proposed_exposure_html = 'hidden';
                if(arr.proposed_exposure > 0){
                    symbol_rs ='<a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>';
                    proposed_exposure_html = 'text';
                }
                    let ptpq_block ='<div class="row mt10 toRemoveDiv">'+
                                        '<input type="hidden" name="group_company_expo_id[]" class="form-control" value="'+arr.group_company_expo_id+'" placeholder="Group Company group_company_expo_id" /><div class="col-md-4">'+
                                            '<input type="text" name="group_company_name[]" class="form-control" value="'+arr.group_company_name+'" placeholder="Group Company" required autocomplete="off">'+
                                        '</div>'+
                                        '<div class="col-md-3 INR">'+
                                            '<div class="relative"><a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                                            '<input type="text" name="sanction_limit[]" class="form-control float_format" value="'+arr.sanction_limit+'" placeholder="Sanction Limit (In Mn)" required autocomplete="off">'+
                                        '</div></div>'+
                                        '<div class="col-md-3 INR">'+
                                            '<div class="relative"><a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                                            '<input type="text" name="outstanding_exposure[]" class="form-control  calTotalExposure float_format" value="'+arr.outstanding_exposure+'" placeholder="Outstanding Exposure (In Mn)" required autocomplete="off">'+
                                        '</div></div>'+

                                        '<div class="col-md-2 center INR">'
                                             +symbol_rs+ 
                                             '<div class="d-flex">' 
                                              +'<input type='+proposed_exposure_html+' name="proposed_exposure[]" class="form-control  calTotalExposure float_format" value="'+arr.proposed_exposure+'" placeholder="Proposed Exposure (In Mn)" required autocomplete="off">'
                                             +'<i class="fa fa-2x fa-times-circle remove-ptpq-block ml-2" style="color: red;"></i></div>'+
                                        '</div>'+
                                    '</div>';
                    $('#ptpq-block').append(ptpq_block);
              }); 
               calTotalExposure();    
            }
      });
   })


$(document).on('keypress','.float_format', function(event) {
let num = $(this).val();
num.split('.')[1]
    if(event.which == 8 || event.which == 0){
        return true;
    }
    if(event.which < 46 || event.which > 59) {
        return false;
    }
   
    if(event.which == 46 && $(this).val().indexOf('.') != -1) {
        return false;
    }
if(typeof num.split('.')[1] !== 'undefined' && num.split('.')[1].length > 1){
return false;
}
});
$(document).ready(function () {
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');
    var messages = {
        unique_security_doc_number: "{{ URL::route('check_unique_security_doc_number') }}",
        token: "{{ csrf_token() }}",
    };
    $.validator.addMethod("checkDocumentNumber",
        function(value, element, params) {
            var result = true;
            var data = {doc_number : value, _token: messages.token};
            var app_security_doc_id = $(element).closest('.toRemoveDiv1').find('.app_security_doc_id').val();
            if(app_security_doc_id > 0 && app_security_doc_id != 'undefined'){
                data['id'] = app_security_doc_id;
            }
            data['app_id'] = $("input[name='app_id']").val();
            $.ajax({
                type:"POST",
                async: false,
                url: messages.unique_security_doc_number, // script to validate in server side
                data: data,
                success: function(data) {                        
                    result = (data.status == 1) ? false : true;
                }
            });                
            return result;                
        },'Document Number is already exists'
    );
    $('#camForm').validate({ignore: ".descexception_received_from"});
$('#camForm').validate({ // initialize the plugin
    rules: {
        'group_company' : {
            required : true,
        },
    },
    messages: {
        'group_company': {
            required: "Please enter Group Name",
        },
    }
});
});
$(document).on('submit', '#camForm', function(e) {
   $('.group_nameId').text(" ");
   $filledInput = 0;
   
   $('#ptpq-block input').each(function () {
      if ($(this).val()) $filledInput++;
   })
   if ($filledInput > 1 && !$('input[name="group_company"]').val()) {
       $('.group_nameId').text("Group Name is required.");
       
       $('input[name="group_company"]').focus();
       return false;
   }
    var valid = true;
    $.each($('input[name="document_number[]"]'), function (index1, item1) {

        $.each($('input[name="document_number[]"]').not(this), function (index2, item2) {
            $(item1).removeAttr("style");
            if ($(item1).val() == $(item2).val()) {
                $(item1).css("border-color", "red");
                valid = false;
            }

        });
    });
    $('select.doc_type').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('select.security_doc_id').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.description').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.document_number').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                checkDocumentNumber: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.due_date').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('select.completed').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('select.exception_received').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });

    $('input.maturity_date').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.renewal_reminder_days').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                max: 365,
                digits:true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.amount_expected').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.document_amount').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.doc_file_sec').each(function () {
        $(this).rules("add",
            {
                extension: "jpg,png,pdf,doc,docx",
                filesize : 200000000,
                messages: {
                    required: "This field is required.",
                    extension:"Only support jpg,png,pdf,doc,docx type format.",
                    filesize:"maximum size for upload 20 MB.",
                }
            });
    });
    if (!$('#camForm').valid()) {
        var valid = false;
    }
   return valid;
});

var counter;
$(document).on('click', '.add-security-doc-block', function(){
    counter = $('#security-doc-block .toRemoveDiv1').length + 1;
    @if(empty($arrAppSecurityDoc)) 
        counter = $('#security-doc-block .toRemoveDiv1').length + 1;
    @endif
    $('.toRemoveDiv1').each(function() {
        $(this).find('div').removeClass('exceptionFields_'+counter);
    });
    let scdocc_block = '<div class="row  mt10 toRemoveDiv1" style="background-color: #e9e7e7;">'+
        '<div class="col-md-2 mt-4">'+
        '<label for="txtPassword"><b>Pre/Post</b></label>'+
        '<div class="relative">'+
            '<select class="form-control doc_type" name="doc_type[]" id="doc_type_'+counter+'">'+
                '<option value="">Select</option>'+
                '<option value="pre">Pre</option>'+
                '<option value="post">Post</option>'+
            '</select>'+
       '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4">'+
            '<label for="txtPassword"><b>Type of Document</b></label>'+
            '<select class="form-control security_doc_id" name="security_doc_id[]" id="security_doc_id_'+counter+'">'+
            '<option value="">Select</option>'+
            '</select>'+
    '</div>'+
    '<div class="col-md-2 mt-4">'+
            '<label for="txtPassword"><b>Description</b></label>'+
            '<div class="relative">'+
                '<input type="text" name="description[]" class="form-control description" value="" placeholder="Description" autocomplete="off" id="description_'+counter+'"/>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4">'+
            '<label for="txtPassword"><b>Document Number</b></label>'+
            '<div class="relative">'+
            '<input type="text" name="document_number[]" class="form-control document_number" value="" placeholder="Document Number" autocomplete="off" id="document_number_'+counter+'"/>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4">'+
            '<label for="txtPassword"><b>Due Date</b></label>'+
            '<div class="relative">'+
                    '<input type="text" name="due_date[]" maxlength="20" class="form-control sc-doc-date due_date" value="" placeholder="Due Date" autocomplete="off" id="due_date_'+counter+'"/>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-1 mt-4">'+
        '<label for="txtPassword"><b>Completed</b></label>'+
        '<div class="relative">'+
            '<select class="form-control completed" name="completed[]" id="completed_'+counter+'">'+
                '<option value="">Select</option>'+
                '<option value="yes">Yes</option>'+
                '<option value="no">No</option>'+
            '</select>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-1 mt-4">'+
            '<label for="txtPassword"><b>Exception Received</b></label>'+
            '<div class="relative">'+
                '<select class="form-control exception_received" name="exception_received[]" onchange="displayExceptionFields(this.value,'+counter+');" id="exception_received_'+counter+'" data-previous="'+counter+'">'+
                    '<option value="">Select</option>'+
                    '<option value="yes">Yes</option>'+
                    '<option value="no">No</option>'+
                '</select>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4 exceptionFields_'+counter+'" style="display: none;">'+
            '<label for="txtPassword"><b>Exception Received From</b></label>'+
            '<div class="relative">'+
            '<input type="text" name="exception_received_from[]" class="form-control exception_received_from required" value="" placeholder="Exception Received From" autocomplete="off" id="exception_received_from_'+counter+'" style="visibility: hidden;height: 0;"/>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4 exceptionFields_'+counter+'" style="display: none;">'+
            '<label for="txtPassword"><b>Exception Received Date</b></label>'+
            '<div class="relative">'+
            '<input type="text" name="exception_received_date[]" class="form-control sc-doc-date exception_received_date required" value="" placeholder="Exception Received Date" autocomplete="off" id="exception_received_date_'+counter+'" style="visibility: hidden;height: 0;"/>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4 exceptionFields_'+counter+'" style="display: none;">'+
        '<label for="txtPassword"><b>Exception Remark</b></label>'+
        '<div class="relative">'+
        '<input type="text" name="exception_remark[]" class="form-control exception_remark required" value="" placeholder="Exception Remark" autocomplete="off" id="exception_remark_'+counter+'" style="visibility: hidden;height: 0;"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4">'+
        '<label for="txtPassword"><b>Maturity Date</b></label>'+
        '<div class="relative">'+
        '<input type="text" name="maturity_date[]" class="form-control sc-doc-date maturity_date" value="" placeholder="Maturity Date" autocomplete="off" id="maturity_date_'+counter+'"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4">'+
        '<label for="txtPassword"><b>Renewal Reminder Days</b></label>'+
        '<div class="relative">'+
        '<input type="text" name="renewal_reminder_days[]" class="form-control digits renewal_reminder_days" value="" placeholder="Renewal Reminder Days" autocomplete="off" id="renewal_reminder_days_'+counter+'"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4 INR">'+
        '<label for="txtPassword"><b>Amount Expected</b></label>'+
        '<div class="relative">'+
        '<a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>'+
        '<input type="text" name="amount_expected[]" class="form-control number float_format amount_expected" value="" placeholder="Amount Expected" autocomplete="off" id="amount_expected_'+counter+'"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4 INR">'+
       '<label for="txtPassword"><b>Document Amount</b></label>'+
        '<div class="relative">'+
        '<a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>'+
        '<input type="text" name="document_amount[]" class="form-control number float_format document_amount" value="" placeholder="Document Amount" autocomplete="off" id="document_amount_'+counter+'"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-4">'+
        '<label for="txtPassword"><b>Doc Upload</b></label>'+
        '<div class="relative">'+
            '<div class="custom-file upload-btn-cls mb-3">'+
                '<input type="file" class="custom-file-input getFileName doc_file_sec required" id="doc_file_'+counter+'" name="doc_file_sec[]">'+
                '<label class="custom-file-label" for="customFile">Choose file</label>'+
            '</div>'+
        '</div>'+
        '<i class="fa fa-2x fa-times-circle remove-security-doc-block" style="color: red;margin-left: 267px;position: absolute;margin-top: -50px;"></i>'+
    '</div>'+
        '</div>';
    $('#security-doc-block').append(scdocc_block);
    makeRequiredFields(counter,'add');
    getAllSecurityDocumentName(counter);
    counter++;
    $('.sc-doc-date').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     startDate: new Date(),
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
   });
  });

  $(document).on('click', '.remove-security-doc-block', function(){
    var app_security_doc_id = $(this).closest('.toRemoveDiv1').find('.app_security_doc_id').val();

    if(app_security_doc_id > 0 && app_security_doc_id != 'undefined'){
            var messages = {
                update_app_security_doc: "{{ URL::route('update_app_security_doc') }}",
                  token: "{{ csrf_token() }}",
             };

             var dataStore = {'app_security_doc_id': app_security_doc_id,'_token': messages.token };
             jQuery.ajax({
                 url: messages.update_app_security_doc,
                 method: 'post',
                 dataType: 'json',
                 data: dataStore,
                 error: function (xhr, status, errorThrown) {
                                   // alert(errorThrown);
                 },
                 success: function (data) {  
                 }
             });   
    }
        $(this).closest('.toRemoveDiv1').remove();
        resetIndexes();
  });
  $(document).on('change','.getFileName',function(){
        $(this).parent('div').children('.custom-file-label').html('Choose file');
    });
    
    $(document).on('change','.getFileName',function(e) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });
    $('.sc-doc-date').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     startDate: new Date(),
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
   });
function getAllSecurityDocumentName(selectId){
  var securityDoc= {!! $securityDocumentListJson !!};
  if(securityDoc){
    $.each(securityDoc, function(i, item) {
        $('#security_doc_id_'+selectId)
          .append($('<option>', { value : item.security_doc_id })
          .text(item.name));
    });
  }
}
function displayExceptionFields(exceptionVal,divId){
    $(".exceptionFields_"+divId).css("display", "none");
    $(".exceptionFields_"+divId).find('input').css("visibility", "hidden").removeClass('required');
    if(exceptionVal == 'yes'){
      $(".exceptionFields_"+divId).removeAttr("style");
      $(".exceptionFields_"+divId).find('input').removeAttr("style").addClass('required');
    }
    if(exceptionVal == 'no'){
        $(".exceptionFields_"+divId).find('input').val('');
    }
    makeRequiredFields(divId,'update');
}

function resetIndexes() {
  var j = 1, id,name, $this;
  // for each element on the page with the class .input-wrap
  var previousId = [];  
  $('.toRemoveDiv1').each(function() {
    if (j > 1) {
      // within each matched .input-wrap element, find each <input> element
      $(this).find('input, select').each(function() {
        $this = $(this);
        id = $this.attr("id");
        name = $this.attr("name");
        console.log(name);
        if(name == 'exception_received[]'){
            $this.removeAttr("onchange");
            $this.attr("onchange",'displayExceptionFields(this.value,'+j+');');
        }
        ids=id.replace(/\d/g, '');
        $(this).attr('id', ids+''+j);
            if(name == 'exception_received_from[]' || name == 'exception_received_date[]' || name == 'exception_remark[]'){
                previousIds = $this.attr("data-previous");
                $(this).parent().parent().removeClass('exceptionFields_'+previousIds).addClass('exceptionFields_'+j);
        }
      })
    }
    j++;
  });
}
function makeRequiredFields(counters, reqType){
    $('#camForm').validate({ignore: ".descexception_received_from"});
    $('select.doc_type').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('select.security_doc_id').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.description').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.document_number').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                checkDocumentNumber: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.due_date').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('select.completed').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('select.exception_received').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    
    $('input.maturity_date').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.renewal_reminder_days').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                max: 365,
                digits:true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.amount_expected').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.document_amount').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('input.doc_file_sec').each(function () {
        $(this).rules("add",
            {
                extension: "jpg,png,pdf,doc,docx",
                filesize : 200000000,
                messages: {
                    required: "This field is required.",
                    extension:"Only support jpg,png,pdf,doc,docx type format.",
                    filesize:"maximum size for upload 20 MB.",
                }
            });
    });
    
}
@if(!empty($arrAppSecurityDoc))
        @foreach($arrAppSecurityDoc as $key=>$arr)
        @php 
            $key =  $key+1;    
        @endphp
        makeRequiredFields({{ $key }},'update');
    @endforeach
    @endif
</script>
@endsection