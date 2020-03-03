@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container mt-4">
      <!-- 
      <div class="row">
         <div class="col-md-12">
         @can('mail_reviewer_summary')
            <a href="{{route('mail_reviewer_summary', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}"><button type="" class="btn btn-success btn-sm float-right">Send Mail</button></a>                 
         @endcan
         </div>
      </div>
      -->
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
                     <textarea id="cover_note" name="cover_note" class="form-control" cols="10" rows="10">{!! isset($reviewerSummaryData->cover_note) ? $reviewerSummaryData->cover_note : '' !!}</textarea>
               </div>


               <div class="col-md-12 data mt-4 ">
                     <h2 class="sub-title bg">Deal Structure</h2>
                        @forelse($leaseOfferData as $key=>$leaseOffer)
                           <div class="pl-4 pr-4 pb-4 pt-2">
                              <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                                 <thead>
                                    <tr role="row">
                                       <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Criteria</th>
                                       <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Particulars</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                   
                                    
                                    <tr role="row" class="odd">
                                       <td class=""><b>Facility Type</b></td>
                                       <td class="">{{isset($leaseOffer->facility_type_id) ?  $facilityTypeList[$leaseOffer->facility_type_id]  : ''}}</td>
                                    </tr>
                                    <tr role="row" class="odd">
                                       <td class=""><b>Equipment Type</b></td>
                                       <td class="">{{isset($leaseOffer->equipment_type_id) ?  (\Helpers::getEquipmentTypeById($leaseOffer->equipment_type_id)['equipment_name']) : ''}}</td>
                                    </tr>
                                    <tr role="row" class="odd">
                                       <td class=""><b>Limit Of The Equipment</b></td>
                                       <td class=""> {!! isset($leaseOffer->prgm_limit_amt) ? ' INR '.number_format($leaseOffer->prgm_limit_amt)  : '0' !!} 
                                             </td>
                                    </tr>
                                 
                                    <tr role="row" class="odd">
                                       <td class=""><b>Tenor (Months)</b></td>
                                       <td class="">{{isset($leaseOffer->tenor) ? $leaseOffer->tenor : ''}}</td>
                                    </tr>
                                    <tr role="row" class="odd">
                                       <td class=""><b>Security Deposit</b></td>
                                       <td class="">  {{isset($leaseOffer->security_deposit) ? $leaseOffer->security_deposit : ''}} {!! isset($leaseOffer->security_deposit_type) ? $arrStaticData['securityDepositType'][$leaseOffer->security_deposit_type] : '' !!} {{isset($leaseOffer->security_deposit_of) ? 'of '. $arrStaticData['securityDepositOf'][$leaseOffer->security_deposit_of] : ''}} </td>
                                    </tr>
                                    <tr role="row" class="odd">
                                       <td class=""><b>Rental Frequency</b></td>
                                       <td class="">{{isset($leaseOffer->rental_frequency) ? $arrStaticData['rentalFrequency'][$leaseOffer->rental_frequency] : ''}}   {{isset($leaseOffer->rental_frequency_type) ? 'in '.$arrStaticData['rentalFrequencyType'][$leaseOffer->rental_frequency_type] : ''}}   </td>
                                    </tr>
                                    <tr role="row" class="odd">
                                       <td class=""><b>Pricing Per Thousand</b></td>
                                       <td class="">
                                          @php 
                                             $i = 1;
                                             if(!empty($leaseOffer->offerPtpq)){
                                             $total = count($leaseOffer->offerPtpq);
                                          @endphp   
                                             @foreach($leaseOffer->offerPtpq as $key => $arr) 

                                                   @if ($i > 1 && $i < $total)
                                                   ,
                                                   @elseif ($i > 1 && $i == $total)
                                                      and
                                                   @endif
                                                   {!!  'INR' !!} {{$arr->ptpq_rate}}  for  {{floor($arr->ptpq_from)}}- {{floor($arr->ptpq_to)}} {{$arrStaticData['rentalFrequencyForPTPQ'][$leaseOffer->rental_frequency]}}

                                                   @php 
                                                      $i++;
                                                   @endphp     
                                             @endforeach
                                             @php 
                                                }
                                             @endphp 
                                       </td>
                                    </tr>
                                    <tr role="row" class="odd">
                                       <td class="" valign="top"><b>XIRR</b></td>
                                       <td class="" valign="top"><b>Ruby Sheet:</b> {{isset($leaseOffer->ruby_sheet_xirr) ? $leaseOffer->ruby_sheet_xirr : ''}}%<br><b>Cash Flow:</b> {{isset($leaseOffer->cash_flow_xirr) ? $leaseOffer->cash_flow_xirr : ''}}%
                                       </td>
                                    </tr>
                                    
                                    <tr role="row" class="odd">
                                       <td class=""><b>Additional Security</b></td>
                                       <td class="">
                                          @php
                                            $add_sec_arr = '';
                                            if(isset($leaseOffer->addl_security) && $leaseOffer->addl_security !=''){
                                                $addl_sec_arr = explode(',', $leaseOffer->addl_security);
                                                foreach($addl_sec_arr as $k=>$v){
                                                    $add_sec_arr .= config('common.addl_security')[$v].', ';
                                                }
                                            }
                                            if($leaseOffer->comment != '' && $leaseOffer->addl_security !=''){
                                                $add_sec_arr .= ' <b>Comment</b>:  '.$leaseOffer->comment;
                                            }else{
                                                $add_sec_arr .= $leaseOffer->comment;
                                            }
                                            @endphp 
                                            {!! trim($add_sec_arr,', ') !!}
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>

                           @empty
                              <div class="pl-4 pr-4 pb-4 pt-2">
                                  <p>No Offer Found</p>
                              </div>
                        @endforelse
               </div>

               <div class="col-md-12 mt-4">
                     <h4><small>Pre Disbursement Conditions:</small></h4>
                     <table id="invoice_history" class="table table-striped no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="50%">Condition</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                           </tr>
                        </thead>                   
                     </table>
                     @if(isset($preCondArr) && count($preCondArr)>0)
                        @foreach($preCondArr as $prekey =>$preval)
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_cond[]" value="" class="form-control form-control-sm">{{$preval['cond']}}</textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_timeline[]" value="" class="form-control form-control-sm">{{$preval['timeline']}}</textarea>
                           </div>
                           <div class="input-group-btn "> 
                            <i class="fa  fa-times-circle remove-ptpq-block remove"></i>
                           </div>
                        </div>
                        @endforeach
                     @endif
                     <div class="after-add-more">
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa  fa-plus-circle add-ptpq-block add-more"></i>
                           </div>
                        </div>
                     </div>
                     <!-- Copy Fields -->
                     <div class="copy hide">
                        <div class="control-group input-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                              <i class="fa  fa-times-circle remove-ptpq-block remove"></i>
                           </div>
                        </div>
                     </div>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Post Disbursement Conditions:</small></h4>
                     <table id="invoice_history" class="table table-striped no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="50%">Condition</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                           </tr>
                        </thead>
                     </table>
                     @if(isset($postCondArr) && count($postCondArr)>0)
                        @foreach($postCondArr as $postkey =>$postval)
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_cond[]" value="" class="form-control form-control-sm">{{$postval['cond']}}</textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_timeline[]" value="" class="form-control form-control-sm">{{$postval['timeline']}}</textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa fa-times-circle remove-ptpq-block  remove-post"></i>
                           </div>
                        </div>
                        @endforeach
                     @endif
                     <div class="after-add-more-post">
                        <div class="input-group control-group  row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa  fa-plus-circle add-ptpq-block add-more-post"></i>
                           </div>
                        </div>
                     </div>
                     <!-- Copy Fields -->
                     <div class="copy-post hide">
                        <div class="control-group input-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                             <i class="fa  fa-times-circle remove-ptpq-block remove-post"></i>
                           </div>
                        </div>
                     </div>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Approval criteria for IC</small></h4>
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
                     <h4><small>Risk Comments</small></h4>
                     <h5><small>Deal Positives</small></h5>
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
                     <h5 class="mt-3"><small>Deal Negatives</small></h5>
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
                     <h4><small>Recommendation</small></h4>
                     <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr role="row">
                                 <td class="">
                                    <textarea  name="recommendation" id="recommendation" class="form-control form-control-sm" cols="3" rows="3">{!! isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : '' !!}</textarea>
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

    CKEDITOR.replace('cover_note');
    CKEDITOR.replace('recommendation');

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
      $(".after-add-more").append(html);
   });


   $("body").on("click",".remove",function(){ 
      $(this).parents(".control-group").remove();
   });

   $(".add-more-post").click(function(){ 
      var html = $(".copy-post").html();
      $(".after-add-more-post").append(html);
   });


   $("body").on("click",".remove-post",function(){ 
      $(this).parents(".control-group").remove();
   });

});
</script>
@endsection