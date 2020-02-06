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
                              @endphp
                              @foreach($arrCompanyOwnersData as $arr)
                              @php
                                 $i++;
                              @endphp
                                   
                              <tr role="row" class="odd">
                                 <td class="sorting_1" width="15%">{{$i}}</td>
                                 <td width="20%">{{$arr->first_name." ".$arr->last_name}}</td>
                                 <td width="20%">{{$arr->pan_number}}</td>
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
               <form method="POST" action="{{route('cam_hygiene_save')}}"> 
                  @csrf
                  <input type="hidden" name="app_id" value="{{isset($arrRequest['app_id']) ? $arrRequest['app_id'] : ''}}" />             
                <input type="hidden" name="biz_id" value="{{isset($arrRequest['biz_id']) ? $arrRequest['biz_id'] : ''}}" />
                <input type="hidden" name="cam_hygiene_id" value="{{isset($arrHygieneData->cam_hygiene_id) ? $arrHygieneData->cam_hygiene_id : ''}}" />
                  <table class="table overview-table">
                     <tbody>
                        <tr class="sub-title bg">
                           <th>Parameter</th>
                        <th>Deviation</th>
                        <th>Remarks</th>
                     </tr>
                     <tr>
                           <td><b>CFPL Defaulter List</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="cfpl_default_check" id="cfpl_default_check_yes" value="Yes" {{isset($arrHygieneData->cfpl_default_check) && $arrHygieneData->cfpl_default_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="cfpl_default_check" id="cfpl_default_check_no" value="No" {{!isset($arrHygieneData->cfpl_default_check) || $arrHygieneData->cfpl_default_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="cfpl_default_cmnt" name="cfpl_default_cmnt" value="{{isset($arrHygieneData->cfpl_default_cmnt) ? $arrHygieneData->cfpl_default_cmnt : ''}}">
                           </td>
                        </tr>

                          <tr>
                           <td><b>RBI Willful Defaulters List </b></td>

                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label for="cibil_check_yes" class="form-check-label">
                                 <input type="radio" id="cibil_check_yes" class="form-check-input" name="cibil_check" value="Yes" {{((isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'Yes')) ? 'checked' : ''}} >Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label for="cibil_check_no" class="form-check-label">
                                 <input type="radio" id="cibil_check_no" class="form-check-input" name="cibil_check" value="No"  {{!isset($arrHygieneData->cibil_check) || $arrHygieneData->cibil_check == 'No' ? 'checked' : ''}} >No
                                 <i class="input-helper"></i></label>
                              </div>
                              </td>
                           <td><input type="text" id="rbi_willfull_defaulters_list" class="form-control from-inline" value="{{isset($arrHygieneData->rbi_willful_defaulters) ? $arrHygieneData->rbi_willful_defaulters : ''}}" name="rbi_willful_defaulters"></td>
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
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="cdr_cmnt" name="cdr_cmnt" value="{{isset($arrHygieneData->cdr_cmnt) ? $arrHygieneData->cdr_cmnt : ''}}">
                           </td>
                        </tr>
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
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label for="cibil_check_yes" class="form-check-label">
                                 <input type="radio" id="cibil_check_yes" class="form-check-input" name="cibil_defaulters_chk" value="Yes" {{((isset($arrHygieneData->cibil_defaulters_chk) && $arrHygieneData->cibil_defaulters_chk == 'Yes')) ? 'checked' : ''}} >Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label for="cibil_check_no" class="form-check-label">
                                 <input type="radio" id="cibil_check_no" class="form-check-input" name="cibil_defaulters_chk" value="No"  {{!isset($arrHygieneData->cibil_defaulters_chk) || $arrHygieneData->cibil_defaulters_chk == 'No' ? 'checked' : ''}} >No
                                 <i class="input-helper"></i></label>
                              </div>
                              </td>
                              <td>
                               <input type="text" name="comment" class="form-control" value="{{$arrHygieneData->comment  ?? ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Watchoutinvestors </b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label for="cibil_check_yes" class="form-check-label">
                                 <input type="radio" id="cibil_check_yes" class="form-check-input" name="watchout_investors_chk" value="Yes" {{((isset($arrHygieneData->watchout_investors_chk) && $arrHygieneData->watchout_investors_chk == 'Yes')) ? 'checked' : ''}} >Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label for="cibil_check_no" class="form-check-label">
                                 <input type="radio" id="cibil_check_no" class="form-check-input" name="watchout_investors_chk" value="No"  {{!isset($arrHygieneData->watchout_investors_chk) || $arrHygieneData->watchout_investors_chk == 'No' ? 'checked' : ''}} >No
                                 <i class="input-helper"></i></label>
                              </div>
                              </td>
                           <td><input type="text" id="watch_out_investors" class="form-control from-inline" name="watchout_investors" value="{{isset($arrHygieneData->watchout_investors) ? $arrHygieneData->watchout_investors : ''}}"></td>
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
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="neg_news_report_cmnt" name="neg_news_report_cmnt" value="{{isset($arrHygieneData->neg_news_report_cmnt) ? $arrHygieneData->neg_news_report_cmnt : ''}}">
                           </td>
                        </tr>

                        <tr class="sub-title bg">
                           <th></th>
                        <th></th>
                        <th></th>
                     </tr>
                       
                     <tr>
                           <td><b>Satisfactory contact point verification</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="contact_point_check" id="contact_point_check_yes" value="Yes" {{isset($arrHygieneData->contact_point_check) && $arrHygieneData->contact_point_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="contact_point_check" id="contact_point_check_no" value="No" {{!isset($arrHygieneData->contact_point_check) || $arrHygieneData->contact_point_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="contact_point_cmnt" name="contact_point_cmnt" value="{{isset($arrHygieneData->contact_point_cmnt) ? $arrHygieneData->contact_point_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Satisfactory banker reference</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="bank_ref_check" id="bank_ref_check_yes" value="Yes" {{isset($arrHygieneData->bank_ref_check) && $arrHygieneData->bank_ref_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="bank_ref_check" id="bank_ref_check_no" value="No" {{!isset($arrHygieneData->bank_ref_check) || $arrHygieneData->bank_ref_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="bank_ref_cmnt" name="bank_ref_cmnt" value="{{isset($arrHygieneData->bank_ref_cmnt) ? $arrHygieneData->bank_ref_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Satisfactory trade reference</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="trade_ref_check" id="trade_ref_check_yes" value="Yes" {{isset($arrHygieneData->trade_ref_check) && $arrHygieneData->trade_ref_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="trade_ref_check" id="trade_ref_check_no" value="No" {{!isset($arrHygieneData->trade_ref_check) || $arrHygieneData->trade_ref_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="trade_ref_cmnt" name="trade_ref_cmnt" value="{{isset($arrHygieneData->trade_ref_cmnt) ? $arrHygieneData->trade_ref_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                        <tr class="sub-title bg">
                           <th colspan="3">Other</th>
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
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="pol_exp_per_cmnt" value="{{isset($arrHygieneData->pol_exp_per_cmnt) ? $arrHygieneData->pol_exp_per_cmnt : ''}}" name="pol_exp_per_cmnt">
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
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="unsc_cmnt" name="unsc_cmnt" value="{{isset($arrHygieneData->unsc_cmnt) ? $arrHygieneData->unsc_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Any  NPA  History  of the  Account  Holder  or Any  of  the  Directors  / Partners  /  Guarantors/ associate concerns?</b> </td>
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
                              </div></td>                           
                           <td>
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
                              </div></td>                           
                           <td>
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
                              </div></td>                           
                           <td>
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
                              </div></td>                           
                           <td>
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
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="audit_report_cmnt" name="audit_report_cmnt" value="{{isset($arrHygieneData->audit_report_cmnt) ? $arrHygieneData->audit_report_cmnt : ''}}">
                           </td>
                        </tr>
                                              
                       
                           <td><b>Negative Industry Segment</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="neg_industry_check" id="neg_industry_check_yes" value="Yes" {{isset($arrHygieneData->neg_industry_check) && $arrHygieneData->neg_industry_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="neg_industry_check" id="neg_industry_check_no" value="No" {{!isset($arrHygieneData->neg_industry_check) || $arrHygieneData->neg_industry_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="neg_industry_cmnt" name="neg_industry_cmnt" value="{{isset($arrHygieneData->neg_industry_cmnt) ? $arrHygieneData->neg_industry_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Exposure to sensitive sectors</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="senstive_sector_check" id="senstive_sector_check_yes" value="Yes" {{isset($arrHygieneData->senstive_sector_check) && $arrHygieneData->senstive_sector_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="senstive_sector_check" id="senstive_sector_check_no" value="No" {{!isset($arrHygieneData->senstive_sector_check) || $arrHygieneData->senstive_sector_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="senstive_sector_cmnt" name="senstive_sector_cmnt" value="{{isset($arrHygieneData->senstive_sector_cmnt) ? $arrHygieneData->senstive_sector_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>Sensitive geography/region/area</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="senstive_region_check" id="senstive_region_check_yes" value="Yes" {{isset($arrHygieneData->senstive_region_check) && $arrHygieneData->senstive_region_check == 'Yes' ? 'checked' : ''}}>Yes
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="senstive_region_check" id="senstive_region_check_no" value="No" {{!isset($arrHygieneData->senstive_region_check) || $arrHygieneData->senstive_region_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="senstive_region_cmnt" name="senstive_region_cmnt" value="{{isset($arrHygieneData->senstive_region_cmnt) ? $arrHygieneData->senstive_region_cmnt : ''}}">
                           </td>
                        </tr>
                        <tr>
                           <td><b>KYC risk profile</b></td>
                           <td>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="kyc_risk_check" id="kyc_risk_check_high" value="High" {{isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'High' ? 'checked' : ''}}>High
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="kyc_risk_check" id="kyc_risk_check_medium" value="Med" {{isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Med' ? 'checked' : ''}}>Medium
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block; margin-right:10px;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="kyc_risk_check" id="kyc_risk_check_low" value="Low" {{isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Low' ? 'checked' : ''}}>Low
                                 <i class="input-helper"></i></label>
                              </div>
                              <div class="form-check" style="display: inline-block;">
                                 <label class="form-check-label">
                                 <input type="radio" class="form-check-input" name="kyc_risk_check" id="kyc_risk_check_no" value="No" {{!isset($arrHygieneData->kyc_risk_check) || $arrHygieneData->kyc_risk_check == 'No' ? 'checked' : ''}}>No
                                 <i class="input-helper"></i></label>
                              </div></td>                           
                           <td>
                              <input type="text" class="form-control from-inline" id="kyc_risk_cmnt" name="kyc_risk_cmnt" value="{{isset($arrHygieneData->kyc_risk_cmnt) ? $arrHygieneData->kyc_risk_cmnt : ''}}">
                           </td>
                        </tr>
                     </tbody>
                  </table>
                  <div class="row">
                     <div class="col-md-12 mt-3">
                        <div class="form-group text-right">
                           @if(request()->get('view_only'))
                           <button  class="btn btn-primary btn-ext submitBtnBank" data-toggle="modal" data-target="#myModal">Save</button>                                        
                           @endif
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
                        
                           $("#download_user_cibil").html(window.atob(data['cibilScoreData']));
                    }else{
                          
                           $("#download_user_cibil").text(data['cibilScoreData']);
                   }                          
                    
            }
      });
   }
   

</script>
@endsection