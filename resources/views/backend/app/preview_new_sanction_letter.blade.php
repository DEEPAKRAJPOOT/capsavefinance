@extends('layouts.backend.admin_popup_layout')

@section('content')
<style>
    .alert {
      padding: 8px;
      background-color: #387d38;
      color: #FFF;
      border-radius: 5px;
    }
    .closebtn {
      margin-left: 15px;
      color: white;
      font-weight: bold;
      float: right;
      font-size: 22px;
      line-height: 20px;
      cursor: pointer;
      transition: 0.3s;
    }
    .closebtn:hover {
      color: black;
    }
    .overlay {
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      position: fixed;
      background: #141415ad;
      z-index: 1;
  }
  .overlay__inner {
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      position: absolute;
  }
  .overlay__content {
      left: 50%;
      position: absolute;
      top: 50%;
      transform: translate(-50%, -50%);
      color: #FFF;
  }
  .spinner {
      width: 40px;
      height: 40px;
      display: inline-block;
      border-width: 2px;
      border-color: rgba(255, 255, 255, 0.05);
      border-top-color: #fff;
      animation: spin 1s infinite linear;
      border-radius: 100%;
      border-style: solid;
      vertical-align: middle;
  }
  @keyframes spin {
    100% {
      transform: rotate(360deg);
    }
  }
  @media print {
    @page {size: A4 portrait; margin: 0; }
    body {margin: 0;}
  }
  </style>
<div id="overlay" class="overlay" style="display: none;">
    <div class="overlay__inner">
        <div class="overlay__content">Sending Email....  &nbsp;<span class="spinner">wait</span></div>
    </div>
</div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                        <div class=" form-fields">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify;">
                                <thead>
                                    <tr>
                                    </tr>
                                    <th bgcolor="#cccccc" class="text-center" height="30"><span>SANCTION LETTER</span>
                                    </th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span>Ref No: CFPL/{{ Carbon\Carbon::now()->format('My') }}/{{request()->get('app_id')? request()->get('app_id') :''}}<br />
                                                {{ $date_of_final_submission?\Carbon\Carbon::parse($date_of_final_submission)->format('F dS, Y'):Carbon\Carbon::now()->format('F dS, Y') }}<br /><br />
                                                <b>{{ $supplyChaindata['EntityName'] }}</b><br />
                                            @if(!empty(trim($supplyChaindata['Address'])))
                                            @php
                                                $cAddress = wordwrap(trim($supplyChaindata['Address']),40,"<br>\n");
                                            @endphp
                                                       {!! $cAddress  !!}
                                            @endif
                                          </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span><b>Kind Attention :</b> {{ $supplyChaindata['ConcernedPersonName'] }}{{ $supplyChainFormData->operational_person??'' }}</span>
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
                                                <b>Dear  </b>
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
                                                    <td width="30%">Borrower</td>
                                                    <td>{{ $supplyChaindata['EntityName'] }} (referred to as “Borrower” henceforth)
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Lender</td>
                                                    <td>Capsave Finance Private Limited (referred to as “Lender”
                                                        henceforth)
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Corporate Anchor</td>
                                                    <td>{{ $supplyChaindata['anchorData'][0]['comp_name'] }} (referred to as
                                                        “Anchor” henceforth)</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Total Sanction Amount</td>
                                                    <td>INR {{ \Helpers::formatCurrencyNoSymbol($supplyChaindata['limit_amt']) }} (Rupees {{ $supplyChaindata['amountInwords'] }} only)</td>
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
                                                    <td width="50%" valign="top" height="40"><b>Yours Sincerely</b></td>
                                                    <td valign="top" height="40"><b>Accepted for and behalf of
                                                            Borrower</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" valign="top" height="40"><b>For Capsave Finance
                                                            Private
                                                            Limited</b></td>
                                                    <td valign="top" height="40"><b>For {{ $supplyChaindata['EntityName'] }}</b>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="40">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0">
                                                <tr>
                                                    <td width="50%" valign="top" height="40"><b>Authorized Signatory</b>
                                                    </td>
                                                    <td valign="top" height="40"><b>Authorized Signatory</b></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify; margin-top:25px;">
                                <thead>
                                    <tr>
                                        <th bgcolor="#cccccc" class="text-center" height="30">Annexure I – Specific
                                            Terms
                                            and Conditions</th>
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
                                    @foreach($supplyChaindata['offerData'] as $key =>  $offerD)
                                    @if ($offerD->status != 2)
                                    @php
                                    if($check == 'no'){
                                        $counter = '';
                                    }else{
                                        $counter = ' -'.$i++;
                                    }
                                        
                                    @endphp
                                    <tr>
                                        <td><b><br />FACILITY{{ $counter }} </b></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="1">
                                                <tr>
                                                    <td width="30%" valign="top"><b>Facility</b></td>
                                                    <td>Working Capital Demand Loan Facility (referred to as “Facility
                                                        1”
                                                        henceforth)</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Sanction Amount</b></td>
                                                    <td>INR {{\Helpers::formatCurrencyNoSymbol($offerD->prgm_limit_amt)}} (Rupees {{ numberTowords($offerD->prgm_limit_amt) }} only)</td>
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
                                                    <td>{{$offerD->interest_rate}}% per annum reckoned from the date of disbursement until the
                                                        date on which repayment becomes due.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Tenor for each tranche</b></td>
                                                    <td>Upto {{$offerD->tenor}} days from date of disbursement of each tranche
                                                    </td>
                                                </tr>
                                                @if($offerD->grace_period)
                                                <tr>
                                                    <td valign="top"><b>Grace Period</b></td>
                                                    <td>{{($offerD->grace_period)? $offerD->grace_period.' days':''}}
                                                    </td>
                                                </tr>  
                                                @endif
                                                <tr>
                                                    <td valign="top"><b>Old Invoice</b></td>
                                                    <td>Borrower can submit invoices not older {{ $arrayOfferData[$offerD->prgm_offer_id ]->old_invoice??'' }} {{$offerD->tenor_old_invoice}}
                                                        days
                                                        (deviation upto {{ $arrayOfferData[$offerD->prgm_offer_id ]->deviation_first_disbursement??'' }}
                                                        days for first disbursement)
                                                    </td>
                                                </tr>
                                                @if($offerD->margin)
                                                <tr>
                                                    <td valign="top"><b>Margin</b></td>
                                                    <td>
                                                        {{($offerD->margin	)? $offerD->margin:''}}% on invoice
                                                        {{ $arrayOfferData[$offerD->prgm_offer_id ]->margin??'' }}
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
                                                                        <td valign="top" width="1%">●
                                                                        </td>
                                                                        <td valign="top">
                                                                                To be paid by Anchor
                                                                                upfront for a period upto 30 days at the time of
                                                                                disbursement of each tranche.
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                <tr>
                                                                    <td valign="top" width="1%">●</td>
                                                                    <td valign="top">Lender will deduct upfront interest for
                                                                        a
                                                                        period upto 30 days at the time of disbursement of
                                                                        each
                                                                        tranche.</td>
                                                                </tr>
                                                                @endif 
                                                            @else
                                                            @if($offerD->payment_frequency == 2)
                                                            <tr>
                                                                <td valign="top" width="1%">●
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
                                                    $processingCharges = ($offerD->BizInvoice->invoice_disbursed->processing_fee ?? 0) + ($offerD->BizInvoice->invoice_disbursed->processing_fee_gst ?? 0);
                                                @endphp
                                               @if($processingCharges)
                                                <tr>
                                                    <td valign="top"><b>One time Processing Charges at the time of
                                                            Sanction
                                                            of credit facility</b></td>
                                                    <td>
                                                        {{ $processingCharges }}% of the sanctioned limit + applicable taxes payable by the
                                                        {{$arrayOfferData[$offerD->prgm_offer_id ]->one_time_processing_charges??'' }}.
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td valign="top"><b>Penal Interest</b></td>
                                                    <td>
                                                        @php
                                                            $penelInterestRate = ($offerD['overdue_interest_rate'] ?? 0) + ($offerD['interest_rate'] ?? 0)/12; 
                                                        @endphp
                                                        {{number_format($penelInterestRate, 2, '.', '')}}% per month in case any tranche remains unpaid after the expiry
                                                        of
                                                        approved tenor from the
                                                        disbursement date. Penal interest to be charged for the relevant
                                                        tranche for such overdue period
                                                        till actual payment of such tranche.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Applicable Taxes</b></td>
                                                    <td>
                                                        @php
                                                            $interest_borne_by = ($offerD->program->interest_borne_by == 1)?'Anchor':'Borrower';
                                                        @endphp
                                                        Any charges/interest payable by the {{ $interest_borne_by }} as mentioned
                                                        in
                                                        the sanction letter are
                                                        excluding applicable taxes. Taxes applicable would be levied
                                                        additionally
                                                    </td>
                                                </tr>
                                                @if($offerD->offerPs->count() || $offerD->offerCs->count() || $offerD->offerPg->count())
                                                <tr>
                                                    <td valign="top"><b>Security from Borrower</b></td>
                                                    <td>
                                                        <table width="100%" border="0">
                                                            @if($offerD->offerPs->count())
                                                            @foreach($offerD->offerPs as $PrimarySecurity)
                                                            <tr>
                                                                <td valign="top" width="1%">●</td>
                                                                <td>{{config('common.ps_security_id.'.$PrimarySecurity->ps_security_id)}} / {{config('common.ps_type_of_security_id.'.$PrimarySecurity->ps_type_of_security_id)}} / {{config('common.ps_status_of_security_id.'.$PrimarySecurity->ps_status_of_security_id)}} /{{config('common.ps_time_for_perfecting_security_id.'.$PrimarySecurity->ps_time_for_perfecting_security_id)}} / {{$PrimarySecurity->ps_desc_of_security}}
                                                                </td>
                                                            </tr>
                                                              @endforeach
                                                            @endif
                                                            @if($offerD->offerCs->count())
                                                            @foreach($offerD->offerCs as $CollateralSecurity)
                                                            <tr>
                                                                <td valign="top" width="1%">●</td>
                                                                <td>{{config('common.cs_desc_security_id.'.$CollateralSecurity->cs_desc_security_id)}} / {{config('common.cs_type_of_security_id.'.$CollateralSecurity->cs_type_of_security_id)}} / {{config('common.cs_status_of_security_id.'.$CollateralSecurity->cs_status_of_security_id)}} / {{config('common.cs_time_for_perfecting_security_id.'.$CollateralSecurity->cs_time_for_perfecting_security_id)}} / {{$CollateralSecurity->cs_desc_of_security}}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @endif
                                                            @if($offerD->offerPg->count())
                                                            <tr>
                                                            <td valign="top" width="1%">●</td>
                                                            <td>Personal Guarantee of
                                                            @foreach($offerD->offerPg as $key=>$PersonalGuarantee)
                                                                @php
                                                                   $Pg = ($supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name']) ?$supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name'] : '';
                                                                    if($key != count($offerD->offerPg)-1) {$Pg .= ", "; }else{
                                                                        $Pg .= ($supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name']) ?' and '.$supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name'] : '';
                                                                    }
                                                                @endphp
                                                            {{ $t }}
                                                            @endforeach
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
                                                        {{ $arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism??'' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Transaction process</b></td>
                                                    <td>
                                                        <table width="100%" border="0">
                                                            <tr>
                                                                <td valign="top" width="1%">●</td>
                                                                <td>Borrower will submit a disbursal request along with
                                                                    proforma invoices / invoices and Anchor will
                                                                    confirm the proforma invoices / invoices.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="1%">●</td>
                                                                <td>Lender will disburse the payment against the
                                                                    proforma
                                                                    invoice / invoices in Borrower’s
                                                                    working capital account/current account / Anchor's
                                                                    working capital account
                                                                    (in case of re-imbursement) post receiving
                                                                    confirmation
                                                                    from {{ $arrayOfferData[$offerD->prgm_offer_id ]->transaction_process ??'' }}.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="1%">●</td>
                                                                <td>Disbursement amount should not exceed 70% of
                                                                    proforma
                                                                    invoices / invoices.</td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="1%">●</td>
                                                                <td>On due date, Anchor will make payment to Lender
                                                                    within
                                                                    credit period of 30 days.</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Specific pre-disbursement conditions</b></td>
                                                    <td>
                                                        <table width="100%" border="0">
                                                            @if(!empty($supplyChaindata['reviewerSummaryData']['preCond']))
                                                            @foreach($supplyChaindata['reviewerSummaryData']['preCond'] as $k => $precond)
                                                            <tr>
                                                                <td valign="top" width="1%">{!! nl2br($precond) !!}</td>
                                                                <td>                                   
                                                                {!! isset($supplyChaindata['reviewerSummaryData']['preCondTimeline'][$k]) ? nl2br($supplyChaindata['reviewerSummaryData']['preCondTimeline'][$k]) : '' !!}                           
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @endif
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                <td valign="top"><b>Specific post-disbursement conditions</b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        @if(!empty($supplyChaindata['reviewerSummaryData']['postCond']))
                                                            @foreach($supplyChaindata['reviewerSummaryData']['postCond'] as $k => $postcond)                                          
                                                            <tr>
                                                                <td valign="top" width="1%">{!! nl2br($postcond) !!}</td>                    
                                                                <td>                                   
                                                                {!! isset($supplyChaindata['reviewerSummaryData']['postCondTimeline'][$k]) ? nl2br($supplyChaindata['reviewerSummaryData']['postCondTimeline'][$k]) : '' !!}                          
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @endif
                                                    </table>
                                                </td>
                                            </tr>
                                    </tr>
                            </table>
                            </td>
                            </tr>
                            @endif
                           @endforeach
                            </tbody>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify; margin-top:25px;">
                                <thead>
                                    <tr>
                                        <th bgcolor="#cccccc" class="text-center" height="30"> {{ $supplyChainFormData->annexure_general_terms_and_condition ??'' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <table width="100%" border="1">
                                                <tr>
                                                    <td width="30%" valign="top"><b>Review Date</b></td>
                                                    <td>{{ $supplyChainFormData->review_date?\Carbon\Carbon::parse($supplyChainFormData->review_date)->format('dS F Y'):'' }}</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Sanction validity for first disbursement</b>
                                                    </td>
                                                    <td>{{ $supplyChainFormData->sanction_validity_for_first_disbursement ??'' }} from the date of
                                                        sanction.</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Default Event</b></td>
                                                    <td>
                                                        <table width="100%" border="0" id="defaultEvent">
                                                            @if(!empty($supplyChainFormData))
                                                            @foreach($supplyChainFormData->defaultEvent as $defaultEvent)
                                                            <tr>
                                                                <td valign="top" width="1%">●</td>
                                                                <td>{{ $defaultEvent??'' }}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @else
                                                            
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
                                                                        <tr>
                                                                            <td valign="top" width="1%">●</td>
                                                                            <td>
                                                                                {{ $supplyChainFormData->general_pre_disbursement_conditions??'' }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" width="1%">●</td>
                                                                            <td>Address Proof (Not older than 60 days)
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" width="1%">●</td>
                                                                            <td>PAN Card</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" width="1%">●</td>
                                                                            <td>GST registration letter</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="1%"><b>4.</b></td>
                                                                <td>
                                                                    {{ $supplyChainFormData->general_pre_disbursement_conditions_second??'' }} signed by 2
                                                                    directors or Company Secretary in favour of company
                                                                    officials to execute such agreements or
                                                                    documents.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%"><b>5.</b></td>
                                                                <td>
                                                                    KYC of authorized signatory:
                                                                    <table width="100%" border="0">
                                                                        <tr>
                                                                            <td valign="top" width="3%">●</td>
                                                                            <td>Name of authorized signatories with
                                                                                their
                                                                                Self Attested ID proof and address proof
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" width="3%">●</td>
                                                                            <td>Signature Verification of authorized
                                                                                signatories from Borrower's banker
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%"><b>6.</b></td>
                                                                <td>Any other documents considered necessary by Lender
                                                                    from
                                                                    time to time
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="30%" valign="top"><b>Monitoring Covenants</b></td>
                                                    <td>
                                                        {{ $supplyChainFormData->monitoring_covenants_select??'' }}
                                                        @if($supplyChainFormData->monitoring_covenants_select == 'Applicable')
                                                        <br/>
                                                            {{ $supplyChainFormData->monitoring_covenants_select_text ??'' }}
                                                        @else
                                                            
                                                        @endif
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Other Conditions </b></td>
                                                    <td>
                                                        <table width="100%" border="0">
                                                            <tr>
                                                                <td valign="top" width="5%">1.</td>
                                                                <td>Borrower undertakes that no deferral or moratorium
                                                                    will
                                                                    be sought by the borrower during the tenure
                                                                    of the facility
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">2.</td>
                                                                <td>The loan shall be utilized for the purpose for which
                                                                    it
                                                                    is sanctioned, and it should not be utilized for –
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">3.</td>
                                                                <td>The Borrower shall maintain adequate books and
                                                                    records
                                                                    which should correctly reflect their
                                                                    financial position and operations and it should
                                                                    submit
                                                                    to CFPL at regular intervals such statements
                                                                    as may be prescribed by CFPL in terms of the RBI /
                                                                    Bank’s instructions issued from time to time.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">4.</td>
                                                                <td>The Borrower will keep CFPL informed of the
                                                                    happening of
                                                                    any event which is likely to have an
                                                                    impact on their profit or business and more
                                                                    particularly, if the monthly production or sale and
                                                                    profit are likely to be substantially lower than
                                                                    already
                                                                    indicated to CFPL. The Borrower will
                                                                    inform accordingly with reasons and the remedial
                                                                    steps
                                                                    proposed to be taken.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">5.</td>
                                                                <td>CFPL will have the right to examine at all times the
                                                                    Borrower’s books of accounts and to have
                                                                    the Borrower’s factory(s)/branches inspected from
                                                                    time
                                                                    to time by officer(s) of the CFPL and/or
                                                                    qualified auditors including stock audit and/or
                                                                    technical experts and/or management consultants of
                                                                    CFPL’s choice and/or we can also get the stock audit
                                                                    conducted by other banker. The cost of such
                                                                    inspections will be borne by the Borrower
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">6.</td>
                                                                <td>The Borrower should not pay any consideration by way
                                                                    of
                                                                    commission, brokerage, fees or in any
                                                                    other form to guarantors directly or indirectly.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">7.</td>
                                                                <td>The Borrower and Guarantor(s) shall be deemed to
                                                                    have
                                                                    given their express consent to CFPL to disclose the
                                                                    information and data furnished by them to CFPL and
                                                                    also
                                                                    those regarding the credit facility/ies enjoyed by
                                                                    the
                                                                    Borrower, conduct of accounts and guarantee
                                                                    obligations
                                                                    undertaken by guarantor to the Credit Information
                                                                    Bureau
                                                                    (India) Ltd. (“CIBIL”), or RBI or any other agencies
                                                                    specified by RBI who are authorized to seek and
                                                                    publish
                                                                    information.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">8.</td>
                                                                <td>The Borrower will keep the CFPL advised of any
                                                                    circumstances adversely affecting their financial
                                                                    position including any action taken by any creditor,
                                                                    Government authority against them.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">9.</td>
                                                                <td>The Borrower shall procure consent every year from
                                                                    the
                                                                    auditors appointed by the borrower to
                                                                    comply with and give report / specific comments in
                                                                    respect of any query or requisition made by us
                                                                    as regards the audited accounts or balance sheet of
                                                                    the
                                                                    Borrower. We may provide information and
                                                                    documents to the Auditors in order to enable the
                                                                    Auditors to carry out the investigation requested
                                                                    for by us. In that event, we shall be entitled to
                                                                    make
                                                                    specific queries to the Auditors in the light
                                                                    of Statements, particulars and other information
                                                                    submitted by the Borrower to us for the purpose of
                                                                    availing finance, and the Auditors shall give
                                                                    specific
                                                                    comments on the queries made by us
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">10.</td>
                                                                <td>The sanction limits would be valid for acceptance
                                                                    for 30
                                                                    days from the date of the issuance
                                                                    of letter.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">11.</td>
                                                                <td>CFPL reserves the right to alter, amend any of the
                                                                    condition or withdraw the facility,
                                                                    at any time without assigning any reason and also
                                                                    without giving any notice.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">12.</td>
                                                                <td>Provided further that notwithstanding anything to
                                                                    the
                                                                    contrary contained in this Agreement,
                                                                    CFPL may at its sole and absolute discretion at any
                                                                    time, terminate, cancel or withdraw the Loan
                                                                    or any part thereof (even if partial or no
                                                                    disbursement
                                                                    is made) without any liability and without
                                                                    any obligations to give any reason whatsoever,
                                                                    whereupon
                                                                    all principal monies, interest thereon and
                                                                    all other costs, charges, expenses and other monies
                                                                    outstanding (if any) shall become due and payable
                                                                    to CFPL by the Borrower forthwith upon demand from
                                                                    CFPL
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>I /We accept all the terms and conditions which have been read and
                                            understood by
                                            me/us. </td>
                                    </tr>
                                    <tr>
                                        <td>We request you to acknowledge and return a copy of the same as a
                                            confirmation.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td width="50%" valign="top" height="40"><b>Yours Sincerely</b>
                                                        </td>
                                                        <td valign="top" height="40"><b>Accepted for and behalf of
                                                                Borrower</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="50%" valign="top" height="40"><b>For Capsave Finance
                                                                Private Limited</b></td>
                                                        <td valign="top" height="40"><b>For {{ $supplyChaindata['EntityName'] }}</b>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="40">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td width="50%" valign="top" height="40"><b>Authorized
                                                                Signatory</b>
                                                        </td>
                                                        <td valign="top" height="40"><b>Authorized Signatory</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="30">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <div style="font-family: 'Federo', sans-serif;"><span style="font-size:20px; font-weight:bold;">CAPSAVE FINANCE PRIVATE
                                                    LIMITED</span><br />
                                                Registered office: Unit No.501 Wing-D, Lotus Corporate Park, Western
                                                Express
                                                Highway, Goregaon (East), Mumbai - 400063<br />
                                                Ph: +91 22 6173 7600, CIN No: U67120MH1992PTC068062
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if($action_type == 'preview' && !Session::has('message'))
                            @if(in_array($sanctionData->status,[2]))
                            <form action="{{route('send_new_sanction_letter_on_mail')}}" method="post" id="new_sanction_letter_form" onSubmit="return submitAlert();">
                                @csrf
                                <input type="hidden" name="sanction_letter_id" value="{{$sanctionId ?? ''}}">
                                <input type="hidden" name="app_id" value="{{$appId ?? ''}}">
                                <input type="hidden" name="offer_id" value="{{$offerId ?? ''}}">
                                <input type="hidden" name="biz_id" value="{{$bizId ?? ''}}">
                                <input type="hidden" name="action_type" value="mail">
                                <button type="submit" class="btn btn-primary pull-right mr-2 mt-3" id="send_sanction" onclick="document.getElementById('overlay').style.display='block'">Send Mail</button>
                            </form>
                                @else 
                            @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('jscript')
<script>
    function submitAlert()
    {               
        if(confirm('Are you sure you wish to final sanction letter submit?'))
        {
            return true;
        }
        document.getElementById('overlay').style.display='none';
        return false;
    }

</script>
@endsection
