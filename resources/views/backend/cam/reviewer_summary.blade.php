@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container mt-4">
      <div class="row">
         <div class="col-md-12">
         @can('mail_reviewer_summary')
            <a href="{{route('mail_reviewer_summary', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}"><button type="" class="btn btn-success btn-sm float-right">Send Mail</button></a>                 
         @endcan
         </div>
      </div>
      <!--Start-->
      <form method="post" action="{{ route('save_reviewer_summary') }}">
      @csrf
      <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}"> 
      <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}"> 
      <input type="hidden" name="cam_reviewer_summary_id" value="{{isset($reviewerSummaryData->cam_reviewer_summary_id) ? $reviewerSummaryData->cam_reviewer_summary_id : ''}}" />                                                                          
      <div class="card mt-4">
         <div class="card-body ">
            <div class="row">
               <div class="col-md-12">
                     <h4><small>Cover Note</small></h4>
                     <textarea id="cover_note" name="cover_note" class="form-control" cols="10" rows="10">{{isset($reviewerSummaryData->cover_note) ? $reviewerSummaryData->cover_note : ''}}</textarea>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Deal Structure:</small></h4>
                     <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Criteria</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Particulars</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr role="row" class="odd">
                                 <td class="">Facility Type</td>
                                 <td class="">Lease Loan</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Limit (₹ In Mn)</td>
                                 <td class="">{{isset($limitOfferData->limit_amt) ? '₹ '.$limitOfferData->limit_amt : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Tenor (Months)</td>
                                 <td class="">{{isset($limitOfferData->tenor) ? $limitOfferData->tenor : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Equipment Type</td>
                                 @php 
                                 @$equipType = ''     
                                 @endphp 
                                 @if(isset($limitOfferData->equipment_type_id) && $limitOfferData->equipment_type_id)
                                    @php
                                       $equipType = Helpers::getEquipmentTypeById($limitOfferData->equipment_type_id)->equipment_name  
                                    @endphp
                                 @endif
                                 <td class="">{{$equipType}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Security Deposit</td>
                                 <td class="">{{isset($limitOfferData->security_deposit) ? $limitOfferData->security_deposit : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Rental Frequency</td>
                                 <td class="">{{isset($limitOfferData->rental_frequency) ? config('common.rental_frequency.'.$limitOfferData->rental_frequency) : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">PTPQ</td>
                                 <td class="">
                                 @if(isset($offerPTPQ) && $offerPTPQ && $offerPTPQ!='')   
                                    @foreach ($offerPTPQ as $ok => $ov)
                                       {{isset($ov->ptpq_from) ? 'From Period '.$ov->ptpq_from : ''}}
                                       {{isset($ov->ptpq_to) ? 'To Period '.$ov->ptpq_to : ''}}
                                       {{isset($ov->ptpq_rate) ? 'Rate '.$ov->ptpq_rate : ''}}
                                       <br/>
                                    @endforeach 
                                 @endif                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" valign="top">XIRR</td>
                                 <td class="" valign="top">
                                    Ruby Sheet : {{isset($limitOfferData->ruby_sheet_xirr) ? $limitOfferData->ruby_sheet_xirr.'%' : ''}}
                                    <br/>Cash Flow : {{isset($limitOfferData->cash_flow_xirr) ? $limitOfferData->cash_flow_xirr.'%' : ''}}
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Additional Security</td>
                                 <td class="">  
                                 @php 
                                 @$addSecArr = []      
                                 @endphp                        
                                 @if(isset($limitOfferData->addl_security))
                                    @php 
                                       $addSecArr = explode(',',$limitOfferData->addl_security)
                                    @endphp                                     
                                 @endif   
                                 @if(isset($addSecArr) && count($addSecArr)>0)   
                                    @foreach ($addSecArr as $k => $v)
                                       {{ config('common.addl_security.'.$v).", " }}
                                       @if($v==4)
                                         {{isset($limitOfferData->comment) ? " Comment- ".$limitOfferData->comment : ''}}
                                       @endif
                                    @endforeach 
                                 @endif                         
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Pre Disbursement Conditions:</small></h4>
                     <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="60%">Condition</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                           </tr>
                        </thead>                   
                     </table>
                     
                     <div class="input-group control-group after-add-more">
                        <div class="input-group-btn"> 
                           <input type="text" name="pre_cond[]" value="" class="form-control form-control-sm">
                        </div>
                        <div class="input-group-btn"> 
                           <input type="text" name="pre_timeline[]" value="" class="form-control form-control-sm">
                        </div>
                        <div class="input-group-btn"> 
                           <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                        </div>
                     </div>

                     <!-- Copy Fields -->
                     <div class="copy hide">
                        <div class="control-group input-group" style="margin-top:10px">
                           <div class="input-group-btn"> 
                              <input type="text" name="pre_cond[]" value="" class="form-control form-control-sm">
                           </div>
                           <div class="input-group-btn"> 
                              <input type="text" name="pre_timeline[]" value="" class="form-control form-control-sm">
                           </div>
                           <div class="input-group-btn"> 
                              <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                           </div>
                        </div>
                     </div>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Post Disbursement Conditions:</small></h4>
                     <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="60%">Condition</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                           </tr>
                        </thead>
                        <tbody>           
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text"  name="cond_pbdit" value="{{isset($reviewerSummaryData->cond_pbdit) ? $reviewerSummaryData->cond_pbdit : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_pbdit" value="{{isset($reviewerSummaryData->time_pbdit) ? $reviewerSummaryData->time_pbdit : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_dscr" value="{{isset($reviewerSummaryData->cond_dscr) ? $reviewerSummaryData->cond_dscr : ''}}"  class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_dscr" value="{{isset($reviewerSummaryData->time_dscr) ? $reviewerSummaryData->time_dscr : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_lender_cfpl" value="{{isset($reviewerSummaryData->cond_lender_cfpl) ? $reviewerSummaryData->cond_lender_cfpl : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_lender_cfpl" value="{{isset($reviewerSummaryData->time_lender_cfpl) ? $reviewerSummaryData->time_lender_cfpl : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" valign="top">
                                    <input type="text" name="cond_ebidta" value="{{isset($reviewerSummaryData->cond_ebidta) ? $reviewerSummaryData->cond_ebidta : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_ebidta" value="{{isset($reviewerSummaryData->time_ebidta) ? $reviewerSummaryData->time_ebidta : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_credit_rating" value="{{isset($reviewerSummaryData->cond_credit_rating) ? $reviewerSummaryData->cond_credit_rating : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text" name="time_credit_rating" value="{{isset($reviewerSummaryData->time_credit_rating) ? $reviewerSummaryData->time_credit_rating : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Approval criteria for IC:</small></h4>
                     <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending">Parameter</th>
                                 <th class="" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Criteria</th>
                                 <th class="" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Deviation</th>
                                 <th class="" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Remarks</th>
                           </tr>
                        </thead>
                        <tbody>  
                           <tr role="row" class="odd">
                                 <td class="">
                                    Nominal RV Position
                                 </td>
                                 <td class="">
                                    Max 5% over the values mentionedin the matrix
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_rv_position_yes" class="form-check-input" name="criteria_rv_position" value="Yes" {{((isset($reviewerSummaryData->criteria_rv_position) && $reviewerSummaryData->criteria_rv_position == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_rv_position_no" class="form-check-input" name="criteria_rv_position" value="No"  {{!isset($reviewerSummaryData->criteria_rv_position) || $reviewerSummaryData->criteria_rv_position == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_rv_position_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_rv_position_remark) ? $reviewerSummaryData->criteria_rv_position_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>         
                           <tr role="row" class="odd">
                                 <td class="">
                                    Asset concentration as % of the total portfolio
                                 </td>
                                 <td class="">
                                    - IT assets and telecommunications max 70%<br/>
                                    - Plant and machinery max 50%<br/>
                                    - Furniture and fit outs max 30%<br/>
                                    - Any other asset type max 20%
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_asset_portfolio_yes" class="form-check-input" name="criteria_asset_portfolio" value="Yes" {{((isset($reviewerSummaryData->criteria_asset_portfolio) && $reviewerSummaryData->criteria_asset_portfolio == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_asset_portfolio_no" class="form-check-input" name="criteria_asset_portfolio" value="No"  {{!isset($reviewerSummaryData->criteria_asset_portfolio) || $reviewerSummaryData->criteria_asset_portfolio == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_asset_portfolio_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_asset_portfolio_remark) ? $reviewerSummaryData->criteria_asset_portfolio_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    Single Borrower Limit
                                 </td>
                                 <td class="">
                                    Max 15% of Net owned funds (Rs150 Mn)
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_sing_borr_limit_yes" class="form-check-input" name="criteria_sing_borr_limit" value="Yes" {{((isset($reviewerSummaryData->criteria_sing_borr_limit) && $reviewerSummaryData->criteria_sing_borr_limit == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_sing_borr_limit_no" class="form-check-input" name="criteria_sing_borr_limit" value="No"  {{!isset($reviewerSummaryData->criteria_sing_borr_limit) || $reviewerSummaryData->criteria_sing_borr_limit == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_sing_borr_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_sing_borr_remark) ? $reviewerSummaryData->criteria_sing_borr_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    Borrower Group Limit 
                                 </td>
                                 <td class="">
                                    Max 25% of Net owned funds (Rs250 Mn)
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_borr_grp_limit_yes" class="form-check-input" name="criteria_borr_grp_limit" value="Yes" {{((isset($reviewerSummaryData->criteria_borr_grp_limit) && $reviewerSummaryData->criteria_borr_grp_limit == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_borr_grp_limit_no" class="form-check-input" name="criteria_borr_grp_limit" value="No"  {{!isset($reviewerSummaryData->criteria_borr_grp_limit) || $reviewerSummaryData->criteria_borr_grp_limit == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_borr_grp_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_borr_grp_remark) ? $reviewerSummaryData->criteria_borr_grp_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    Exposure on customers below investment grade <br/>
                                    (BBB -CRISIL/CARE/ICRA/India Ratings) and unrated customers
                                 </td>
                                 <td class="">
                                    Max 50% of CFPL portfolio
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_invest_grade_yes" class="form-check-input" name="criteria_invest_grade" value="Yes" {{((isset($reviewerSummaryData->criteria_invest_grade) && $reviewerSummaryData->criteria_invest_grade == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_invest_grade_no" class="form-check-input" name="criteria_invest_grade" value="No"  {{!isset($reviewerSummaryData->criteria_invest_grade) || $reviewerSummaryData->criteria_invest_grade == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_invest_grade_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_invest_grade_remark) ? $reviewerSummaryData->criteria_invest_grade_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    Exposure to a particular industry/sector as a percentage of total portfolio
                                 </td>
                                 <td class="">
                                    Max 50% of the total CFPL portfolio
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_particular_portfolio_yes" class="form-check-input" name="criteria_particular_portfolio" value="Yes" {{((isset($reviewerSummaryData->criteria_particular_portfolio) && $reviewerSummaryData->criteria_particular_portfolio == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_particular_portfolio_no" class="form-check-input" name="criteria_particular_portfolio" value="No"  {{!isset($reviewerSummaryData->criteria_particular_portfolio) || $reviewerSummaryData->criteria_particular_portfolio == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_particular_portfolio_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_particular_portfolio_remark) ? $reviewerSummaryData->criteria_particular_portfolio_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>                           
                        </tbody>
                  </table>
               </div>                  
               <div class="col-md-12 mt-4">
                     <h4><small>Risk Comments:</small></h4>
                     <h5><small>Deal Positives:</small></h5>
                     <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_pos_track_rec" value="{{isset($reviewerSummaryData->cond_pos_track_rec) ? $reviewerSummaryData->cond_pos_track_rec : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <textarea name="cmnt_pos_track_rec" class="form-control form-control-sm">{{isset($reviewerSummaryData->cmnt_pos_track_rec) ? $reviewerSummaryData->cmnt_pos_track_rec : ''}}</textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_pos_credit_rating" value="{{isset($reviewerSummaryData->cond_pos_credit_rating) ? $reviewerSummaryData->cond_pos_credit_rating : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_pos_credit_rating" class="form-control form-control-sm">{{isset($reviewerSummaryData->cmnt_pos_credit_rating) ? $reviewerSummaryData->cmnt_pos_credit_rating : ''}}</textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_pos_fin_matric" value="{{isset($reviewerSummaryData->cond_pos_fin_matric) ? $reviewerSummaryData->cond_pos_fin_matric : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_pos_fin_matric" class="form-control form-control-sm">{{isset($reviewerSummaryData->cmnt_pos_fin_matric) ? $reviewerSummaryData->cmnt_pos_fin_matric : ''}}</textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_pos_establish_client" value="{{isset($reviewerSummaryData->cond_pos_establish_client) ? $reviewerSummaryData->cond_pos_establish_client : ''}}" class="form-control form-control-sm">  
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_pos_establish_client" class="form-control form-control-sm">{{isset($reviewerSummaryData->cmnt_pos_establish_client) ? $reviewerSummaryData->cmnt_pos_establish_client : ''}}</textarea>
                                 </td>
                           </tr>
                        </tbody>
                     </table>
                     <h5 class="mt-3"><small>Deal Negatives:</small></h5>
                     <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_neg_competition" value="{{isset($reviewerSummaryData->cond_neg_competition) ? $reviewerSummaryData->cond_neg_competition : ''}}" class="form-control form-control-sm">  
                                 </td>
                                 <td class="">
                                    <textarea name="cmnt_neg_competition" class="form-control form-control-sm">{{isset($reviewerSummaryData->cmnt_neg_competition) ? $reviewerSummaryData->cmnt_neg_competition : ''}}</textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_neg_forex_risk" value="{{isset($reviewerSummaryData->cond_neg_forex_risk) ? $reviewerSummaryData->cond_neg_forex_risk : ''}}" class="form-control form-control-sm">  
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_neg_forex_risk" class="form-control form-control-sm">{{isset($reviewerSummaryData->cmnt_neg_forex_risk) ? $reviewerSummaryData->cmnt_neg_forex_risk : ''}}</textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_neg_pbdit" value="{{isset($reviewerSummaryData->cond_neg_pbdit) ? $reviewerSummaryData->cond_neg_pbdit : ''}}" class="form-control form-control-sm">  
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_neg_pbdit" class="form-control form-control-sm">{{isset($reviewerSummaryData->cmnt_neg_pbdit) ? $reviewerSummaryData->cmnt_neg_pbdit : ''}}</textarea>
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Recommendation:</small></h4>
                     <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr role="row">
                                 <td class="">
                                    <textarea  name="recommendation" class="form-control form-control-sm" cols="3" rows="3">{{isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : ''}}</textarea>
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-2">
               @can('save_reviewer_summary')
                  <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
               @endcan
               </div>
            </div>
         </div>
      </div>
      </form>
      <!--End-->
   </div>
</div>   
@endsection
@section('jscript')
<script type="text/javascript">
   appId = '{{$appId}}';
   appurl = '{{URL::route("financeAnalysis") }}';
   process_url = '{{URL::route("process_financial_statement") }}';
   _token = "{{ csrf_token() }}";
</script>

<script type="text/javascript">
   $(document).ready(function(){
      $("#cover_note").focus();
   });

   $(document).on('click', '.getAnalysis', function() {
      data = {appId, _token};
      $.ajax({
         url  : appurl,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
         },
         dataType : 'json',
         success:function(result) {
            console.log(result);
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
            if (result['status']) {
               window.open(result['value']['file_url'], '_blank');
            }
         },
         error:function(error) {
            // body...
         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })

   $(document).on('click', '.process_stmt', function() {
      biz_perfios_id = $(this).attr('pending');
      data = {appId, _token, biz_perfios_id};
      $.ajax({
         url  : process_url,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
         },
         dataType : 'json',
         success:function(result) {
            console.log(result);
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
            if (result['status']) {
               window.open(result['value']['file_url'], '_blank');
            }
         },
         error:function(error) {

         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })

$(document).ready(function() {

   $(".add-more").click(function(){ 
      var html = $(".copy").html();
      $(".after-add-more").after(html);
   });


   $("body").on("click",".remove",function(){ 
      $(this).parents(".control-group").remove();
   });

});
</script>
@endsection