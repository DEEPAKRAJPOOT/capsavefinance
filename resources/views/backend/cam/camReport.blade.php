

   <div class="data mt-4">
      <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
         <thead>
            <tr role="row">
               <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Group</th>
               <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Borrower</th>
               <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Proposed Limit(Mn)</th>
               <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Existing Exposure(Mn)</th>
               <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Total Exposure(Mn)</th>
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
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Deal Structure</td>
          </tr>
       </table>

      @forelse($leaseOfferData as $key=>$leaseOffer)
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
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
                  <td class="">  
                      @if($leaseOffer->facility_type_id == 3)
                         NILL
                      @else
                         {{(($leaseOffer->security_deposit_type == 1)?'₹ ':'').$leaseOffer->security_deposit.(($leaseOffer->security_deposit_type == 2)?' %':'')}} of {{config('common.deposit_type')[$leaseOffer->security_deposit_of]}}
                      @endif
                  </td>
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
                  <td class="" valign="top"><b>{{($leaseOffer->facility_type_id == 3)? 'Rental Discounting' : 'XIRR'}} (%)</b></td>
                  <td class="" valign="top">
                      @if($leaseOffer->facility_type_id == 3)
                         {{$leaseOffer->discounting}}%
                      @else
                         <b>Ruby Sheet</b>: {{$leaseOffer->ruby_sheet_xirr}}%<br/><b>Cash Flow</b>: {{$leaseOffer->cash_flow_xirr}}%
                      @endif
                  </td>
               </tr>
               
               <tr role="row" class="odd">
                  <td class=""><b>Additional Security</b></td>
                  <td class="">
                     @php
                      $add_sec_arr = '';
                      if(isset($leaseOffer->addl_security)){
                          $addl_sec_arr = explode(',', $leaseOffer->addl_security);
                          foreach($addl_sec_arr as $k=>$v){
                              $add_sec_arr .= config('common.addl_security')[$v].', ';
                          }
                          if(isset($leaseOffer->comment)) {
                              $add_sec_arr .=  ' <b>Comment</b>:  '.$leaseOffer->comment; 
                           }   
                      }
                      $add_sec_arr = trim($add_sec_arr, ', ');
                      @endphp
                      {!! $add_sec_arr !!}
                  </td>
               </tr>
            </tbody>
         </table>
      <!-- </div> -->

      @empty
         <div class="pl-4 pr-4 pb-4 pt-2">
             <p>No Offer Found</p>
         </div>
   @endforelse

   </div>

   <div class="data mt-4">
        <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Pre Disbursement Conditions</td>
          </tr>
       </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
         <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
               <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="60%">Condition</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
               </tr>
            </thead>
            <tbody>
               @if(isset($preCondArr) && count($preCondArr)>0)
                  @foreach($preCondArr as $prekey =>$preval)
                  <tr role="row" class="odd">
                     <td class="">
                        <p>{{$preval['cond']}}</p> 
                        
                     </td>
                     <td class="">
                        <p>{{$preval['timeline']}}</p>
                     </td>
                  </tr>
                  @endforeach
               @endif
            </tbody>
         </table>
      <!-- </div> -->
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Post Disbursement Conditions</td>
          </tr>
       </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
         <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
               <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="60%">Condition</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
               </tr>
            </thead>
            <tbody>
               @if(isset($postCondArr) && count($postCondArr)>0)
                  @foreach($postCondArr as $postkey =>$postval)
                     <tr role="row" class="odd">
                           <td class="">
                              <p> {{$postval['cond']}} </p>
                           </td>
                           <td class="">
                              <p> {{$postval['timeline']}} </p>
                           </td>
                     </tr>
                  @endforeach
               @endif
            </tbody>
         </table>
      <!-- </div> -->
   </div>

 
   <div class="data mt-4">
      <table class="table  no-footer overview-table" cellpadding="0" cellspacing="0" role="grid">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Minimum Acceptance Criteria as per NBFC Credit Policy</td>
          </tr>
       </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
         <table id="invoice_history" class="table no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
               <tr>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Parameter</th>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Criteria</th>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="10%">Deviation</th>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Remarks</th>
               </tr>
            </thead>
            <tbody>
                <tr style="background-color: #d2d4de;">
                  <th colspan="4" class="blank" >Borrower Vintage &amp; Constitution</th>
               </tr>
               <tr>
                  <td>Constitution</td>
                  <td>- Registered Partnership Firm<br/> - Private Limited Company<br/> - Public Limited Company<br/> - Limited Liability Partnership </td>
                  <td>No</td>
                  <td>{{isset($arrEntityData->name) ? trim($arrEntityData->name) : ''}}</td>
               </tr>
               <tr>
                  <td>Vintage</td>
                  <td> - Minimum 3 years of vintage in relevant business<br/> - Parent or group company with requisite vintage<br/> - Key promoter with 5 years of relevant vintage </td>
                  <td>No</td>
                  <td>{{isset($arrBizData->date_of_in_corp) ? \Carbon\Carbon::parse($arrBizData->date_of_in_corp)->format('d/m/Y') : '' }}</td>
               </tr>
               <tr style="background-color: #d2d4de;">
                  <td colspan="4" class="blank">&nbsp;</td>
               </tr>
               <tr>
                  <td>CFPL Defaulter List</td>
                  <td>No</td>
                  <td>{{isset($arrHygieneData->cfpl_default_check) && $arrHygieneData->cfpl_default_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->cfpl_default_cmnt) ? trim($arrHygieneData->cfpl_default_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>RBI Defaulter list</td>
                  <td>No</td>
                  <td>{{isset($arrHygieneData->cibil_check) && $arrHygieneData->cibil_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->rbi_willful_defaulters) ? trim($arrHygieneData->rbi_willful_defaulters) : ''}}</td>
               </tr>
               <tr>
                  <td>CDR/ BIFR/ OTS/ Restructuring</td>
                  <td>No</td>
                  <td>{{isset($arrHygieneData->cdr_check) && $arrHygieneData->cdr_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->cdr_cmnt) ? trim($arrHygieneData->cdr_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>CIBIL</td>
                  <td>No Adverse Remarks</td>
                  <td>{{isset($arrHygieneData->cibil_defaulters_chk) && $arrHygieneData->cibil_defaulters_chk == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->comment) ?trim($arrHygieneData->comment) : ''}}</td>
               </tr>
               <tr>
                  <td>Watchout Investors</td>
                  <td>No Adverse Remarks</td>
                  <td>{{isset($arrHygieneData->watchout_investors_chk) && $arrHygieneData->watchout_investors_chk == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->watchout_investors) ? trim($arrHygieneData->watchout_investors) : ''}}</td>
               </tr>
               <tr>
                  <td>Google Search (Negative searches)</td>
                  <td>No </td>
                  <td>{{isset($arrHygieneData->neg_news_report_check) && $arrHygieneData->neg_news_report_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->neg_news_report_cmnt) ? trim($arrHygieneData->neg_news_report_cmnt) : ''}}</td>
               </tr>
               <tr style="background-color: #d2d4de;">
                  <td colspan="4" class="blank">&nbsp;</td>
               </tr>
               <tr>
                  <td>Satisfactory contact point verification</td>
                  <td>Yes </td>
                  <td>{{isset($arrHygieneData->contact_point_check) && $arrHygieneData->contact_point_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->contact_point_cmnt) ? trim($arrHygieneData->contact_point_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>Satisfactory banker reference</td>
                  <td>Yes </td>
                  <td>{{isset($arrHygieneData->bank_ref_check) && $arrHygieneData->bank_ref_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->bank_ref_cmnt) ? trim($arrHygieneData->bank_ref_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>Satisfactory trade reference</td>
                  <td>Yes </td>
                  <td>{{isset($arrHygieneData->trade_ref_check) && $arrHygieneData->trade_ref_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->trade_ref_cmnt) ? trim($arrHygieneData->trade_ref_cmnt) : ''}}</td>
               </tr>
               <tr style="background-color: #d2d4de;">
                  <td colspan="4"  class="blank">&nbsp;</td>
               </tr>
               <tr>
                  <td>Adjusted Tangible Net Worth</td>
                  <td>Positive for last 2 financial years </td>
                  <td>{{isset($finacialDetails->adj_net_worth_check) && $finacialDetails->adj_net_worth_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($finacialDetails->adj_net_worth_cmnt) ? trim($finacialDetails->adj_net_worth_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>Cash Profit</td>
                  <td>Positive for 2 out of last 3 financial years <br/>(positive in last year)</td>
                  <td>{{isset($finacialDetails->cash_profit_check) && $finacialDetails->cash_profit_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($finacialDetails->cash_profit_cmnt) ? trim($finacialDetails->cash_profit_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>DSCR</td>
                  <td>&gt;1.2X</td>
                  <td>{{isset($finacialDetails->dscr_check) && $finacialDetails->dscr_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($finacialDetails->dscr_cmnt) ? trim($finacialDetails->dscr_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>Debt/EBIDTA</td>
                  <td>&lt;5X</td>
                  <td>{{isset($finacialDetails->debt_check) && $finacialDetails->debt_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($finacialDetails->debt_cmnt) ? trim($finacialDetails->debt_cmnt) : ''}}</td>
               </tr>
               <tr style="background-color: #d2d4de;">
                  <th colspan="4" class="blank"> Other                </th>
               </tr>
               <tr>
                  <td>Negative Industry Segment</td>
                  <td>No</td>
                  <td>{{isset($arrHygieneData->neg_industry_check) && $arrHygieneData->neg_industry_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->neg_industry_cmnt) ? trim($arrHygieneData->neg_industry_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>Exposure to sensitive sectors</td>
                  <td>No</td>
                  <td>{{isset($arrHygieneData->senstive_sector_check) && $arrHygieneData->senstive_sector_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->senstive_sector_cmnt) ? trim($arrHygieneData->senstive_sector_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>Sensitive geography/region/area</td>
                  <td>No</td>
                  <td>{{isset($arrHygieneData->senstive_region_check) && $arrHygieneData->senstive_region_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->senstive_region_cmnt) ? trim($arrHygieneData->senstive_region_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>Politically exposed person</td>
                  <td>No</td>
                  <td>{{isset($arrHygieneData->politically_check) && $arrHygieneData->politically_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->pol_exp_per_cmnt) ? trim($arrHygieneData->pol_exp_per_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>KYC risk profile</td>
                  <td>{{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'High' ? 'High' : '' }} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Med' ?  'Medium' : ''}} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Low' ? 'Low' : '' }} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'No' ? 'No' : '' }} </td>
                  <td>{{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'High' ? 'Highf' : '' }} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Med' ?  'Medium' : ''}} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Low' ? 'Low' : '' }} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'No' ? 'No' : '' }} </td>
                  <td>{{isset($arrHygieneData->kyc_risk_cmnt) ? trim($arrHygieneData->kyc_risk_cmnt) : ''}}</td>
               </tr>
               <tr>
                  <td>UNSC List</td>
                  <td>No</td>
                  <td>{{isset($arrHygieneData->unsc_check) && $arrHygieneData->unsc_check == 'Yes' ? 'Yes' : 'No'}}</td>
                  <td>{{isset($arrHygieneData->unsc_cmnt) ? trim($arrHygieneData->unsc_cmnt) : ''}}</td>
               </tr>
            </tbody>
         </table>
      <!-- </div> -->
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Approval Criteria for IC</td>
          </tr>
       </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
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
                  <td>- IT assets and telecommunications max 70%<br/>- Plant and machinery max 50%<br/>- Furniture and fit outs max 30%
                     <br/>- Any other asset type max 20%
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
      <!-- </div> -->
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Purpose of Rental Facility</td>
          </tr>
       </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
         <p>{!! isset($arrCamData->t_o_f_purpose) ? $arrCamData->t_o_f_purpose : '' !!}</p>
      <!-- </div> -->
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">About the Company</td>
          </tr>
       </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($arrCamData->t_o_f_profile_comp) ? $arrCamData->t_o_f_profile_comp : '' !!} </p>
      </div>
   </div>

   <div class="data mt-4">
    <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Brief Background of {{isset($arrCamData->contact_person) ? $arrCamData->contact_person : ''}} Managing Director</td>
          </tr>
       </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($arrCamData->promoter_cmnt) ? $arrCamData->promoter_cmnt : '' !!}</p>
      </div>
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Board of Directors as on {{isset($arrBizData->share_holding_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arrBizData->share_holding_date)->format('j F, Y') : ''}}</td>
          </tr>
       </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
         <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0">
            <thead>
               <tr>
                  <th width="50%">Name of Director</th>
                  <th width="50%">Designation</th>
               </tr>
            </thead>
            <tbody>
            @if(!empty($arrOwnerData))
               @foreach($arrOwnerData as $key => $arrData)
               @if ($arrData->gender != '3')
               <tr>
                  <td>{{$arrData->first_name}}</td>
                  <td>{{$arrData->designation}}</td>
               </tr>
               @endif
               @endforeach
            @endif  
               
            </tbody>
         </table>
         <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Shareholding Pattern as on {{isset($arrBizData->share_holding_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arrBizData->share_holding_date)->format('j F, Y') : ''}}</td>
          </tr>
       </table>
         <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0">
            <thead>
               <tr>
                  <th class="text-center" width="50%">Name</th>
                  <th class="text-center" width="50%">% Holding</th>
               </tr>
            </thead>
            <tbody>
            @if(!empty($arrOwnerData))
                  @foreach($arrOwnerData as $key => $arrData)
                  @if ($arrData->gender == '3' || $arrData->is_promoter)
                        <tr>
                           <td>{{$arrData->first_name}}</td>
                           <td>{{$arrData->share_per}}</td>
                        </tr>
                  @endif
                  @endforeach
            @endif
            </tbody>
         </table>
      <!-- </div> -->
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">External Rating</td>
          </tr>
       </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($arrCamData->rating_comment) ? $arrCamData->rating_comment : '' !!}</p>
      </div>
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Rating Rationale of {{$arrBizData->biz_entity_name}}</td>
          </tr>
       </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p> {!! isset($arrCamData->rating_rational) ? $arrCamData->rating_rational : '' !!} </p>
      </div>
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Standalone Financials of {{$arrBizData->biz_entity_name}}</td>
          </tr>
       </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
         <table width="100%" id="invoice_history" class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
                  <tr>
                     <th valign="middle" bgcolor="#efefef"> Particular</th>
                     @if(!empty($audited_years))
                        @foreach($audited_years as $year_aud)
                        <th valign="middle" bgcolor="#efefef">{{$year_aud}}</th>
                        @endforeach
                     @endif    
                  </tr>
            </thead>
            <tbody>
                  <tr @if (empty($audited_years)) class='hide' @endif>
                     <td width="40%"></td>
                     @foreach($financeData as $year => $fin_data)
                     <td><strong>Aud.</strong></td>
                     @endforeach
                  </tr>
                  @foreach($FinanceColumns as $key => $finance_col)
                  <tr>
                     <td>{{$finance_col}}</td>
                     @foreach($financeData as $year => $fin_data)
                     <td align="right">
                        @php $yearly_fin_data = getTotalFinanceData($fin_data) @endphp
                        {{sprintf('%.2f', $yearly_fin_data[$key ] ?? '')}}
                        @endforeach
                     </td>
                  </tr>
                  @endforeach
            </tbody>
         </table>
         <!-- 
            <h5 class="mt-4">Notes:</h5>
            <ul class="pl-3">
               <li>&#x2714; Cash profit = PAT + Depreciation + Non-operating non-cash outflow items – Provisions</li>
               <li>&#x2714; Total Outside liabilities = Current Liabilities + Term Liabilities</li>
               <li>&#x2714; Net Worth = Share Capital + Reserves – Revaluation reserve</li>
            </ul> 
         -->
      <!-- </div> -->
   </div>

   <div class="data mt-4">
    <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Financial Comment</td>
          </tr>
       </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($finacialDetails->financial_risk_comments) ? $finacialDetails->financial_risk_comments : '' !!}</p>
      </div>
   </div>

   <div class="data mt-4">
     <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Debt Position as on {{isset($arrBankDetails->debt_on) ? \Carbon\Carbon::createFromFormat('d/m/Y', $arrBankDetails->debt_on)->format('j F, Y') : ''}}</td>
          </tr>
       </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p> {!! isset($arrBankDetails->debt_position_comments) ? $arrBankDetails->debt_position_comments: '' !!}</p>
      </div>
   </div>

   <div class="data mt-4">
       <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Contingent Liabilities and Auditors Observations as on {{isset($arrCamData->debt_on) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arrCamData->debt_on)->format('j F, Y') : ''}}</td>
          </tr>
       </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($arrCamData->contigent_observations) ? $arrCamData->contigent_observations: '' !!}</p>
      </div>
   </div>

   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Risk Comments</td>
          </tr>
       </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->

         <div class="data mt-4">
            <table class="table" cellpadding="0" cellspacing="0">
              <tr>
                  <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#138864">Deal Positives</td>
              </tr>
          </table>
            <div class="pl-4 pr-4 pb-4 pt-2">
               <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0">
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
              <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#138864">Deal Negatives</td>
                </tr>
            </table>
            <div class="pl-4 pr-4 pb-4 pt-2">
               <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0">
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

      <!-- </div> -->
   </div>

   <div class="data mt-4">
    <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Recommendation</td>
                </tr>
            </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : '' !!} </p>
      </div>
   </div>

   <div class="data mt-4">
    <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">The proposed deal is <span id="isApproved"></span> subject to above conditions and any other conditions mentioned below.</td>
                </tr>
            </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
         <table width="100%" id="invoice_history" class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
               <tr>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Recommended By</th>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="3" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="75%">Investment Committee Members</th>
               </tr>
            </thead>
            <tbody>
               <tr role="row" >
                  <td align="center">
                     <table class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0" style="border:none;">
                           @php 
                              $i=0;
                           @endphp
                           @while(!empty($arrReviewer[$i])) 
                              <tr>
                                 <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" style="background-color:transparent !important; color:#696969 !important;">{{$arrReviewer[$i]->assignee}}</th>
                                 @php $i++; @endphp
                              </tr>
                        @endwhile
                     </table> 
                  </td>
                  <td align="center" colspan="3">
                     <table class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0" style="border:none;">
                           @php 
                              $i=0;
                              $j= 0;
                              $arrApproverDataCount = count($arrApproverData);
                           @endphp
                           @if(!empty($arrApproverData))
                              @while(!empty($arrApproverData[$i])) 
                                 <tr>
                                       <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" style="background-color:transparent !important; color:#696969 !important;"> {{$arrApproverData[$i]->approver}}
                                        @if ($arrApproverData[$i]->status == 1) 
                                          <h5 style="color:#37c936; font-size: 11px;">(Approved)</h5> @php $j++; @endphp 
                                          <span style="font-size: 11px;">Approved at </br>
                                          {{ \Carbon\Carbon::parse($arrApproverData[$i]->updated_at)->format('h:i A, j F, Y')}}</span>
                                       @endif 

                                       </th>
                                       @php $i++; @endphp
                                       @if (!empty($arrApproverData[$i]))
                                          <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" style="background-color:transparent !important; color:#696969 !important;">{{$arrApproverData[$i]->approver}} 
                                          @if ($arrApproverData[$i]->status == 1)
                                           <h5 style="color:#37c936; font-size: 11px;">(Approved)</h5> @php $j++; @endphp 
                                             <span style="font-size: 11px;">Approved at </br>{{ \Carbon\Carbon::parse($arrApproverData[$i]->updated_at)->format('h:i A, j F, Y')}}</span>
                                          @endif   
                                          </th>
                                          @php $i++; @endphp
                                       @endif
                                 </tr>
                              @endwhile
                           @endif
                     </table>
                  </td>
               </tr>
            </tbody>
         </table>
      <!-- </div> -->
   </div>

<script>
if('{{$arrApproverDataCount}}' ==  '{{$j}}' && '{{$arrApproverDataCount}}' != 0){
   document.getElementById("isApproved").textContent += "approved";
}
</script>         
 <!-- End PDF Section -->