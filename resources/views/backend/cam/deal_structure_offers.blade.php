@if(isset($leaseOfferData) && count($leaseOfferData))
<div class="data  col-md-12 mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Leasing Deal Structure </td>
          </tr>
       </table>
      @forelse($leaseOfferData as $key=>$leaseOffer)
      @if ($leaseOffer->status != 2) 
         <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
               <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Criteria</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending"  width="30%">Particulars</th>
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Criteria</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending"  width="30%">Particulars</th>
               </tr>

            </thead>
            <tbody>
               <tr role="row" class="odd">
                  <td class=""><b>Facility Type</b></td>
                  <td class="">{{isset($leaseOffer->facility_type_id) ?  $facilityTypeList[$leaseOffer->facility_type_id]  : ''}}</td>
                  <td class=""><b>Equipment Type</b></td>
                  <td class="">{{isset($leaseOffer->equipment_type_id) ?  (\Helpers::getEquipmentTypeById($leaseOffer->equipment_type_id)['equipment_name']) : ''}}</td>
               </tr>
               <tr role="row" class="odd">
                  <td class=""><b>Limit Of The Equipment</b></td>
                  <td class=""> {!! isset($leaseOffer->prgm_limit_amt) ? ' INR '.number_format($leaseOffer->prgm_limit_amt)  : '0' !!} </td>
                  <td class=""><b>Tenor (Months)</b></td>
                  <td class="">{{isset($leaseOffer->tenor) ? $leaseOffer->tenor : ''}}</td>
               </tr>
               @if($leaseOffer->facility_type_id != 3)
                 <tr role="row" class="odd">
                    <td class=""><b>Security Deposit</b></td>
                    <td class="">  
                           {{(($leaseOffer->security_deposit_type == 1)?'INR ':'').$leaseOffer->security_deposit.(($leaseOffer->security_deposit_type == 2)?' %':'')}} of {{config('common.deposit_type')[$leaseOffer->security_deposit_of]}}
                    </td>
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
               @endif
               <tr role="row" class="odd">
                  <td class=""><b>Rental Frequency</b></td>
                  <td class="">{{isset($leaseOffer->rental_frequency) ? $arrStaticData['rentalFrequency'][$leaseOffer->rental_frequency] : ''}}   {{isset($leaseOffer->rental_frequency_type) ? 'in '.$arrStaticData['rentalFrequencyType'][$leaseOffer->rental_frequency_type] : ''}}   </td>
              
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
                  <td class=""><b>Processing Fee (%)</b></td>
                  <td class="">{{isset($leaseOffer->processing_fee) ? $leaseOffer->processing_fee.' %': ''}}</td>
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
              <tr>
                    <td><b>XIRR %:</b></td>
                    <td>{{number_format($leaseOffer->xirr,2)}}%</td>
                    <td><b>DSA Applicable: </b></td>
                    <td >{{($leaseOffer->dsa_applicable == '1')?'Yes':'No'}}</td>
                    <td></td>
                    <td></td>
                </tr>
                @if($leaseOffer->dsa_applicable == '1')
                <tr>
                    <td><b>DSA Name: </b></td>
                    <td>{{$leaseOffer->programOfferDsa->dsa_name}}</td>
                    <td><b>Payout %:</b></td>
                    <td>{{number_format($leaseOffer->programOfferDsa->payout,2)}}%</td>
                </tr>
                <tr>
                    <td><b>Payout Event: </b></td>
                    <td>{{$leaseOffer->programOfferDsa->payout_event}}</td>
                    
                </tr>
              @endif
            </tbody>
         </table>
      @endif
      @empty
         <div class="pl-4 pr-4 pb-4 pt-2">
             <p>No Offer Found</p>
         </div>
   @endforelse

</div>
@endif

@if(isset($supplyOfferData) && count($supplyOfferData))
<div class="data col-md-12 mt-4">
   <table class="table" cellpadding="0" cellspacing="0">
      <tr>
          <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Supply Chain Deal Structure</td>
      </tr>
   </table>
  <!----supply chain  offer ---->
  
  @forelse($supplyOfferData as $key=>$supplyOffer)
  @if ($supplyOffer->status != 2)
    <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
        <thead>
             <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Criteria</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending"  width="30%">Particulars</th>
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Criteria</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending"  width="30%">Particulars</th>
               </tr>
          </thead>
          <tbody>
                <tr>
                  <td><b>Anchor Name: </b> </td>
                  <td>{{ isset($supplyOffer->anchorData) ? $supplyOffer->anchorData : null }}</td>
                  <td><b>Anchor Total Limit: </b> </td>
                  <td>INR {{ isset($supplyOffer->programData->anchor_limit) ? number_format($supplyOffer->programData->anchor_limit) : null }}</td>
                </tr>
                <tr>
                  <td><b>Anchor Program Name: </b> </td>
                  <td>{{ isset($supplyOffer->programData->prgm_name) ? $supplyOffer->programData->prgm_name : null }}</td>
                  <td><b>Anchor Program Limit: </b></td>
                  <td>INR {{ isset($supplyOffer->subProgramData->anchor_sub_limit) ? number_format($supplyOffer->subProgramData->anchor_sub_limit) : null }}</td>
                </tr>
                <tr>
                  <td><b>Program Type: </b> </td>
                  <td>{{ isset($supplyOffer->programData->prgm_type) ? ($supplyOffer->programData->prgm_type=="1" ? trans('backend.add_program.vendor_finance') : (($supplyOffer->programData->prgm_type=="2") ? trans('backend.add_program.channel_finance') : null ))  : null }}</td>
                  <td><b>Program Min-Max Loan Size: </b> </td>
                  <td>INR {{ isset($supplyOffer->subProgramData->min_loan_size) ? number_format($supplyOffer->subProgramData->min_loan_size) : null }} - {{ isset($supplyOffer->subProgramData->max_loan_size) ? number_format($supplyOffer->subProgramData->max_loan_size) : null }}</td>
                </tr>
              <tr>
                  <td><b>Limit: </b> </td>
                  <td>INR {{number_format($supplyOffer->prgm_limit_amt)}}</td>
                  <!--
                  <td><b>Documentation Fee (%): </b></td>
                  <td>{{$supplyOffer->document_fee}} %</td>
                  -->
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>                  
              </tr>
              
              <tr>
                 <td><b>Interest Rate(%): </b></td>
                 <td>{{$supplyOffer->interest_rate}} %</td>
                 <td><b>Tenor (Days) : </b></td>
                 <td>{{$supplyOffer->tenor}}</td>
              </tr>
              <tr>
                 <td><b>Tenor for old invoice (Days): </b></td>
                 <td>{{$supplyOffer->tenor_old_invoice}}</td>
                 <td><b>Margin (%): </b></td>
                 <td>{{$supplyOffer->margin}} %</td>
              </tr>
              <tr>
                  <td><b>Overdue Interest Rate (%): </b></td>
                  <td>{{($supplyOffer->overdue_interest_rate ?? 0) + ($supplyOffer->interest_rate ?? 0)}} %</td>
                  <td><b>Adhoc Interest Rate (%): </b></td>
                  <td>{{$supplyOffer->adhoc_interest_rate}} %</td>
              </tr>
              <tr>
                  <td><b>Grace Period (Days): </b></td>
                  <td>{{$supplyOffer->grace_period}}</td>
                  <td><b>XIRR %: </b></td>
                    <td>{{number_format($supplyOffer->xirr,2)}}%</td> 
                  <!--<td><b>Processing Fee (%): </b></td>
                  <td>{{$supplyOffer->processing_fee}} %</td>-->
                  <td>&nbsp;</td>
                  <td>&nbsp;</td> 
                                    
              </tr>
                @foreach($supplyOffer->offerCharges as $key=>$offerCharge)              
                @if($key%2 == 0)
                <tr>                    
                    @endif
                    <td><b>{{$offerCharge->chargeName->chrg_name}} (
                            @if ($offerCharge->chrg_type == 2)
                                %
                            @else
                                <img src="{{url('backend/assets/images/Indian_Rupee_symbol.png')}}" height="12px" width="10px">
                            @endif
                            ): </b></td>
                    <td>{{$offerCharge->chrg_value}}</td>
                    @if(($supplyOffer->offerCharges->count() == $key+1) && ($supplyOffer->offerCharges->count() %2 != 0))
                    <td colspan="2">&nbsp;</td>
                    @endif
                    @if($key%2 != 0)
                </tr>
                @endif
                @endforeach
              <tr>
                  <td><b>Bench Mark Date: </b></td>
                  <td colspan="3">{{getBenchmarkType($supplyOffer->benchmark_date)}}</td>
              </tr>
              <tr>
                  <td><b>Investment Payment Frequency: </b></td>
                  <td colspan="3">{{getInvestmentPaymentFrequency($supplyOffer->payment_frequency)}}</td>
              </tr>
              <tr>
                  <td><b>Comment: </b></td>
                  <td colspan="3">{{$supplyOffer->comment}}</td>
              </tr>
              
              <tr>
                    <td><b>DSA Applicable: </b></td>
                    <td >{{($supplyOffer->dsa_applicable == '1')?'Yes':'No'}}</td>
                    <td></td>
                    <td></td>
                </tr>
                @if($supplyOffer->dsa_applicable == '1')
                <tr>
                    <td><b>DSA Name: </b></td>
                    <td>{{$supplyOffer->programOfferDsa->dsa_name}}</td>
                    <td><b>Payout %: </b></td>
                    <td>{{number_format($supplyOffer->programOfferDsa->payout,2)}}%</td>
                </tr>
                <tr>
                    <td><b>Payout Event: </b></td>
                    <td>{{$supplyOffer->programOfferDsa->payout_event}}</td>
                    
                </tr>
              @endif
              @if($supplyOffer->offerPs->count() > 0)
              <tr>
                  <td colspan="4" >
                      <table width="100%" cellpadding="0" cellspacing="0">
                          <tr style="background-color: #d2d4de;">
                              <td rowspan="3" style="background-color: #fff;" width="10%"><b>Primary Security</b></td>
                              <td width="15%"><b>Security</b></td>
                              <td width="15%"><b>Type of Security</b></td>
                              <td width="10%"><b>Status of Security</b></td>
                              <td width="25%"><b>Time for security</b></td>
                              <td width="25%"><b>Description of Security</b></td>
                          </tr>
                          @foreach($supplyOffer->offerPs as $key=>$ops)
                          <tr>
                              <td>{{($ops->ps_security_id != null)? config('common.ps_security_id')[$ops->ps_security_id]: 'NA'}}</td>
                              <td>{{($ops->ps_type_of_security_id != null)? config('common.ps_type_of_security_id')[$ops->ps_type_of_security_id]: 'NA'}}</td>
                              <td>{{($ops->ps_status_of_security_id != null)? config('common.ps_status_of_security_id')[$ops->ps_status_of_security_id]: 'NA'}}</td>
                              <td>{{($ops->ps_time_for_perfecting_security_id != null)? config('common.ps_time_for_perfecting_security_id')[$ops->ps_time_for_perfecting_security_id]: 'NA'}}</td>
                              <td>{{$ops->ps_desc_of_security}}</td>
                          </tr>
                          @endforeach
                      </table>
                  </td>
              </tr>
              @endif

              @if($supplyOffer->offerCs->count() > 0)
              <tr>
                  <td colspan="4">
                      <table width="100%" >
                          <tr style="background-color: #d2d4de;" >
                              <td rowspan="3" style="background-color: #fff;" width="10%"><b>Collateral Security</b></td>
                              <td width="15%"><b>Security</b></td>
                              <td width="15%"><b>Type of Security</b></td>
                              <td width="10%"><b>Status of Security</b></td>
                              <td width="25%"><b>Time for security</b></td>
                              <td width="25%"><b>Description of Security</b></td>
                          </tr>
                          @foreach($supplyOffer->offerCs as $key=>$ocs)
                          <tr>
                              <td>{{($ocs->cs_desc_security_id != null)? config('common.cs_desc_security_id')[$ocs->cs_desc_security_id]: 'NA'}}</td>
                              <td>{{($ocs->cs_type_of_security_id != null)? config('common.cs_type_of_security_id')[$ocs->cs_type_of_security_id]: 'NA'}}</td>
                              <td>{{($ocs->cs_status_of_security_id != null)? config('common.cs_status_of_security_id')[$ocs->cs_status_of_security_id]: 'NA'}}</td>
                              <td>{{($ocs->cs_time_for_perfecting_security_id != null)? config('common.cs_time_for_perfecting_security_id')[$ocs->cs_time_for_perfecting_security_id]: 'NA'}}</td>
                              <td>{{$ocs->cs_desc_of_security}}</td>
                          </tr>
                          @endforeach
                      </table>
                  </td>
              </tr>
              @endif

              @if($supplyOffer->offerPg->count() > 0)
              <tr>
                  <td colspan="4">
                      <table width="100%">
                          <tr style="background-color: #d2d4de;">
                              <td rowspan="3" style="background-color: #fff;" width="10%"><b>Personal Guarantee</b></td>
                              <td width="15%"><b>Guarantor</b></td>
                              <td width="15%"><b>Time for security</b></td>
                              <td width="10%"><b>Residential Address</b></td>
                              <td width="25%"><b>Net worth as per ITR/CA Cert</b></td>
                              <td width="25%"><b>Comments if any</b></td>
                          </tr>
                          @foreach($supplyOffer->offerPg as $key=>$opg)
                          <tr>
                              <td>{{($opg->owner)? $opg->owner->first_name: 'NA'}}</td>
                              <td>{{($opg->pg_time_for_perfecting_security_id != null)? config('common.pg_time_for_perfecting_security_id')[$opg->pg_time_for_perfecting_security_id]: 'NA'}}</td>
                              <td>{{$opg->pg_residential_address}}</td>
                              <td>{{$opg->pg_net_worth}}</td>
                              <td>{{$opg->pg_comments}}</td>
                          </tr>
                          @endforeach
                      </table>
                  </td>
              </tr>
              @endif

              @if($supplyOffer->offerCg->count() > 0)
              <tr>
                  <td colspan="4">
                      <table width="100%">
                          <tr style="background-color: #d2d4de;">
                              <td rowspan="3" style="background-color: #fff;" width="10%"><b>Corporate Guarantee</b></td>
                              <td width="15%"><b>Type</b></td>
                              <td width="15%"><b>Guarantor</b></td>
                              <td width="10%"><b>Time for security</b></td>
                              <td width="25%"><b>Residential Address</b></td>
                              <td width="25%"><b>Comments if any</b></td>
                          </tr>
                          @foreach($supplyOffer->offerCg as $key=>$ocg)
                          <tr>
                              <td>{{($ocg->cg_type_id != null)? config('common.cg_type_id')[$ocg->cg_type_id]: 'NA'}}</td>
                              <td>{{($ocg->owner)? $ocg->owner->first_name: 'NA'}}</td>
                              <td>{{($ocg->cg_time_for_perfecting_security_id != null)? config('common.cg_time_for_perfecting_security_id')[$ocg->cg_time_for_perfecting_security_id]: 'NA'}}</td>
                              <td>{{$ocg->cg_residential_address}}</td>
                              <td>{{$ocg->cg_comments}}</td>
                          </tr>
                          @endforeach
                      </table>
                  </td>
              </tr>
              @endif

              @if($supplyOffer->offerEm->count() > 0)
              <tr>
                  <td colspan="4">
                      <table width="100%">
                          <tr style="background-color: #d2d4de;">
                              <td rowspan="3" style="background-color: #fff;" width="10%"><b>Escrow Mechanism</b></td>
                              <td width="15%"><b>Debtor</b></td>
                              <td width="15%"><b>Expected cash flow per month</b></td>
                              <td width="10%"><b>Time for security</b></td>
                              <td width="25%"><b>Mechanism</b></td>
                              <td width="25%"><b>Comments if any</b></td>
                          </tr>
                          @foreach($supplyOffer->offerEm as $key=>$oem)
                          <tr>
                              <td>{{($oem->anchor)? $oem->anchor->comp_name: 'NA'}}</td>
                              <td>{{$oem->em_expected_cash_flow}}</td>
                              <td>{{($oem->em_time_for_perfecting_security_id != null)? config('common.em_time_for_perfecting_security_id')[$oem->em_time_for_perfecting_security_id]: 'NA'}}</td>
                              <td>{{($oem->em_mechanism_id != null)? config('common.em_mechanism_id')[$oem->em_mechanism_id]: 'NA'}}</td>
                              <td>{{$oem->em_comments}}</td>
                          </tr>
                          @endforeach
                      </table>
                  </td>
              </tr>
              @endif
        </tbody>
    </table>
    @endif
    @empty
         <div class="pl-4 pr-4 pb-4 pt-2">
             <p>No Offer Found</p>
         </div>
  @endforelse
<!----supply chain  offer ---->
</div>
@endif

@if(isset($termLoanOfferData) && count($termLoanOfferData))
<div class="data  col-md-12 mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Term Loan Deal Structure </td>
          </tr>
       </table>
      @forelse($termLoanOfferData as $key => $termLoanOffer)
      @if ($termLoanOffer->status != 2) 
         <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
            <thead>
               <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Criteria</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending"  width="30%">Particulars</th>
                  <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Criteria</th>
                  <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending"  width="30%">Particulars</th>
               </tr>

            </thead>
            <tbody>
               <tr role="row" class="odd">
                  <td class=""><b>Facility Type</b></td>
                  <td class="">{{isset($termLoanOffer->facility_type_id) ?  $facilityTypeList[$termLoanOffer->facility_type_id]  : ''}}</td>
                  <td class=""><b>Asset Type: </b></td>
                  <td class="">{{ $termLoanOffer->asset->asset_type }}</td>
               </tr>
               <tr role="row" class="odd">
                  <td class=""><b>Apply Loan Amount</b></td>
                  <td class=""> {!! isset($termLoanOffer->prgm_limit_amt) ? ' INR '.number_format($termLoanOffer->prgm_limit_amt)  : '0' !!} </td>
                  <td class=""><b>Tenor (Months)</b></td>
                  <td class="">{{isset($termLoanOffer->tenor) ? $termLoanOffer->tenor : ''}}</td>
               </tr>
               <tr role="row" class="odd">
                  <td class=""><b>Frequency Type</b></td>
                  <td class="">{{ $termLoanOffer->rental_frequency_type == 1 ? 'Advance' : (($termLoanOffer->rental_frequency_type == 2) ? 'Arrears' : '')  }}</td>              
                  <td class="" valign="top"><b>Payment Frequency</b></td>
                  <td class="" valign="top">
                    {{ (($termLoanOffer->rental_frequency == 1) ? 'Yearly' : (($termLoanOffer->rental_frequency == 2) ? 'Bi-Yearly' : (($termLoanOffer->rental_frequency == 3) ? 'Quaterly' : 'Monthly')))}}
                  </td>
               </tr>
               <tr role="row" class="odd">
                  <td class=""><b>Processing Fee (%)</b></td>
                  <td class="">{{isset($termLoanOffer->processing_fee) ? $termLoanOffer->processing_fee.' %': ''}}</td>
                  <td class=""><b>Interest Rate (%)</b></td>
                  <td class="">{{isset($termLoanOffer->interest_rate) ? $termLoanOffer->interest_rate.' %': ''}}</td>                  
               </tr>
               <tr role="row" class="odd">
                  <td class=""><b>Security Deposit (%)</b></td>
                  <td class="">{{isset($termLoanOffer->security_deposit) ? $termLoanOffer->security_deposit.' %': ''}}</td>
                  <td class=""><b>Margin Money (%)</b></td>
                  <td class="">{{isset($termLoanOffer->margin) ? $termLoanOffer->margin.' %': ''}}</td>                  
               </tr>
               <tr role="row" class="odd">
                  <td class=""><b>IRR</b></td>
                  <td class="">{{isset($termLoanOffer->irr) ? $termLoanOffer->irr : ''}}</td>  
                  <td><b>XIRR %:</b></td>
                    <td>{{number_format($termLoanOffer->xirr,2)}}%</td>
                  <td></td>                                 
               </tr>
              
                <tr>
                    <td><b>DSA Applicable: </b></td>
                    <td >{{($termLoanOffer->dsa_applicable == '1')?'Yes':'No'}}</td>
                    <td></td>
                    <td></td>
                </tr>
                @if($termLoanOffer->dsa_applicable == '1')
                <tr>
                    <td><b>DSA Name: </b></td>
                    <td>{{$termLoanOffer->programOfferDsa->dsa_name}}</td>
                    <td><b>Payout %:</b></td>
                    <td>{{number_format($termLoanOffer->programOfferDsa->payout,2)}}%</td>
                </tr>
                <tr>
                    <td><b>Payout Event: </b></td>
                    <td>{{$termLoanOffer->programOfferDsa->payout_event}}</td>
                    
                </tr>
              @endif
               @if(isset($termLoanOffer->asset_insurance) && $termLoanOffer->asset_insurance == 1)
              <tr role="row" class="odd">
                  <td colspan="4">
                      <table width="100%">
                          <tr style="background-color: #d2d4de;">
                              <td rowspan="3" style="background-color: #fff;" width="10%"><b>Asset Insurance</b></td>
                              <td width="25%"><b>Asset Name</b></td>
                              <td width="25%"><b>Timelines For Insurance</b></td>
                              <td width="30%"><b>Asset Comment</b></td>
                          </tr>
                          <tr>
                              <td>{{ $termLoanOffer->asset_name ?? 'NA'}}</td>
                              <td>{{ $termLoanOffer->timelines_for_insurance ?? 'NA'}}</td>
                              <td>{{ $termLoanOffer->asset_comment ?? 'NA'}}</td>
                          </tr>
                      </table>
                  </td>
              </tr>
              @endif
               @if($termLoanOffer->offerPg->count() > 0)
              <tr role="row" class="odd">
                  <td colspan="4">
                      <table width="100%">
                          <tr style="background-color: #d2d4de;">
                              <td rowspan="3" style="background-color: #fff;" width="10%"><b>Personal Guarantee</b></td>
                              <td width="15%"><b>Guarantor</b></td>
                              <td width="15%"><b>Time for security</b></td>
                              <td width="10%"><b>Residential Address</b></td>
                              <td width="25%"><b>Net worth as per ITR/CA Cert</b></td>
                              <td width="25%"><b>Comments if any</b></td>
                          </tr>
                          @foreach($termLoanOffer->offerPg as $key => $opg)
                          <tr>
                              <td>{{($opg->owner)? $opg->owner->first_name: 'NA'}}</td>
                              <td>{{($opg->pg_time_for_perfecting_security_id != null)? config('common.pg_time_for_perfecting_security_id')[$opg->pg_time_for_perfecting_security_id]: 'NA'}}</td>
                              <td>{{$opg->pg_residential_address}}</td>
                              <td>{{$opg->pg_net_worth}}</td>
                              <td>{{$opg->pg_comments}}</td>
                          </tr>
                          @endforeach
                      </table>
                  </td>
              </tr>
              @endif
               <tr role="row" class="odd">
                    <td class=""><b>Additional Security</b></td>
                    <td class="">
                        @php
                        $add_sec_arr = '';
                        if(isset($termLoanOffer->addl_security) && $termLoanOffer->addl_security !=''){
                            $addl_sec_arr = explode(',', $termLoanOffer->addl_security);
                            foreach($addl_sec_arr as $k=>$v){
                                $add_sec_arr .= config('common.addl_security')[$v].', ';
                            }
                        }
                        if($termLoanOffer->comment != '' && $termLoanOffer->addl_security !=''){
                            $add_sec_arr .= ' <b>Comment</b>:  '.$termLoanOffer->comment;
                        }else{
                            $add_sec_arr .= $termLoanOffer->comment;
                        }
                        @endphp 
                        {!! trim($add_sec_arr,', ') !!}
                    </td>
                    <td></td>
                    <td></td>
               </tr>
            </tbody>
         </table>
      @endif
      @empty
         <div class="pl-4 pr-4 pb-4 pt-2">
             <p>No Offer Found</p>
         </div>
   @endforelse
</div>
@endif