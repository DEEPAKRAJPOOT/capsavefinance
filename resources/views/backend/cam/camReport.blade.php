@if(!empty($arrGroupCompany))
   <div class="data mt-4">
       

         <table class="table" cellpadding="0" cellspacing="0">
         <tr>
            <td bgcolor="#8a8989" style="color:#fff;font-size: 15px;font-weight: bold;">Group Company Exposure</td>
            <td bgcolor="#8a8989" align="right"><span  style="font-size: 11px; color: #ffffff;">
                                        @if(isset($arrCamData->By_updated))  
                                            Updated By: {{$arrCamData->By_updated}} ({!! isset($arrCamData->updated_at) ?  \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$arrCamData->updated_at)->format('j F, Y') : '' !!})
                                        @endif
                                    </span> </td>

         </tr>
            
         </table>                           
         
         <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
               <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="12%">Group Name</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="18%">Borrower</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="18%">Sanction Limit (In Mn)</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="22%">Outstanding Exposure (In Mn)</th> 
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="18%">Proposed Limit (In Mn) </th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="12%">Total (In Mn)</th>
                 
               </tr>
            </thead>
            <tbody>
                  
                     @php $count = count($arrGroupCompany);
                      @endphp
                     @foreach($arrGroupCompany as $key=>$arr)
                        <tr role="row" class="odd">
                           @if($loop->first)
                               <td class="" rowspan="{{$count}}"> {{isset($arrCamData->group_company) ? $arrCamData->group_company : ''}}</td>
                           @endif
                           <td class="">{{isset($arr['group_company_name']) ? $arr['group_company_name'] : ''}}</td>
                           <td class="">{{($arr['sanction_limit'] > 0) ? $arr['sanction_limit'] : ''}}</td>
                           <td class="">{{ ($arr['outstanding_exposure'] > 0) ? $arr['outstanding_exposure']: '' }}</td>
                           <td class="">{{ (($arr['proposed_exposure'] > 0)) ? $arr['proposed_exposure'] : '--' }}</td>
                           <td class="">{{ (($arr['outstanding_exposure'] > 0) || ($arr['proposed_exposure'] > 0)) ?  $arr['outstanding_exposure'] + $arr['proposed_exposure'] : '' }}</td>
                          
                        </tr>
                        @endforeach
                    
                     
                     <tr>
                           <td class="" colspan="5"><b>Total Exposure (In Mn)</b></td>
                           <td class=""><b>{{($arrCamData && $arrCamData->total_exposure_amount > 0) ? $arrCamData->total_exposure_amount : ''}}</b></td>   
                     </tr>
            </tbody>
         </table>
         
   </div>
@endif 

@include('backend.cam.deal_structure_offers')

@if(isset($preCondArr) && count($preCondArr)>0)
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Pre Disbursement Conditions</td>
        </tr>
     </table>
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
               @else
                  <tr role="row" class="odd">
                     <td colspan="2">
                        <p>No Record Found.</p> 
                        
                     </td> 
                  </tr>  
               @endif
            </tbody>
         </table>
   </div>
@endif 
@if(isset($postCondArr) && count($postCondArr)>0)
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Post Disbursement Conditions</td>
        </tr>
      </table>
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
               @else
                  <tr role="row" class="odd">
                     <td class="" colspan="2">
                        <p>No Record Found.</p> 
                        
                     </td> 
                  </tr> 
               @endif
            </tbody>
         </table>
   </div>
@endif 
 
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Minimum Acceptance Criteria as per NBFC Credit Policy</td>
        </tr>
      </table>
         <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
               <tr>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Parameter <br/></th>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Criteria</th>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="10%">Deviation</th>
                  <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Remarks</th>
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
                  <td>{{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'High' ? 'High' : '' }} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Med' ?  'Medium' : ''}} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'Low' ? 'Low' : '' }} {{ isset($arrHygieneData->kyc_risk_check) && $arrHygieneData->kyc_risk_check == 'No' ? 'No' : '' }} </td>
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
   </div>

        @if (isset($anchorRelationData))
        <div class="data">
            <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Anchor Criteria</td>
                </tr>
            </table>
            <div class="pl-4 pr-4 pb-4 pt-2">

                <table class="table table-bordered overview-table" id="myTable3">

                    <thead>
                        <tr bgcolor="#ccc">
                            <th>Parameter</th>
                            <th>Criteria</th>
                            <th>Actual  </th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="working_capital_facility">

                        <tr>
                            <td> Vintage with Anchor</td>
                            <td>{{isset($anchorRelationData['year_of_association']) ? $anchorRelationData['year_of_association'] . ' Years' : ''}}</td>
                            <td>{{isset($anchorRelationData['year_of_assoc_actual']) ? $anchorRelationData['year_of_assoc_actual'] : ''}}</td>
                            <td>{{isset($anchorRelationData['year_of_assoc_remark']) ? $anchorRelationData['year_of_assoc_remark'] : ''}}</td>
                        </tr>
                        <tr>
                            <td>Dependence on Anchor </td>
                            <td>{{isset($anchorRelationData['dependence_on_anchor']) ? $anchorRelationData['dependence_on_anchor'] : ''}} </td>
                            <td>{{isset($anchorRelationData['dependence_on_anchor_actual']) ? $anchorRelationData['dependence_on_anchor_actual'] : ''}}</td>
                            <td>{{isset($anchorRelationData['dependence_on_anchor_remark']) ? $anchorRelationData['dependence_on_anchor_remark'] : ''}}</td>
                        </tr>
                        <tr>
                            <td>Quarter on Quarter off take from Anchor </td>
                            <td>{{isset($anchorRelationData['qoq_ot_from_anchor']) ? $anchorRelationData['qoq_ot_from_anchor'] : ''}} </td>
                            <td>{{isset($anchorRelationData['qoq_ot_from_anchor_actual']) ? $anchorRelationData['qoq_ot_from_anchor_actual'] : ''}}</td>
                            <td>{{isset($anchorRelationData['qoq_ot_from_anchor_remark']) ? $anchorRelationData['qoq_ot_from_anchor_remark'] : ''}}</td>
                        </tr>
                        <tr>
                            <td>Categorization/ Relevance by Anchor </td>
                            <td>{{isset($anchorRelationData['cat_relevance_by_anchor']) ? $anchorRelationData['cat_relevance_by_anchor'] : ''}}</td>
                            <td>{{isset($anchorRelationData['cat_relevance_by_anchor_actual']) ? $anchorRelationData['cat_relevance_by_anchor_actual'] : ''}}</td>
                            <td>{{isset($anchorRelationData['cat_relevance_by_anchor_remark']) ? $anchorRelationData['cat_relevance_by_anchor_remark'] : ''}}</td>
                        </tr>
                        <tr>
                            <td>Repayment track record with Anchor </td>
                            <td>{{isset($anchorRelationData['repayment_track_record']) ? $anchorRelationData['repayment_track_record'] : ''}}</td>
                            <td>{{isset($anchorRelationData['repayment_track_record_actual']) ? $anchorRelationData['repayment_track_record_actual'] : ''}}</td>
                            <td>{{isset($anchorRelationData['repayment_track_record_remark']) ? $anchorRelationData['repayment_track_record_remark'] : ''}}</td>
                        </tr>

                    </tbody>
                </table>
                <div class="clearfix"></div>
            </div>
        </div>
        @endif
        @if (isset($anchorRelationData['sec_third_gen_trader']) || isset($anchorRelationData['alt_buss_of_trader']) || isset($anchorRelationData['self_owned_prop']) || isset($anchorRelationData['trade_ref_check_actual']) || isset($anchorRelationData['adv_tax_payment']))
        <div class="data mt-4">
            <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Surrogate Criteria</td>
                </tr>
            </table>
            <div class="pl-4 pr-4 pb-4 pt-2">
                <table class="table table-bordered overview-table" id="myTable3">
                    <thead>
                        <tr bgcolor="#ccc">
                            <th>Parameter</th>
                            <th>Criteria</th>
                            <th>Actual </th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td width="25%">Second/Third Generation Trader</td>
                            <td>
                                <label class="checkbox-inline mr-3">
                                    {{isset($anchorRelationData['sec_third_gen_trader']) ? Helpers::getYesFlag($anchorRelationData['sec_third_gen_trader']) : ''}}
                                </label>
                            </td>
                            <td>
                                {{isset($anchorRelationData['gen_trader_actual']) ? $anchorRelationData['gen_trader_actual'] : ''}}
                            </td>
                            <td>
                                {{isset($anchorRelationData['gen_trader_remark']) ? $anchorRelationData['gen_trader_remark'] : ''}}
                            </td>
                        </tr>
                        <tr>
                            <td>Alternate business of trader </td>
                            <td>
                                <label class="checkbox-inline mr-3">
                                    {{isset($anchorRelationData['alt_buss_of_trader']) ? Helpers::getYesFlag($anchorRelationData['alt_buss_of_trader']) : ''}}
                                </label>
                            </td>
                            <td>
                                {{isset($anchorRelationData['alt_buss_of_trader_actual']) ? $anchorRelationData['alt_buss_of_trader_actual'] : ''}}
                            </td>
                            <td>
                                {{isset($anchorRelationData['alt_buss_of_trader_remark']) ? $anchorRelationData['alt_buss_of_trader_remark'] : ''}}
                            </td>
                        </tr>
                        <tr>
                            <td>Self occupied/self owned property </td>
                            <td>
                                <label class="checkbox-inline mr-3">
                                {{isset($anchorRelationData['self_owned_prop']) ? Helpers::getYesFlag($anchorRelationData['self_owned_prop']) : ''}}
                                </label>
                            </td>
                            <td>
                                {{isset($anchorRelationData['self_owned_prop_actual']) ? $anchorRelationData['self_owned_prop_actual'] : ''}}
                            </td>
                            <td>
                                {{isset($anchorRelationData['self_owned_prop_remark']) ? $anchorRelationData['self_owned_prop_remark'] : ''}}
                            </td>
                        </tr>
                        <tr>
                            <td>Trade reference check</td>
                            <td>
                                <label class="checkbox-inline mr-3">
                                Positive
                                </label>
                                </td>
                            <td>
                                {{isset($anchorRelationData['trade_ref_check_actual']) ? $anchorRelationData['trade_ref_check_actual'] : ''}}
                            </td>
                            <td>
                                {{isset($anchorRelationData['trade_ref_check_remark']) ? $anchorRelationData['trade_ref_check_remark'] : ''}}
                            </td>
                        </tr>
                        <tr>
                            <td>Advance/sales tax payment </td>
                            <td>
                                <label class="checkbox-inline mr-3">
                                {{isset($anchorRelationData['adv_tax_payment']) ? Helpers::getYesFlag($anchorRelationData['adv_tax_payment']) : ''}}
                                </label>
                            </td>
                            <td>
                                {{isset($anchorRelationData['adv_tax_payment_actual']) ? $anchorRelationData['adv_tax_payment_actual'] : ''}}
                            </td>
                            <td>
                                {{isset($anchorRelationData['adv_tax_payment_remark']) ? $anchorRelationData['adv_tax_payment_remark'] : ''}}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="clearfix"></div>
            </div>
        </div>
        @endif
        @if (isset($anchorRelationData) && (isset($data) && count($data) > 0))
        <div class="data mt-4">
            <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Relationship With Anchor Company</td>
                </tr>
            </table>
            <div class="pl-4 pr-4 pb-4 pt-2">

                <table class="table table-bordered overview-table" id="myTable3">
                    <tbody>
                        <tr>
                            <td width="27%">Years of Association with Group</td>
                            <td colspan="4">Since {{isset($anchorRelationData['year_of_association']) ? $anchorRelationData['year_of_association'] . ' Years' : ''}} </td>

                        </tr>
                        
                        <tr>
                            <td rowspan="3">Comments on Relationship with the Group, if any</td>
                            <td colspan="2"></td>
                            <td width="24%">Qty (In MT)</td>
                            <td width="24%">Value (In Rs. Mn)</td>

                        </tr>

                        @php $months_r = ['April', 'May' , 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'] @endphp
                        @if(count($data) > 0)
                            @php $m = 0 @endphp
                            @foreach($data as $k => $val)
                            @php $mt_val_ye = 0; $mt_amounta = 0; @endphp 
                            @foreach($months_r as $key => $month)
                                @php
                                $mt_val_ye += !empty($data[$k]['mt_value'][$key]) ? $data[$k]['mt_value'][$key] : 0;
                                $mt_amounta += !empty($data[$k]['mt_amount'][$key]) ? $data[$k]['mt_amount'][$key] : 0;
                                @endphp
                              @endforeach
                              <tr>
                                <td colspan="2">{{$k}}</td>
                                <td>{{$mt_val_ye}}</td>
                                <td>{{$mt_amounta}}</td>
                              </tr>
                              @php $m++; @endphp
                            @endforeach
                        @endif

                        <tr>
                          <td rowspan="4">Dependence  on the Group</td>
                          <td width="24%" rowspan="2"><p>&nbsp;</p></td>
                          <td width="24%" rowspan="2">Total Purchases of traded material(Rs. Mn)</td>
                          <td rowspan="2">Purchases From Group (Rs. Mn)</td>
                          <td>% of Dependence</td>
                        </tr>
                         <tr>
                           <td>On total traded material purchase</td>
                         </tr>
                         @if(count($data) > 0)
                            @php $n = 0 @endphp
                            @foreach($data as $k => $val)
                            @php $mt_amountam = 0; $dep_per = ''; $totalPurMaterial = isset($val['total_pur_material']) ? $val['total_pur_material'] : '' @endphp 
                            @foreach($months_r as $key => $month)
                                @php
                                $mt_amountam += !empty($data[$k]['mt_amount'][$key]) ? $data[$k]['mt_amount'][$key] : 0;
                                @endphp
                              @endforeach
                              @if (!empty($mt_amountam) && ($mt_amountam > 0) && !empty($totalPurMaterial) && ($totalPurMaterial > 0))
                               @php $dep_per = number_format(($mt_amountam / $totalPurMaterial), 2); @endphp
                              @endif
                              <tr>
                                <td>{{$k}}</td>
                                <td>{{$totalPurMaterial}}</td>
                                <td>{{$mt_amountam}}</td>
                                <td>{{$dep_per}}</td>
                           </tr>
                            @php $n++; @endphp
                            @endforeach
                        @endif
                        
                        <tr>
                          <td>Payment Terms with the Group</td>
                          <td colspan="4">{{isset($anchorRelationData['payment_terms']) ? $anchorRelationData['payment_terms'] : ''}}</td>
                        </tr>
                        <tr>
                          <td>Rating / Reference by the Group</td>
                          <td colspan="4">{{isset($anchorRelationData['grp_rating']) ? $anchorRelationData['grp_rating'] : ''}}</td>
                        </tr>
                        <tr>
                          <td>Contact Person in Group Co. / Contact No.</td>
                          <td colspan="4">{{isset($anchorRelationData['contact_person']) ? $anchorRelationData['contact_person'] : ''}} / {{isset($anchorRelationData['contact_number']) ? $anchorRelationData['contact_number'] : ''}}</td>
                        </tr>
                        <tr>
                          <td>Security Deposit with Anchor Company</td>
                          <td colspan="4">{{isset($anchorRelationData['security_deposit']) ? $anchorRelationData['security_deposit'] : ''}}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="clearfix"></div>
          </div>
        </div>
        @endif
        @if(isset($data) && (count($data) > 0))
        <div class="data mt-4">
            <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Month on Month Lifting with Anchor Company</td>
                </tr>
            </table>
            <div class="pl-4 pr-4 pb-4 pt-2">
                
              <table class="table table-bordered overview-table" id="myTable">
                    <thead>
                      <tr>
                        <th>Month</th>
                        @if(count($data) > 0)
                            @php $j = 0 @endphp
                            @foreach($data as $key => $val)
                                <th colspan="2" class="text-center">{{$key}}</th>
                                @php 
                                $year = 'year_'.$j;
                                $$year = $key;
                                $j++;
                                @endphp
                            @endforeach
                        @endif
                      </tr>
                    @php 

                    $year_0_kg = isset($year_0) && isset($data[$year_0]['mt_type']) && !empty($data[$year_0]['mt_type']) && $data[$year_0]['mt_type'] == 'KG'? 'selected' : '';
                    $year_0_ton = isset($year_0) && isset($data[$year_0]['mt_type']) && !empty($data[$year_0]['mt_type']) && $data[$year_0]['mt_type'] == 'TON'? 'selected' : '';
                    $year_0_unit = isset($year_0) && isset($data[$year_0]['mt_type']) && !empty($data[$year_0]['mt_type']) && $data[$year_0]['mt_type'] == 'UNIT'? 'selected' : '';
                    $year_1_kg = isset($year_1) && isset($data[$year_1]['mt_type']) && !empty($data[$year_1]['mt_type']) && $data[$year_1]['mt_type'] == 'KG'? 'selected' : '' ;
                    $year_1_ton = isset($year_1) && isset($data[$year_1]['mt_type']) && !empty($data[$year_1]['mt_type']) && $data[$year_1]['mt_type'] == 'TON'? 'selected' : '';
                    $year_1_unit = isset($year_1) && isset($data[$year_1]['mt_type']) && !empty($data[$year_1]['mt_type']) && $data[$year_1]['mt_type'] == 'UNIT'? 'selected' : '';

                    @endphp
                    </thead>
                    <tbody>
                      <tr class="sub-heading">
                        @php 
                        $mt_type0 = isset($year_0) && isset($data[$year_0]['mt_type']) && !empty($data[$year_0]['mt_type']) ? $data[$year_0]['mt_type'] : '';
                        $mt_type1 = isset($year_1) && isset($data[$year_1]['mt_type']) && !empty($data[$year_1]['mt_type']) ? $data[$year_1]['mt_type'] : '' ;
                        @endphp
                        <td></td>
                        <td>Qty (in MT) {{$mt_type0}}</td>
                        <td> Rs In Mn</td>
                        <td>Qty (in MT) {{$mt_type1}}</td>
                        <td> Rs In Mn</td>
                      </tr>
                      @php $myKey = 0; $mt_val_y0 = 0; $mt_val_y1 = 0; $mt_amount0 = 0; $mt_amount1 = 0; @endphp 
                      @php $months = ['April', 'May' , 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'] @endphp
                      @foreach($months as $key => $month)
                      @php 
                      $mt_val_y0 += isset($year_0) && isset($data[$year_0]['mt_value'][$key]) && !empty($data[$year_0]['mt_value'][$key]) ? $data[$year_0]['mt_value'][$key] : 0;
                      $mt_val_y1 += isset($year_1) && isset($data[$year_1]['mt_value'][$key]) && !empty($data[$year_1]['mt_value'][$key]) ? $data[$year_1]['mt_value'][$key] : 0;
                      $mt_amount0 += isset($year_0) && isset($data[$year_0]['mt_amount'][$key]) && !empty($data[$year_0]['mt_amount'][$key]) ? $data[$year_0]['mt_amount'][$key] : 0;
                      $mt_amount1 += isset($year_1) && isset($data[$year_1]['mt_amount'][$key]) && !empty($data[$year_1]['mt_amount'][$key]) ? $data[$year_1]['mt_amount'][$key] : 0;
                      @endphp
                      <tr>
                        <td>{{$month}}</td>
                        <td width="20%">{{isset($year_0) && isset($data[$year_0]['mt_value'][$key]) && !empty($data[$year_0]['mt_value'][$key]) ? $data[$year_0]['mt_value'][$key] : ''}}</td>
                        <td width="20%">{{isset($year_0) && isset($data[$year_0]['mt_amount'][$key]) && !empty($data[$year_0]['mt_amount'][$key]) ? $data[$year_0]['mt_amount'][$key] : ''}}</td>
                        <td width="20%">{{isset($year_1) && isset($data[$year_1]['mt_value'][$key]) && !empty($data[$year_1]['mt_value'][$key]) ? $data[$year_1]['mt_value'][$key] : ''}}</td>
                        <td width="20%">{{isset($year_1) && isset($data[$year_1]['mt_amount'][$key]) && !empty($data[$year_1]['mt_amount'][$key]) ? $data[$year_1]['mt_amount'][$key] : ''}}</td>
                      </tr>
                      @endforeach
                      <tr>
                        <td><b>Total</b></td>
                        <td>{{$mt_val_y0}}</td>
                        <td>{{$mt_amount0}}</td>
                        <td>{{$mt_val_y1}}</td>
                        <td>{{$mt_amount1}}</td>
                      </tr>
                    </tbody>
                  </table>
              <div class="clearfix"></div>
          </div>
        </div>
        @endif
        @if(isset($dataWcf) && count($dataWcf)>0)
        <div class="data mt-4">
            <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Working Capital Facility</td>
                </tr>
            </table>
              <div class="pl-4 pr-4 pb-4 pt-2">

                <table class="table table-bordered overview-table" id="myTable3">

                    <thead>
                        <tr bgcolor="#ccc">
                            <th> Name of Bank/ NBFC</th>
                            <th>Fund based Facility </th>
                            <th>Facility Amount(Rs. Mn) </th>
                            <th>O/S as on {{$debtPosition->fund_ason_date ?? '' }} (Rs. Mn) </th>
                            <th>Non-fund based Facility </th>
                            <th>Facility Amount(Rs. Mn) </th>
                            <th>O/S as on {{$debtPosition->nonfund_ason_date ?? '' }} (Rs. Mn) </th>
                            <th>Length of Relationship </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataWcf as $key =>$val)
                        <tr>
                            <td>{{$val['bank_name']}}</td>
                            <td>{{$val['fund_facility']}}</td>
                            <td><img src="{{url('backend/assets/images/Indian_Rupee_symbol.png')}}" height="10px" width="8px">{{number_format($val['fund_amt'])}}</td>
                            <td><img src="{{url('backend/assets/images/Indian_Rupee_symbol.png')}}" height="10px" width="8px">{{number_format($val['fund_os_amt'])}}</td>
                            <td>{{$val['nonfund_facility']}}</td>
                            <td><img src="{{url('backend/assets/images/Indian_Rupee_symbol.png')}}" height="10px" width="8px">{{number_format($val['nonfund_amt'])}}</td>
                            <td><img src="{{url('backend/assets/images/Indian_Rupee_symbol.png')}}" height="10px" width="8px">{{number_format($val['nonfund_os_amt'])}}</td>
                            <td>{{$val['relationship_len']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="clearfix"></div>
            </div>
        </div>
        @endif
        @if(isset($dataTlbl) && count($dataTlbl)>0)
        <div class="data mt-4">
            <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Term Loans & Business Loans</td>
                </tr>
            </table>
              <div class="pl-4 pr-4 pb-4 pt-2">
                <table class="table table-bordered overview-table" id="myTable3">
                    <thead>
                        <tr bgcolor="#ccc">
                            <th>Name of the bank     </th>
                            <th>Loan name     </th>
                            <th>facility amount(Mn)     </th>
                            <th>O/S as On {{$debtPosition->tbl_fund_ason_date ?? '' }} </th>
                            <th>length of relationship  </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataTlbl as $key =>$val)
                        <tr>
                            <td>{{$val['bank_name_tlbl']}}</td>
                            <td>{{$val['loan_name']}}</td>
                            <td><img src="{{url('backend/assets/images/Indian_Rupee_symbol.png')}}" height="10px" width="8px">{{number_format($val['facility_amt'])}}</td>
                            <td><img src="{{url('backend/assets/images/Indian_Rupee_symbol.png')}}" height="10px" width="8px">{{number_format($val['facility_os_amt'])}}</td>
                            <td>{{$val['relationship_len_tlbl']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="clearfix"></div>
            </div>
        </div>
        @endif
    @if(isset($dataBankAna) && count($dataBankAna)>0)
    <div class="data mt-4">
        <table class="table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Banking Analysis</td>
            </tr>
        </table>
        <div class="pl-4 pr-4 pb-4 pt-2">
	<table class="table table-bordered overview-table banking" id="myTable3">
            <thead>
               <tr bgcolor="#ccc">
                  <th>Name of Bank</th>
                  <th>Acct. Type</th>
                  <th colspan="3">Utilization %</th>
                  <th colspan="4">Cheque Return</th>
                  <th colspan="2">Summations</th>
                  <th></th>
               </tr>
            </thead>
            <tbody id="working_capital_facility">
              <tr class="sub-heading">
                  <td></td>
                  <td></td>
                  <td>Max </td>
                  <td>Min</td>
                  <td>Avg</td>
                  <td>Inward</td>
                  <td>% of total cheques presented</td>
                  <td>Outward</td>
                  <td>% of total cheques deposited  </td>
                  <td>Credit</td>
                  <td>Debit</td>
                  <td>Over drawings in last 6 months</td>
              </tr>
              @foreach($dataBankAna as $key =>$val)
              <tr>
                  <td style="padding:.1rem;">{{$val['bank_name']}}</td>
                  <td style="padding:.1rem;">{{$val['act_type']}}</td>
                  <td style="padding:.1rem;">{{$val['uti_max']}}</td>
                  <td style="padding:.1rem;">{{$val['uti_min']}}</td>
                  <td style="padding:.1rem;">{{$val['uti_avg']}}</td>
                  <td style="padding:.1rem;">{{number_format($val['chk_inward'])}}</td>
                  <td style="padding:.1rem;">{{$val['chk_presented_per']}}</td>
                  <td style="padding:.1rem;">{{number_format($val['chk_outward'])}}</td>
                  <td style="padding:.1rem;">{{$val['chk_deposited_per']}}</td>
                  <td style="padding:.1rem;">{{number_format($val['submission_credit'])}}</td>
                  <td style="padding:.1rem;">{{number_format($val['submission_debbit'])}}</td>
                  <td style="padding:.1rem;">{{$val['overdrawing_in_six_month']}}</td>
              </tr>
              @endforeach
            </tbody>
        </table>
       <div class="clearfix"></div>
    </div>
  </div>
  @endif
<!--eeeeeeeeeeee-->
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Approval Criteria for IC</td>
        </tr>
      </table>
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

@if(isset($arrCamData->t_o_f_purpose))
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Purpose of Rental Facility</td>
        </tr>
      </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($arrCamData->t_o_f_purpose) ? \Helpers::replaceImagePath($arrCamData->t_o_f_purpose) : '' !!}</p>
      </div>
   </div>
@endif

@if(isset($arrCamData->t_o_f_profile_comp))
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">About the Company</td>
        </tr>
      </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($arrCamData->t_o_f_profile_comp) ? \Helpers::replaceImagePath($arrCamData->t_o_f_profile_comp) : '' !!} </p>
      </div>
   </div>
@endif

@if(isset($arrCamData->promoter_cmnt))
   <div class="data mt-4">
    <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Brief Background of Management</td>
          </tr>
       </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($arrCamData->promoter_cmnt) ? \Helpers::replaceImagePath($arrCamData->promoter_cmnt) : '' !!}</p>
      </div>
   </div>
@endif
@if(!empty($arrOwnerData) && count($arrOwnerData))
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
@endif

@if(isset($arrCamData->rating_comment))
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
@endif

@if(isset($arrCamData->rating_rational))
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Rating Rationale of {{$arrBizData->biz_entity_name}} </td>
          </tr>
      </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p> {!! isset($arrCamData->rating_rational) ? \Helpers::replaceImagePath($arrCamData->rating_rational) : '' !!} </p>
      </div>
   </div>
@endif

@php 
$finFlag = false;
@endphp
@foreach($FinanceColumns as $key => $finance_col)
@foreach($financeData as $year => $fin_data)
   @php       
      $yearly_fin_data = getTotalFinanceData($fin_data);
      $growth = $growthData[$year];   
      $amtval = sprintf('%.2f', isset($yearly_fin_data[$key]) ? $yearly_fin_data[$key] : (isset($growth[$key]) ? $growth[$key] : ''));
   @endphp
   @if($amtval!='0' && $amtval!='0.00')
      @php 
      $finFlag = true;      
      @endphp   
      @break;   
   @endif
@endforeach
   @if($finFlag)
      @break;
   @endif
@endforeach

@if($finFlag)
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Standalone Financials of {{$arrBizData->biz_entity_name}} In INR (Mn)</td>
          </tr>
      </table>
      <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
         <table width="100%" id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
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
                  @php
                     $colFlag = false;
                     function test_amt($var)
                     {
                        return ($var != 0);
                     }
                  @endphp
                  @foreach($FinanceColumns as $key => $finance_col)
                     @php
                     $arrAmt = [];
                     @endphp
                     @foreach($financeData as $year => $fin_data)
                        @php 
                        $yearly_fin_data = getTotalFinanceData($fin_data);
                        $growth = $growthData[$year];                        
                        $arrAmt[] = isset($yearly_fin_data[$key]) ? $yearly_fin_data[$key] : (isset($growth[$key]) ? $growth[$key] : '');
                        @endphp
                        @if($loop->last)
                           @php
                           $testAmtArr = [];
                           $testAmtArr  = (array_filter($arrAmt,"test_amt"));
                           @endphp
                           @if(!empty($testAmtArr)) 
                           <tr>
                           <td>{{$finance_col}}</td>
                           @foreach($arrAmt as $kk => $vv)
                           <td>{{sprintf('%.2f', $vv)}}</td>
                           @endforeach 
                           </tr>                          
                           @endif
                        @endif                        
                     @endforeach
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
@endif

@if(isset($finacialDetails->financial_risk_comments))
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Financial Comment</td>
          </tr>
      </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($finacialDetails->financial_risk_comments) ? \Helpers::replaceImagePath($finacialDetails->financial_risk_comments) : '' !!}</p>
      </div>
   </div>
@endif

@if(isset($arrBankDetails->debt_position_comments))
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Debt Position as on {{isset($arrBankDetails->debt_on) ? \Carbon\Carbon::createFromFormat('d/m/Y', $arrBankDetails->debt_on)->format('j F, Y') : ''}}</td>
          </tr>
      </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p> {!! isset($arrBankDetails->debt_position_comments) ? \Helpers::replaceImagePath($arrBankDetails->debt_position_comments) : '' !!}</p>
      </div>
   </div>
@endif

@if(isset($arrCamData->contigent_observations))
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Contingent Liabilities and Auditors Observations as on {{isset($arrCamData->debt_on) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arrCamData->debt_on)->format('j F, Y') : ''}}</td>
          </tr>
      </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($arrCamData->contigent_observations) ? \Helpers::replaceImagePath($arrCamData->contigent_observations) : '' !!}</p>
      </div>
   </div>
@endif

@if(count($positiveRiskCmntArr) || count($negativeRiskCmntArr))
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Risk Comments</td>
          </tr>
      </table>
      @if(isset($positiveRiskCmntArr) && count($positiveRiskCmntArr)>0)
            <table class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0" style="table-layout: fixed;">
               <thead>
               <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="40%">Deal Positives</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending"></th>
               </tr>
               </thead>           
               <tbody>
               @foreach($positiveRiskCmntArr as $postkey =>$postval)         
                     <tr>
                        <td width="40%">{{$postval['cond']}}</td>
                        <td width="60%" style="white-space: unset; word-break: break-all;">{{$postval['timeline']}}</td>
                     </tr>                     
               @endforeach
               </tbody>       
            </table>
      @endif

      @if(isset($negativeRiskCmntArr) && count($negativeRiskCmntArr)>0)
            <table class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0" style="table-layout: fixed;">
               <thead>
               <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="40%">Deal Negatives</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending"></th>
               </tr>
               </thead> 
               <tbody>
               @foreach($negativeRiskCmntArr as $postkey =>$postval)
                     <tr>
                        <td width="40%">{{$postval['cond']}}</td>
                        <td width="60%" style="white-space: unset; word-break: break-all;">{{$postval['timeline']}}</td>
                     </tr>                     
               @endforeach
               </tbody>               
            </table>
      @endif  
   </div> 
@endif

@if(isset($reviewerSummaryData->recommendation))
   <div class="data mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Recommendation</td>
          </tr>
     </table>
      <div class="pl-4 pr-4 pb-4 pt-2">
         <p>{!! isset($reviewerSummaryData->recommendation) ? \Helpers::replaceImagePath($reviewerSummaryData->recommendation) : '' !!} </p>
      </div>
   </div>
@endif

@if(!empty($arrReviewer[0])) 
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
                                 <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" style="background-color:transparent !important; color:#696969 !important;">{{$arrReviewer[$i]->assignee}}
                                     <span style="font-size: 11px;"></br>Updated at </br>
                                          {{ \Helpers::convertDateTimeFormat($arrReviewer[$i]->updated_at, 'Y-m-d H:i:s', 'j F, Y h:i A') }}</span>
                                 </th>
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
                                          {{ \Helpers::convertDateTimeFormat($arrApproverData[$i]->updated_at, 'Y-m-d H:i:s', 'j F, Y h:i A') }}
                                          </span>
                                       @endif 

                                       </th>
                                       @php $i++; @endphp
                                       @if (!empty($arrApproverData[$i]))
                                          <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" style="background-color:transparent !important; color:#696969 !important;">{{$arrApproverData[$i]->approver}} 
                                          @if ($arrApproverData[$i]->status == 1)
                                           <h5 style="color:#37c936; font-size: 11px;">(Approved)</h5> @php $j++; @endphp 
                                             <span style="font-size: 11px;">Approved at </br>{{ \Helpers::convertDateTimeFormat($arrApproverData[$i]->updated_at, 'Y-m-d H:i:s', 'j F, Y h:i A') }}</span>
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
@endif
