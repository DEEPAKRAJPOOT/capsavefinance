@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">

        <div class="card mt-3">
            <div class="card-body pt-3 pb-3">
                <ul class="float-left mb-0 pl-0">
                    <li><b class="bold">Application ID: {{isset($arrRequest['app_id'])?'CAPS000'.$arrRequest['app_id']:''}}</b> </li>
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
             <form method="POST" id="camForm" action="{{route('cam_information_save')}}"> 
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
                                <label for="txtPassword"><b>Group Name</b></label>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="group_company" class="form-control group-company" value="{{isset($arrCamData->group_company) ? $arrCamData->group_company : ''}}" placeholder="Group Name" autocomplete="off"/ style="padding: -;position:absolute; right: 17px;" >
                            </div>
                            
                        </div>
                     </div>   

                    

                    <div class="col-md-12 mt-4" id="ptpq-block">
                        @if(!empty($arrGroupCompany))
                            @foreach($arrGroupCompany as $key=>$arr)
                            <div class="row  toRemoveDiv {{($loop->first)? '': 'mt10'}}">
                                <input type="hidden" name="group_company_expo_id[]" class="form-control" value="{{$arr['group_company_expo_id'] ?? ''}}" placeholder="Group Company" />
                                <div class="col-md-4">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Borrower</b></label>
                                    @endif
                                    <input type="text" name="group_company_name[]" class="form-control" value="{{$arr['group_company_name'] ?? ''}}" placeholder="Group Company" />
                                </div>
                                <div class="col-md-3 INR">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Sanction Limit (In Mn)</b></label>
                                    @endif
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:{{($loop->first) ? '40px;': '9px;' }}"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                     <input type="text" name="sanction_limit[]" class="form-control calTotalExposure float_format" value="{{($arr['sanction_limit'] > 0) ? $arr['sanction_limit'] :'' }}" placeholder="Sanction Limit (In Mn)" autocomplete="off"/>
                                </div>
                                <div class="col-md-3 INR">
                                    @if($loop->first)
                                        <label for="txtPassword"><b>Outstanding Exposure (In Mn)</b></label>
                                    @endif
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:{{($loop->first) ? '40px;': '9px;' }}"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                     <input type="text" name="outstanding_exposure[]" class="form-control  calTotalExposure float_format" value="{{ (($arr['outstanding_exposure'] > 0) && $loop->first) ? $arr['outstanding_exposure']: $arr['outstanding_exposure'] + $arr['proposed_exposure'] }}" placeholder="Outstanding Exposure (In Mn)" autocomplete="off"/>
                                </div>
                                <div class="col-md-2 center INR">
                                     @if($loop->first)
                                        <label for="txtPassword"><b>Proposed Limit (In Mn)</b></label>
                                    @endif
                                         @if($arr['proposed_exposure'] > 0 && $loop->first)
                                             <a href="javascript:void(0);" class="verify-owner-no" style="top:{{($loop->first) ? '40px;': '9px;' }}"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                         @endif    
                                     <div class="d-flex">
                                        @if($arr['proposed_exposure'] > 0 && $loop->first)
                                          <input type="text" name="proposed_exposure[]" maxlength="20" class="form-control  calTotalExposure float_format proposed_exposureInput"  value="{{($arr['proposed_exposure'] > 0) ? $arr['proposed_exposure'] : ''}}" placeholder="Proposed Limit (In Mn)" />
                                        @endif  
                                           @if($loop->first)
                                                <i class="fa fa-2x fa-plus-circle add-ptpq-block ml-2"  style="color: green;"></i>
                                           @else
                                            <i class="fa fa-2x fa-times-circle remove-ptpq-block" style="color: red;"></i>
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
                                    <input type="text" class="form-control" name="group_company_name[]" value="{{$arrBizData->biz_entity_name}}" readonly/>
                                    
                                </div>
                                <div class="col-md-3 mt-4 INR">
                                        <label for="txtPassword"><b>Sanction Limit (In Mn)</b></label>
                                        <a href="javascript:void(0);" class="verify-owner-no" style="top:39px;"><i class="fa fa-inr" aria-hidden="true"></i></a> 
                                         <input type="text" name="sanction_limit[]" class="form-control float_format" value="" placeholder="Sanction Limit (In Mn)" autocomplete="off"/>
                                </div>
                                <div class="col-md-3 mt-4 INR">
                                     <label for="txtPassword"><b>Outstanding Exposure (In Mn)</b></label>
                                     <a href="javascript:void(0);" class="verify-owner-no" style="top:39px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                     <input type="text" name="outstanding_exposure[]" class="form-control  calTotalExposure float_format" value="" placeholder="Outstanding Exposure (In Mn)" autocomplete="off"/>
                                </div>
                                <div class="col-md-2 mt-4 INR">
                                     <label for="txtPassword"><b>Proposed Limit (In Mn)</b></label>
                                     <a href="javascript:void(0);" class="verify-owner-no" style="top:39px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                     <div class="d-flex">
                                          <input type="text" name="proposed_exposure[]" maxlength="20" class="form-control  calTotalExposure float_format"  value="" placeholder="Proposed Limit (In Mn)" />
                                           <i class="fa fa-2x fa-plus-circle add-ptpq-block ml-2"  style="color: green;"></i>
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
                                <a href="javascript:void(0);" class="verify-owner-no" style="top:10px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                  <input type="text" class="form-control " name="total_exposure" value="{{($arrCamData &&  $arrCamData->total_exposure > 0) ? $arrCamData->total_exposure : ''}}" readonly />
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
                                    <td><b>Maximum Tenor of Invoices/tranch</b></td>
                                    <td value=""></td>
                                    <td><b>Purpose</b></td>
                                    <td>
                                        <input type="text" name="t_o_f_purpose" id="purpose" class="form-control" value="{{isset($arrCamData->t_o_f_purpose) ? $arrCamData->t_o_f_purpose : ''}}">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Takeout</b></td>
                                    <td>
                                        <input type="text" name="t_o_f_takeout" id="takeout" class="form-control" value="{{isset($arrCamData->t_o_f_takeout) ? $arrCamData->t_o_f_takeout : ''}}" @if ($checkDisburseBtn=='showDisburseBtn') readonly="readonly" @endif>
                                    </td>
                                    <td><b>Recourse</b></td>
                                    <td>
                                        <input type="text" name="t_o_f_recourse" id="recourse" class="form-control" value="{{isset($arrCamData->t_o_f_recourse) ? $arrCamData->t_o_f_recourse : ''}}" @if ($checkDisburseBtn=='showDisburseBtn') readonly="readonly" @endif>
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

                <div class="data mt-4">
                    <h2 class="sub-title bg" style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Risk Comments</h2>
                    <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                        <textarea class="form-control" id="risk_comments" name="risk_comments" rows="3" spellcheck="false">{{isset($arrCamData->risk_comments) ? $arrCamData->risk_comments : ''}}</textarea>
                    <!-- </div> -->
                </div>

                <div class="data mt-4">
                    <h2 class="sub-title bg" style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Recommendation and Comments of Credit Manager</h2>
                    <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                        @php 
                        $role_id=Helpers::getUserRole(Auth::user()->user_id);
                        @endphp
                        
                        <textarea @if (in_array($role_id[0]->pivot->role_id ,[config('common.user_role')['SALES'],config('common.user_role')['CPA']])) disabled="true" @endif class="form-control" id="anchor_risk_comments" rows="3" spellcheck="false" name="cm_comment">{{ isset($arrCamData->cm_comment) ? $arrCamData->cm_comment : ''}}</textarea>

                        <div class="clearfix"></div>
                    <!-- </div> -->

                </div>
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
                @if(request()->get('view_only'))
                <button class="btn btn-success pull-right  mt-3" type="Submit"> Save</button>
                @endif
              </form>
            </div>
        </div>
    </div>
</div>
@endsection
{!!Helpers::makeIframePopup('changeAppDisbursStatus','Change App Status', 'modal-md')!!}
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
      CKEDITOR.replace('contigent_observations');
      CKEDITOR.replace('risk_comments');
      CKEDITOR.replace('anchor_risk_comments');
      CKEDITOR.replace('profile_of_company');
      CKEDITOR.replace('rating_rational');

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
            $('input[name*=outstanding_exposure]').each(function(){
                if($.isNumeric($(this).val())){
                    outstandingExposure  = parseFloat(outstandingExposure) + parseFloat($(this).val());
    
                }      
            });
        var proposed =  parseFloat($(".proposed_exposureInput").val());
       //var outstandingExposureCam =  parseFloat($("input[name='outstanding_exposure_cam']").val());
        var outstandingExposure = (!isNaN(outstandingExposure))?outstandingExposure:0;
       // outstandingExposureCam = (!isNaN(outstandingExposureCam))?outstandingExposureCam:0;
        proposed = (!isNaN(proposed))?proposed:0;
        
        $("input[name='total_exposure']").val((proposed+outstandingExposure).toFixed(2));

    }

    $(document).on('input', 'input.calTotalExposure', function(){
           calTotalExposure();
    })
    
    $(document).on('click', '.add-ptpq-block', function(){
    let ptpq_block = '<div class="row mt10 toRemoveDiv">'+
            '<div class="col-md-4">'+
                '<input type="text" name="group_company_name[]" class="form-control" value="" placeholder="Group Company" required>'+
            '</div>'+
            '<div class="col-md-3 INR">'+
                '<a href="javascript:void(0);" class="verify-owner-no" style="top:9px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                '<input type="text" name="sanction_limit[]" class="form-control " value="" placeholder="Sanction Limit (In Mn)" required autocomplete="off">'+
            '</div>'+
            '<div class="col-md-3 INR">'+
                '<a href="javascript:void(0);" class="verify-owner-no" style="top:9px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                '<input type="text" name="outstanding_exposure[]" class="form-control  calTotalExposure" value="" placeholder="Outstanding Exposure (In Mn)" required autocomplete="off">'+
            '</div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-ptpq-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#ptpq-block').append(ptpq_block);
  });

  $(document).on('click', '.remove-ptpq-block', function(){
    $(this).closest('.toRemoveDiv').remove();
        calTotalExposure();
  });


  $(document).on('click', '.dropdown-menu .dropdown-item ', function(argument) {
       var messages = {
              get_group_company_exposure: "{{ URL::route('get_group_company_exposure') }}",
              data_not_found: "{{ trans('error_messages.data_not_found') }}",
              token: "{{ csrf_token() }}",
         };
         var groupid = $(this).find('.groupid').attr('groupid');
         var dataStore = {'groupid': groupid,'_token': messages.token };
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
                    let ptpq_block = '<div class="row mt10 toRemoveDiv">'+
                                '<input type="hidden" name="group_company_expo_id[]" class="form-control" value="'+arr.group_company_expo_id+'" placeholder="Group Company" /><div class="col-md-4">'+
                                    '<input type="text" name="group_company_name[]" class="form-control" value="'+arr.group_company_name+'" placeholder="Group Company" required>'+
                                '</div>'+
                                '<div class="col-md-3 INR">'+
                                    '<a href="javascript:void(0);" class="verify-owner-no" style="top:9px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                                    '<input type="text" name="sanction_limit[]" class="form-control float_format" value="'+arr.sanction_limit+'" placeholder="Sanction Limit (In Mn)" required autocomplete="off">'+
                                '</div>'+
                                '<div class="col-md-3 INR">'+
                                    '<a href="javascript:void(0);" class="verify-owner-no" style="top:9px;"><i class="fa fa-inr" aria-hidden="true"></i></a>'+
                                    '<input type="text" name="outstanding_exposure[]" class="form-control  calTotalExposure float_format" value="'+arr.outstanding_exposure+'" placeholder="Outstanding Exposure (In Mn)" required autocomplete="off">'+
                                '</div>'+
                                '<div class="col-md-2 center">'+
                                    '<i class="fa fa-2x fa-times-circle remove-ptpq-block" style="color: red;"></i>'+
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


$(document).on('submit', '#camForm', function(e) {
   $filledInput = 0;
   $('#ptpq-block input').each(function () {
      if ($(this).val()) $filledInput++;
   })
   if ($filledInput > 1 && !$('input[name="group_company"]').val()) {
       $('input[name="group_company"]').closest('div').after('<span style="color:red"> Group Name is required.</span>');
       $('input[name="group_company"]').focus();
       return false;
   }
   return true;
});
</script>
@endsection