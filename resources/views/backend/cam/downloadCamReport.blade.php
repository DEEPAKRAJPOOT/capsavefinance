@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
 
<div class="inner-container">
    <div class="card mt-3">
         <div class="card-body pt-3 pb-3">
            <a href="{{route('generate_cam_report', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}">
               <button type="button" class="btn btn-primary float-right btn-sm" > Generate Cam Report</button>
            </a>
         </div>
        <div class="card-body pt-3 pb-3">
          <p class="pull-left"><b>CAM Report For {{isset($arrBizData->biz_entity_name) ? $arrBizData->biz_entity_name : ''}}</b></p>
          @if(($currStageCode == 'approver') && ($approveStatus && $approveStatus->status == 0))
          <div class="float-right">
            <form method="POST" action="{{route('approve_offer')}}">
            @csrf
            <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
            <input name="btn_save_offer" class="btn btn-success btn-sm float-right mt-3 ml-3" type="submit" value="Approve Limit">
            </form>
          </div>
          @elseif(($approveStatus && $approveStatus->status == 1))
            <p class="float-right mt-3 ml-3"><b style="color: green; font-size: 17px;">Limit Approved</b></p>
          @endif
          <button onclick="downloadCam()" class="btn btn-primary float-right btn-sm mt-3 " > Download Report</button>
        </div>
    </div>

<!-- Start PDF Section -->

<div class="card mt-3" id="camReport">
   <div class="card-body pt-3 pb-3">
      <div class="row">
         <div class="col-md-12">
            <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr role="row">
                     <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Group</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Borrower</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Proposed Limit</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Existing Exposure</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Total Exposure</th>
                  </tr>
               </thead>
               <tbody>
                  <tr role="row" class="odd">
                    <td class="">{{isset($arrCamData->group_company) ? $arrCamData->group_company : ''}}</td>
                     <td class="">{{isset($arrBizData->biz_entity_name) ? $arrBizData->biz_entity_name : ''}}</td>
                     <td class="">{{isset($arrCamData->proposed_exposure) ? $arrCamData->proposed_exposure : ''}}</td>
                     <td class="">{{isset($arrCamData->existing_exposure) ? $arrCamData->existing_exposure : ''}}</td>
                     <td class="">{{ isset($arrCamData->total_exposure) ? $arrCamData->total_exposure : '' }}</td>
                  </tr>
               </tbody>
            </table>
           

            <div class="data mt-4">
             <h2 class="sub-title bg">Deal Structure</h2>
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
                     <td class=""><b>Facility Type</td>
                     <td class="">Lease</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class=""><b>Limit</b></td>
                     <td class=""> {{isset($leaseOfferData->prgm_limit_amt) ? $leaseOfferData->prgm_limit_amt : ''}}
                           </td>
                     
                  </tr>
                  <tr role="row" class="odd">
                     <td class=""><b>Tenor (Months)</b></td>
                     <td class="">{{isset($leaseOfferData->tenor) ? $leaseOfferData->tenor : ''}}</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class=""><b>Equipment Type</b></td>
                     <td class="">{{isset($leaseOfferData->security_deposit_type) ?  (\Helpers::getEquipmentTypeById($leaseOfferData->security_deposit_type)['equipment_name']) : ''}}</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class=""><b>Security Deposit</b></td>
                     <td class="">{{isset($leaseOfferData->security_deposit) ? $leaseOfferData->security_deposit : ''}}</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class=""><b>Rental Frequency</b></td>
                     <td class="">{{isset($leaseOfferData->rental_frequency) ? $arrStaticData['rentalFrequency'][$leaseOfferData->rental_frequency] : ''}}</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class=""><b>PTPQ</b></td>
                     <td class="">
                        @php 
                           $i = 1;
                           if(!empty($leaseOfferData->offerPtpq)){
                           $total = count($leaseOfferData->offerPtpq);
                        @endphp   

                           @foreach($leaseOfferData->offerPtpq as $key => $arr) 

                                 @if ($i > 1 && $i < $total)
                                  ,
                                 @elseif ($i > 1 && $i == $total)
                                    and
                                 @endif
                                 Rs. {{$arr->ptpq_rate}}  for  {{floor($arr->ptpq_from)}}- {{floor($arr->ptpq_to)}} {{$arrStaticData['rentalFrequencyForPTPQ'][$leaseOfferData->rental_frequency]}}

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
                     <td class="" valign="top">Ruby Sheet : {{isset($leaseOfferData->ruby_sheet_xirr) ? $leaseOfferData->ruby_sheet_xirr : ''}}%<br>Cash Flow : {{isset($leaseOfferData->cash_flow_xirr) ? $leaseOfferData->cash_flow_xirr : ''}}%
                     </td>
                  </tr>
                 
                  <tr role="row" class="odd">
                     <td class=""><b>Additional Security</b></td>
                     <td class="">
                           <div class="form-check" style="display: inline-block; margin-right:10px;">
                             <label class="form-check-label">
                             <input type="checkbox" class="form-check-input"  disabled value="1" {{isset($leaseOfferData->addl_security) && (strpos($leaseOfferData->addl_security, '1') !== false) ? 'checked' : ''}} >BG
                             <i class="input-helper"></i></label>
                           </div>
                           <div class="form-check" style="display: inline-block;">
                             <label class="form-check-label">
                             <input type="checkbox" class="form-check-input"   disabled value="2" {{isset($leaseOfferData->addl_security) && (strpos($leaseOfferData->addl_security, '2') !== false) ? 'checked' : ''}}>FD
                             <i class="input-helper"></i></label>
                          </div>
                          <div class="form-check" style="display: inline-block;">
                             <label class="form-check-label">
                             <input type="checkbox" class="form-check-input"   disabled value="MF" {{isset($leaseOfferData->addl_security) && (strpos($leaseOfferData->addl_security, '3') !== false) ? 'checked' : ''}}>MF
                             <i class="input-helper"></i></label>
                          </div>
                          <div class="form-check" style="display: inline-block;">
                             <label class="form-check-label">
                             <input type="checkbox" class="form-check-input" id="othersCheckbox" name="t_o_f_security_check[]"  disabled value="4" {{isset($leaseOfferData->addl_security) && (strpos($leaseOfferData->addl_security, '4') !== false) ? 'checked' : ''}}>Others
                             <i class="input-helper"></i></label>
                          </div>
                          <p style="display: {{isset($leaseOfferData->comment) && (strpos($leaseOfferData->addl_security, '4') !== false) ? '' : 'none'}} " >{{isset($leaseOfferData->comment) ? $leaseOfferData->comment : ''}} </p>
                     </td>
                  </tr>
               </tbody>
            </table>
              </div>
            </div>

             <div class="data mt-4">
             <h2 class="sub-title bg">Pre Disbursement Conditions</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
               <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr role="row">
                     <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="60%">Condition</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                  </tr>
               </thead>
               <tbody>
                  <tr role="row" class="odd">
                     <td class="">
                        <p>{{isset($reviewerSummaryData->cond_nach) ? $reviewerSummaryData->cond_nach : ''}}</p> 
                       
                     </td>
                     <td class="">
                        <p>{{isset($reviewerSummaryData->time_nach) ? $reviewerSummaryData->time_nach : ''}}</p>
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">
                        <p>{{isset($reviewerSummaryData->cond_insp_asset) ? $reviewerSummaryData->cond_insp_asset : ''}} </p>
                     </td>
                     <td class="">
                        <p> {{isset($reviewerSummaryData->time_insp_asset) ? $reviewerSummaryData->time_insp_asset : ''}} </p>
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                    <td class="">
                        <p>{{isset($reviewerSummaryData->cond_insu_pol_cfpl) ? $reviewerSummaryData->cond_insu_pol_cfpl : ''}} </p>
                     </td>
                     <td class="">
                        <p>{{isset($reviewerSummaryData->time_insu_pol_cfpl) ? $reviewerSummaryData->time_insu_pol_cfpl : ''}} </p>
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">
                        <p>{{isset($reviewerSummaryData->cond_personal_guarantee) ? $reviewerSummaryData->cond_personal_guarantee : ''}} </p>
                     </td>
                     <td class="">
                        <p>{{isset($reviewerSummaryData->time_personal_guarantee) ? $reviewerSummaryData->cond_insu_pol_cfpl : ''}} </p>
                     </td>
                  </tr>
                  
               </tbody>
            </table>
              </div>
             </div>
            
           
           <div class="data mt-4">
             <h2 class="sub-title bg">Post Disbursement Conditions</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
               <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr role="row">
                     <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="60%">Condition</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                  </tr>
               </thead>
               <tbody>
                  <tr role="row" class="odd">
                       <td class="">
                            <p> {{isset($reviewerSummaryData->cond_pbdit) ? $reviewerSummaryData->cond_pbdit : ''}} </p>
                        </td>
                        <td class="">
                            <p> {{isset($reviewerSummaryData->time_pbdit) ? $reviewerSummaryData->time_pbdit : ''}} </p>
                        </td>
                  </tr>
                  <tr role="row" class="odd">
                      <td class="">
                         <p> {{isset($reviewerSummaryData->cond_dscr) ? $reviewerSummaryData->cond_dscr : ''}} </p>
                     </td>
                     <td class="">
                         <p> {{isset($reviewerSummaryData->time_dscr) ? $reviewerSummaryData->time_dscr : ''}} </p>
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">
                         <p> {{isset($reviewerSummaryData->cond_lender_cfpl) ? $reviewerSummaryData->cond_lender_cfpl : ''}} </p>
                     </td>
                     <td class="">
                         <p> {{isset($reviewerSummaryData->time_lender_cfpl) ? $reviewerSummaryData->time_lender_cfpl : ''}} </p>
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="" valign="top">
                         <p> {{isset($reviewerSummaryData->cond_ebidta) ? $reviewerSummaryData->cond_ebidta : ''}} </p>
                     </td>
                     <td class="">
                         <p> {{isset($reviewerSummaryData->time_ebidta) ? $reviewerSummaryData->time_ebidta : ''}} </p>
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">
                         <p> {{isset($reviewerSummaryData->cond_credit_rating) ? $reviewerSummaryData->cond_credit_rating : ''}} </p>
                     </td>
                     <td class="">
                         <p> {{isset($reviewerSummaryData->time_credit_rating) ? $reviewerSummaryData->time_credit_rating : ''}} </p>
                     </td>
                     
                  </tr>
               </tbody>
            </table>
              </div>
              </div>

           

            <div class="data mt-4">
             <h2 class="sub-title bg">The proposed deal is approved/declined/deferred subject to above conditions and any other conditions mentioned below.</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
              <table id="invoice_history" class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Recommended By</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="3" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="75%">Investment Committee Members</th>
                  </tr>
                 
                   
               </thead>
               <tbody>
                    <tr role="row" >
                        <td align="center" rowspan="">
                          <table class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                                  @php 
                                    $i=0;
                                 @endphp
                                 @while(!empty($arrCM[$i])) 
                                    <tr>
                                        <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%">{{$arrCM[$i]->assignee}}</th>
                                        @php $i++; @endphp
                                   </tr>
                               @endwhile
                           </table> 
                        </td>
                     <td align="center" colspan="3">
                        <table class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                              @php 
                                 $i=0;
                              @endphp
                              @while(!empty($arrApproverData[$i])) 
                                 <tr>
                                     <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%">{{$arrApproverData[$i]->approver}}</th>
                                     @php $i++; @endphp
                                     <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%">{{$arrApproverData[$i]->approver}}</th>
                                      @php $i++; @endphp
                                </tr>
                            @endwhile
                        </table>
                     </td>
                  </tr>



               </tbody>
            </table>
              </div>
              </div>
           

           


           <div class="data mt-4">
             <h2 class="sub-title bg">Minimum Acceptance Criteria as per NBFC Credit Policy</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
              <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Parameter</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Criteria</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Deviation</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Remarks</th>
                  </tr>
                  <tr>
                     <th class="sorting_asc " tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%" style="background:#62b59b;">Borrower Vintage &amp; Constitution</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="3" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="75%" style="background:#62b59b;"></th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>Constitution</td>
                     <td>
                        <p class="m-0">
                           - Registered Partnership Firm<br>
                           - Private Limited Company<br>
                           - Public Limited Company<br>
                           - Limited Liability Partnership
                        </p>
                     </td>
                     <td>No</td>
                     <td>{{$arrEntityData->name}}</td>
                  </tr>
                  <tr>
                     <td>Vintage</td>
                     <td>
                        <p class="m-0">
                           - Minimum 3 years of vintage in relevant business<br>
                           - Parent or group company with requisite vintage<br>
                           - Key promoter with 5 years of relevant vintage
                        </p>
                     </td>
                     <td>No</td>
                     <td>{{\Carbon\Carbon::parse($arrBizData->date_of_in_corp)->format('d/m/Y')                          }}</td>
                  </tr>
                  <tr>
                     <td colspan="4" bgcolor="#cccccc">&nbsp;</td>
                  </tr>
                  <tr>
                     <td>CFPL Defaulter List</td>
                     <td>No</td>
                     <td>{{isset($arrHygieneData->cfpl_default_check) && $arrHygieneData->cfpl_default_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->cfpl_default_cmnt) ? $arrHygieneData->cfpl_default_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>RBI Defaulter list</td>
                     <td>No</td>
                     <td>{{isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->rbi_willful_defaulters) ? $arrHygieneData->rbi_willful_defaulters : ''}}</td>
                  </tr>
                  <tr>
                     <td>CDR/ BIFR/ OTS/ Restructuring</td>
                     <td>No</td>
                    <td>{{isset($arrHygieneData->cdr_check) && $arrHygieneData->cdr_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->cdr_cmnt) ? $arrHygieneData->cdr_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>CIBIL</td>
                     <td>No Adverse Remarks</td>
                     <td>{{isset($arrHygieneData->cibil_defaulters_chk) && $arrHygieneData->cibil_defaulters_chk == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->comment) ? $arrHygieneData->comment : ''}}</td>
                  </tr>
                  <tr>
                     <td>Watchout Investors</td>
                     <td>No Adverse Remarks</td>
                    <td>{{isset($arrHygieneData->watchout_investors_chk) && $arrHygieneData->watchout_investors_chk == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->watchout_investors) ? $arrHygieneData->watchout_investors : ''}}</td>
                  </tr>
                  <tr>
                     <td>Google Search (Negative searches)</td>
                     <td>No </td>
                     <td>{{isset($arrHygieneData->neg_news_report_check) && $arrHygieneData->neg_news_report_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->neg_news_report_cmnt) ? $arrHygieneData->neg_news_report_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td colspan="4" bgcolor="#cccccc">&nbsp;</td>
                  </tr>
                  <tr>
                     <td>Satisfactory contact point verification</td>
                     <td>Yes </td>
                     <td>{{isset($arrHygieneData->contact_point_check) && $arrHygieneData->contact_point_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->contact_point_cmnt) ? $arrHygieneData->contact_point_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>Satisfactory banker reference</td>
                     <td>Yes </td>
                     <td>{{isset($arrHygieneData->bank_ref_check) && $arrHygieneData->bank_ref_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->bank_ref_cmnt) ? $arrHygieneData->bank_ref_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>Satisfactory trade reference</td>
                     <td>Yes </td>
                     <td>{{isset($arrHygieneData->trade_ref_check) && $arrHygieneData->trade_ref_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->trade_ref_cmnt) ? $arrHygieneData->trade_ref_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td colspan="4" bgcolor="#cccccc">&nbsp;</td>
                  </tr>
                  <tr>
                     <td>Adjusted Tangible Net Worth</td>
                     <td>Positive for last 2 financial years </td>
                    <td>{{isset($finacialDetails->adj_net_worth_check) && $finacialDetails->adj_net_worth_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($finacialDetails->adj_net_worth_cmnt) ? $finacialDetails->adj_net_worth_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>Cash Profit</td>
                     <td>Positive for 2 out of last 3 financial years(positive in last year)</td>
                     <td>{{isset($finacialDetails->cash_profit_check) && $finacialDetails->cash_profit_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($finacialDetails->cash_profit_cmnt) ? $finacialDetails->cash_profit_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>DSCR</td>
                     <td>&gt;1.2X</td>
                     <td>{{isset($finacialDetails->dscr_check) && $finacialDetails->dscr_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($finacialDetails->dscr_cmnt) ? $finacialDetails->dscr_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>Debt/EBIDTA</td>
                     <td>&lt;5X</td>
                     <td>{{isset($finacialDetails->debt_check) && $finacialDetails->debt_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($finacialDetails->debt_cmnt) ? $finacialDetails->debt_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td colspan="4" bgcolor="#cccccc">
                        <h5 class="m-0">Other</h5>
                     </td>
                  </tr>
                  <tr>
                     <td>Negative Industry Segment</td>
                     <td>No</td>
                    <td>{{isset($arrHygieneData->neg_industry_check) && $arrHygieneData->neg_industry_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->neg_industry_cmnt) ? $arrHygieneData->neg_industry_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>Exposure to sensitive sectors</td>
                     <td>No</td>
                     <td>{{isset($arrHygieneData->senstive_sector_check) && $arrHygieneData->senstive_sector_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->senstive_sector_cmnt) ? $arrHygieneData->senstive_sector_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>Sensitive geography/region/area</td>
                     <td>No</td>
                     <td>{{isset($arrHygieneData->senstive_region_check) && $arrHygieneData->senstive_region_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->senstive_region_cmnt) ? $arrHygieneData->senstive_region_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>Politically exposed person</td>
                     <td>No</td>
                     <td>{{isset($arrHygieneData->politically_check) && $arrHygieneData->politically_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->pol_exp_per_cmnt) ? $arrHygieneData->pol_exp_per_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>KYC risk profile</td>
                     <td>  
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'High' ? 'High' : '' }}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Med' ?  'Medium' : ''}}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Low' ? 'Low' : '' }}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'No' ? 'No' : ''
                           }}
                        </td>
                     <td>
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'High' ? 'Highf' : '' }}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Med' ?  'Medium' : ''}}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Low' ? 'Low' : '' }}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'No' ? 'No' : ''
                           }}
                     </td>
                     <td>{{isset($arrHygieneData->kyc_risk_cmnt) ? $arrHygieneData->kyc_risk_cmnt : ''}}</td>
                  </tr>
                  <tr>
                     <td>UNSC List</td>
                     <td>No</td>
                     <td>{{isset($arrHygieneData->unsc_check) && $arrHygieneData->unsc_check == 'Yes' ? 'Yes' : 'No'}}</td>
                     <td>{{isset($arrHygieneData->unsc_cmnt) ? $arrHygieneData->unsc_cmnt : ''}}</td>
                  </tr>
               </tbody>
            </table>

              </div>
              </div>
            
              


              <div class="data mt-4">
             <h2 class="sub-title bg">Approval Criteria for IC</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">

              <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="10%">Sr. No.</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Parameter</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="40%">Criteria</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Remarks</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>1</td>
                     <td>Nominal RV Position</td>
                     <td>Max 5% over the values mentioned in the matrix</td>
                     <td>{{isset($reviewerSummaryData->criteria_rv_position_remark) ? $reviewerSummaryData->criteria_rv_position_remark : ''}}</td>
                  </tr>
                  <tr>
                     <td>2</td>
                     <td>Asset concentration as % of the total portfolio</td>
                     <td>- IT assets and telecommunications max 70%<br>- Plant and machinery max 50%<br>- Furniture and fit outs max 30%
                        <br>- Any other asset type max 20%
                     </td>
                     <td>{{isset($reviewerSummaryData->criteria_asset_portfolio_remark) ? $reviewerSummaryData->criteria_asset_portfolio_remark : ''}}</td>
                  </tr>
                  <tr>
                     <td>3</td>
                     <td>Single Borrower Limit</td>
                     <td>Max 15% of Net owned funds (Rs 150 Mn)</td>
                     <td>{{isset($reviewerSummaryData->criteria_sing_borr_remark) ? $reviewerSummaryData->criteria_sing_borr_remark : ''}}</td>
                  </tr>
                  <tr>
                     <td>4</td>
                     <td>Borrower Group Limit</td>
                     <td>Max 25% of Net owned funds (Rs 250 Mn)</td>
                     <td>{{isset($reviewerSummaryData->criteria_borr_grp_remark) ? $reviewerSummaryData->criteria_borr_grp_remark : ''}}</td>
                  </tr>
                  <tr>
                     <td>5</td>
                     <td>Exposure on customers below investment grade (BBB - CRISIL/CARE/ICRA/India Ratings) and unrated customers</td>
                     <td>Max 50% of CFPL portfolio</td>
                     <td>{{isset($reviewerSummaryData->criteria_invest_grade_remark) ? $reviewerSummaryData->criteria_invest_grade_remark : ''}}</td>
                  </tr>
                  <tr>
                     <td>6</td>
                     <td>Exposure to a particular industry/sector as a percentage of total portfolio</td>
                     <td>Max 50% of the total CFPL portfolio</td>
                     <td>{{isset($reviewerSummaryData->criteria_particular_portfolio_remark) ? $reviewerSummaryData->criteria_particular_portfolio_remark : ''}}</td>
                  </tr>
               </tbody>
            </table>
           

              </div>
              </div>

              <div class="data mt-4">
             <h2 class="sub-title bg">Purpose of Rental Facility</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
               <p>{{isset($arrCamData->t_o_f_purpose) ? $arrCamData->t_o_f_purpose : ''}}</p>
              </div>
              </div>

               <div class="data mt-4">
             <h2 class="sub-title bg">About the Company</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
              <p>{!! isset($arrCamData->t_o_f_profile_comp) ? $arrCamData->t_o_f_profile_comp : '' !!} </p>
              </div>
              </div>

               <div class="data mt-4">
             <h2 class="sub-title bg">Brief Background of {{isset($arrCamData->contact_person) ? $arrCamData->contact_person : ''}} Managing Director </h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
              <p>{{isset($arrCamData->promoter_cmnt) ? $arrCamData->promoter_cmnt : ''}}</p>
              </div>
              </div>


               <div class="data mt-4">
             <h2 class="sub-title bg">Board of Directors as on {{isset($arrBizData->share_holding_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arrBizData->share_holding_date)->format('j F, Y') : ''}}</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
             <table class="table table-bordered overview-table">
               <thead>
                  <tr>
                     <th width="50%">Name of Director</th>
                     <th width="50%">Designation</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($arrOwnerData as $key => $arrData)
                  <tr>
                     <td>{{$arrData->first_name}}</td>
                     <td>{{$arrData->designation}}</td>
                  </tr>
                  @endforeach
                  
               </tbody>
            </table>
            <h5 class="mt-4">Shareholding Pattern as on {{isset($arrBizData->share_holding_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arrBizData->share_holding_date)->format('j F, Y') : ''}}</h5>
            <table class="table table-bordered overview-table">
               <thead>
                  <tr>
                     <th class="text-center" width="50%">Name</th>
                     <th class="text-center" width="50%">% Holding</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($arrOwnerData as $key => $arrData)
                     @if ($arrData->is_promoter)
                        <tr>
                           <td>{{$arrData->first_name}}</td>
                           <td>{{$arrData->share_per}}</td>
                        </tr>
                     @endif
                  @endforeach
                  
               </tbody>
            </table>
              </div>
              </div>

            
           
            
               <div class="data mt-4">
             <h2 class="sub-title bg">External Rating</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
               <p>{{isset($arrCamData->rating_comment) ? $arrCamData->rating_comment : ''}}</p>
              </div>
              </div>

               <div class="data mt-4">
             <h2 class="sub-title bg">Rating Rationale of {{$arrBizData->biz_entity_name}} </h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
               <p> {!! isset($arrCamData->rating_rational) ? $arrCamData->rating_rational : '' !!} </p>
              </div>
              </div>


           <div class="data mt-4">
             <h2 class="sub-title bg">Standalone Financials of {{$arrBizData->biz_entity_name}}</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
              <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr>
                     <tr>
                          <th valign="middle" bgcolor="#efefef">Particular</th>
                          @foreach($audited_years as $year_aud)
                          <th valign="middle" bgcolor="#efefef">{{$year_aud}}</th>
                          @endforeach
                     </tr>
               </thead>
               <tbody>
                  <tr>
                     <td></td>
                     <td class="text-center"><strong>Aud.</strong></td>
                     <td class="text-center"><strong>Aud.</strong></td>
                     <td class="text-center"><strong>Aud.</strong></td>
                  </tr>
                  <tr>
                     <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                        <table class="table-border-none" width="100%">
                          <tbody>
                             @foreach($FinanceColumns as $finance_col)
                             <tr>
                                 <td height="46">{{$finance_col}}</td>
                             </tr>
                             @endforeach
                          </tbody>
                       </table>
                    </td>
                    @foreach($financeData as $year => $fin_data)
                     <td style="vertical-align:top; padding:0px !important; border-right:none;">
                       <table class="table-border-none" width="100%">
                          <tbody>
                            @php $yearly_fin_data = getTotalFinanceData($fin_data) @endphp
                            @foreach($FinanceColumns as $key => $cols)
                              <tr>
                                <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_fin_data[$key] ?? '')}}"></td>
                             </tr>
                             @endforeach
                          </tbody>
                       </table>
                     </td>
                     @endforeach
                  </tr>
               </tbody>
            </table>
             <h5 class="mt-4">Notes:</h5>
            <ul class="pl-3">
               <li>&#x2714; Cash profit = PAT + Depreciation + Non-operating non-cash outflow items – Provisions</li>
               <li>&#x2714; Total Outside liabilities = Current Liabilities + Term Liabilities</li>
               <li>&#x2714; Net Worth = Share Capital + Reserves – Revaluation reserve</li>
            </ul>
              </div>
              </div>



           
            

            <div class="data mt-4">
             <h2 class="sub-title bg">Financial Comment</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
                 <p>{!! isset($finacialDetails->debt_cmnt) ? $finacialDetails->debt_cmnt : '' !!}</p>
              </div>
              </div>


              <div class="data mt-4">
             <h2 class="sub-title bg">Debt Position as on {{isset($arrBankDetails->debt_on) ? \Carbon\Carbon::createFromFormat('d/m/Y', $arrBankDetails->debt_on)->format('j F, Y') : ''}}</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
               <p> {!! isset($arrBankDetails->debt_position_comments) ? $arrBankDetails->debt_position_comments: '' !!}</p>
              </div>
              </div>


            <div class="data mt-4">
             <h2 class="sub-title bg">Contingent Liabilities and Auditors Observations as on {{isset($arrCamData->debt_on) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arrCamData->debt_on)->format('j F, Y') : ''}}</h2>
              <div class="pl-4 pr-4 pb-4 pt-2">
              <p>{!! isset($arrCamData->contigent_observations) ? $arrCamData->contigent_observations: '' !!}</p>
              </div>
              </div>




           <div class="data mt-4">
           <h2 class="sub-title bg">Risk Comments</h2>
            <div class="pl-4 pr-4 pb-4 pt-2">
                 <div class="data mt-4">
                 <h2 class="sub-title bg">Deal Positives</h2>
                  <div class="pl-4 pr-4 pb-4 pt-2">
                    
                    <table class="table table-bordered overview-table">
                       <tbody>
                          <tr>
                             <td width="50%"><strong>{{isset($reviewerSummaryData->cond_pos_track_rec) ? $reviewerSummaryData->cond_pos_track_rec : ''}}</strong></td>
                             <td width="50%">
                                   {{isset($reviewerSummaryData->cmnt_pos_track_rec) ? $reviewerSummaryData->cmnt_pos_track_rec : ''}}
                             </td>
                          </tr>
                          <tr>
                             <td><strong>{{isset($reviewerSummaryData->cond_pos_credit_rating) ? $reviewerSummaryData->cond_pos_credit_rating : ''}}</strong></td>
                             <td>{{isset($reviewerSummaryData->cmnt_pos_credit_rating) ? $reviewerSummaryData->cmnt_pos_credit_rating : ''}}
                             </td>
                          </tr>
                          <tr>
                             <td><strong>{{isset($reviewerSummaryData->cond_pos_fin_matric) ? $reviewerSummaryData->cond_pos_fin_matric : ''}}</strong></td>
                             <td>{{isset($reviewerSummaryData->cmnt_pos_fin_matric) ? $reviewerSummaryData->cmnt_pos_fin_matric : ''}}
                             </td>
                          </tr>
                          <tr>
                             <td><strong>{{isset($reviewerSummaryData->cond_pos_establish_client) ? $reviewerSummaryData->cond_pos_establish_client : ''}}</strong></td>
                             <td>{{isset($reviewerSummaryData->cmnt_pos_establish_client) ? $reviewerSummaryData->cmnt_pos_establish_client : ''}}
                             </td>
                          </tr>
                       </tbody>
                    </table>
                   </div>
                  </div>  

                  <div class="data mt-4">
                 <h2 class="sub-title bg">Deal Negatives</h2>
                  <div class="pl-4 pr-4 pb-4 pt-2">
               
                    <table class="table table-bordered overview-table">
                       <tbody>
                          <tr>
                             <td width="50%"><strong>{{isset($reviewerSummaryData->cond_neg_competition) ? $reviewerSummaryData->cond_neg_competition : ''}}</strong></td>
                             <td width="50%">{{isset($reviewerSummaryData->cmnt_neg_competition) ? $reviewerSummaryData->cmnt_neg_competition : ''}}
                             </td>
                          </tr>
                          <tr>
                             <td><strong>{{isset($reviewerSummaryData->cond_neg_forex_risk) ? $reviewerSummaryData->cond_neg_forex_risk : ''}}</strong></td>
                             <td>{{isset($reviewerSummaryData->cmnt_neg_forex_risk) ? $reviewerSummaryData->cmnt_neg_forex_risk : ''}}
                             </td>
                          </tr>
                          <tr>
                             <td><strong>{{isset($reviewerSummaryData->cond_neg_pbdit) ? $reviewerSummaryData->cond_neg_pbdit : ''}}</strong></td>
                             <td>{{isset($reviewerSummaryData->cmnt_neg_pbdit) ? $reviewerSummaryData->cmnt_neg_pbdit : ''}}
                             </td>
                          </tr>
                       </tbody>
                    </table>
                    </div>
                  </div>
            </div>
            </div>


              <div class="data mt-4">
                 <h2 class="sub-title bg">Recommendation</h2>
                  <div class="pl-4 pr-4 pb-4 pt-2">
                    <p>{{isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : ''}}
                  </p>
                 </div>
            </div>



         </div>
      </div>
   </div>
</div>
<!-- End PDF Section -->

 
 </div>
</div>
<div class="isloader" style="display:none;">  
        <img src="http://admin.rent.local/backend/assets/images/loader.gif">
    </div>
@endsection
@section('jscript')
<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" ></script>

<script>

function downloadCam(){
    var pdf = new jsPDF('px', 'pt', [1400, 1175]);
    var  res = pdf.html(document.getElementById('camReport'), {
        callback: function (pdf) {
            pdf.save('camReport');
        }
    });
    
}





</script>


@endsection
