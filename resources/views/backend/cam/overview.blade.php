@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">
        @php
            $route_name = \Request::route()->getName();
            // echo $route_name;
        @endphp
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
                <input type="hidden" name="user_id" value="{{$user_id}}" />             
                <input type="hidden" name="cam_report_id" value="{{isset($arrCamData->cam_report_id) ? $arrCamData->cam_report_id : ''}}" />    
                @if($route_name!="security_deposit")
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
                        </span>  
                    </h2>                    
                    <div class="col-md-12 mt-4">
                         <div class="row">
                            <div class="col-md-2">
                                <label for="txtPassword"><b>Group Name</b></label>
                                <span style="color: red; font-size: 20px"> * </span>
                            </div>
                            <div class="col-md-4">
                                @if($arrCamData['group_id'])
                                    <input type="hidden" name="group_id" value="{{ $arrCamData['group_id'] }}">                                
                                @endif
                                @if($appData->is_old_app == 0)
                                    <select class="form-control group-company"  id="groupExpoId" 
                                    {{ isset($arrCamData['group_id']) ? 'disabled' : 'name=group_id' }} 
                                    onchange="getGroupUcicUsersData(this)">
                                        @if(count($allNewGroups))
                                        <option value="">Please Select Group</option>
                                        @endif
                                        @forelse($allNewGroups as $allNewGroup)
                                        <option value="{{ $allNewGroup->group_id }}" {{ isset($arrCamData['group_id']) && $arrCamData['group_id'] == $allNewGroup->group_id ? 'selected' : ''}}>{{ $allNewGroup->group_name }}</option>
                                        @empty
                                        <option value="">No Groups Found</option>
                                        @endforelse
                                    </select>
                                @else
                                    <input class="form-control"  disabled value="{{ $arrCamData['oldAppGroupName'] }}">
                                @endif
                                <label class="error" for="group_id"></label>
                                <span  class="group_nameId" style="color:red;"></span>
                            </div>
                        </div>
                     </div>  
                    @if($appData->is_old_app == 0) 

                     <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="groupUcicBorrowerList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="group-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>Borrower </th>
                                        <th width="10%">Product Type</th>
                                        <th width="17%">Sanction Limit (In Mn)</th>
                                        <th width="17%">Outstanding Exposure (In Mn)</th>
                                        <th width="17%">Proposed Limit (In Mn)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div id="groupUcicBorrowerListing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-4" id="ptpq-block">
                        @if(!empty($arrGroupCompany))
                            @foreach($arrGroupCompany as $key=>$arr)
                            <div class="row  toRemoveDiv {{($loop->first)? '': 'mt10'}}">
                                <input type="hidden" name="app_group_detail_id[]" class="form-control group_company_expo_id" value="{{$arr['app_group_detail_id'] ?? ''}}" placeholder="Group Company" />
                                <div class="col-md-4">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Borrower</b></label>
                                    @endif
                                    <input type="text" name="borrower[{{$key}}]" class="form-control" value="{{$arr['borrower'] ?? ''}}" placeholder="Borrower Name"  autocomplete="off"{{ $arr['status'] == 1 ? 'readonly' : '' }} required/>
                                </div>
                                <div class="col-md-3 INR">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Sanction Limit (In Mn)</b></label>
                                    @endif
                                    <div class="relative">
                                        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <input type="text" name="sanction[{{$key}}]" class="form-control calTotalExposure float_format sanction-input" min="0" value="{{ isset($arr['sanction']) ? $arr['sanction'] :'' }}" placeholder="Sanction Limit (In Mn)" autocomplete="off" {{ $arr['status'] == 1 ? 'readonly' : '' }}/>
                                     </div>
                                </div>
                                <div class="col-md-3 INR">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Outstanding Exposure (In Mn)</b></label>
                                    @endif
                                    <div class="relative">
                                        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <input type="text" name="outstanding[{{$key}}]" class="form-control  calTotalExposure float_format" min="0" value="{{ isset($arr['outstanding']) ? $arr['outstanding'] : '' }}" placeholder="Outstanding Exposure (In Mn)" autocomplete="off" {{ $arr['status'] == 1 ? 'readonly' : '' }}/>
                                    </div>
                                </div>
                                <div class="col-md-2 center INR">
                                     @if($loop->first)
                                        <label for="txtPassword"><b>Proposed Limit (In Mn)</b></label>
                                    @endif
                                    @if($arr['proposed'] > 0 || ($loop->first))
                                        <a href="javascript:void(0);" class="verify-owner-no" style="top:28px;left:14px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    @endif
                                     <div class="d-flex">
                                         @if(($arr['proposed'] > 0) || ($loop->first))
                                          <input type="text" name="proposed[{{$key}}]" maxlength="20" class="form-control  calTotalExposure float_format proposed_exposureInput" min="0" value="{{ isset($arr['proposed']) ? $arr['proposed'] : ''}}" placeholder="Proposed Limit (In Mn)" autocomplete="off" {{ $arr['status'] == 1 ? 'readonly' : '' }}/>
                                          @endif
                                          {{--@if($arr['status'] == 0)
                                           @if($loop->first)
                                                <i class="fa fa-2x fa-plus-circle add-ptpq-block ml-2" style="color: green;"></i>
                                           @else
                                            <i class="fa fa-2x fa-times-circle remove-ptpq-block" style="color: red;"></i>
                                           @endif
                                           @endif--}}              
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else                                
                            {{--<div class="row">
                                <input type="hidden" name="app_group_detail_id[]" class="form-control" value="" placeholder="Group Company" />
                                <div class="col-md-4 mt-4">
                                     <label for="txtPassword"><b>Borrower</b></label>
                                    <input type="text" class="form-control" name="borrower[0]" value="{{$arrBizData->biz_entity_name}}" placeholder="Borrower Name" required />
                                </div>
                                <div class="col-md-3 mt-4 INR">
                                        <label for="txtPassword"><b>Sanction Limit (In Mn)</b></label>
                                        <div class="relative">
                                            <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a> 
                                            <input type="text" name="sanction[0]" class="form-control float_format sanction-input" min="0" placeholder="Sanction Limit (In Mn)" autocomplete="off"/>
                                        </div>
                                </div>        
                                <div class="col-md-3 mt-4 INR">
                                     <label for="txtPassword"><b>Outstanding Exposure (In Mn)</b></label>
                                     <div class="relative">
                                        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <input type="text" name="outstanding[0]" class="form-control calTotalExposure float_format" min="0" placeholder="Outstanding Exposure (In Mn)" autocomplete="off"/>
                                     </div>
                                </div>
                                <div class="col-md-2 mt-4 INR">
                                     <label for="txtPassword"><b>Proposed Limit (In Mn)</b></label>
                                     <div class="relative">
                                        <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <div class="d-flex">
                                             <input type="text" name="proposed[0]" maxlength="20" class="form-control calTotalExposure float_format proposed_exposureInput" min="0" placeholder="Proposed Limit (In Mn)" />
                                              <i class="fa fa-2x fa-plus-circle add-ptpq-block ml-2"  style="color: green;"></i>
                                        </div>
                                     </div>
                                </div>
                            </div>--}}
                        @endif
                    </div>

                    <div class="col-md-12 mt-4 mb-2" style="background: #e1f0eb; padding: 5px;">
                        <div class="row">
                            <div class="col-md-3 mt-2">
                                <label for="txtPassword"><b>Total Exposure (In Mn) </b></label>
                            </div>
                            <div class="col-md-6 "></div>
                             <div class="col-md-3">
                                <div class="relative">
                                    {{-- <a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a> --}}
                                    <input type="text" class="form-control" id="total_exposure_in_millions" value="{{($arrCamData &&  isset($arrCamData['total_exposure_amount']) && $arrCamData['total_exposure_amount'] > 0) ? \Helpers::convertToMillions($arrCamData['total_exposure_amount']) : 0}}" readonly />
                                    <input type="hidden" class="form-control " name="total_exposure" value="{{($arrCamData &&  isset($arrCamData['total_exposure_amount']) && $arrCamData['total_exposure_amount'] > 0) ? $arrCamData['total_exposure_amount'] : 0}}" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
@endif
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
                @if($route_name!="security_deposit")
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
                @endif
                @if(request()->get('view_only'))
                @can('cam_information_save')
                    <button class="btn btn-success pull-right  mt-3" type="submit"> Save</button>
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
<script type="text/javascript">
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
    // $('input.group-company').typeahead({
    //     source:  function (query, process) {
    //         return $.get(path, { query: query }, function (data) {
    //             return process(data);
    //         });
    //     },
    //     minLength: '3'
    // });

    function calTotalExposure(){
       var outstandingExposure = 0;
       var proposed = 0;
            $('input[name*=outstanding]').each(function(){
                if($.isNumeric($(this).val())){
                    outstandingExposure  = parseFloat(outstandingExposure) + parseFloat($(this).val());
                }      
            });
            $('input[name*=proposed]').each(function(){
                if($.isNumeric($(this).val())){
                    proposed  = parseFloat(proposed) + parseFloat($(this).val());
                }      
            });
        var outstandingExposure = (!isNaN(outstandingExposure))?outstandingExposure:0;
        proposed = (!isNaN(proposed))?proposed:0;
        totalExposureInMn = getConvertToMillions((proposed+outstandingExposure));
        $("#total_exposure_in_millions").val(totalExposureInMn);
        $("input[name='total_exposure']").val((proposed+outstandingExposure).toFixed(2));

    }

    $(document).on('input', 'input.calTotalExposure', function(){
           calTotalExposure();
    })
    
    $(document).on('click', '.add-ptpq-block', function(){
        var keyCounter = ($('.sanction-input').length - 1) + 1;
        let ptpq_block = '<div class="row mt10 toRemoveDiv">'+
            '<div class="col-md-4">'+
                '<input type="text" name="borrower['+keyCounter+']" required class="form-control" value="" placeholder="Borrower Name" autocomplete="off" id="borrowerInput'+keyCounter+'">'+
            '</div>'+
            '<div class="col-md-3 INR">'+
                '<div class="relative"><a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                '<input type="text" name="sanction['+keyCounter+']" class="form-control float_format sanction-input" min="0" placeholder="Sanction Limit (In Mn)" required autocomplete="off">'+
            '</div></div>'+
            '<div class="col-md-3 INR">'+
                '<div class="relative"><a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                '<input type="text" name="outstanding['+keyCounter+']" class="form-control  calTotalExposure float_format" min="0" placeholder="Outstanding Exposure (In Mn)" required autocomplete="off">'+
            '</div></div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-ptpq-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#ptpq-block').append(ptpq_block);
  });

  $(document).on('click', '.remove-ptpq-block', function(){
    var group_company_expo_id = $(this).closest('.toRemoveDiv').find('.group_company_expo_id').val();
    var app_id = $("input[name='app_id']").val();

    if(group_company_expo_id > 0 && group_company_expo_id != 'undefined'){
            var messages = {
                  update_group_company_exposure: "{{ URL::route('update_group_company_exposure') }}",
                  token: "{{ csrf_token() }}",
             };

             var dataStore = {'app_group_detail_id': group_company_expo_id,'_token': messages.token, app_id: app_id };
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
                                        '<input type="hidden" name="app_group_detail_id[]" class="form-control" value="'+arr.app_group_detail_id+'" placeholder="Group Company group_company_expo_id" /><div class="col-md-4">'+
                                            '<input type="text" name="borrower[]" class="form-control" value="'+arr.borrower+'" placeholder="Borrower Name" required autocomplete="off">'+
                                        '</div>'+
                                        '<div class="col-md-3 INR">'+
                                            '<div class="relative"><a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                                            '<input type="text" name="sanction[]" class="form-control float_format sanction-input" min="0" value="'+arr.sanction+'" placeholder="Sanction Limit (In Mn)" required autocomplete="off">'+
                                        '</div></div>'+
                                        '<div class="col-md-3 INR">'+
                                            '<div class="relative"><a href="javascript:void(0);" class="verify-owner-no" style="top:1px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                                            '<input type="text" name="outstanding[]" class="form-control  calTotalExposure float_format" min="0" value="'+arr.outstanding+'" placeholder="Outstanding Exposure (In Mn)" required autocomplete="off">'+
                                        '</div></div>'+

                                        '<div class="col-md-2 center INR">'
                                             +symbol_rs+ 
                                             '<div class="d-flex">' 
                                              +'<input type='+proposed_exposure_html+' name="proposed[]" class="form-control  calTotalExposure float_format" min="0" value="'+arr.proposed+'" placeholder="Proposed Exposure (In Mn)" required autocomplete="off">'
                                             +'<i class="fa fa-2x fa-times-circle remove-ptpq-block ml-2" style="color: red;"></i></div>'+
                                        '</div>'+
                                    '</div>';
                    $('#ptpq-block').append(ptpq_block);
              }); 
               calTotalExposure();    
            }
      });
   });

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
    
$('#camForm').validate({ // initialize the plugin
    rules: {
        'group_id' : {
            required : true,
        },
    },
    messages: {
        'group_id': {
            required: "Please Select Group Name",
        },
    }
});
});
$(document).on('submit', '#camForm', function(e) {
//    e.preventDefault();
   $('.group_nameId').text(" ");
   $filledInput = 0;
   
   $('#ptpq-block input').each(function () {
      if ($(this).val()) $filledInput++;
   })
   if ($filledInput > 1 && !$('select[name="group_id"]').val()) {
       $('.group_nameId').text("Group Name is required.");
       
       $('select[name="group_id"]').focus();
       return false;
   }
   
   return true;
});

var messages = {
    get_group_exposure_ucic_user_data: "{{ URL::route('get_group_exposure_ucic_user_data') }}",
    token: "{{ csrf_token() }}",
};

function getGroupUcicUsersData(event) {
    oTable.ajax.reload();
}



$(document).on('propertychange change click keyup input paste','.sum_grp_amt',function(){
    this.value = this.value.replace(/[^0-9\.]/g,'');
    
    var total_exposure_amt = 0;
    $(".sum_grp_amt").each(function (index, element) {
        var payamt = parseFloat($(this).val());
        if($.isNumeric(payamt)){
            total_exposure_amt += payamt;
        }
    });
    totalExposureInMn = getConvertToMillions(total_exposure_amt);
    total_exposure_amt = total_exposure_amt.toFixed(2);
    $("#total_exposure_in_millions").val(totalExposureInMn);
    $("input[name=total_exposure]").val(total_exposure_amt)
});

function convertToMillions(input, divID) {
  var value = parseFloat(input.value);
  if (!isNaN(value)) {
    var millions = getConvertToMillions(value);
    $('#'+divID).html(millions + " in mn");
  } else {
    $('#'+divID).html('');
  }
}

function getConvertToMillions(amount) {
  if (isNaN(amount)) {
    throw new Error('Invalid argument. Amount must be a number.');
  }

  var result = amount / 1000000;

  if (result < 0) {
    throw new Error('Invalid argument. Amount cannot be negative.');
  }

  const formatted = (result * 100) / 100;
  return formatted.toFixed(2);
}

try {
    var oTable;
    jQuery(document).ready(function ($) {
        oTable = $('#groupUcicBorrowerList').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            "bLengthChange": false,
            "bInfo" : false,
            bSort: false,
            pageLength: '*',
            bPaginate: false,
            info: false,
            order: [[0, 'asc']],
            ajax: {
                "url": messages.get_group_exposure_ucic_user_data, // json datasource
                "method": 'GET',
                data: function (d) {
                    d.group_id = $('#groupExpoId').find(":selected").val();
                    d.app_id = $("input[name=app_id]").val();
                },
                "error": function () {  // error handling
                    $("#groupUcicBorrowerList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#groupUcicBorrowerListing_processing").css("display", "none");
                }
            },
            "drawCallback": function(settings) {
                totalExposureAmt = settings.json.total_exposure_amt;
                totalExposureInMn = getConvertToMillions(totalExposureAmt);
                $("#total_exposure_in_millions").val(totalExposureInMn);
                $("input[name=total_exposure]").val(totalExposureAmt);
                // Apply the validation rules to the input fields
                var appType = "{{$appData->app_type}}";
                if(appType != 3){
                    $('.proposed_amt').each(function() {
                        $(this).rules('add', {
                            required: true,
                            number: true,
                            min: 0.01, // set minimum value to 0.01
                                messages: {
                                required: 'Please enter a Proposed Limit',
                                number: 'Please enter a valid Proposed Limit',
                                min: 'Proposed Limit must be greater than 0.00' // custom error message for min validation
                            }
                        });
                    });
                }
            },
            columns: [
                {data: 'borrower'},
                {data: 'product_type'},
                {data: 'sanction_amt'},
                {data: 'outstanding_amt'},
                {data: 'proposed_amt'},
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4]},{ targets: [2,3,4], className: 'dt-right' }]            
        });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endsection