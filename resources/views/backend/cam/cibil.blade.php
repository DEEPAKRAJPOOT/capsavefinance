@extends('layouts.backend.admin-layout')
@section('content')
<style>
   .isloader{ 
   position: fixed;    
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   background: rgba(0,0,0,.6);
   display: flex;
   flex-wrap: wrap;
   justify-content: center;
   align-content: center;
   z-index: 9;
   }
</style>
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container">
      <div class="card mt-4">
         <div class="card-body">
            <div class="data">
               <!--
                  <h2 class="sub-title bg mb-4"><span class=" mt-2">Company CIBIL</span> <button  class="btn btn-primary  btn-sm float-right"> Upload Document</button></h2>
                  -->
               <h2 class="sub-title bg">Company</h2>
               <div id="pullMsgCommercial"></div>
               <div class="pl-4 pr-4 pb-4 pt-2">
                  <div class="row ">
                     <div class="col-sm-12">
                        <table id="cibil-table" class="table table-striped  no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="cibil-table_info" style="width: 100%;">
                           <thead>
                              <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 118px;" aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending">Sr.No.</th>
                                 <th class="sorting" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 162px;" aria-label="Company: activate to sort column ascending">Company</th>
                                 <th class="sorting" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 96px;" aria-label="PAN: activate to sort column ascending">PAN</th>
                                 <th class="sorting" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 105px;" aria-label="Rank: activate to sort column ascending">Score</th>
                                 <th class="numericCol sorting" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 209px;" aria-label="Action: activate to sort column ascending">Action</th>
                              </tr>
                           </thead>
                           <tbody>
                              @php
                              $i = 0;
                              @endphp
                              @foreach($arrCompanyDetail as $arr)
                              @php
                              $i++;
                              @endphp
                              <tr role="row" class="odd">
                                 <td class="sorting_1" width="15%">{{$i}}</td>
                                 <td width="20%">{{$arr->biz_entity_name}}</td>
                                 <td width="20%">{{$arr->pan_gst_hash}}</td>
                                 <td width="20%" id="cibilScore{{$arr->biz_id}}">{{$arr->cibil_score}}</td>
                                 <td class=" numericCol" width="25%">
                                    <button class="btn btn-success btn-sm" supplier="49" id="cibilScoreBtn{{$arr->biz_id}}" onclick="pull_cibil_commercialModal({{$arr->biz_id}})">@if ($arr->is_cibil_pulled == 1) Re-Pull @else Pull @endif</button>
                                    @if ($arr->is_cibil_pulled == 1)
                                    <button class="btn btn-warning btn-sm" supplier="49" onclick="downloadCommercialCibil({{$arr->biz_id}})">View Report</button>
                                    @endif
                                    <!--  <button class="btn btn-info btn-sm" supplier="49" onclick="pull_cibil_org(this)">UPLOAD</button> -->
                                 </td>
                              </tr>
                              @endforeach
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
            <div class="data mt-4">
               <h2 class="sub-title bg ">Director / Proprietor / Owner / Partner</h2>
               <div id="pullMsg"></div>
               <div class="pl-4 pr-4 pb-4 pt-2">
                  <div class="row ">
                     <div class="col-sm-12">
                        <table id="cibil-dpop-table" class="table table-striped  no-footer overview-table"" cellspacing="0" width="100%" role="grid" aria-describedby="cibil-dpop-table_info" style="width: 100%;">
                           <thead>
                              <tr role="row">
                                 <th class="sorting_asc" tabindex="0" width="15%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1"  aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending">Sr.No.</th>
                                 <th class="sorting" tabindex="0" width="20%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1"  aria-label="Name: activate to sort column ascending">Name</th>
                                 <th class="sorting" tabindex="0" width="20%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1"  aria-label="PAN: activate to sort column ascending">PAN</th>
                                 <th class="sorting" tabindex="0" width="20%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1" aria-label="Score: activate to sort column ascending">Score</th>
                                 <th class="sorting" tabindex="0" width="25%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1"  aria-label="Action: activate to sort column ascending">Action</th>
                              </tr>
                           </thead>
                           <tbody>
                              @php
                              $i = 0;
                              $defpro = 0;
                              @endphp
                              @foreach($arrCompanyOwnersData as $arr)
                              @php
                                 $i++;
                              @endphp
                              @if ($arr->cibil_score < 500 && $arr->is_cibil_pulled == 1)
                                    @php ($defpro++)
                              @endif         
                              <tr role="row" class="odd">
                                 <td class="sorting_1" width="15%">{{$i}}</td>
                                 <td width="20%">{{$arr->first_name." ".$arr->last_name}}</td>
                                 <td width="20%">{{$arr->pan_gst_hash}}</td>
                                 <td width="20%" id="cibilScore{{$arr->biz_owner_id}}">{{$arr->cibil_score}}</td>
                                 <td class=" numericCol" width="25%">
                                    <button class="btn btn-success btn-sm" id="cibilScoreBtn{{$arr->biz_owner_id}}" supplier="49" onclick="pull_cibil_promoterModal({{$arr->biz_owner_id}})">@if ($arr->is_cibil_pulled == 1) Re-Pull @else Pull @endif</button>
                                    @if ($arr->is_cibil_pulled == 1)
                                    <button class="btn btn-warning btn-sm" supplier="49" onclick="downloadPromoterCibil({{$arr->biz_owner_id}})" >View Report</button>
                                    @endif
                                    <!--
                                       <button class="btn btn-info btn-sm" supplier="49" onclick="">UPLOAD</button>
                                       -->
                                 </td>
                              </tr>
                              @endforeach  
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
            <div class="data mt-4">
               <h2 class="sub-title bg">Hygiene Check</h2>
               <div class="pl-4 pr-4 pb-4 pt-2">
               <form method="POST" action="{{url('cam/cam-hygiene-save')}}"> 
                  @csrf
                  <input type="hidden" name="app_id" value="{{isset($arrRequest['app_id']) ? $arrRequest['app_id'] : ''}}" />             
                <input type="hidden" name="biz_id" value="{{isset($arrRequest['biz_id']) ? $arrRequest['biz_id'] : ''}}" />
                <input type="hidden" name="cam_hygiene_id" value="{{isset($arrHygieneData->cam_hygiene_id) ? $arrHygieneData->cam_hygiene_id : ''}}" />
                  <table class="table overview-table">
                     <tbody>
                        <tr>
                           <td style="width:30%">
                              <b>
                                 Whether Appearing in any of the below
                                 <ul>
                                    <li>CIBIL Defaulters List</li>
                                 </ul>
                              </b>
                           </td>
                           <td>
                              <table style="width: 100%;">
                                 <tbody>
                                    <tr>
                                       <td colspan="4">
                                          <div class="form-check" style="display: inline-block; margin-right:10px;">
                                             <label for="cibil_check_yes" class="form-check-label">
                                             <input type="radio" id="cibil_check_yes" class="form-check-input" name="cibil_check" value="Yes" {{((isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'Yes') ||($defpro > 0)) ? 'checked' : ''}} onclick="showDefPro('yes')">Yes
                                             <i class="input-helper"></i></label>
                                          </div>

                                          <div class="form-check" style="display: inline-block;">
                                             <label for="cibil_check_no" class="form-check-label">
                                             <input type="radio" id="cibil_check_no" class="form-check-input" name="cibil_check" value="No" {{((isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'No') || ((!isset($arrHygieneData->cibil_check)) && ($defpro == 0))) ? 'checked' : ''}} onclick="showDefPro('no')">No
                                             <i class="input-helper"></i></label>
                                          </div>
                                          <p id="defProHeading"  style="margin: 0; display:@if ((isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'Yes') ||($defpro > 0 && (!isset($arrHygieneData->cibil_check)))) ? show  @else none @endif">CIBIL Analysis (for promoters / guarantors):</p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>

                                          <table style="width: 100%;">
                                             <tbody>
                                                <tr>
                                                </tr>
                                             </tbody>
                                             <thead>
                                                <tr id="defProTr" style="display:@if ((isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'Yes') ||($defpro > 0 && (!isset($arrHygieneData->cibil_check)))) ? show  @else none @endif">
                                                   <th>Name</th>
                                                   <th class="white-space">PAN Number</th>
                                                   <th class="white-space">CIBIL Rank/Score</th>
                                                   <th>Remarks</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                               @php ($count = 0)
                                               @foreach($arrCompanyOwnersData as $arr)
                                                   @if ($arr->cibil_score < 500 && $arr->is_cibil_pulled == 1)
                                                       @php ($count++)
                                                         <tr id="defProDetailsTr" style="display:@if ((isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'Yes') ||($defpro > 0 && (!isset($arrHygieneData->cibil_check)))) ? show  @else none @endif">
                                                            <td>{{$arr->first_name." ".$arr->last_name}}</td>
                                                            <td name="promoterPan[]">
                                                                  <input type="text" name="promoterPan[]" value="{{$arr->pan_gst_hash}}" class="form-control" readonly >   
                                                            </td>
                                                            <td>{{$arr->cibil_score}}</td>
                                                            <td>
                                                                <input type="text" name="remarks[]" id="remarks" class="form-control" value="{{$arrHygieneData->remarks[$arr->pan_gst_hash]  ?? ''}}">
                                                                 
                                                            </td>
                                                         </tr>
                                                   @endif
                                                @endforeach

                                                

                                                    <tr id="noDefProTr" style="display:@if (($count == 0 && (!isset($arrHygieneData->cibil_check))) || (isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'No')) ? show  @else none @endif">
                                                         <td>No defaulters found</td>
                                                          <td>
                                                             <input type="text" name="comment" id="remarks" class="form-control" value="{{$arrHygieneData->comment  ?? ''}}">
                                                         </td>
                                                   </tr> 
                                                  
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                   
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td><b>RBI Willful Defaulters List </b></td>
                           <td><input type="text" id="rbi_willfull_defaulters_list" class="form-control from-inline" value="{{isset($arrHygieneData->rbi_willful_defaulters) ? $arrHygieneData->rbi_willful_defaulters : ''}}" name="rbi_willful_defaulters"></td>
                        </tr>
                        <tr>
                           <td><b>Watchoutinvestors </b></td>
                           <td><input type="text" id="watch_out_investors" class="form-control from-inline" name="watchout_investors" value="{{isset($arrHygieneData->watchout_investors) ? $arrHygieneData->watchout_investors : ''}}"></td>
                        </tr>
                        <tr>
                           <td><b>Politically Exposed Person</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="politically_check" id="politically_check_yes" value="Yes" {{isset($arrHygieneData->politically_check) && $arrHygieneData->politically_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="politically_check" id="politically_check_no" value="No" {{!isset($arrHygieneData->politically_check) || $arrHygieneData->politically_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="pol_exp_per_cmnt" value="{{isset($arrHygieneData->pol_exp_per_cmnt) ? $arrHygieneData->pol_exp_per_cmnt : ''}}" name="pol_exp_per_cmnt">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Any CDR/BIFR History</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="cdr_check" id="cdr_check_yes" value="Yes" {{isset($arrHygieneData->cdr_check) && $arrHygieneData->cdr_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="cdr_check" id="cdr_check_no" value="No" {{!isset($arrHygieneData->cdr_check) || $arrHygieneData->cdr_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="cdr_cmnt" name="cdr_cmnt" value="{{isset($arrHygieneData->cdr_cmnt) ? $arrHygieneData->cdr_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>UNSC List</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="unsc_check" id="UNSC_yes" value="Yes" {{isset($arrHygieneData->unsc_check) && $arrHygieneData->unsc_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="unsc_check" id="UNSC_no" value="No" {{!isset($arrHygieneData->unsc_check) || $arrHygieneData->unsc_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="unsc_cmnt" name="unsc_cmnt" value="{{isset($arrHygieneData->unsc_cmnt) ? $arrHygieneData->unsc_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Any  NPA  History  of the  Account  Holder  or Any  of  the  Directors  / Partners  /  Guarantors/ assoc</b>iate concerns? </td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="npa_history_check" id="npa_history_account_holder_check_yes" value="Yes" {{isset($arrHygieneData->npa_history_check) && $arrHygieneData->npa_history_check == 'Yes' ? 'checked' : ''}} >Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="npa_history_check" id="npa_history_account_holder_check_no" value="No" {{!isset($arrHygieneData->npa_history_check) || $arrHygieneData->npa_history_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="npa_history_cmnt" name="npa_history_cmnt" value="{{isset($arrHygieneData->npa_history_cmnt) ? $arrHygieneData->npa_history_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Any Corporate Governance issues?</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="cop_gov_check" id="cop_gov_issues_check_yes" value="Yes" {{isset($arrHygieneData->cop_gov_check) && $arrHygieneData->cop_gov_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="cop_gov_check" id="cop_gov_issues_check_no" value="No" {{!isset($arrHygieneData->cop_gov_check) || $arrHygieneData->cop_gov_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="cop_gov_issues_cmnt" name="   cop_gov_issues_cmnt" value="{{isset($arrHygieneData->cop_gov_issues_cmnt) ? $arrHygieneData->cop_gov_issues_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Change in Auditor</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="change_auditor_check" id="change_in_auditor_check_yes" value="Yes" {{isset($arrHygieneData->change_auditor_check) && $arrHygieneData->change_auditor_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="change_auditor_check" id="change_in_auditor_check_no" value="No" {{!isset($arrHygieneData->change_auditor_check) || $arrHygieneData->change_auditor_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="change_in_audit_cmnt" name="change_in_audit_cmnt" value="{{isset($arrHygieneData->change_in_audit_cmnt) ? $arrHygieneData->change_in_audit_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Any Auditor’s Qualifications as per latest Audited Financials</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="change_audit_qual_check" id="auditor_qualification_check_yes" value="Yes" {{isset($arrHygieneData->change_audit_qual_check) && $arrHygieneData->change_audit_qual_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="change_audit_qual_check" id="auditor_qualification_check_no" value="No" {{!isset($arrHygieneData->change_audit_qual_check) || $arrHygieneData->change_audit_qual_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" name="change_audit_qual_cmnt" id="auditor_qualification_comments" value="{{isset($arrHygieneData->change_audit_qual_cmnt) ? $arrHygieneData->change_audit_qual_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Any delay in repayment of Statutory Dues (As per tax audit report/ auditor’s report)</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="audit_report_check" id="audit_report_check_yes" value="Yes" {{isset($arrHygieneData->audit_report_check) && $arrHygieneData->audit_report_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="audit_report_check" id="audit_report_check_no" value="No" {{!isset($arrHygieneData->audit_report_check) || $arrHygieneData->audit_report_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="audit_report_cmnt" name="audit_report_cmnt" value="{{isset($arrHygieneData->audit_report_cmnt) ? $arrHygieneData->audit_report_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Availability of adequate insurance cover for stock and fixed assets</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="adeq_ins_check" id="adeq_ins_check_yes" value="Yes" {{isset($arrHygieneData->adeq_ins_check) && $arrHygieneData->adeq_ins_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="adeq_ins_check" id="adeq_ins_check_no" value="No" {{!isset($arrHygieneData->adeq_ins_check) || $arrHygieneData->adeq_ins_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="adeq_ins_cmnt" name="adeq_ins_cmnt" value="{{isset($arrHygieneData->adeq_ins_cmnt) ? $arrHygieneData->adeq_ins_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Any other Negative news reported on public domain (by way of Google search etc.)</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="neg_news_report_check" id="negative_news_report_yes" value="Yes" {{isset($arrHygieneData->neg_news_report_check) && $arrHygieneData->neg_news_report_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="neg_news_report_check" id="negative_news_report_no" value="No" {{!isset($arrHygieneData->neg_news_report_check) || $arrHygieneData->neg_news_report_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div>
                              <input type="text" class="form-control from-inline" id="neg_news_report_cmnt" name="neg_news_report_cmnt" value="{{isset($arrHygieneData->neg_news_report_cmnt) ? $arrHygieneData->neg_news_report_cmnt : ''}}">
                           </td>
                        </tr>
                     </tbody>
                  </table>
                  <div class="row">
                     <div class="col-md-12 mt-3">
                        <div class="form-group text-right">
                           <button  class="btn btn-primary btn-ext submitBtnBank" data-toggle="modal" data-target="#myModal">Save</button>                                        
                        </div>
                     </div>
                  </div>
               </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="pull_cibil_promoterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <input type="hidden"  id="biz_owner_id">
            <p>Are you sure you want to pull the cibil score for this promoter?</p>
            <p id="pullMsg"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-success btn-sm" id="cibilScoreBtn" onclick="pull_cibil_promoter()">Yes</button>
         </div>
      </div>
   </div>
</div
   >
<div class="modal fade" id="download_cibil_promoterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body table-responsive" id="download_user_cibil" style="max-height: 500px;">
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="pull_cibil_commercialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <input type="hidden"  id="biz_id">
            <p>Are you sure you want to pull the cibil score for this Company?</p>
            <p id="pullMsg"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-success btn-sm" id="cibilScoreBtn" onclick="pull_cibil_commercial()">Yes</button>
         </div>
      </div>
   </div>
</div>
<div class="isloader" style="display:none;">  
   <img src="http://rent.local/backend/assets/images/loader.gif">
</div>
@endsection
@section('jscript')
<script>
   function pull_cibil_promoterModal(biz_owner_id) {
      $("#pull_cibil_promoterModal").modal("show");
      $("#biz_owner_id").val(biz_owner_id);
     
   }
   
   
   
   function pull_cibil_promoter(){
         var biz_owner_id = $("#biz_owner_id").val();
       $("#pull_cibil_promoterModal").modal("hide");
            $(".isloader").show();
         var messages = {
              chk_user_cibil: "{{ URL::route('chk_user_cibil') }}",
              data_not_found: "{{ trans('error_messages.data_not_found') }}",
              token: "{{ csrf_token() }}",
         };
         var dataStore = {'biz_owner_id': biz_owner_id,'_token': messages.token };
         var postData = dataStore;
          jQuery.ajax({
             url: messages.chk_user_cibil,
             method: 'post',
             dataType: 'json',
             data: postData,
             error: function (xhr, status, errorThrown) {
                               // alert(errorThrown);
             },
             success: function (data) {
              $(".isloader").hide();
                var status =  data['status'];
                var html = '<div class="alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+data['message']+'</div>';
                  if(status==1)
                    {  
                         $("#pullMsg").html(html);
                         $("#cibilScore"+biz_owner_id).text(data['cibilScore']);
                         if($("#cibilScoreBtn"+biz_owner_id).text() == ' Pull '){
                               $("#cibilScoreBtn"+biz_owner_id).text("Re-Pull");
                               $("#cibilScoreBtn"+biz_owner_id).after("   <button class='btn btn-warning btn-sm' onclick='downloadPromoterCibil("+biz_owner_id+")' >View Report</button>");
                          }                              
                    }else{
                         $("#pullMsg").html(html);
                   }                          
            }
       });
   }   
   
   
   function downloadPromoterCibil(biz_owner_id) {
      var messages = {
              download_user_cibil: "{{ URL::route('download_user_cibil') }}",
              data_not_found: "{{ trans('error_messages.data_not_found') }}",
              token: "{{ csrf_token() }}",
         };
         var dataStore = {'biz_owner_id': biz_owner_id,'_token': messages.token };
         var postData = dataStore;
      jQuery.ajax({
             url: messages.download_user_cibil,
             method: 'post',
             dataType: 'json',
             data: postData,
             error: function (xhr, status, errorThrown) {
                               // alert(errorThrown);
             },
             success: function (data) {
                var status =  data['status'];
                  $("#download_cibil_promoterModal").modal("show");
                  $("#download_user_cibil").html('');
                  if(status==1)
                    {  
                           $("#download_user_cibil").html(window.atob(data['cibilScoreData']));
                    }else{
                           $("#download_user_cibil").text(data['cibilScoreData']);
                   }                          
                    
            }
      });
   }
   
   
   
   
   function pull_cibil_commercialModal(biz_id) {
      $("#pull_cibil_commercialModal").modal("show");
      $("#biz_id").val(biz_id);
   }
   
   
   function pull_cibil_commercial(){
         var biz_id = $("#biz_id").val();
         $("#pull_cibil_commercialModal").modal("hide");
            $(".isloader").show();
         var messages = {
              chk_commerical_cibil: "{{ URL::route('chk_commerical_cibil') }}",
              data_not_found: "{{ trans('error_messages.data_not_found') }}",
              token: "{{ csrf_token() }}",
         };
         var dataStore = {'biz_id': biz_id,'_token': messages.token };
         var postData = dataStore;
          jQuery.ajax({
             url: messages.chk_commerical_cibil,
             method: 'post',
             dataType: 'json',
             data: postData,
             error: function (xhr, status, errorThrown) {
                               // alert(errorThrown);
             },
             success: function (data) {
              $(".isloader").hide();
                var status =  data['status'];
                var html = '<div class="alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+data['message']+'</div>';
                  if(status==1)
                    {  
                         $("#pullMsgCommercial").html(html);
                         $("#cibilScore"+biz_id).text(data['cibilScore']);
                         if($("#cibilScoreBtn"+biz_id).text() == ' Pull '){
                              $("#cibilScoreBtn"+biz_id).text("Re-Pull");
                              $("#cibilScoreBtn"+biz_id).after("   <button class='btn btn-warning btn-sm' onclick='downloadCommercialCibil("+biz_id+")' >View Report</button>");
                         }                            
                    }else{
                         $("#pullMsgCommercial").html(html);
                   }                          
                    
            }
   
       });
   }   
   
   function downloadCommercialCibil(biz_id){
   var messages = {
              download_commerial_cibil: "{{ URL::route('download_commerial_cibil') }}",
              data_not_found: "{{ trans('error_messages.data_not_found') }}",
              token: "{{ csrf_token() }}",
         };
         var dataStore = {'biz_id': biz_id,'_token': messages.token };
         var postData = dataStore;
      jQuery.ajax({
             url: messages.download_commerial_cibil,
             method: 'post',
             dataType: 'json',
             data: postData,
             error: function (xhr, status, errorThrown) {
                               // alert(errorThrown);
             },
             success: function (data) {
                var status =  data['status'];
                  $("#download_cibil_promoterModal").modal("show");
                  $("#download_user_cibil").html('');
                  if(status==1)
                    {  
                        var scoreDetails = data['cibilScoreData'];
                           
                        var html = '<table class="table  table-striped table-hover overview-table"><thead class="thead-primary"><tr><th width="10%" class="text-left" colspan="2">CRIF Score Report</th></tr></thead><tbody><tr><th>Report Id</th><td>'+scoreDetails['report-id']+'</td></tr><tr><th>Application-id</th><td>'+scoreDetails['application-id']+'</td></tr><tr><th>Status</th><td>'+scoreDetails['status']+'</td></tr><tr><th>Name</th><td>'+scoreDetails['name']+'</td></tr><tr><th>Legal Constitution</th><td>'+scoreDetails['legal-constitution']+'</td></tr><tr><th>Loan Purpose</th><td>'+scoreDetails['loan-purpose']+'</td></tr><tr><th>Pan No</th><td>'+scoreDetails['pan-no']+'</td></tr><tr><th>cin</th><td>'+scoreDetails['cin']+'</td></tr><tr><th>Score</th><td>'+scoreDetails['score']+'</td></tr><tr><th>Purpose</th><td>'+scoreDetails['purpose']+'</td></tr><tr><th>Ownership Type</th><td>'+scoreDetails['ownership-type']+'</td></tr><tr><th>Own Ioi Indicator</th><td>'+scoreDetails['own-ioi-indicator']+'</td></tr><tr><th>Credit Facility Group</th><td>'+scoreDetails['credit-facility-group']+'</td></tr><tr><th>Standard Acc Cnt</th><td>'+scoreDetails['standard-acc-cnt']+'</td></tr><tr><th>Standard Outstanding Amount Percentage</th><td>'+scoreDetails['standard-outstanding-amount-percentage']+'</td></tr><tr><th>Sub Standard Acc Cnt</th><td>'+scoreDetails['sub-standard-acc-cnt']+'</td></tr><tr><th>Sub Standard Outstanding Amount Percentage</th><td>'+scoreDetails['sub-standard-outstanding-amount-percentage']+'</td></tr><tr><th>Special Mention Acc Cnt</th><td>'+scoreDetails['special-mention-acc-cnt']+'</td></tr><tr><th>Special Mention Outstanding Amount Percentage</th><td>'+scoreDetails['special-mention-outstanding-amount-percentage']+'</td></tr><tr><th>Doubtful Acc Cnt</th><td>'+scoreDetails['doubtful-acc-cnt']+'</td></tr><tr><th>Doubtful Outstanding Amount Percentage</th><td>'+scoreDetails['doubtful-outstanding-amount-percentage']+'</td></tr><tr><th>Loss Acc Cnt</th><td>'+scoreDetails['loss-acc-cnt']+'</td></tr><tr><th>Loss Outstanding Amount Percentage</th><td>'+scoreDetails['loss-outstanding-amount-percentage']+'</td></tr><tr><th>Enquiry Cnt Last Three Months</th><td>'+scoreDetails['enquiry-cnt-last-three-mnths']+'</td></tr><tr><th>Enquiry Cnt Between Three To Six Months</th><td>'+scoreDetails['enquiry-cnt-between-three-to-six-mnths']+'</td></tr><tr><th>enquiry-cnt-between-six-to-nine Months</th><td>'+scoreDetails['enquiry-cnt-between-six-to-nine-mnths']+'</td></tr><tr><th>enquiry-cnt-between-nine-to-twelve-mnths</th><td>'+scoreDetails['enquiry-cnt-between-nine-to-twelve-mnths']+'</td></tr><tr><th>enquiry-cnt-more-than-twelve-mnths</th><td>'+scoreDetails['enquiry-cnt-more-than-twelve-mnths']+'</td></tr><tr><th>own-active-acc-cnt</th><td>'+scoreDetails['own-active-acc-cnt']+'</td></tr><tr><th>own-closed-acc-cnt</th><td>'+scoreDetails['own-closed-acc-cnt']+'</td></tr><tr><th>own-delinquent-acc-cnt</th><td>'+scoreDetails['own-delinquent-acc-cnt']+'</td></tr><tr><th>totalaccts</th><td>'+scoreDetails['totalaccts']+'</td></tr><tr><th>lender</th><td>'+scoreDetails['lender']+'</td></tr><tr><th>other-active-acc-cnt</th><td>'+scoreDetails['other-active-acc-cnt']+'</td></tr><tr><th>other-closed-acc-cnt</th><td>'+scoreDetails['other-closed-acc-cnt']+'</td></tr><tr><th>other-delinquent-acc-cnt</th><td>'+scoreDetails['other-delinquent-acc-cnt']+'</td></tr><tr><th>new-accounts-in-last-twelve-months</th><td>'+scoreDetails['new-accounts-in-last-twelve-months']+'</td></tr><tr><th>closed-accounts-in-last-twelve-months</th><td>'+scoreDetails['closed-accounts-in-last-twelve-months']+'</td></tr><tr><th>new-delinq-account-in-last-twelve-months</th><td>'+scoreDetails['new-delinq-account-in-last-twelve-months']+'</td></tr><tr><th>length-of-credit-history-year</th><td>'+scoreDetails['length-of-credit-history-year']+'</td></tr><tr><th>length-of-credit-history-month</th><td>'+scoreDetails['length-of-credit-history-month']+'</td></tr><tr><th>recency-in-months</th><td>'+scoreDetails['recency-in-months']+'</td></tr><tr><th>suitfiled-acc-cnt</th><td>'+scoreDetails['suitfiled-acc-cnt']+'</td></tr><tr><th>written-off-acc-cnt</th><td>'+scoreDetails['written-off-acc-cnt']+'</td></tr><tr><th>wilful-defaulter-acc-cnt</th><td>'+scoreDetails['wilful-defaulter-acc-cnt']+'</td></tr><tr><th>restructred-acc-cnt</th><td>'+scoreDetails['restructred-acc-cnt']+'</td></tr><tr><th>invoked-acc-cnt</th><td>'+scoreDetails['invoked-acc-cnt']+'</td></tr><tr><th>devolved-acc-cnt</th><td>'+scoreDetails['devolved-acc-cnt']+'</td></tr><tr><th>sanctioned-amount-range</th><td>'+scoreDetails['sanctioned-amount-range']+'</td></tr><tr><th>delinquent-acc-cnt</th><td>'+scoreDetails['delinquent-acc-cnt']+'</td></tr><tr><th>top-five-delinquent-tradelines-list</th><td>'+scoreDetails['top-five-delinquent-tradelines-list']+'</td></tr></tbody></table>';
                           $("#download_user_cibil").html(html);
                    }else{
                          
                           $("#download_user_cibil").text(data['cibilScoreData']);
                   }                          
                    
            }
      });
   }
   

   function showDefPro(value){
      if(value == 'yes'){
         $("#defProHeading").show();
         $("#defProTr").show();
         $("#defProDetailsTr").show();
         $("#noDefProTr").hide();
      }else if(value == 'no'){
         $("#defProHeading").hide();
         $("#defProTr").hide();
         $("#defProDetailsTr").hide();
         $("#noDefProTr").show();

      }
   }
</script>
@endsection