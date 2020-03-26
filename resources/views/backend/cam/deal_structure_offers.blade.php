<div class="data  col-md-12 mt-4">
      <table class="table" cellpadding="0" cellspacing="0">
          <tr>
              <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Leasing Deal Structure </td>
          </tr>
       </table>
      @forelse($leaseOfferData as $key=>$leaseOffer)
     
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
                  <td class=""><b>Days</b></td>
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
            </tbody>
         </table>
      @empty
         <div class="pl-4 pr-4 pb-4 pt-2">
             <p>No Offer Found</p>
         </div>
   @endforelse

</div>


<div class="data col-md-12 mt-4">
   <table class="table" cellpadding="0" cellspacing="0">
      <tr>
          <td style="color:#fff;font-size: 15px;font-weight: bold;" bgcolor="#8a8989">Supply Chain Deal Structure</td>
      </tr>
   </table>
  <!----supply chain  offer ---->
  
  @forelse($supplyOfferData as $key=>$supplyOffer)
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
                  <td><b>Documentation Fee (%): </b></td>
                  <td>{{$supplyOffer->document_fee}} %</td>
              </tr>
              
              <tr>
                 <td><b>Interest Rate(%): </b></td>
                 <td>{{$supplyOffer->interest_rate}} %</td>
                 <td><b>Days : </b></td>
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
                  <td>{{$supplyOffer->overdue_interest_rate}} %</td>
                  <td><b>Adhoc Interest Rate (%): </b></td>
                  <td>{{$supplyOffer->adhoc_interest_rate}} %</td>
              </tr>
              <tr>
                  <td><b>Grace Period (Days): </b></td>
                  <td>{{$supplyOffer->grace_period}}</td>
                  <td><b>Processing Fee (%): </b></td>
                  <td>{{$supplyOffer->processing_fee}} %</td>
              </tr>
              <tr>
                  <td><b>Comment: </b></td>
                  <td colspan="3">{{$supplyOffer->comment}}</td>
              </tr>
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

    @empty
         <div class="pl-4 pr-4 pb-4 pt-2">
             <p>No Offer Found</p>
         </div>
  @endforelse
<!----supply chain  offer ---->
</div>