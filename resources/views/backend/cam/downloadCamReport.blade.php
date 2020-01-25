@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
 
<div class="inner-container">
    <div class="card mt-3">
        <div class="card-body pt-3 pb-3">
            <button onclick="downloadCam()" class="btn btn-primary float-right btn-sm " > Download Report</button>
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
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Proposed Limit (₹ Mn)</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Existing Exposure (₹ Mn)</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Total Exposure (₹ Mn)</th>
                  </tr>
               </thead>
               <tbody>
                  <tr role="row" class="odd">
                     <td class=""></td>
                     <td class="">{{$arrBizData->biz_entity_name}}</td>
                     <td class=""><span class="fa fa-inr" aria-hidden="true" style="position:absolute; margin:4px -9px;  "></span>{{isset($arrCamData->proposed_exposure) ? $arrCamData->proposed_exposure : ''}}</td>
                     <td class=""><span class="fa fa-inr" aria-hidden="true" style="position:absolute; margin:4px -9px;  "></span>{{isset($arrCamData->existing_exposure) ? $arrCamData->existing_exposure : ''}}</td>
                     <td class="">{!! $arrCamData->total_exposure ? \Helpers::formatCurreny($arrCamData->total_exposure) : '' !!}</td>
                  </tr>
               </tbody>
            </table>
            <h5 class="mt-4">Deal Structure:</h5>
            <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr role="row">
                     <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Criteria</th>
                     <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Particulars</th>
                  </tr>
               </thead>
               <tbody>
                  <tr role="row" class="odd">
                     <td class="">Facility Type</td>
                     <td class="">Rental facility</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">Limit (₹ In Mn)</td>
                     <td class="">40</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">Tenor (Months)</td>
                     <td class="">60</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">Equipment Type</td>
                     <td class="">Plant and Machinery</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">Security Deposit</td>
                     <td class="">5 % of invoice value</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">Rental Frequency</td>
                     <td class="">Quarterly in advance</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">PTPQ</td>
                     <td class="">Rs. 32 per quarter for first 8 quarters and Rs. 92 for balance 12 quarters</td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="" valign="top">XIRR</td>
                     <td class="" valign="top">Ruby Sheet : 14.69%<br>Cash Flow : 13.79%
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">Additional Security</td>
                     <td class="">Personal guarantee of Mr. Anand Desai
                     </td>
                  </tr>
               </tbody>
            </table>
            <h5 class="mt-4">Pre Disbursement Conditions:</h5>
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
                        <input type="text"  value="{{isset($reviewerSummaryData->cond_nach) ? $reviewerSummaryData->cond_nach : ''}}" class="form-control form-control-sm">
                     </td>
                     <td class="">
                        <input type="text" value="{{isset($reviewerSummaryData->time_nach) ? $reviewerSummaryData->time_nach : ''}}" class="form-control form-control-sm">
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">
                        <input type="text" name="cond_insp_asset" value="{{isset($reviewerSummaryData->cond_insp_asset) ? $reviewerSummaryData->cond_insp_asset : ''}}" class="form-control form-control-sm">
                     </td>
                     <td class="">
                        <input type="text" name="time_insp_asset" value="{{isset($reviewerSummaryData->time_insp_asset) ? $reviewerSummaryData->time_insp_asset : ''}}" class="form-control form-control-sm">
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                    <td class="">
                        <input type="text" name="cond_insu_pol_cfpl" value="{{isset($reviewerSummaryData->cond_insu_pol_cfpl) ? $reviewerSummaryData->cond_insu_pol_cfpl : ''}}" class="form-control form-control-sm">
                     </td>
                     <td class="">
                        <input type="text" name="time_insu_pol_cfpl" value="{{isset($reviewerSummaryData->time_insu_pol_cfpl) ? $reviewerSummaryData->time_insu_pol_cfpl : ''}}" class="form-control form-control-sm">
                     </td>
                  </tr>
                  <tr role="row" class="odd">
                     <td class="">
                        <input type="text"  name="cond_personal_guarantee" value="{{isset($reviewerSummaryData->cond_personal_guarantee) ? $reviewerSummaryData->cond_personal_guarantee : ''}}" class="form-control form-control-sm">
                     </td>
                     <td class="">
                        <input type="text"  name="time_personal_guarantee" value="{{isset($reviewerSummaryData->time_personal_guarantee) ? $reviewerSummaryData->cond_insu_pol_cfpl : ''}}" class="form-control form-control-sm">
                     </td>
                  </tr>
                  
               </tbody>
            </table>

            <h5 class="mt-4">Post Disbursement Conditions:</h5>
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

            <h5 class="mt-4">The proposed deal is approved/declined/deferred subject to above conditions and any other conditions mentioned below.</h5>
            <table id="invoice_history" class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Recommended By</th>
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="3" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="75%">Investment Committee Members</th>
                  </tr>
                  <tr role="row">
                     <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%" style="background:#62b59b;">Dhriti Barman</th>
                     <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%" style="background:#62b59b;">Vivek Tolat/Sharon Coorlawala</th>
                     <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%" style="background:#62b59b;">Jinesh Kumar Jain</th>
                     <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%" style="background:#62b59b;">Praveen Chauhan</th>
                  </tr>
               </thead>
               <tbody>
                  <tr role="row" class="odd">
                     <td align="center">Text</td>
                     <td align="center">Text</td>
                     <td align="center">Text</td>
                     <td align="center">Text</td>
                  </tr>
               </tbody>
            </table>
            <h5 class="mt-4">Minimum Acceptance Criteria as per NBFC Credit Policy:</h5>
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
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'No' ? No : ''
                           }}
                        </td>
                     <td>
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'High' ? 'Highf' : '' }}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Med' ?  'Medium' : ''}}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Low' ? 'Low' : '' }}
                           {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'No' ? No : ''
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
            <h5 class="mt-4">Approval criteria for IC:</h5>
            <table id="invoice_history" class="table   table-striped no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
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
            <h5 class="mt-4">Purpose of Rental Facility</h5>
            <p>{{isset($arrCamData->t_o_f_purpose) ? $arrCamData->t_o_f_purpose : ''}}</p>
            <h5 class="mt-4"> About the Company</h5>
            <p>{{isset($arrCamData->t_o_f_profile_comp) ? $arrCamData->t_o_f_profile_comp : ''}} </p>
            



            <h5 class="mt-4">Brief Background of Mr. Anand Desai; Managing Director :</h5>
            <p>{{isset($arrCamData->promoter_cmnt) ? $arrCamData->promoter_cmnt : ''}}</p>
            <!-- <p class="text-center "><img class="img-fluid" src="assets/img/image.png"></p> -->
            <h5 class="mt-4"> Board of Directors as on December 2019</h5>
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
            <h5 class="mt-4">  Shareholding Pattern as on October 30, 2019</h5>
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
            <br>       
           
            
            <h5 class="mt-4">External Rating</h5>
            <p>{{isset($arrCamData->rating_comment) ? $arrCamData->rating_comment : ''}}</p>

            <h5>Rating rationale of Anupam Rasayan India Limited :</h5>
            <p> {{isset($arrCamData->rating_rational) ? $arrCamData->rating_rational : ''}} </p>
           







            <h5 class="mt-3">Standalone Financials of ARIL:</h5>
            <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
               <thead>
                  <tr>
                     <tr>
                          <th valign="middle" bgcolor="#efefef">Perticular</th>
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
                                <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_fin_data[$key])}}"></td>
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
               <li><i class="fa fa-check" aria-hidden="true"></i> Cash profit = PAT + Depreciation + Non-operating non-cash outflow items – Provisions</li>
               <li><i class="fa fa-check" aria-hidden="true"></i> Total Outside liabilities = Current Liabilities + Term Liabilities</li>
               <li><i class="fa fa-check" aria-hidden="true"></i> Net Worth = Share Capital + Reserves – Revaluation reserve</li>
            </ul>




            <h5 class="mt-4">Fin Comment:</h5>
           
           
            
            
            
            <h5 class="mt-4">Debt Position as on March 31, 2019:</h5>
            <table class="table table-bordered overview-table">
               <thead>
                  <tr>
                     <th width="50%">Particulars</th>
                     <th width="50%">Rs in Mn</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>Long term borrowings*</td>
                     <td>4552.10</td>
                  </tr>
                  <tr>
                     <td>Short term borrowings</td>
                     <td>1538.80</td>
                  </tr>
               </tbody>
            </table>
            <p>*Includes long term ECB loan from Pref Shareholders (Kiran Pallavi Investments LLC) of Rs. 2343.70 Mn</p>
           

           
            <h5 class="mt-4">Contingent Liabilities and Auditors Observations as on March 31, 2019:</h5>
            <p>Nil as on March 31, 2019.</p>
            <h5 class="mt-4">Risk Comments:</h5>
            <h5 class="mt-2"><small>Deal Positives:</small></h5>
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
            <h5 class="mt-2"><small>Deal Negatives:</small></h5>
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
            <h5 class="mt-4">Recommendation:</h5>
            <p>{{isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : ''}}
            </p>
         </div>
      </div>
   </div>
</div>
<!-- End PDF Section -->

 
 </div>
</div>
@endsection
@section('jscript')


<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" ></script>



<script>

function downloadCam(){
    var pdf = new jsPDF('px', 'pt', [1150, 1500]);
    pdf.html(document.getElementById('camReport'), {
        callback: function (pdf) {
            pdf.save('camReport');
        }
    });
}





</script>


@endsection
