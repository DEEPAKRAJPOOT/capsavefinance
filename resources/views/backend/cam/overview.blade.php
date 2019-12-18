@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">

        <div class="card mt-3">
            <div class="card-body pt-3 pb-3">
                <ul class="float-left mb-0 pl-0">
                    <li><b class="bold">Application ID: {{ $arrRequest['app_id']}}</b> </li>
                   <!--  <li><b class="bold">Credit Head Status :</b> Reject</li> -->

                </ul>
              <!--  <button onclick="downloadCam(49)" class="btn btn-primary float-right btn-sm "> Download</button>
                <ul class="float-right mr-5 mb-0">

                     <li><b class="bold">Requested Loan Amount :</b> 5Lac</li>
                    <li><b class="bold">Assigned Underwriter :</b> abc</li>
 
                </ul>-->

            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body ">
             <form method="POST" action="{{url('cam/cam-information-save')}}"> 
             @csrf

                <input type="hidden" name="app_id" value="{{isset($arrRequest['app_id']) ? $arrRequest['app_id'] : ''}}" />             
                <input type="hidden" name="biz_id" value="{{isset($arrRequest['biz_id']) ? $arrRequest['biz_id'] : ''}}" />             
                <input type="hidden" name="cam_report_id" value="{{isset($arrCamData->cam_report_id) ? $arrCamData->cam_report_id : ''}}" />    

                <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0" border="1">
                    <tbody>
                        <tr>
                            <td width="25%"><b>Name of Borrower</b></td>
                            <td width="25%">{{$arrBizData->biz_entity_name}}</td>
                            <td><b>Contact Person of Key Personal</b></td>
                            <td>{{$arrBizData->ownerName}}</td>
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
                           
                            <td>{{$arrBizData->communicationAddress->addr_1.' '.(isset($arrBizData->address[1]->city_name) ? $arrBizData->address[1]->city_name : '').' '. (isset($arrBizData->address[1]->state->name) ? $arrBizData->address[1]->state->name : '').' '. (isset($arrBizData->address[1]->pin_code) ? $arrBizData->address[1]->pin_code : '')}}
                            </td>




                            <td><b>Communication Address </b></td>

                            <td>{{$arrBizData->registeredAddress->addr_1.' '.(isset($arrBizData->address[0]->city_name) ? $arrBizData->address[0]->city_name : '').' '. (isset($arrBizData->address[0]->state->name) ? $arrBizData->address[0]->state->name : '').' '. (isset($arrBizData->address[0]->pin_code) ? $arrBizData->address[0]->pin_code : '')}}
                            </td>


                        </tr>
                        <tr>
                            <td><b>Factory Address</b></td>
                             <td>{{$arrBizData->factoryAddress->addr_1.' '.(isset($arrBizData->address[4]->city_name) ? $arrBizData->address[4]->city_name : '').' '. (isset($arrBizData->address[4]->state->name) ? $arrBizData->address[4]->state->name : '').' '. (isset($arrBizData->address[4]->pin_code) ? $arrBizData->address[4]->pin_code : '')}}
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
                                <fieldset class="rating" id="goof" name="goof">
                                    <input type="radio" id="star5" name="rating_no" value="5" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 5 ? 'checked' : ''}}>
                                    <label class="full" for="star5" title="Awesome - 5 stars"></label>
                                    <input type="radio" id="star4half" name="rating_no" value="4.5" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 4.5 ? 'checked' : ''}}>
                                    <label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                                    <input type="radio" id="star4" name="rating_no" value="4" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 4 ? 'checked' : ''}}> 
                                    <label class="full" for="star4" title="Pretty good - 4 stars"></label>
                                    <input type="radio" id="star3half" name="rating_no" value="3.5" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 3.5 ? 'checked' : ''}}>
                                    <label class="half" for="star3half" title="Meh - 3.5 stars"></label>
                                    <input type="radio" id="star3" name="rating_no" value="3" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 3 ? 'checked' : ''}}>
                                    <label class="full" for="star3" title="Meh - 3 stars"></label>
                                    <input type="radio" id="star2half" name="rating_no" value="2.5" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 2.5 ? 'checked' : ''}}>
                                    <label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                                    <input type="radio" id="star2" name="rating_no" value="2" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 2 ? 'checked' : ''}}>
                                    <label class="full" for="star2" title="Kinda bad - 2 stars"></label>
                                    <input type="radio" id="star1half" name="rating_no" value="1.5" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 1.5 ? 'checked' : ''}}>
                                    <label class="half" for="star1half" title="Meh - 1.5 stars"></label>
                                    <input type="radio" id="star1" name="rating_no" value="1" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == 1 ? 'checked' : ''}}>
                                    <label class="full" for="star1" title="Sucks big time - 1 star"></label>
                                    <input type="radio" id="starhalf" name="rating_no" value=".5" {{isset($arrCamData->rating_no) && $arrCamData->rating_no == .5 ? 'checked' : ''}}>
                                    <label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                                </fieldset>
                                <textarea class="form-control" id="external_rating_comments" rows="2" name="rating_comment"> {{isset($arrCamData->rating_comment) ? $arrCamData->rating_comment : ''}}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td width="25%"><b>Existing Group Exposure</b></td>
                            <td width="25%"><span class="fa fa-inr" aria-hidden="true" style="position:absolute; margin:12px 5px; "></span><input type="text" class="form-control number_format" maxlength="20" name="existing_exposure" value="{{isset($arrCamData->existing_exposure) ? $arrCamData->existing_exposure : ''}}"></td>
                            <td width="25%"><b>Proposed Group Exposure</b></td>
                            <td width="25%"><span class="fa fa-inr" aria-hidden="true" style="position:absolute; margin:12px 5px; "></span><input type="text" name="proposed_exposure" maxlength="20" class="form-control number_format" value="{{isset($arrCamData->proposed_exposure) ? $arrCamData->proposed_exposure : ''}}" ></td>
                        </tr>
                    </tbody>
                </table>

                <div class="data mt-4">
                    <h2 class="sub-title bg">Terms Of Facility</h2>
                    <div class="pl-4 pr-4 pb-4 pt-2">
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
                                        <input type="text" name="t_o_f_takeout" id="takeout" class="form-control" value="{{isset($arrCamData->t_o_f_takeout) ? $arrCamData->t_o_f_takeout : ''}}">
                                    </td>
                                    <td><b>Recourse</b></td>
                                    <td>
                                        <input type="text" name="t_o_f_recourse" id="recourse" class="form-control" value="{{isset($arrCamData->t_o_f_recourse) ? $arrCamData->t_o_f_recourse : ''}}">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Security</b></td>
                                    <td>
                                        <input type="text" name="t_o_f_security" id="security" class="form-control" value="{{isset($arrCamData->t_o_f_security) ? $arrCamData->t_o_f_security : ''}}" <="" td="">
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
                                 <tr>
                                    <td><b>Brief Profile of the Company</b></td>
                                    <td colspan="3">
                                        <input type="text" name="t_o_f_profile_comp" id="profile_of_company" class="form-control" value="{{isset($arrCamData->t_o_f_profile_comp) ? $arrCamData->t_o_f_profile_comp : ''}}">
                                    </td>
                                </tr>
                                 <tr>
                                    <td><b>Risk Comments</b></td>
                                    <td colspan="3">
                                        <input type="text" name="risk_comments" id="last_approval_status_stipulated" class="form-control" value="{{isset($arrCamData->risk_comments) ? $arrCamData->risk_comments : ''}}">
                                    </td>
                                </tr>
                                 <tr>
                                    <td><b>Recommendation and Comments of Credit Manager</b></td>
                                    <td colspan="3">
                                        <input type="text" name="cm_comment" id="anchor_risk_comments" class="form-control" value="{{isset($arrCamData->cm_comment) ? $arrCamData->cm_comment : ''}}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="data mt-4">
                    <h2 class="sub-title bg">Brief Profile of the Company</h2>
                    <div class="pl-4 pr-4 pb-4 pt-2">
                        <textarea class="form-control" id="profile_of_company" name="t_o_f_profile_comp" rows="3" spellcheck="false" >{{isset($arrCamData->t_o_f_profile_comp) ? $arrCamData->t_o_f_profile_comp : ''}}</textarea>
                    </div>
                </div>

                <div class="data mt-4">
                    <h2 class="sub-title bg">Risk Comments</h2>
                    <div class="pl-4 pr-4 pb-4 pt-2">
                        <textarea class="form-control" id="profile_of_company" name="risk_comments" rows="3" spellcheck="false">{{isset($arrCamData->risk_comments) ? $arrCamData->risk_comments : ''}}</textarea>
                    </div>
                </div>

                <div class="data mt-4">
                    <h2 class="sub-title bg">Recommendation and Comments of Credit Manager</h2>
                    <div class="pl-4 pr-4 pb-4 pt-2">
                        <textarea class="form-control" id="anchor_risk_comments" rows="3" spellcheck="false" name="cm_comment">{{isset($arrCamData->cm_comment) ? $arrCamData->cm_comment : ''}}</textarea>

                        <div class="clearfix"></div>
                    </div>

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
@section('jscript')

@endsection