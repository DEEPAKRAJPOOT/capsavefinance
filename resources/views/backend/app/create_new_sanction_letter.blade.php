@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
@include('layouts.backend.partials.admin-subnav')
<style>
#cke_monitoring_covenants_select_text {
    margin-top: 8px;
}
</style>
@php $actionText = (!empty($actionType) && $actionType == 'add')?'Create':'Edit'; @endphp
@php $actionIcon = (!empty($actionType) && $actionType == 'add')?'fa fa-plus':'fa fa-pencil'; @endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="{{ $actionIcon }}"></i>
        </div>
        <div class="header-title">
            <h3>{{ $actionText }} Sanction Letter</h3>
            <small>{{ $actionText }} Sanction Letter</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">New Sanction Letter</li>
                <li class="active">{{ $actionText }} Sanction Letter</li>
            </ol>
        </div>
    </section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-0 justify-content-between pull-right">
                        @if($actionType != 'download')
                            {{-- @if(in_array($sanctionData->status,[0,1,2,3])) --}}
                        @php 
                            $limitValidityEndDate = $appLimit->actual_end_date ?? $appLimit->end_date ?? NULL;
                        @endphp
                        <span class="badge badge-info mb-3">
                            Limit Validity: From Date 
                            {{ isset($appLimit->start_date)? Carbon\Carbon::parse($appLimit->start_date)->format('d/m/Y'):'N/A' }} - To Date 
                            {{ isset($limitValidityEndDate)? Carbon\Carbon::parse($limitValidityEndDate)->format('d/m/Y'):'N/A' }}
                        </span>
                         <!-- add limit validity date -->
                            @else
                        {{-- @endif --}}
                    @endif
                    </div>
                    <form action="{{route('save_new_sanction_letter')}}" method="post" id="new_sanction_letter_form" onSubmit="return submitAlert();">
                        @csrf
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
                                            <span><b>Kind Attention :</b>
                                            @if($contact_person)
                                                {{ $contact_person }}   
                                            @else
                                                <input type="text" style="width:250px; height:30px;" name="operational_person" id="operational_person" value="{{ $supplyChainFormData->operational_person??'' }}">  
                                            @endif
                                            </span>
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
                                                <b>Dear :</b>
                                                <select style="width:150px; height:30px;" name="title" id="title">
                                                    <option {{ isset($supplyChainFormData->title) && $supplyChainFormData->title == 'sir'?'selected':'' }}>Sir</option>
                                                    <option {{ isset($supplyChainFormData->title) && $supplyChainFormData->title == 'Madam'?'selected':'' }}>Madam</option>
                                                    <option {{ isset($supplyChainFormData->title) && $supplyChainFormData->title == 'Sir/Madam'?'selected':'' }}>Sir/Madam</option>
                                                </select>
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
                                                        “Anchor” henceforth)</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%"><b>Total Sanction Amount</b></td>
                                                    <td>INR {{ number_format($supplyChaindata['limit_amt']) }} (Rupees {{ $supplyChaindata['amountInwords'] }} only)</td>
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
                                                    <td width="50%" valign="top" height="40"><b>Yours Sincerely,</b></td>
                                                    <td valign="top" height="40" style="float: right;"><b>Accepted for and behalf of
                                                            Borrower</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" valign="top" height="40"><b>For CAPSAVE FINANCE
                                                        PRIVATE
                                                        LIMITED</b></td>
                                                    <td valign="top" height="40" style="float: right;"><b>For {{ $supplyChaindata['EntityName'] }}</b>
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
                                                    <td valign="top" height="40" style="float: right;"><b>Authorized Signatory</b></td>
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
                                    $securityDataPre=Helpers::getSecurityDoc($offerD->prgm_offer_id,$offerD->app_id,1);
                                    $securityDataPost=Helpers::getSecurityDoc($offerD->prgm_offer_id,$offerD->app_id,2);   
                                    @endphp
                                    <tr>
                                        <td><b><br />FACILITY{{ $counter }} </b></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="1">
                                                <tr>
                                                    <td width="30%" valign="top"><b>Facility</b></td>
                                                    <td>Working Capital Demand Loan Facility (referred to as “Facility”henceforth)</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Sanction Amount</b></td>
                                                    <td>INR {{number_format($offerD->prgm_limit_amt)}} (Rupees {{ numberTowords($offerD->prgm_limit_amt) }} only)</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Facility Tenor</b></td>
                                                    <td>
                                                        <input type="text" value="{{ $arrayOfferData[$offerD->prgm_offer_id]->facility_tenor??'3 months' }}" name="offerData[{{ $offerD->prgm_offer_id }}][facility_tenor]" id="facility_tenor" style=" min-height:30px;padding:0 5px; " class="facility_tenor">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Purpose of the facility</b></td>
                                                    <td><input type="text" value="{{ $arrayOfferData[$offerD->prgm_offer_id ]->purpose_of_the_facility??'Working Capital' }}" name="offerData[{{ $offerD->prgm_offer_id }}][purpose_of_the_facility]" id="purpose_of_the_facility" style=" min-height:30px;padding:0 5px; margin-top:5px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Rate of Interest </b></td>
                                                    @if(isset($offerD->is_lending_rate) && $offerD->is_lending_rate == 1)
                                                    <td><textarea name="offerData[{{ $offerD->prgm_offer_id }}][r_o_i]" id="r_o_i" class="form-control textarea r_o_i" cols="30" rows="10">@if(!empty($arrayOfferData[$offerD->prgm_offer_id ]->r_o_i) && $arrayOfferData[$offerD->prgm_offer_id ]->r_o_i){!! $arrayOfferData[$offerD->prgm_offer_id ]->r_o_i !!} @else{{$offerD->interest_rate}}% per annum i.e., ROI equal to CFPL Benchmark Lending Rate less {{ $offerD->lending_rate_diff??0.00 }}% (to be reckoned from the date of disbursement until the date on which repayment becomes due).
                                                    Presently Benchmark Lending Rate (BLR) as on date is {{ $offerD->lending_rate??0.00 }}%. Interest rate on repayment would change based on the changes in BLR as announced by Lender from time to time. This would lead to change in interest payable to Lender. @endif</textarea>
                                                    <label id="r_o_i_{{ $offerD->prgm_offer_id }}-error" class="error" for="r_o_i_{{ $offerD->prgm_offer_id }}"></label>
                                                    </td>
                                                    @else
                                                    <td><textarea id="r_o_i_{{ $offerD->prgm_offer_id }}" name="offerData[{{ $offerD->prgm_offer_id }}][r_o_i]" class="form-control textarea r_o_i" cols="30" rows="10">
                                                        @if(!empty($arrayOfferData[$offerD->prgm_offer_id ]->r_o_i) && $arrayOfferData[$offerD->prgm_offer_id ]->r_o_i){!! $arrayOfferData[$offerD->prgm_offer_id ]->r_o_i !!} @else{{$offerD->interest_rate}}% per annum i.e., ROI equal to CFPL Benchmark Lending Rate less 0.00% (to be reckoned from the date of disbursement until the date on which repayment becomes due).
                                                        Presently Benchmark Lending Rate (BLR) as on date is 0.00%. Interest rate on repayment would change based on the changes in BLR as announced by Lender from time to time. This would lead to change in interest payable to Lender.@endif
                                                        </textarea>
                                                        <label id="r_o_i_{{ $offerD->prgm_offer_id }}-error" class="error" for="r_o_i_{{ $offerD->prgm_offer_id }}"></label>
                                                    </td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Tenor for each tranche</b></td>
                                                    <td>
                                                        Upto {{($offerD->tenor + $offerD->grace_period)}} days{{($offerD->grace_period)?' (including grace period of '.$offerD->grace_period.' days)':''}} from date of disbursement of each tranche
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Old Invoice</b></td>
                                                    <td>Borrower can submit invoices not older than {{$offerD->tenor_old_invoice}}
                                                        days. Door to door tenor shall not exceed <input type="text" value="{{ $arrayOfferData[$offerD->prgm_offer_id ]->deviation_first_disbursement??($offerD->tenor + $offerD->grace_period + $offerD->tenor_old_invoice) }}" name="offerData[{{ $offerD->prgm_offer_id }}][deviation_first_disbursement]" id="deviation_first_disbursement" style=" min-height:30px;padding:0 5px; margin-top:5px;"> days 
                                                        from date of invoice.
                                                    </td>
                                                </tr>
                                                @if($offerD->margin && $offerD->margin > 0)
                                                <tr>
                                                    <td valign="top"><b>Margin</b></td>
                                                    @php
                                                        $list_type = array('Tax Invoice','Proforma Invoice','Purchase Order');
                                                    @endphp
                                                    <td>
                                                        @php
                                                                   $checked = '';
                                                               @endphp
                                                        {{($offerD->margin	)? $offerD->margin:'NIL'}}% on 
                                                               @foreach ($list_type as $l=>$a)
                                                               @php
                                                                   $checked = '';
                                                               @endphp
                                                               @if (!empty($arrayOfferData[$offerD->prgm_offer_id]->margin) && is_array($arrayOfferData[$offerD->prgm_offer_id]->margin))
                                                                   @foreach ($arrayOfferData[$offerD->prgm_offer_id]->margin as $g=>$r)
                                                                       @if ($r == $a)
                                                                           @php
                                                                               $checked = 'checked';
                                                                           @endphp
                                                                       @endif
                                                                   @endforeach  
                                                                   @else
                                                                   @php
                                                                       if(isset($arrayOfferData[$offerD->prgm_offer_id]->margin) && ($arrayOfferData[$offerD->prgm_offer_id]->margin == $a)){
                                                                            $checked = 'checked';
                                                                       }
                                                                   @endphp
                                                                @endif
                                                               <input type = "checkbox" id = "margin1" name="offerData[{{ $offerD->prgm_offer_id }}][margin][]" class="margin_input" value = "{{ $a }}" {{ $checked }} required> {{ $a }} 
                                                               @endforeach
                                                               value. (in case margin is nil in offer – not to capture in final SL)
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
                                                                        <td valign="top" width="1%">&bull;
                                                                        </td>
                                                                        <td valign="top">
                                                                                To be paid by Anchor
                                                                                upfront for a period upto {{($offerD->tenor)}} days at the time of
                                                                                disbursement of each tranche.
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                <tr>
                                                                    <td valign="top" width="1%">&bull;</td>
                                                                    <td valign="top">Lender will deduct upfront interest for
                                                                        a
                                                                        period upto {{($offerD->tenor)}} days at the time of disbursement of
                                                                        each
                                                                        tranche.</td>
                                                                </tr>
                                                                @endif 
                                                            @else
                                                            @if($offerD->payment_frequency == 2)
                                                            <tr>
                                                                <td valign="top" width="1%">&bull;
                                                                </td>
                                                                <td valign="top">
                                                                    Lender shall charge monthly interest to the
                                                                    <select style="min-height:30px; padding:0 5px; min-width:180px;" name="offerData[{{ $offerD->prgm_offer_id }}][lender_shall_charge]" id="lender_shall_charge">
                                                                        <option {{ isset($arrayOfferData[$offerD->prgm_offer_id ]->lender_shall_charge) && $arrayOfferData[$offerD->prgm_offer_id ]->lender_shall_charge == 'Anchor'?'selected':'' }}>Anchor</option>
                                                                        <option {{ isset($arrayOfferData[$offerD->prgm_offer_id ]->lender_shall_charge) && $arrayOfferData[$offerD->prgm_offer_id ]->lender_shall_charge == 'Borrower'?'selected':'' }}>Borrower</option>
                                                                    </select>
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
                                                <tr>
                                                    <td valign="top"><b>One time Processing Charges at the time of
                                                            Sanction
                                                            of credit facility</b></td>
                                                    <td>
                                                        @php
                                                        $processingCharges = '0.00';
                                                          if(isset($offerD->offerCharges)){
                                                            foreach($offerD->offerCharges as $key=>$offerCharge){
                                                            if($offerCharge->chargeName->chrg_name == 'Processing Fee'){
                                                             if($offerCharge->chrg_type == '2'){
                                                                $processingCharges = $offerCharge->chrg_value;
                                                             }
                                                            }
                                                          }
                                                        }
                                                        @endphp
                                                        {{ $processingCharges }}% of the sanctioned limit + applicable taxes payable by the
                                                         @php
                                                            $selected1 = $selected2 = '';
                                                            if(isset($arrayOfferData[$offerD->prgm_offer_id ]->one_time_processing_charges) &&  $arrayOfferData[$offerD->prgm_offer_id ]->one_time_processing_charges == 'Anchor'){
                                                                $selected1 = 'selected';
                                                            }elseif (isset($arrayOfferData[$offerD->prgm_offer_id ]->one_time_processing_charges) &&  $arrayOfferData[$offerD->prgm_offer_id ]->one_time_processing_charges == 'Borrower') {
                                                                $selected2 = 'selected';
                                                            }elseif (isset($offerD->program->interest_borne_by) && $offerD->program->interest_borne_by == 1) {
                                                                $selected1 = 'selected';
                                                            }elseif (isset($offerD->program->interest_borne_by) && $offerD->program->interest_borne_by == 2) {
                                                                $selected2 = 'selected';
                                                            }
                                                         @endphp
                                                        <select style="min-height:30px; padding:0 5px; min-width:180px;" name="offerData[{{ $offerD->prgm_offer_id }}][one_time_processing_charges]" id="one_time_processing_charges">
                                                            <option {{ $selected1 }}>Anchor</option>
                                                            <option {{ $selected2 }}>Borrower</option>
                                                        </select>
                                                        (non-refundable). *(If Nil is selected in offer– not to capture in final SL).
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Default/Penal Interest</b></td>
                                                    <td>
                                                        @php
                                                            $penelInterestRate = (($offerD['overdue_interest_rate'] ?? 0) + ($offerD['interest_rate'] ?? 0)); 
                                                        @endphp
                                                        <textarea class="form-control textarea penal_interest"  name="offerData[{{ $offerD->prgm_offer_id }}][penal_interest]" id="penal_interest_{{ $offerD->prgm_offer_id }}" cols="30" rows="10">@if(!empty($arrayOfferData[$offerD->prgm_offer_id ]->penal_interest) && $arrayOfferData[$offerD->prgm_offer_id ]->penal_interest){!! $arrayOfferData[$offerD->prgm_offer_id ]->penal_interest !!} @else
                                                        <b>{{number_format($penelInterestRate, 2, '.', '')}}% per annum including above regular rate of interest in case any tranche remains unpaid after the expiry of approved tenor from the disbursement date. Penal interest to be charged for the relevant tranche for such overdue period till actual payment of such tranche.</b>@endif
                                                        </textarea>
                                                        <label id="penal_interest_{{ $offerD->prgm_offer_id }}-error" class="error" for="penal_interest_{{ $offerD->prgm_offer_id }}"></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Applicable Taxes</b></td>
                                                    <td>
                                                        @php
                                                            $selected3 = $selected4 = '';
                                                            if(isset($arrayOfferData[$offerD->prgm_offer_id ]->applicable_taxes) &&  $arrayOfferData[$offerD->prgm_offer_id ]->applicable_taxes == 'Anchor'){
                                                                $selected3 = 'selected';
                                                            }elseif (isset($arrayOfferData[$offerD->prgm_offer_id ]->applicable_taxes) &&  $arrayOfferData[$offerD->prgm_offer_id ]->applicable_taxes == 'Borrower') {
                                                                $selected4 = 'selected';
                                                            }elseif (isset($offerD->program->interest_borne_by) && $offerD->program->interest_borne_by == 1) {
                                                                $selected3 = 'selected';
                                                            }elseif (isset($offerD->program->interest_borne_by) && $offerD->program->interest_borne_by == 2) {
                                                                $selected4 = 'selected';
                                                            }
                                                        @endphp
                                                        Any charges/interest payable by the 
                                                        <select style="min-height:30px; padding:0 5px; min-width:180px;" name="offerData[{{ $offerD->prgm_offer_id }}][applicable_taxes]" id="applicable_taxes">
                                                            <option {{ $selected3 }}>Anchor</option>
                                                            <option {{ $selected4 }}>Borrower</option>
                                                        </select> as mentioned
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
                                                            @if($offerD->offerPs->count() || isset($arrayOfferData[$offerD->prgm_offer_id]->ps_security))
                                                            @if (isset($arrayOfferData[$offerD->prgm_offer_id]->ps_security) && !empty($arrayOfferData[$offerD->prgm_offer_id]->ps_security))
                                                            @foreach($arrayOfferData[$offerD->prgm_offer_id]->ps_security as $PrimarySecurityS)
                                                            <tr>
                                                                <td valign="top" width="1%">&bull;</td>
                                                                <td><textarea name="offerData[{{ $offerD->prgm_offer_id }}][ps_security][]" class="form-control form-control-sm">{!! $PrimarySecurityS !!}</textarea>
                                                                </td>
                                                            </tr>
                                                              @endforeach 
                                                            @else
                                                            @foreach($offerD->offerPs as $PrimarySecurity)
                                                            <tr>
                                                                <td valign="top" width="1%">&bull;</td>
                                                                <td><textarea name="offerData[{{ $offerD->prgm_offer_id }}][ps_security][]" class="form-control form-control-sm">{{config('common.ps_security_id.'.$PrimarySecurity->ps_security_id)}} / {{config('common.ps_type_of_security_id.'.$PrimarySecurity->ps_type_of_security_id)}} / {{config('common.ps_status_of_security_id.'.$PrimarySecurity->ps_status_of_security_id)}} /{{config('common.ps_time_for_perfecting_security_id.'.$PrimarySecurity->ps_time_for_perfecting_security_id)}} / {{$PrimarySecurity->ps_desc_of_security}}</textarea>
                                                                </td>
                                                            </tr>
                                                              @endforeach
                                                              @endif
                                                            @endif
                                                            @if($offerD->offerCs->count() || isset($arrayOfferData[$offerD->prgm_offer_id]->cs_security))
                                                            @if (isset($arrayOfferData[$offerD->prgm_offer_id]->cs_security) && !empty($arrayOfferData[$offerD->prgm_offer_id]->cs_security))
                                                            @foreach($arrayOfferData[$offerD->prgm_offer_id]->cs_security as $CsSecurityS)
                                                            <tr>
                                                                <td valign="top" width="1%">&bull;</td>
                                                                <td><textarea name="offerData[{{ $offerD->prgm_offer_id }}][cs_security][]" class="form-control form-control-sm">{!! $CsSecurityS !!}</textarea>
                                                                </td>
                                                            </tr>
                                                            @endforeach   
                                                            @else
                                                            @foreach($offerD->offerCs as $CollateralSecurity)
                                                            <tr>
                                                                <td valign="top" width="1%">&bull;</td>
                                                                <td><textarea name="offerData[{{ $offerD->prgm_offer_id }}][cs_security][]" class="form-control form-control-sm">{{config('common.cs_desc_security_id.'.$CollateralSecurity->cs_desc_security_id)}} / {{config('common.cs_type_of_security_id.'.$CollateralSecurity->cs_type_of_security_id)}} / {{config('common.cs_status_of_security_id.'.$CollateralSecurity->cs_status_of_security_id)}} / {{config('common.cs_time_for_perfecting_security_id.'.$CollateralSecurity->cs_time_for_perfecting_security_id)}} / {{$CollateralSecurity->cs_desc_of_security}}</textarea>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @endif
                                                            @endif
                                                            @if($offerD->offerPg->count())
                                                            <tr>
                                                            <td valign="top" width="1%">&bull;</td>
                                                            <td>Personal Guarantee of
                                                            @php
                                                                $Pg='';
                                                            @endphp
                                                            @foreach($offerD->offerPg as $key=>$PersonalGuarantee)
                                                                @php
                                                                   $Pg = ($supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name']) ?$supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name'] : '';
                                                                    if($key != count($offerD->offerPg)-1) {$Pg .= ", "; }else{
                                                                        $Pg .= ($supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name']) ?' and '.$supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name'] : '';
                                                                    }
                                                                @endphp
                                                            @endforeach
                                                            @if(isset($arrayOfferData[$offerD->prgm_offer_id ]->pg_guarantor) &&  $arrayOfferData[$offerD->prgm_offer_id ]->pg_guarantor != '')
                                                            <textarea name="offerData[{{ $offerD->prgm_offer_id }}][pg_guarantor]" class="form-control form-control-sm">{!! $arrayOfferData[$offerD->prgm_offer_id ]->pg_guarantor??''!!}</textarea>
                                                            @else
                                                            <textarea name="offerData[{{ $offerD->prgm_offer_id }}][pg_guarantor]" class="form-control form-control-sm">{{ $Pg }}</textarea>
                                                            @endif
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
                                                        @php
                                                            $selected5 = '';
                                                            if(isset($arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism) &&  $arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism == 'Anchor'){
                                                                $selected5 = 'Anchor';
                                                            }elseif (isset($arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism) &&  $arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism == 'Borrower') {
                                                                $selected5 = 'Borrower';
                                                            }elseif (isset($offerD->program->interest_borne_by) && $offerD->program->interest_borne_by == 1) {
                                                                $selected5 = 'Anchor';
                                                            }elseif (isset($offerD->program->interest_borne_by) && $offerD->program->interest_borne_by == 2) {
                                                                $selected5 = 'Borrower';
                                                            }
                                                        @endphp
                                                        <textarea class="form-control textarea payment_mechanism" name="offerData[{{ $offerD->prgm_offer_id }}][payment_mechanism]" id="payment_mechanism_{{ $offerD->prgm_offer_id }}" cols="30" rows="10">@if(!empty($arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism) && $arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism){!! $arrayOfferData[$offerD->prgm_offer_id ]->payment_mechanism !!} @else Direct payment by the {{ $selected5 }} to the Lender on or before the tranche due date based on tranche tenure through RTGS/NEFT/NACH/Cheque or any other mode acceptable to Lender.@endif
                                                    </textarea>
                                                    <label id="payment_mechanism_{{ $offerD->prgm_offer_id }}-error" class="error" for="payment_mechanism_{{ $offerD->prgm_offer_id }}"></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Moratorium (if applicable)</b></td>
                                                    <td>
                                                        <textarea class="form-control textarea moratorium" name="offerData[{{ $offerD->prgm_offer_id }}][moratorium]" id="moratorium{{ $offerD->prgm_offer_id }}" cols="30" rows="10">@if(!empty($arrayOfferData[$offerD->prgm_offer_id ]->moratorium) && $arrayOfferData[$offerD->prgm_offer_id ]->moratorium){!! $arrayOfferData[$offerD->prgm_offer_id ]->moratorium !!} @else NA @endif</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Transaction process</b></td>
                                                    <td>
                                                        <textarea class="form-control textarea transaction_process" name="offerData[{{ $offerD->prgm_offer_id }}][transaction_process]" id="transaction_process{{ $offerD->prgm_offer_id }}" cols="30" rows="10">@if(!empty($arrayOfferData[$offerD->prgm_offer_id ]->transaction_process) && $arrayOfferData[$offerD->prgm_offer_id ]->transaction_process){!! $arrayOfferData[$offerD->prgm_offer_id ]->transaction_process !!} @else  @endif</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Specific pre-disbursement conditions</b></td>
                                                    <td>
                                                        @if(!empty($securityDataPre) && count($securityDataPre) > 0 && $securityDataPre)
                                                        <table width="100%" border="1">
                                                            <thead>
                                                                <tr>
                                                                   <th style="text-align: left;">Type of Document</th>
                                                                   <th style="text-align: left;">Description</th>
                                                                   <th style="text-align: left;">Document Number</th>
                                                                   <th style="text-align: left;">Original Due Date</th>
                                                                </tr>
                                                             </thead>
                                                            @foreach($securityDataPre as $k => $precond)
                                                              <tr>
                                                                 <td>{{ $precond->mstSecurityDocs->name??'N/A' }}</td>
                                                                 <td>{{ $precond->description??'N/A' }}</td>
                                                                 <td>{{ $precond->document_number??'N/A' }}</td>
                                                                 <td>{{ $precond->due_date?\Carbon\Carbon::parse($precond->due_date)->format('d-m-Y'):'N/A' }}</td>
                                                              </tr>  
                                                            @endforeach
                                                        </table>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                <td valign="top"><b>Specific post-disbursement conditions</b></td>
                                                <td>
                                                    @if(!empty($securityDataPost) && count($securityDataPost) > 0 && $securityDataPost)
                                                    <table width="100%" border="1">
                                                            <thead>
                                                                <tr>
                                                                   <th style="text-align: left;">Type of Document</th>
                                                                   <th style="text-align: left;">Description</th>
                                                                   <th style="text-align: left;">Document Number</th>
                                                                   <th style="text-align: left;">Original Due Date</th>
                                                                </tr>
                                                             </thead>
                                                            @foreach($securityDataPost as $k => $postcond)
                                                              <tr>
                                                                 <td>{{ $postcond->mstSecurityDocs->name??'N/A' }}</td>
                                                                 <td>{{ $postcond->description??'N/A' }}</td>
                                                                 <td>{{ $postcond->document_number??'N/A' }}</td>
                                                                 <td>{{ $postcond->due_date?\Carbon\Carbon::parse($postcond->due_date)->format('d-m-Y'):'N/A' }}</td>
                                                              </tr>
                                                            @endforeach
                                                    </table>
                                                    @endif
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
                                        <th bgcolor="#cccccc" class="text-center" height="30"> <input type="text" value="{{ $supplyChainFormData->annexure_general_terms_and_condition ??'Annexure II - General Terms and Conditions' }}" style=" min-height:30px;padding:0 5px; min-width:300px;" name="annexure_general_terms_and_condition" id="annexure_general_terms_and_condition"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <table width="100%" border="1">
                                                <tr>
                                                    <td width="30%" valign="top"><b>Review Date</b></td>
                                                    <td><input type="date" name="review_date" id="review_date" style="min-height:30px;" value="{{ $supplyChainFormData->review_date ?? '' }}"></td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Sanction validity for first disbursement</b>
                                                    </td>
                                                    <td>
                                                        <select style="min-height:30px; padding:0 5px; min-width:180px;" name="sanction_applicable" id="sanction_applicable">
                                                            <option value="A" {{ isset($supplyChainFormData->sanction_applicable) && $supplyChainFormData->sanction_applicable == 'A'?'selected':'' }}>Applicable</option>
                                                            <option value="NA" {{ isset($supplyChainFormData->sanction_applicable) && $supplyChainFormData->sanction_applicable == 'NA'?'selected':'' }}>Not applicable</option>
                                                        </select>
                                                        @php
                                                        $class = '';  
                                                        @endphp 
                                                  @if(!empty($supplyChainFormData))
                                                     @if(isset($supplyChainFormData->sanction_applicable)  &&$supplyChainFormData->sanction_applicable == 'A')
                                                         @php
                                                           $class = '';  
                                                         @endphp   
                                                     @else
                                                        @php
                                                           $class = 'hide';         
                                                        @endphp  
                                                     @endif
                                                     @endif
                                                     <div class="clearfix"></div>
                                                     <div class="{{ $class }}" id="sanction_validity_for_first_disbursement_div">
                                                     <input value="{{ $supplyChainFormData->sanction_validity_for_first_disbursement ??'' }}" type="text" name="sanction_validity_for_first_disbursement" id="sanction_validity_for_first_disbursement" class="input_sanc {{ $class }}" style=" min-height:30px;padding:0 5px;margin-top: 1%;" placeholder="Sanction validity for first disbursement"> days from the date of sanction.
                                                     </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Default Event</b></td>
                                                    <td>
                                                        <table width="100%" border="0" id="defaultEvent">
                                                            @if(!empty($supplyChainFormData))
                                                            @foreach($supplyChainFormData->defaultEvent as $defaultEvent)
                                                            <tr>
                                                                <td valign="top" width="1%">&bull;</td>
                                                                <td><input type="text" value="{{ $defaultEvent }}" name="defaultEvent[]" style=" min-height:30px;padding:0 5px; min-width:100%;" class="row_defevent_input">
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @else
                                                            <tr>
                                                                <td valign="top" width="1%">&bull;</td>
                                                                <td><input type="text" value="Payments not received on or before the due date will be treated as overdue / default by the Borrower." name="defaultEvent[]" style=" min-height:30px;padding:0 5px; min-width:100%;" class="row_defevent_input"> </td>
                                                             </tr>
                                                             <tr>
                                                                <td valign="top" width="1%">&bull;</td>
                                                                <td><input type="text" value="No further disbursement will be made in case of any default under the Facility." name="defaultEvent[]" style=" min-height:30px;padding:0 5px; min-width:100%;" class="row_defevent_input"></td>
                                                             </tr>
                                                            @endif
                                                        </table>
                                                        <span class="btn btn-danger btn-sm remove_defaultevent" style="float: right;margin: 5px;cursor: pointer;">Remove
                                                            -</span>
                                                        <span class="btn btn-success btn-sm clone_defaultevent" style="float: right;margin: 5px;cursor: pointer;">Add More
                                                            +</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>General pre-disbursement conditions</b></td>
                                                    <td>
                                                        <table width="100%" border="0" id="general_pre_disbursement_conditions">
                                                            <tbody id="ger_cond">
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
                                                                            if(isset($supplyChaindata['BizConstitution']) && (strtolower($supplyChaindata['BizConstitution']) == 'private limited company' || strtolower($supplyChaindata['BizConstitution']) == 'public limited company')  ){
                                                                                $bizConstitution = 'Certificate of incorporation, MOA, AOA';
                                                                            }else if(isset($supplyChaindata['BizConstitution']) && (strtolower($supplyChaindata['BizConstitution']) == 'partnership firm')  ){
                                                                                $bizConstitution = 'Partnership Deed';
                                                                            }else if(isset($supplyChaindata['BizConstitution']) && (strtolower($supplyChaindata['BizConstitution']) == 'proprietorship firm' || strtolower($supplyChaindata['BizConstitution']) == 'sole proprietor')  ){
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
                                                                    $bizConstitutionDes = '';
                                                                     if(isset($supplyChaindata['BizConstitution']) && (strtolower($supplyChaindata['BizConstitution']) == 'private limited company')  ){
                                                                         $bizConstitutionDes = 'Board Resolution signed by 2 directors or Company Secretary in favour of company officials to execute such agreements or documents';
                                                                     } else if(isset($supplyChaindata['BizConstitution']) && (strtolower($supplyChaindata['BizConstitution']) == 'huf letter')  ){
                                                                         $bizConstitutionDes = 'HUF';
                                                                     }else if(isset($supplyChaindata['BizConstitution']) && (strtolower($supplyChaindata['BizConstitution']) == 'partnership firm')  ){
                                                                         $bizConstitutionDes = 'Partnership Authority Letter';
                                                                     }else if(isset($supplyChaindata['BizConstitution']) && (strtolower($supplyChaindata['BizConstitution']) == 'proprietorship firm' || strtolower($supplyChaindata['BizConstitution']) == 'sole proprietor')  ){
                                                                         $bizConstitutionDes = 'Proprietorship Declaration';
                                                                     }
                                                                    @endphp
                                                                    @if ($bizConstitutionDes)
                                                                        {{ $bizConstitutionDes }} 
                                                                    @endif 
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
                                                                        @if ($supplyChaindata['isNachPdc'])
                                                                        <tr>
                                                                            <td valign="top" width="3%">&bull;</td>
                                                                            <td>Signature Verification of authorized
                                                                                signatories from Borrower's banker?
                                                                            </td>
                                                                        </tr>
                                                                        @endif
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%"><b>6.</b></td>
                                                                <td><input type="text" value="{{isset($supplyChainFormData->any_other) && $supplyChainFormData->any_other?$supplyChainFormData->any_other:'Any other documents considered necessary by Lender from time to time'}}" name="any_other" style=" min-height:30px;padding:0 5px; min-width:100%;" class="row_gen_input">
                                                                </td>
                                                            </tr>
                                                            @if(!empty($supplyChainFormData->general_pre_disbursement_condition))
                                                            @foreach($supplyChainFormData->general_pre_disbursement_condition as $k=>$genCondition)
                                                            <tr class='row_gen'>
                                                                <td valign="top" width="1%"><b>{{ $k+7 }}.</b></td>
                                                                <td><input type="text" value="{{ $genCondition }}" name="general_pre_disbursement_condition[]" style=" min-height:30px;padding:0 5px; min-width:100%;" class="row_gen_input">
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @endif
                                                        </tbody>
                                                        </table>
                                                        <span class="btn btn-danger btn-sm remove_general_pre_disbursement_conditions" style="float: right;margin: 5px;cursor: pointer;">Remove
                                                            -</span>
                                                        <span class="btn btn-success btn-sm clone_general_pre_disbursement_conditions" style="float: right;margin: 5px;cursor: pointer;">Add More
                                                            +</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="30%" valign="top"><b>Monitoring Covenants</b></td>
                                                    <td>
                                                        <select style="width:200px; min-height:30px; padding:0 5px;" name="monitoring_covenants_select" id="monitoring_covenants_select">
                                                            <option value="Applicable" {{  isset($supplyChainFormData->monitoring_covenants_select) &&$supplyChainFormData->monitoring_covenants_select == 'Applicable'?'selected':'' }}>Applicable</option>
                                                            <option value="Not applicable" {{  isset($supplyChainFormData->monitoring_covenants_select) &&$supplyChainFormData->monitoring_covenants_select == 'Not applicable'?'selected':'' }}>Not applicable</option>
                                                        </select>
                                                        @php
                                                        $class = ''; 
                                                        if(!empty($actionType) && $actionType == 'add'){
                                                        echo '<style>
                                                            #cke_monitoring_covenants_select_text {
                                                                display: none;
                                                            }
                                                            </style>'; 
                                                        }
                                                        @endphp 
                                                  @if(!empty($supplyChainFormData))
                                                     @if(isset($supplyChainFormData->monitoring_covenants_select)  &&$supplyChainFormData->monitoring_covenants_select == 'Applicable')
                                                         @php
                                                           $class = '';  
                                                         @endphp   
                                                     @else
                                                        @php
                                                           $class = ' hide'; 
                                                           echo '<style>
                                                            #cke_monitoring_covenants_select_text {
                                                                display: none;
                                                            }
                                                            </style>';
                                                        @endphp  
                                                     @endif
                                                     @endif
                                                     <div class="clearfix"></div>
                                                     <textarea class="form-control" name="monitoring_covenants_select_text" id="monitoring_covenants_select_text" cols="30" rows="10">@if(!empty($supplyChainFormData->monitoring_covenants_select_text) && $supplyChainFormData->monitoring_covenants_select_text){!! $supplyChainFormData->monitoring_covenants_select_text !!} @else NA @endif</textarea>
                                                       
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><b>Other Conditions </b></td>
                                                    <td>
                                                        <table width="100%" border="0">
                                                            <tr>
                                                                <td valign="top" width="5%">1.</td>
                                                                <td>Borrower undertakes that no deferral or moratorium will be sought by the borrower at any time during the tenor of the facility.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">2.</td>
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
                                                                <td valign="top" width="5%">3.</td>
                                                                <td>The Borrower shall maintain adequate books and records which should correctly reflect their financial position and operations and it should submit to Lender at regular intervals such statements as may be prescribed by Lender in terms of the RBI / Bank’s instructions issued from time to time.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">4.</td>
                                                                <td>The Borrower will keep Lender informed of the happening of any event which is likely to have an impact on their profit or business and more particularly, if the monthly production or sale and profit are likely to be substantially lower than already indicated to Lender. The Borrower will inform accordingly with reasons and the remedial steps proposed to be taken. 
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">5.</td>
                                                                <td>Lender will have the right to examine at all times the Borrower’s books of accounts and to have the Borrower’s factory(s)/branches inspected from time to time by officer(s) of the Lender and/or qualified auditors including stock audit and/or technical experts and/or management consultants of Lender’s choice and/or we can also get the stock audit conducted by other banker. The cost of such inspections will be borne by the Borrower.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">6.</td>
                                                                <td>The Borrower should not pay any consideration by way of commission, brokerage, fees or in any other form to guarantors directly or indirectly.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">7.</td>
                                                                <td>The Borrower and Guarantor(s) shall be deemed to have given their express consent to Lender to disclose the information and data furnished by them to Lender and also those regarding the credit facility/ies enjoyed by the Borrower, conduct of accounts and guarantee obligations undertaken by guarantor to the Credit Information Bureau (India) Ltd. (“CIBIL”), or RBI or any other agencies specified by RBI who are authorized to seek and publish information.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">8.</td>
                                                                <td>The Borrower will keep the Lender advised of any circumstances adversely affecting their financial position including any action taken by any creditor, Government authority against them.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">9.</td>
                                                                <td>In order to remove any ambiguity, it is clarified that the intervals are intended to be continuous and accordingly, the basis for classification of SMA/NPA categories shall be considered as follows:
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%"></td>
                                                                <td valign="top" width="100%">
                                                                    <table width="100%" border="0" style="border:1px #181616 solid;">
                                                                        <tr valign="top" width="100%">
                                                                            <td valign="top" width="100%"style="
                                                                            text-align: center;
                                                                            text-decoration: underline;
                                                                        " colspan="2">
                                                                            <b>“Example of SMA/NPA”</b>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" width="1%" colspan="2">
                                                                                <table width="100%" border="1" style="
                                                                                font-weight: bold;
                                                                            ">
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
                                                                            <td valign="top" width="3%">&bull;</td>
                                                                            <td>Any amount due to the lender under any credit facility is ‘overdue’ if it is not paid on the due date fixed by the Lender. If there is any overdue in an account, the default/ non-repayment is reported with the credit bureau companies like CIBIL etc. and the CIBIL report of the customer will reflect defaults and its classification status.

                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" width="3%">&bull;</td>
                                                                            <td>Once an account is classified as NPAs then it shall be upgraded as ‘standard’ asset only if entire arrears of interest and principal are paid by the borrower.
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">10.</td>
                                                                <td>The Borrower shall procure consent every year from the auditors appointed by the borrower to comply with and give report / specific comments in respect of any query or requisition made by us as regards the audited accounts or balance sheet of the Borrower. We may provide information and documents to the Auditors in order to enable the Auditors to carry out the investigation requested for by us. In that event, we shall be entitled to make specific queries to the Auditors in the light of Statements, particulars and other information submitted by the Borrower to us for the purpose of availing finance, and the Auditors shall give specific comments on the queries made by us
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">11.</td>
                                                                <td>The sanction limits would be valid for acceptance for <input type="text" value="@if(!empty($supplyChainFormData->other_cond_11) && $supplyChainFormData->other_cond_11){!! $supplyChainFormData->other_cond_11 !!} @else 60 days @endif" name="other_cond_11" id="other_cond_11" class="row_gen_input"> from the date of the issuance of letter.
                                                                    <label id="other_cond_11-error" class="error" for="other_cond_11"></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">12.</td>
                                                                <td>Lender reserves the right to alter, amend any of the condition or withdraw the facility, at any time without assigning any reason and also without giving any notice.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">13.</td>
                                                                <td>Borrower have read and understood the terms and conditions of the Loan including the annual rate of interest and the approach for gradation of risk and rationale for charging different rates of interest to different categories of borrowers adopted by the Lender(s). The Borrower understand the Lender (s) has its own model for arriving at lending interest rates on the basis of  various (i) risks such as interest rate risk, credit  and default risk in the related business segment, (ii)based on various cost such as  average cost of borrowed funds, matching tenure cost ,market liquidity, cost of underwriting, cost of customer acquisition etc. and other factors like profile of the borrower, repayment track record of the existing customer, future potential, deviations permitted , tenure of relationship with the borrower, overall  customer yield etc. Such information is gathered based on the information provided by the borrower, credit reports, data sources and market intelligence. The Borrower accept the terms and conditions and agree that these terms and conditions may be changed by the Lender at any time, and the Borrower shall be bound by the amended terms and conditions.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">14.</td>
                                                                <td>Lender(s) reserves the right to change the rate of interest and other charges, at any time, with previous notice/intimation, and any such changes shall have prospective effect.
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" width="5%">15.</td>
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
                                                        <td width="50%" valign="top" height="40"><b>Yours Sincerely,</b>
                                                        </td>
                                                        <td valign="top" height="40" style="float: right;"><b>Accepted for and behalf of
                                                                Borrower</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="50%" valign="top" height="40"><b>For CAPSAVE FINANCE
                                                            PRIVATE
                                                            LIMITED</b></td>
                                                        <td valign="top" height="40" style="float: right;"><b>For {{ $supplyChaindata['EntityName'] }}</b>
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
                                                        <td valign="top" height="40" style="float: right;"><b>Authorized Signatory</b></td>
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
                                            <div><span style="font-size:20px; font-weight:bold;">CAPSAVE FINANCE PRIVATE LIMITED</span><br/> 
                                                Registered office: 3rd Floor, Unit No 301-302,D-Wing, Lotus Corporate Park, CTS No.185/A, Graham Firth Compound, Western Express Highway, Goregaon East, Mumbai Maharashtra, 400063<br/>
                                                Ph: +91 22 6173 7600, CIN No: U67120MH1992PTC068062
                                             </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group mb-0 mt-5 justify-content-between pull-right">
                                {{-- <button type="button" class="btn btn-default mr-2"
                                    id="preview_invoice">Preview</button> --}}
                                <input type="hidden" name="sanction_letter_id" value="{{$sanction_id ?? ''}}">
                                <input type="hidden" name="app_id" value="{{$appId ?? ''}}">
                                <input type="hidden" name="offer_id" value="{{$offerId ?? ''}}">
                                <input type="hidden" name="biz_id" value="{{$bizId ?? ''}}">
                                <input type="hidden" name="action_type_url" value="{{$actionType ?? ''}}">
                                <input type="hidden" name="ref_no" value="CFPL/{{Carbon\Carbon::now()->format('My') }}/{{request()->get('app_id')? request()->get('app_id') :''}}">
                                @if(!empty($actionType) && $actionType == 'edit')
                                <button type="submit" class="btn btn-default btn-sm mr-2" name="action_type" id="update_sanction" value="update" onclick="whichPressed=this.value" >Update</button>
                                <button type="submit" class="btn btn-primary btn-sm mr-2" name="action_type" id="final_sanction" value="final_submit" onclick="whichPressed=this.value" >Generate Sanction Letter</button>
                                @else
                                <button type="submit" class="btn btn-default btn-sm mr-2" name="action_type" id="update_sanction" value="update_create" onclick="whichPressed=this.value" >Save</button>
                                <button type="submit" class="btn btn-primary btn-sm mr-2" name="action_type" id="final_sanction" value="final_submit" onclick="whichPressed=this.value" >Generate Sanction Letter</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
{!! Helpers::makeIframePopup('previewSanctionLetter', 'Preview/Send Mail Sanction Letter', 'modal-lg') !!}
{!! Helpers::makeIframePopup('previewSupplyChainSanctionLetter', 'Send Mail Supply Chain Letter', 'modal-lg') !!}
{!! Helpers::makeIframePopup('uploadSanctionLetter', 'Upload Sanction Letter', 'modal-md') !!}
@endsection
@section('jscript')
<script>
    var messages = {
        get_applications: "{{ URL::route('ajax_app_list') }}"
        , data_not_found: "{{ trans('error_messages.data_not_found') }}"
        , token: "{{ csrf_token() }}",

    };
    var ckeditorOptions =  {
        toolbarGroups : [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                // { name: 'forms', groups: [ 'forms' ] },
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                { name: 'links', groups: [ 'links' ] },
                { name: 'insert', groups: [ 'insert' ] },
                { name: 'styles', groups: [ 'styles' ] },
                { name: 'colors', groups: [ 'colors' ] },
                { name: 'tools', groups: [ 'tools' ] },
                { name: 'others', groups: [ 'others' ] },
                { name: 'about', groups: [ 'about' ] }
            ],
            removePlugins: 'link,image,flash,smiley,pagebreak,iframe'
};
   CKEDITOR.replace('monitoring_covenants_select_text',ckeditorOptions);
    
    $(document).ready(function() {
        setReviewDateByDefault();

        $('#payment_type').on('change', function() {
            $('#payment_type_comment').val('');
            if ($(this).val() == '5') {
                $('#payment_type_comment').removeClass('hide');
            } else {
                $('#payment_type_comment').addClass('hide');
            }
        })

        $('.r_o_i').each(function () {
            CKEDITOR.replace($(this).prop('id'),ckeditorOptions);
        });
        $('.penal_interest').each(function () {
            CKEDITOR.replace($(this).prop('id'),ckeditorOptions);
        });
        $('.payment_mechanism').each(function () {
            CKEDITOR.replace($(this).prop('id'),ckeditorOptions);
        });
        $('.moratorium').each(function () {
            CKEDITOR.replace($(this).prop('id'),ckeditorOptions);
        });
        $('.transaction_process').each(function () {
            CKEDITOR.replace($(this).prop('id'),ckeditorOptions);
        });
        $('#monitoring_covenants_select').on('change', function() {
            $('#cke_monitoring_covenants_select_text').hide();
            if ($(this).val() == 'Applicable') {
                $('#monitoring_covenants_select_text,#cke_monitoring_covenants_select_text').removeClass('hide');
                $('#monitoring_covenants_select_text-error').remove();
                $('#cke_monitoring_covenants_select_text').show();
            } else {
                CKEDITOR.instances['monitoring_covenants_select_text'].setData('');
                $('#monitoring_covenants_select_text').val('');
                $('#monitoring_covenants_select_text,#cke_monitoring_covenants_select_text').addClass('hide');
                $('#monitoring_covenants_select_text-error').remove();
                $('#cke_monitoring_covenants_select_text').hide();
            }
        });

        $("input[name='sanction_validity_date']").datetimepicker({
            format: 'dd/mm/yyyy'
            , autoclose: true
            , minView: 2
            , startDate: '-0m'
        , }).on('changeDate', function(e) {
            $("input[name='sanction_expire_date']").val(ChangeDateFormat(e.date, 'dmy', '/', 30));

        });

        $("input[name='sanction_expire_date']").datetimepicker({
            format: 'dd/mm/yyyy'
            , autoclose: true
            , minView: 2
            , startDate: '+1m'
        });

        $('#sanction_applicable').on('change', function() {
            if ($(this).val() == 'A') {
                $('#sanction_validity_for_first_disbursement,#sanction_validity_for_first_disbursement_div').removeClass('hide');
                $('#sanction_validity_for_first_disbursement-error').remove();
            } else {
                $('#sanction_validity_for_first_disbursement').val('');
                $('#sanction_validity_for_first_disbursement,#sanction_validity_for_first_disbursement_div').addClass('hide');
                $('#sanction_validity_for_first_disbursement-error').remove();
            }
        });
        @if(!empty($actionType) && $actionType == 'add')
            $('#sanction_applicable').val('NA').trigger('change');
            $('#monitoring_covenants_select').val('Not applicable').trigger('change');
            CKEDITOR.instances['monitoring_covenants_select_text'].setData('');
            $('#monitoring_covenants_select_text').val('');
        @endif

        @if(!empty($actionType) && $actionType == 'edit')
            if($('#monitoring_covenants_select').val() == 'Not applicable'){
                $('#monitoring_covenants_select_text,#cke_monitoring_covenants_select_text').addClass('hide');
                $('#monitoring_covenants_select_text-error').remove();
                $('#cke_monitoring_covenants_select_text').hide();
            }
        @endif
    });

    function setReviewDateByDefault() {
        var currentMinDate = new Date();
        currentMinDate.setDate(currentMinDate.getDate() + 7);
        var minDate = currentMinDate.getFullYear()+'-'+('0'+(currentMinDate.getMonth()+1)).slice(-2)+'-'+('0'+(currentMinDate.getDate())).slice(-2);
        $('#review_date').attr('min', minDate);

        var currentMaxDate = new Date();
        currentMaxDate.setDate(currentMaxDate.getDate() - 1);
        currentMaxDate.setFullYear(currentMaxDate.getFullYear() + 1);
        var maxDate = currentMaxDate.getFullYear()+'-'+('0'+(currentMaxDate.getMonth()+1)).slice(-2)+'-'+('0'+(currentMaxDate.getDate())).slice(-2);
        $('#review_date').attr('max', maxDate);
    }

    function ChangeDateFormat(dateObj, out_format = 'ymd', out_separator = '/', dateAddMinus = 0) {
        dateObj.setDate(dateObj.getDate() + dateAddMinus);
        var twoDigitMonth = ((dateObj.getMonth().length + 1) === 1) ? (dateObj.getMonth() + 1) : '0' + (dateObj
            .getMonth() + 1);
        var twoDigitDate = dateObj.getDate() + "";
        if (twoDigitDate.length == 1) twoDigitDate = "0" + twoDigitDate;
        var Digityear = dateObj.getFullYear();
        switch (out_format) {
            case 'myd':
                outdate = twoDigitMonth + out_separator + Digityear + out_separator + twoDigitDate;
                break;
            case 'ydm':
                outdate = Digityear + out_separator + twoDigitDate + out_separator + twoDigitMonth;
                break;
            case 'dmy':
                outdate = twoDigitDate + out_separator + twoDigitMonth + out_separator + Digityear;
                break;
            case 'dym':
                outdate = twoDigitDate + out_separator + Digityear + out_separator + twoDigitMonth;
                break;
            case 'mdy':
                outdate = twoDigitMonth + out_separator + twoDigitDate + out_separator + Digityear;
                break;
            default:
                outdate = Digityear + out_separator + twoDigitMonth + out_separator + twoDigitDate;
                break;
        }
        return outdate;
    }

    $(document).on('click', '.clone_defaultevent', function() {
        // covenants_clone_tr_html =  $('.covenants_clone_tr').html();
        covenants_clone_tr_html =
            '<td valign="top" width="1%">&bull;</td><td><input type="text" name="defaultEvent[]" value="" style=" min-height:30px;padding:0 5px; min-width:100%;" id="row_defEvent_'+counter+'" class="row_defevent_input" required></td>';
        $('#defaultEvent').append("<tr>" + covenants_clone_tr_html + "</tr>");
        $("#new_sanction_letter_form .row_defevent_input").each(function () {
            $(this).rules('add', {
                required: true
            });
        }); 
    })
    $(document).on('click', '.remove_defaultevent', function() {
        totalrows = $('#defaultEvent tbody').children().length - 1;
        if (totalrows > 1) {
            $('#defaultEvent tbody tr:last-child').remove();
        }
    })
    var counter = 7;
    $(document).on('click', '.clone_general_pre_disbursement_conditions', function() {
        // covenants_clone_tr_html =  $('.covenants_clone_tr').html();
        counter=$('#general_pre_disbursement_conditions > tbody > tr.row_gen').length+7;
        covenants_clone_tr_html =
            '<td valign="top" width="1%"><b>'+counter+'.</b></td><td><input type="text" name="general_pre_disbursement_condition[]" value="" style=" min-height:30px;padding:0 5px; min-width:100%;" id="row_gen_'+counter+'" class="row_gen_input" required></td>';
        $('#general_pre_disbursement_conditions').append("<tr class='row_gen'>" + covenants_clone_tr_html + "</tr>");
        $("#new_sanction_letter_form .row_gen_input").each(function () {
            $(this).rules('add', {
                required: true
            });
        });  
        counter++;
    })
    $(document).on('click', '.remove_general_pre_disbursement_conditions', function() {
        totalrows = $('#general_pre_disbursement_conditions tbody').children().length - 1;
        if (totalrows > 1) {
            console.log('remove');
            $('#general_pre_disbursement_conditions > tbody > tr.row_gen:last-child').remove();
        }
    })

    $(document).ready(function() {
        jQuery.validator.addMethod("alphanumeric", function(value, element) {
            return this.optional(element) || /^[\w\s.]+$/i.test(value);
        }, "Letters, numbers, and underscores only please");

        jQuery.validator.addMethod("ratio", function(value, element) {
            return this.optional(element) || /^[0-9:]+$/i.test(value);
        }, "Numbers and colon only please");

        $('#new_sanction_letter_form').validate({
            ignore : ':hidden:not(.textarea)',
            rules: {
                "review_date": {
                    required : true,
                }, "annexure_general_terms_and_condition": {
                    required : true,
                }
                , "operational_person": {
                    required : true,
                }
                // , "defaultEvent[]": {
                //     required : true,
                // }
                , "monitoring_covenants_select_text": {
                    required : true,
                    // alphanumeric: true
                },
                "sanction_validity_for_first_disbursement": {
                    required : true,
                    min:1,
                    max:365
                    // alphanumeric: true
                }
                // , "general_pre_disbursement_condition[]": {
                //     required : true,
                // }
                ,
                'margin_input': {
                    required: true
                },
                'other_cond_11': {
                    required: true
                }
            }
        });
         $('#new_sanction_letter_form .r_o_i').each(function() {
            $(this).rules('add', {
            required: function(textarea){
                //CKEDITOR.instances[textarea.id].focus();
                return true;
            },
            messages: {
            required: "This field is required.",

                    }
                })
            });

            $('#new_sanction_letter_form .penal_interest').each(function() {
            $(this).rules('add', {
                required: function(textarea){
                //CKEDITOR.instances[textarea.id].focus();
                return true;
            },
            messages: {
            required: "This field is required.",

                    }
                })
            });

            $('#new_sanction_letter_form .payment_mechanism').each(function() {
            $(this).rules('add', {
                required: function(textarea){
                //CKEDITOR.instances[textarea.id].focus();
                return true;
            },
            messages: {
            required: "This field is required.",

                    }
                })
            });
        // $("input.facility_tenor").each(function(){
        //     $(this).rules("add", {
        //         required: true,
        //         messages: {
        //             required: "Specify the reference name"
        //         }
        //     } );            
        // });
    });
    let whichPressed;
    function submitAlert()
    {  
        $("#new_sanction_letter_form .row_defevent_input").each(function () {
            $(this).rules('add', {
                required: true
            });
        }); 

        $("#new_sanction_letter_form .row_gen_input").each(function () {
            $(this).rules('add', {
                required: true
            });
        });

        $("#new_sanction_letter_form .r_o_i").each(function () {
            var ID = $(this).attr('id');
            CKEDITOR.instances[ID].updateElement();
        }); 

        $('#new_sanction_letter_form .penal_interest').each(function() {
            var ID = $(this).attr('id');
            CKEDITOR.instances[ID].updateElement();
        }); 
        $('#new_sanction_letter_form .payment_mechanism').each(function() {
            var ID = $(this).attr('id');
            CKEDITOR.instances[ID].updateElement();
        });  

        if(whichPressed=="final_submit")
        {
            return confirm('Are you sure you want to generate the sanction letter?');
        }
    }

</script>
@endsection
