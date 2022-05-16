<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <link href="https://fonts.googleapis.com/css2?family=Federo&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      {{-- <link rel="stylesheet" href="{{url('backend/assets/css/style.css')}}?v="{{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}"" /> --}}
      {{-- <link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}?v="{{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}""/> --}}
      <style>
         table {
            border-collapse: collapse;
            width: 100% !important;
         }
         table, tr, td, th, tbody, thead, tfoot {
            page-break-inside: avoid !important;
         }
         table {
            page-break-inside: avoid !important;
         }
         table { page-break-inside:auto }
         tr    { page-break-inside:avoid; page-break-after:auto }
         /* thead { display:table-header-group } */
         tfoot { display:table-footer-group }
         thead {display: table-row-group;}
         @page { margin-top: 110px; margin-bottom: 110px}
         header { position: fixed; left: 0px; top: -80px; right: 0px;}
         #footer { position: fixed; left: 0px; bottom: -145px; right: 0px; height: 150px; }
         table tr td{position:relative; padding: .3rem !important;}
         /* Create two equal columns that floats next to each other */
         .column {
            float: left;
         }

         .column1 {
            float: right;
         }
         /* Clear floats after the columns */
         .row:after {
         content: "";
         display: table;
         clear: both;
         }
         hr.new5 {
            border: 1px solid #ffa500;
         }
   </style>
   </head>
   <body style="font-size: 15px;background-color: #fff">
      <script type="text/php">
         if (isset($pdf)) {
            $y = $pdf->get_height() - 50; 
            $x = $pdf->get_width() - 900;
            //$text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $text = "{PAGE_NUM}";
            $font = $fontMetrics->getFont("Federo", "regular");
            $size = 12;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            $size1 = 14;
            $text1 = date('d/m/Y');
            $font1 = $fontMetrics->getFont("Federo", "regular");
            $text_height1 = $fontMetrics->getFontHeight($font1, $size1);
            $width1 = $fontMetrics->getTextWidth($text, $font1, $size1);
            $w1 = $pdf->get_width() - $width1 - 30;
            $y1 = $pdf->get_height() - $text_height1 - 30;

            $pdf->page_text($w1, $y1, $text1, $font1, $size1,$color, $word_space, $char_space, $angle);
       }
      </script>
      @if (isset($templateType) && $templateType == 'pdfTemplate')
      <header style="width: 100%;">
         <div class="row">
            <div class="column">
               <img src="{{url('backend/assets/images/letterHeadLogo.png')}}" alt="header" style="width: 15%;">
            </div>
            <div class="column1">
               CIN Number: U67120MH1992PTC068062
            </div>
          </div>
          <hr class="new5">
     </header>
      @endif
     {{-- <footer style="width: 100%;" id="footer">
         <img src="{{ public_path('img/invoice_footer.png') }}" alt="footer" style="width: 100%;">
     </footer> --}}
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify;">
         <thead>
            <tr>
               <th bgcolor="#cccccc" class="text-center" height="30" style="text-align: center;"><span>SANCTION LETTER</span>
               </th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>
                  <span>Ref No: CFPL/{{ Carbon\Carbon::now()->format('My') }}/{{request()->get('app_id')?
                  request()->get('app_id') :''}}<br />
                  {{ $date_of_final_submission?\Carbon\Carbon::parse($date_of_final_submission)->format('F dS,
                  Y'):Carbon\Carbon::now()->format('F dS, Y') }}<br /><br />
                  <b>{{ $supplyChaindata['EntityName'] }}</b><br />
                  @if(!empty(trim($supplyChaindata['Address'])))
                  @php
                  $cAddress = wordwrap(trim($supplyChaindata['Address']),40,"<br>\n");
                  @endphp
                  {!! $cAddress !!}
                  @endif
                  </span>
               </td>
            </tr>
            <tr>
               <td>
                   <span><b>Kind Attention :</b> 
                   @if($contact_person)
                       {{ $contact_person }}   
                   @else
                       {{ $supplyChainFormData->operational_person??'' }} 
                   @endif</span>
               </td>
           </tr>
            <tr>
               <td>
                  <span><b>Subject :</b> Sanction Letter for Working Capital Demand Loan
                  Facility
                  to {{ $supplyChaindata['EntityName'] }}.</span>
               </td>
            </tr>
            <tr>
               <td>
                  <span>
                  <b>Dear </b>
                  {{ $supplyChainFormData->title??'' }},
                  </span>
               </td>
            </tr>
            <tr>
               <td>
                  <span>We are pleased to inform you that Capsave Finance Private Limited has
                  sanctioned the below mentioned credit
                  facility, based upon the information furnished in the loan application
                  form
                  and supporting documents submitted to us.
                  The credit facility is subject to acceptance of the terms and condition
                  as
                  set out in the attached annexures.</span>
               </td>
            </tr>
            <tr>
               <td>
                  <table width="100%" border="1">
                     <tr>
                        <td width="30%"><b>Borrower</b></td>
                        <td>{{ $supplyChaindata['EntityName'] }} (referred to as “Borrower” henceforth)
                        </td>
                     </tr>
                     <tr>
                        <td width="30%"><b>Lender</b></td>
                        <td>Capsave Finance Private Limited (referred to as “Lender”
                           henceforth)
                        </td>
                     </tr>
                     <tr>
                        <td width="30%"><b>Corporate Anchor</b></td>
                        <td>{{ $supplyChaindata['anchorData'][0]['comp_name'] }} (referred to as
                           “Anchor” henceforth)
                        </td>
                     </tr>
                     <tr>
                        <td width="30%"><b>Total Sanction Amount</b></td>
                        <td>INR {{ \Helpers::formatCurrencyNoSymbol($supplyChaindata['limit_amt']) }} (Rupees {{
                           $supplyChaindata['amountInwords'] }} only)
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td>
                  <span>I /We accept all the terms and conditions as per the attached
                  annexures
                  which have been read and understood by
                  me/us. </span>
               </td>
            </tr>
            <tr>
               <td>
                  <span>We request you to acknowledge and return a copy of the same as a
                  confirmation. </span>
               </td>
            </tr>
            <tr>
               <td>
                  <table width="100%" border="0">
                     <tr>
                        <td width="50%"  height="40"><b>Yours Sincerely,</b></td>
                        <td  height="40" style="float: right;"><b>Accepted for and behalf of
                           Borrower</b>
                        </td>
                     </tr>
                     <tr>
                        <td width="50%"  height="40"><b>For Capsave Finance
                           Private
                           Limited</b>
                        </td>
                        <td  height="40" style="float: right;"><b>For {{ $supplyChaindata['EntityName'] }}</b>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td height="40"></td>
            </tr>
            <tr>
               <td>
                  <table width="100%" border="0">
                     <tr>
                        <td width="50%" valign="top" height="40"><b>Authorized Signatory</b></td>
                        <td valign="top" height="40" style="float: right;"><b>Authorized Signatory</b></td>
                     </tr>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      @if(!empty($supplyChaindata['offerData']) && $supplyChaindata['offerData']->count() == 1)
        <div style="page-break-before: always;"></div>
      @endif
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify;">
         <thead>
            <tr>
               <th bgcolor="#cccccc" class="text-center" height="30" style="text-align: center;">Annexure I – Specific
                  Terms
                  and Conditions
               </th>
            </tr>
         </thead>
         <tbody>
            @if(!empty($supplyChaindata['offerData']) && $supplyChaindata['offerData']->count() == 1)
            @php
            $check = 'no';
            @endphp
            @else
            @php
            $i=1;
            $check = 'yes';
            @endphp
            @endif
            @foreach($supplyChaindata['offerData'] as $key => $offerD)
            @if ($offerD->status != 2)
            @php
            if($check == 'no'){
            $counter = '';
            }else{
            $counter = ' -'.$i++;
            }
            @endphp
            <tr>
               <td><b>FACILITY{{ $counter }} </b></td>
            </tr>
            <tr>
               <td>
                  <table width="100%" border="1">
                     <tr>
                        <td width="30%" valign="top"><b>Facility</b></td>
                        <td>Working Capital Demand Loan Facility (referred to as “Facility”
                           henceforth)
                        </td>
                     </tr>
                     <tr>
                        <td valign="top"><b>Sanction Amount</b></td>
                        <td>INR {{\Helpers::formatCurrencyNoSymbol($offerD->prgm_limit_amt)}} (Rupees {{
                           numberTowords($offerD->prgm_limit_amt) }} only)
                        </td>
                     </tr>
                     <tr>
                        <td valign="top"><b>Facility Tenor</b></td>
                        <td>
                           {{ $arrayOfferData[$offerD->prgm_offer_id ]->facility_tenor??'' }}
                        </td>
                     </tr>
                     <tr>
                        <td valign="top"><b>Purpose of the facility</b></td>
                        <td>{{ $arrayOfferData[$offerD->prgm_offer_id ]->purpose_of_the_facility??'' }}
                        </td>
                     </tr>
                     <tr>
                        <td valign="top"><b>Rate of Interest </b></td>
                        @if(isset($offerD->is_lending_rate) && $offerD->is_lending_rate == 1)
                        <td>{{$offerD->interest_rate}}% per annum i.e., ROI equal to CFPL Benchmark Lending Rate less {{ $offerD->lending_rate_diff??0.00 }}% (to be reckoned from the date of disbursement until the date on which repayment becomes due).
                        Presently Benchmark Lending Rate (BLR) as on date is {{ $offerD->lending_rate??0.00 }}%. Interest rate on repayment would change based on the changes in BLR as announced by Lender from time to time. This would lead to change in interest payable to Lender.
                        </td>
                        @else
                        <td>{{$offerD->interest_rate}}% per annum reckoned from the date of disbursement until the
                            date on which repayment becomes due.
                        </td>
                        @endif
                    </tr>
                     @if($offerD->grace_period && $offerD->tenor)
                     <tr>
                        <td valign="top"><b>Tenor for each tranche</b></td>
                        <td>
                            Upto {{($offerD->tenor + $offerD->grace_period)}} days{{($offerD->grace_period)?' (including grace period of '.$offerD->grace_period.' days)':''}} from date of disbursement of each tranche
                        </td>
                    </tr>
                     @endif
                     @if($offerD->tenor_old_invoice)
                     <tr>
                        <td valign="top"><b>Old Invoice</b></td>
                        <td>Borrower can submit invoices not older than {{$offerD->tenor_old_invoice}} days. Door to door tenor shall not exceed {{ $arrayOfferData[$offerD->prgm_offer_id ]->deviation_first_disbursement??($offerD->tenor + $offerD->grace_period + $offerD->tenor_old_invoice) }} days from date of invoice.
                        </td>
                    </tr>
                     @endif
                     @if($offerD->margin)
                     <tr>
                         <td valign="top"><b>Margin</b></td>
                         <td>
                             {{($offerD->margin	)? $offerD->margin:'NIL'}}% on 
                             @if (isset($arrayOfferData[$offerD->prgm_offer_id]->margin) && !empty($arrayOfferData[$offerD->prgm_offer_id]->margin) && is_array($arrayOfferData[$offerD->prgm_offer_id]->margin))   
                             @foreach ($arrayOfferData[$offerD->prgm_offer_id]->margin as $g=>$r)
                                      {{ $r }} 
                                     @if( !$loop->last),
                                     @endif     
                             @endforeach
                             @else
                                 {{ $arrayOfferData[$offerD->prgm_offer_id]->margin??'' }}
                             @endif
                              value.
                         </td>
                     </tr>
                     @endif
                     <tr>
                        <td valign="top"><b>Interest frequency </b></td>
                        <td>
                           <table width="100%" border="0">
                              @if($offerD->payment_frequency == 1)
                              @if($offerD->program->interest_borne_by == 1)
                              <tr>
                                 <td valign="top" width="1%"><span style="margin-top:7px;display: inline-block;"><b>&bull;</b></span>
                                 </td>
                                 <td  valign="top">
                                    To be paid by Anchor
                                    upfront for a period upto {{($offerD->tenor)}} days at the time of
                                    disbursement of each tranche.
                                 </td>
                              </tr>
                              @else
                              <tr>
                                 <td valign="top" width="1%">&bull;</td>
                                 <td >Lender will deduct upfront interest for
                                    a
                                    period upto {{($offerD->tenor)}} days at the time of disbursement of
                                    each
                                    tranche.
                                 </td>
                              </tr>
                              @endif
                              @else
                              @if($offerD->payment_frequency == 2)
                              <tr>
                                 <td valign="top" width="1%">&bull;
                                 </td>
                                 <td valign="top">
                                    Lender shall charge monthly interest to the
                                    {{ $arrayOfferData[$offerD->prgm_offer_id]->lender_shall_charge??'' }}
                                    at the month end based on utilization done during
                                    the
                                    month. (Interest need to be paid by the borrower
                                    immediately, after which delayed penal charges on
                                    interest would be applicable)
                                 </td>
                              </tr>
                              @endif
                              @endif
                           </table>
                        </td>
                     </tr>
                     @php
                     $processingCharges = '';  
                      @endphp
                         @if(isset($offerD->offerCharges))
                             @foreach($offerD->offerCharges as $key=>$offerCharge)
                             @if($offerCharge->chargeName->chrg_name == 'Processing Fee')
                                 @if($offerCharge->chrg_type == '2')
                                 @php
                                    $processingCharges = $offerCharge->chrg_value;  
                                 @endphp
                                 @endif
                                 @endif
                             @endforeach
                             @endif
                     @if($processingCharges)
                     <tr>
                        <td valign="top"><b>One time Processing Charges at the time of
                           Sanction
                           of credit facility</b>
                        </td>
                        <td>
                           {{ $processingCharges }}% of the sanctioned
                           limit + applicable taxes payable by the
                           {{$arrayOfferData[$offerD->prgm_offer_id ]->one_time_processing_charges??'' }} (non-refundable).
                        </td>
                     </tr>
                     @endif
                     <tr>
                        <td valign="top"><b>Default/Penal Interest</b></td>
                        <td>
                            <b>
                            @php
                              $penelInterestRate = (($offerD['overdue_interest_rate'] ?? 0) + ($offerD['interest_rate'] ?? 0));
                            @endphp
                            {{number_format($penelInterestRate, 2, '.', '')}}% per annum including above regular rate of interest in case any tranche remains unpaid after the expiry of approved tenor from the disbursement date. Penal interest to be charged for the relevant tranche for such overdue period till actual payment of such tranche.</b>
                        </td>
                    </tr>
                    <tr>
                     <td valign="top"><b>Applicable Taxes</b></td>
                     <td>
                         Any charges/interest payable by the 
                        {{ $arrayOfferData[$offerD->prgm_offer_id ]->applicable_taxes??'' }} as mentioned
                         in
                         the sanction letter are
                         excluding applicable taxes. Taxes applicable would be levied
                         additionally
                     </td>
                 </tr>
                 @if(isset($arrayOfferData[$offerD->prgm_offer_id]->ps_security) || isset($arrayOfferData[$offerD->prgm_offer_id]->cs_security) || isset($arrayOfferData[$offerD->prgm_offer_id]->pg_guarantor))
                 <tr>
                     <td valign="top"><b>Security from Borrower</b></td>
                     <td>
                         <table width="100%" border="0">
                             @if (isset($arrayOfferData[$offerD->prgm_offer_id]->ps_security) && !empty($arrayOfferData[$offerD->prgm_offer_id]->ps_security))
                             @foreach($arrayOfferData[$offerD->prgm_offer_id]->ps_security as $PrimarySecurityS)
                             <tr>
                                 <td valign="top" width="1%">&bull;</td>
                                 <td>{!! $PrimarySecurityS !!}
                                 </td>
                             </tr>
                               @endforeach
                             @endif
                             @if (isset($arrayOfferData[$offerD->prgm_offer_id]->cs_security) && !empty($arrayOfferData[$offerD->prgm_offer_id]->cs_security))
                             @foreach($arrayOfferData[$offerD->prgm_offer_id]->cs_security as $CsSecurityS)
                             <tr>
                                 <td valign="top" width="1%">&bull;</td>
                                 <td>{!! $CsSecurityS !!}
                                 </td>
                             </tr>
                             @endforeach
                             @endif
                             @if(isset($arrayOfferData[$offerD->prgm_offer_id ]->pg_guarantor) &&  $arrayOfferData[$offerD->prgm_offer_id ]->pg_guarantor != '')
                             <tr>
                             <td valign="top" width="1%">&bull;</td>
                             <td>Personal Guarantee of
                                 {!! $arrayOfferData[$offerD->prgm_offer_id ]->pg_guarantor??''!!}
                              </td>
                             </tr>
                         @endif
                         </table>
                     </td>
                 </tr>
                 @endif
                     <tr>
                        <td valign="top"><b>Payment mechanism</b></td>
                        <td>
                            Direct payment by the {{ $arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism??'' }} to the Lender on or before the tranche due date based on tranche tenure through RTGS/NEFT/NACH/Cheque or any other mode acceptable to Lender.
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><b>Moratorium (if applicable)</b></td>
                        <td>
                            @if(!empty($arrayOfferData[$offerD->prgm_offer_id ]->moratorium) && $arrayOfferData[$offerD->prgm_offer_id ]->moratorium){!! $arrayOfferData[$offerD->prgm_offer_id ]->moratorium !!} @else NA @endif
                        </td>
                    </tr>
                     <tr>
                        <td valign="top"><b>Transaction process</b></td>
                        <td>
                            {!! $arrayOfferData[$offerD->prgm_offer_id ]->transaction_process ??'NA' !!}
                        </td>
                    </tr>
                    <tr>
                     <td valign="top"><b>Specific pre-disbursement conditions</b></td>
                     <td>
                         <table width="100%" border="1">
                             @if(!empty($supplyChaindata['reviewerSummaryData']['preCond']))
                             <thead>
                                 <tr>
                                    <th style="text-align: left;">Condition</th>
                                    <th style="text-align: left;">Timeline</th>
                                 </tr>
                              </thead>
                             @foreach($supplyChaindata['reviewerSummaryData']['preCond'] as $k => $precond)
                             <tr>
                                 @if(isset($arrayOfferData[$offerD->prgm_offer_id]->pre_cond[$k]) && !empty($arrayOfferData[$offerD->prgm_offer_id ]->pre_cond[$k]))
                                 <td style="text-align: left;">{!! nl2br($arrayOfferData[$offerD->prgm_offer_id]->pre_cond[$k]) !!}</td>
                                 @endif
                                 @if(isset($arrayOfferData[$offerD->prgm_offer_id]->pre_timeline[$k]) && !empty($arrayOfferData[$offerD->prgm_offer_id ]->pre_timeline[$k]))
                                 <td style="text-align: left;"> {!! isset($arrayOfferData[$offerD->prgm_offer_id ]->pre_timeline[$k]) ? nl2br($arrayOfferData[$offerD->prgm_offer_id ]->pre_timeline[$k]) : '' !!}         
                                 </td>
                                 @endif
                             </tr>
                             @endforeach
                             @endif
                         </table>
                     </td>
                 </tr>
                     <tr>
                        <td ><b>Specific post-disbursement conditions</b></td>
                        <td>
                           <table width="100%" border="1">
                              @if(!empty($supplyChaindata['reviewerSummaryData']['postCond']))
                              <thead>
                                  <tr>
                                     <th style="text-align: left;">Condition</th>
                                     <th style="text-align: left;">Timeline</th>
                                  </tr>
                               </thead>
                                  @foreach($supplyChaindata['reviewerSummaryData']['postCond'] as $k => $postcond)                                          
                                  <tr>
                                      @if(isset($arrayOfferData[$offerD->prgm_offer_id]->post_cond[$k]) && !empty($arrayOfferData[$offerD->prgm_offer_id ]->post_cond[$k]))
                                      <td style="text-align: left;">{!! nl2br($arrayOfferData[$offerD->prgm_offer_id ]->post_cond[$k]) !!}</td> 
                                     
                                      @endif
                                      @if(isset($arrayOfferData[$offerD->prgm_offer_id]->post_timeline[$k]) && !empty($arrayOfferData[$offerD->prgm_offer_id ]->post_timeline[$k]))
                                      <td style="text-align: left;">
                                          {!! isset($arrayOfferData[$offerD->prgm_offer_id ]->post_timeline[$k]) ? nl2br($arrayOfferData[$offerD->prgm_offer_id ]->post_timeline[$k]) : '' !!}      
                                      </td> 
                                      @endif
                                  </tr>
                                  @endforeach
                                  @endif
                          </table>
                        </td>
                     </tr>
                  </table>
                  {{-- <div class="page-break"></div> --}}
               </td>
            </tr>
            @endif
            @endforeach
         </tbody>
      </table>
      {{-- <div class="page-break"></div> --}}
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify;">
         <thead>
            <tr>
               <th bgcolor="#cccccc" class="text-center" height="30" style="text-align: center;">  {{ $supplyChainFormData->annexure_general_terms_and_condition ??'' }}</th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>
                  <table width="100%" border="1" >
                     <tr>
                        <td><b>Review Date</b></td>
                        <td>{{
                           $supplyChainFormData->review_date?\Carbon\Carbon::parse($supplyChainFormData->review_date)->format('dS
                           F Y'):'' }}
                        </td>
                     </tr>
                     <tr>
                        <td valign="top"><b>Sanction validity for first disbursement</b>
                        </td>
                        <td>
                            @if(!empty($supplyChainFormData))
                            @if(isset($supplyChainFormData->sanction_applicable)  && $supplyChainFormData->sanction_applicable == 'A')
                            {{ $supplyChainFormData->sanction_validity_for_first_disbursement ??'' }} days from the date of sanction  
                            @else
                            {{ 'Not applicable' }} 
                            @endif
                            @endif
                       </td>
                    </tr>
                     <tr>
                        <td><b>Default Event</b></td>
                        <td>
                           <table  border="0" id="defaultEvent">
                              @if(!empty($supplyChainFormData))
                              @foreach($supplyChainFormData->defaultEvent as $defaultEvent)
                              <tr>
                                 <td>&bull;</td>
                                 <td>{{ $defaultEvent??'' }}
                                 </td>
                              </tr>
                              @endforeach
                              @endif
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td valign="top"><b>General pre-disbursement conditions</b></td>
                        <td>
                            <table width="100%" border="0">
                                <tr>
                                    <td colspan="2">One-time requirement:</td>
                                </tr>
                                <tr>
                                    <td valign="top" width="1%"><b>1.</b></td>
                                    <td>Accepted Sanction Letter</td>
                                </tr>
                                <tr>
                                    <td valign="top" width="1%"><b>2.</b></td>
                                    <td>Loan Agreement </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="1%"><b>3.</b></td>
                                    <td>
                                        Self-Attested KYC of borrower (True Copy)
                                        <table width="100%" border="0">
                                            @php
                                               $bizConstitution = '';
                                                if(isset($supplyChaindata['BizConstitution']) && ($supplyChaindata['BizConstitution'] == 'Private Limited Company' || $supplyChaindata['BizConstitution'] == 'Public Limited Company')  ){
                                                    $bizConstitution = 'Certificate of incorporation, MOA, AOA';
                                                }else if(isset($supplyChaindata['BizConstitution']) && ($supplyChaindata['BizConstitution'] == 'Partnership Firm')  ){
                                                    $bizConstitution = 'Partnership Deed';
                                                }else if(isset($supplyChaindata['BizConstitution']) && ($supplyChaindata['BizConstitution'] == 'Proprietorship firm' || $supplyChaindata['BizConstitution'] == 'Sole Proprietor')  ){
                                                    $bizConstitution = 'Shop and Establishment registration certificate / Udyog Adhar';
                                                }
                                            @endphp
                                           @if ($bizConstitution)
                                           <tr>
                                            <td valign="top" width="1%">&bull;</td>
                                            <td>
                                               {{ $bizConstitution }} 
                                              </td>
                                            </tr>
                                           @endif
                                            <tr>
                                                <td valign="top" width="1%">&bull;</td>
                                                <td>Valid Address Proof
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top" width="1%">&bull;</td>
                                                <td>PAN Card</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" width="1%">&bull;</td>
                                                <td>GST Registration Certificate</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                 <td valign="top" width="1%"><b>4.</b></td>
                                 <td>
                                     @php
                                        $generalCon = $supplyChainFormData->general_pre_disbursement_conditions_second??'';
                                         if($generalCon == 'Board Resolution'){
                                             $generalCon ='Board Resolution signed by 2 directors or Company Secretary in favour of company officials to execute such agreements or documents.';
                                         }
                                     @endphp
                                     {{ $generalCon??'' }}.
                                 </td>
                             </tr>
                                <tr>
                                    <td valign="top" width="5%"><b>5.</b></td>
                                    <td>
                                        KYC of authorized signatory:
                                        <table width="100%" border="0">
                                            <tr>
                                                <td valign="top" width="3%">&bull;</td>
                                                <td>Name of authorized signatories with
                                                    their
                                                    Self Attested ID proof and address proof
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top" width="3%">&bull;</td>
                                                <td>Signature Verification of authorized
                                                    signatories from Borrower's banker?
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="5%"><b>6.</b></td>
                                    <td>{{ $supplyChainFormData->any_other??'Any other documents considered necessary by Lender from time to time' }}
                                    </td>
                                </tr>
                                @if(!empty($supplyChainFormData->general_pre_disbursement_condition))
                                 @foreach($supplyChainFormData->general_pre_disbursement_condition as $k=>$genCondition)
                                 <tr class='row_gen'>
                                       <td valign="top" width="1%"><b>{{ $k+7 }}.</b></td>
                                       <td>{{ $genCondition }}
                                       </td>
                                 </tr>
                                 @endforeach
                                 @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                           <td width="30%" valign="top"><b>Monitoring Covenants</b></td>
                           <td>
                              @if($supplyChainFormData->monitoring_covenants_select == 'Applicable')
                              {!! $supplyChainFormData->monitoring_covenants_select_text ??'' !!}
                              @else
                                 {{ $supplyChainFormData->monitoring_covenants_select??'' }} 
                              @endif
                              
                           </td>
                     </tr>
                     <tr>
                        <td valign="top"><b>Other Conditions </b></td>
                        <td>
                           <table width="100%" border="0">
                              <tr>
                                 <td valign="top" width="1%">1.</td>
                                 <td>Borrower undertakes that no deferral or moratorium will be sought by the borrower at any time during the tenor of the facility.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">2.</td>
                                 <td>The loan shall be utilized for the purpose for which it is sanctioned, and it should not be utilized for –
                                    <table width="100%" border="0">
                                       <tr>
                                           <td valign="top" width="3%">a.</td>
                                           <td>Subscription to or purchase of shares/debentures.
                                           </td>
                                       </tr>
                                       <tr>
                                           <td valign="top" width="3%">b.</td>
                                           <td>Extending loans to subsidiary companies/associates or for making inter-corporate deposits.
                                           </td>
                                       </tr>
                                       <tr>
                                           <td valign="top" width="3%">c.</td>
                                           <td>Any speculative purposes.
                                           </td>
                                       </tr>
                                       <tr>
                                          <td valign="top" width="3%">d.</td>
                                          <td>Purchase/payment towards any immovable property.
                                          </td>
                                      </tr>
                                   </table>
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">3.</td>
                                 <td>The Borrower shall maintain adequate books and records which should correctly reflect their financial position and operations and it should submit to Lender at regular intervals such statements as may be prescribed by Lender in terms of the RBI / Bank’s instructions issued from time to time.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">4.</td>
                                 <td>The Borrower will keep Lender informed of the happening of any event which is likely to have an impact on their profit or business and more particularly, if the monthly production or sale and profit are likely to be substantially lower than already indicated to Lender. The Borrower will inform accordingly with reasons and the remedial steps proposed to be taken.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">5.</td>
                                 <td>Lender will have the right to examine at all times the Borrower’s books of accounts and to have the Borrower’s factory(s)/branches inspected from time to time by officer(s) of the Lender and/or qualified auditors including stock audit and/or technical experts and/or management consultants of Lender’s choice and/or we can also get the stock audit conducted by other banker. The cost of such inspections will be borne by the Borrower.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%"">6.</td>
                                 <td>The Borrower should not pay any consideration by way of commission, brokerage, fees or in any other form to guarantors directly or indirectly.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">7.</td>
                                 <td>The Borrower and Guarantor(s) shall be deemed to have given their express consent to Lender to disclose the information and data furnished by them to Lender and also those regarding the credit facility/ies enjoyed by the Borrower, conduct of accounts and guarantee obligations undertaken by guarantor to the Credit Information Bureau (India) Ltd. (“CIBIL”), or RBI or any other agencies specified by RBI who are authorized to seek and publish information.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">8.</td>
                                 <td>The Borrower will keep the Lender advised of any circumstances adversely affecting their financial position including any action taken by any creditor, Government authority against them.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">9.</td>
                                 <td>In order to remove any ambiguity, it is clarified that the intervals are intended to be continuous and accordingly, the basis for classification of SMA/NPA categories shall be considered as follows:
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="5%"></td>
                                 <td valign="top" width="100%">
                                     <table width="100%" border="0" style="border:1px #181616 solid;">
                                         <tr valign="top" width="100%">
                                             <td valign="top" width="100%"style="text-align: center;text-decoration: underline;" colspan="2">
                                             <b>“Example of SMA/NPA”</b>
                                             </td>
                                         </tr>
                                         <tr>
                                             <td valign="top" width="100%" colspan="2">
                                                 <table width="100%" border="1" style="font-weight: bold;">
                                                     <tr>
                                                         <td valign="top" width="50%">If the EMI / Tranche Amount/Interest is not paid within 30 days from the due date of repayment</td>
                                                         <td>SMA-0
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td valign="top" width="50%">If the EMI / Tranche Amount/Interest is not paid within 60 days from the due date of repayment</td>
                                                         <td>SMA-1
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td valign="top" width="50%">If the EMI / Tranche Amount/Interest is not paid within 90 days from the due date of repayment</td>
                                                         <td>SMA-2
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td valign="top" width="50%">If the EMI / Tranche Amount/Interest is not paid for more than 90 days from the due date of repayment</td>
                                                         <td>NPA
                                                         </td>
                                                     </tr>
                                                 </table>
                                             </td>
                                         </tr>
                                         <tr>
                                             <td colspan="2">
                                                <table width="100%" border="0">
                                                <tr>
                                                    <td valign="top" width="1%">&bull;</td>
                                                    <td valign="top" width="50%">Any amount due to the lender under any credit facility is ‘overdue’ if it is not paid on the due date fixed by the Lender. If there is any overdue in an account, the default/ non-repayment is reported with the credit bureau companies like CIBIL etc. and the CIBIL report of the customer will reflect defaults and its classification status.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" width="1%">&bull;</td>
                                                    <td valign="top" width="50%">Once an account is classified as NPAs then it shall be upgraded as ‘standard’ asset only if entire arrears of interest and principal are paid by the borrower.
                                                    </td>
                                                </tr>
                                            </table>
                                          </td>
                                            
                                         </tr>
                                     </table>
                                 </td>
                             </tr>
                              <tr>
                                 <td valign="top" width="1%">10.</td>
                                 <td>The Borrower shall procure consent every year from the auditors appointed by the borrower to comply with and give report / specific comments in respect of any query or requisition made by us as regards the audited accounts or balance sheet of the Borrower. We may provide information and documents to the Auditors in order to enable the Auditors to carry out the investigation requested for by us. In that event, we shall be entitled to make specific queries to the Auditors in the light of Statements, particulars and other information submitted by the Borrower to us for the purpose of availing finance, and the Auditors shall give specific comments on the queries made by us.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">11.</td>
                                 <td>The sanction limits would be valid for acceptance for 60 days from the date of the issuance of letter.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">12.</td>
                                 <td>Lender reserves the right to alter, amend any of the condition or withdraw the facility, at any time without assigning any reason and also without giving any notice.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">13.</td>
                                 <td>Borrower have read and understood the terms and conditions of the Loan including the annual rate of interest and the approach for gradation of risk and rationale for charging different rates of interest to different categories of borrowers adopted by the Lender(s). The Borrower understand the Lender (s) has its own model for arriving at lending interest rates on the basis of  various (i) risks such as interest rate risk, credit  and default risk in the related business segment, (ii)based on various cost such as  average cost of borrowed funds, matching tenure cost ,market liquidity, cost of underwriting, cost of customer acquisition etc. and other factors like profile of the borrower, repayment track record of the existing customer, future potential, deviations permitted , tenure of relationship with the borrower, overall  customer yield etc. Such information is gathered based on the information provided by the borrower, credit reports, data sources and market intelligence. The Borrower accept the terms and conditions and agree that these terms and conditions may be changed by the Lender at any time, and the Borrower shall be bound by the amended terms and conditions.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">14.</td>
                                 <td>Lender(s) reserves the right to change the rate of interest and other charges, at any time, with previous notice/intimation, and any such changes shall have prospective effect.
                                 </td>
                              </tr>
                              <tr>
                                 <td valign="top" width="1%">15.</td>
                                 <td>Provided further that notwithstanding anything to the contrary contained in this Agreement, Lender may at its sole and absolute discretion at any time, terminate, cancel or withdraw the Loan or any part thereof (even if partial or no disbursement is made) without any liability and without any obligations to give any reason whatsoever, whereupon all principal monies, interest thereon and all other costs, charges, expenses and other monies outstanding (if any) shall become due and payable to Lender by the Borrower forthwith upon demand from Lender.
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td>I /We accept all the terms and conditions which have been read and understood by me/us. </td>
            </tr>
            <tr>
               <td>We request you to acknowledge and return a copy of the same as a confirmation.</td>
            </tr>
            <tr>
               <td>
                  <table width="100%" border="0">
                     <tbody>
                        <tr>
                           <td width="50%" valign="top" height="40"><b>Yours Sincerely,</b></td>
                           <td valign="top" height="40" style="float: right;"><b>Accepted for and behalf of Borrower</b></td>
                        </tr>
                        <tr>
                           <td width="50%" valign="top" height="40"><b>For Capsave Finance Private Limited</b></td>
                           <td valign="top" height="40" style="float: right;"><b>For {{ $supplyChaindata['EntityName'] }}</b></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
            <tr>
               <td height="40"></td>
            </tr>
            <tr>
               <td>
                  <table width="100%" border="0">
                     <tbody>
                        <tr>
                           <td width="50%" valign="top" height="40"><b>Authorized Signatory</b></td>
                           <td valign="top" height="40" style="float: right;"><b>Authorized Signatory</b></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
            <tr>
               <td height="30"></td>
            </tr>
            <tr>
               <td align="center">
                  <div><span style="font-size:20px; font-weight:bold;">CAPSAVE FINANCE PRIVATE LIMITED</span><br/> 
                     Registered office: Unit No.1501 Wing-D, Lotus Corporate Park, Western Express Highway, Goregaon (East), Mumbai - 400063<br/>
                     Ph: +91 22 6173 7600, CIN No: U67120MH1992PTC068062
                  </div>
               </td>
            </tr>
         </tbody>
      </table>
   </body>
</html>