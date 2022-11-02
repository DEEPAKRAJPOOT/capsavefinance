@extends('layouts.email')
@section('email_content')
<table width="700" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:14px;margin-top:10px; font-family:Arial; ">
    <tr>
        <td>
        <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
            <tr>
                <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left">
            <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; margin-top: 0; text-align: left;">Hi,</p> 
            <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;"><strong>Application ID:</strong> {{ $dispAppId }} is waiting for your approval</p> 
            <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;"><a href="{{ $url }}">Click</a> here to login and approve </p>
            <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Note: Go to Manage Application -> My Application and click on application ID.</p>
                </td>
            </tr>
        </table>    
        </td>
    </tr>
    <tr>
        <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
            <tr>
                <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left">{!!isset($reviewerSummaryData->cover_note) ? ($reviewerSummaryData->cover_note) : ''!!}</td>
            </tr>
        </table>
    </tr>
</table>
<table width="700" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:14px;margin-top:10px; font-family:Arial; ">
    

    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 0;">Leasing Deal Structure</h3></td>
    </tr>

    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
            @forelse($leaseOfferData as $key=>$leaseOffer)
            @if ($leaseOffer->status != 2)
                <tr>
                    <th width="20%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Criteria</th>
                    <th width="30%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px; border-bottom: #ccc solid 1px;">Particulars</th>
                    <th width="20%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Criteria</th>
                    <th width="30%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Particulars</th>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Facility Type</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->facility_type_id) ?  $facilityTypeList[$leaseOffer->facility_type_id]  : ''}}</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Equipment Type</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->equipment_type_id) ?  (\Helpers::getEquipmentTypeById($leaseOffer->equipment_type_id)['equipment_name']) : ''}}</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Limit Of The Equipment</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{!! isset($leaseOffer->prgm_limit_amt) ? ' INR '.number_format($leaseOffer->prgm_limit_amt)  : '0' !!} </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"> Tenor (Months)</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->tenor) ? $leaseOffer->tenor : ''}}</td>
                </tr>
                @if($leaseOffer->facility_type_id != 3)
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Security Deposit</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{(($leaseOffer->security_deposit_type == 1)?'INR ':'').$leaseOffer->security_deposit.(($leaseOffer->security_deposit_type == 2)?' %':'')}} of {{config('common.deposit_type')[$leaseOffer->security_deposit_of]}}
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Pricing Per Thousand</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">@php 
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
                                             @endphp </td>
                </tr>
                @endif
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Rental Frequency</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->rental_frequency) ? $arrStaticData['rentalFrequency'][$leaseOffer->rental_frequency] : ''}}   {{isset($leaseOffer->rental_frequency_type) ? 'in '.$arrStaticData['rentalFrequencyType'][$leaseOffer->rental_frequency_type] : ''}}</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($leaseOffer->facility_type_id == 3)? 'Rental Discounting' : 'XIRR'}}</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">@if($leaseOffer->facility_type_id == 3)
                         {{$leaseOffer->discounting}}%
                      @else
                         <b>Ruby Sheet</b>: {{$leaseOffer->ruby_sheet_xirr}}%<br/><b>Cash Flow</b>: {{$leaseOffer->cash_flow_xirr}}%
                      @endif</td>

                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Processing Fee</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->processing_fee) ? $leaseOffer->processing_fee."%" : ''}}</td>
                
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Additional Security</td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                    @php
                    $add_sec_arr = '';                    
                    if(isset($leaseOffer->addl_security)){
                        $addl_sec_arr = explode(',', $leaseOffer->addl_security);
                        foreach($addl_sec_arr as $k=>$v){
                            $add_sec_arr .= config('common.addl_security')[$v].', ';
                        }
                    }
                    if(isset($leaseOffer->comment)) {
                        $add_sec_arr .=  ' <b>Comment</b>:  '.$leaseOffer->comment; 
                    }                       
                    $add_sec_arr = trim($add_sec_arr, ', ');
                    @endphp
                    {!! $add_sec_arr !!}   
                    </td>
                </tr>
                @endif
                @empty
                    <tr class="">
                        <td colspan="4" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">No Offer Found</td>
                    </tr>
            @endforelse
            </table>
        </td>
    </tr>



    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 0;">Supply Chain Deal Structure</h3></td>
    </tr>

    <tr>
        <td align="left">
           @forelse($supplyOfferData as $key=>$supplyOffer)
           @if ($supplyOffer->status != 2)
                <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                    <thead>
                         <tr role="row">
                              <th width="20%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px; border-bottom: #ccc solid 1px;">Criteria</th>
                              <th  width="30%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px; border-bottom: #ccc solid 1px;">Particulars</th>
                              <th  width="20%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px; border-bottom: #ccc solid 1px;">Criteria</th>
                              <th width="30%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px; border-bottom: #ccc solid 1px;">Particulars</th>
                           </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Sub Limit: </b> </td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$supplyOffer->prgm_limit_amt}}</td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>{{ isset($fee['2']) ? $fee['2']['chrg_name'] : ''}}{{ isset($fee['2']) ? ($fee['2']['chrg_type'] == 2 ? '(%)' : '(₹)') : ''}}: </b></td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{ isset($fee['2']) ? $fee['2']['chrg_value'] : ''}}</td>
                          </tr>
                          
                          <tr>
                             <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Interest Rate(%): </b></td>
                             <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$supplyOffer->interest_rate}} %</td>
                             <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Tenor (Days): </b></td>
                             <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$supplyOffer->tenor}}</td>
                          </tr>
                          <tr>
                             <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Tenor for old invoice (Days): </b></td>
                             <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$supplyOffer->tenor_old_invoice}}</td>
                             <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Margin (%): </b></td>
                             <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$supplyOffer->margin}} %</td>
                          </tr>
                          <tr>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Overdue Interest Rate (%): </b></td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$supplyOffer->overdue_interest_rate}} %</td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Adhoc Interest Rate (%): </b></td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$supplyOffer->adhoc_interest_rate}} %</td>
                          </tr>
                          <tr>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Grace Period (Days): </b></td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$supplyOffer->grace_period}}</td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>{{ isset($fee['1']) ? $fee['1']['chrg_name'] : ''}}{{ isset($fee['1']) ? ($fee['1']['chrg_type'] == 2 ? '(%)' : '(₹)') : ''}}: </b></td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{ isset($fee['1']) ? $fee['1']['chrg_value'] : ''}}</td>
                          </tr>
                          <tr>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Comment: </b></td>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" colspan="3">{{$supplyOffer->comment}}</td>
                          </tr>
                          @if($supplyOffer->offerPs->count() > 0)
                          <tr>
                              <td colspan="4" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" >
                                  <table width="100%" cellpadding="0" cellspacing="0">
                                      <tr style="background-color: #d2d4de;">
                                          <td width="10%" rowspan="3"  style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px; background-color: #fff;"><b>Primary Security</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Security</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Type of Security</b></td>
                                          <td width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Status of Security</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Time for security</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Description of Security</b></td>
                                      </tr>
                                      @foreach($supplyOffer->offerPs as $key=>$ops)
                                      <tr>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ops->ps_security_id != null)? config('common.ps_security_id')[$ops->ps_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ops->ps_type_of_security_id != null)? config('common.ps_type_of_security_id')[$ops->ps_type_of_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ops->ps_status_of_security_id != null)? config('common.ps_status_of_security_id')[$ops->ps_status_of_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ops->ps_time_for_perfecting_security_id != null)? config('common.ps_time_for_perfecting_security_id')[$ops->ps_time_for_perfecting_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$ops->ps_desc_of_security}}</td>
                                      </tr>
                                      @endforeach
                                  </table>
                              </td>
                          </tr>
                          @endif

                          @if($supplyOffer->offerCs->count() > 0)
                          <tr>
                              <td colspan="4" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" >
                                  <table width="100%" cellpadding="0" cellspacing="0">
                                      <tr style="background-color: #d2d4de;" >
                                          <td width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px; background-color: #fff;" rowspan="3" ><b>Collateral Security</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Security</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Type of Security</b></td>
                                          <td width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Status of Security</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Time for security</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Description of Security</b></td>
                                      </tr>
                                      @foreach($supplyOffer->offerCs as $key=>$ocs)
                                      <tr>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ocs->cs_desc_security_id != null)? config('common.cs_desc_security_id')[$ocs->cs_desc_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ocs->cs_type_of_security_id != null)? config('common.cs_type_of_security_id')[$ocs->cs_type_of_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ocs->cs_status_of_security_id != null)? config('common.cs_status_of_security_id')[$ocs->cs_status_of_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ocs->cs_time_for_perfecting_security_id != null)? config('common.cs_time_for_perfecting_security_id')[$ocs->cs_time_for_perfecting_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$ocs->cs_desc_of_security}}</td>
                                      </tr>
                                      @endforeach
                                  </table>
                              </td>
                          </tr>
                          @endif

                          @if($supplyOffer->offerPg->count() > 0)
                          <tr>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" colspan="4">
                                  <table width="100%" cellpadding="0" cellspacing="0">
                                      <tr style="background-color: #d2d4de;">
                                          <td width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px; background-color: #fff;" rowspan="3" ><b>Personal Guarantee</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Guarantor</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Time for security</b></td>
                                          <td width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Residential Address</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Net worth as per ITR/CA Cert</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Comments if any</b></td>
                                      </tr>
                                      @foreach($supplyOffer->offerPg as $key=>$opg)
                                      <tr>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($opg->owner)? $opg->owner->first_name: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($opg->pg_time_for_perfecting_security_id != null)? config('common.pg_time_for_perfecting_security_id')[$opg->pg_time_for_perfecting_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$opg->pg_residential_address}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$opg->pg_net_worth}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$opg->pg_comments}}</td>
                                      </tr>
                                      @endforeach
                                  </table>
                              </td>
                          </tr>
                          @endif

                          @if($supplyOffer->offerCg->count() > 0)
                          <tr>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" colspan="4">
                                  <table width="100%" cellpadding="0" cellspacing="0">
                                      <tr style="background-color: #d2d4de;">
                                          <td  width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px; background-color: #fff;" rowspan="3" ><b>Corporate Guarantee</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Type</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Guarantor</b></td>
                                          <td width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Time for security</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Residential Address</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Comments if any</b></td>
                                      </tr>
                                      @foreach($supplyOffer->offerCg as $key=>$ocg)
                                      <tr>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ocg->cg_type_id != null)? config('common.cg_type_id')[$ocg->cg_type_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ocg->owner)? $ocg->owner->first_name: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($ocg->cg_time_for_perfecting_security_id != null)? config('common.cg_time_for_perfecting_security_id')[$ocg->cg_time_for_perfecting_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$ocg->cg_residential_address}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$ocg->cg_comments}}</td>
                                      </tr>
                                      @endforeach
                                  </table>
                              </td>
                          </tr>
                          @endif

                          @if($supplyOffer->offerEm->count() > 0)
                          <tr>
                              <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" colspan="4">
                                  <table width="100%" cellpadding="0" cellspacing="0">
                                      <tr style="background-color: #d2d4de;">
                                          <td width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px; background-color: #fff;" rowspan="3"><b>Escrow Mechanism</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Debtor</b></td>
                                          <td width="15%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Expected cash flow per month</b></td>
                                          <td width="10%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Time for security</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Mechanism</b></td>
                                          <td width="25%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;"><b>Comments if any</b></td>
                                      </tr>
                                      @foreach($supplyOffer->offerEm as $key=>$oem)
                                      <tr>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($oem->anchor)? $oem->anchor->comp_name: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$oem->em_expected_cash_flow}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($oem->em_time_for_perfecting_security_id != null)? config('common.em_time_for_perfecting_security_id')[$oem->em_time_for_perfecting_security_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($oem->em_mechanism_id != null)? config('common.em_mechanism_id')[$oem->em_mechanism_id]: 'NA'}}</td>
                                          <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$oem->em_comments}}</td>
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
                     <tr class="">
                        <td colspan="5" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">No Offer Found</td>
                    </tr>
              @endforelse 

        </td>

    </tr>    























    <tr>
        <td  align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 0;">Pre Disbursement Conditions</h3></td>
    </tr>
    <tr>
        <td align="left">

            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <th width="35%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Type of Document</th>
                    <th width="25%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
                    border-bottom: #ccc solid 1px;">Original Due Date</th>
        <th width="40%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Description</th>
                </tr>
                @if(isset($preCondArr) && count($preCondArr)>0)
                    @foreach($preCondArr as $prekey =>$preval)
                    <tr>
                        <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{ $preval['mst_security_docs']['name']??'N/A' }}</td>
                        <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{ $preval['due_date']?\Carbon\Carbon::parse($preval['due_date'])->format('d-m-Y'):'N/A' }}</td>
                        <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;white-space: normal;">{{ trim($preval['description'])??'N/A' }}</td>
                    </tr>
                    @endforeach
                @else
                     <tr>   
                            <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;" colspan="3">No Record Found</td>    
                     </tr>   
                @endif        
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 0;">Post Disbursement Conditions</h3></td>
    </tr>
    <tr>
        <td align="left">

            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <th width="35%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Type of Document</th>
                    <th width="25%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
                    border-bottom: #ccc solid 1px;">Original Due Date</th>
        <th width="40%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Description</th>
                </tr>
                @if(isset($postCondArr) && count($postCondArr)>0)
                    @foreach($postCondArr as $postkey =>$postval)
                        <tr>
                            <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                                {{ $postval['mst_security_docs']['name']??'N/A' }}
                            </td>
                            <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                                {{ $postval['due_date']?\Carbon\Carbon::parse($postval['due_date'])->format('d-m-Y'):'N/A' }}
                            </td>
                            <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;white-space: normal;">
                                {{ trim($postval['description'])??'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                @else
                     <tr>   
                            <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;" colspan="3">No Record Found</td>    
                     </tr>      
                @endif
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 0;">Approval criteria for IC</h3></td>
    </tr>
    <tr>
        <td align="left">

            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <th width="25%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Parameter</th>
                    <th style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Criteria</th>
                    <th style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Deviation</th>
                    <th style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Remarks</th>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Nominal RV Position
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 5% over the values mentionedin the matrix
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_rv_position) ? $reviewerSummaryData->criteria_rv_position : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_rv_position_remark) ? $reviewerSummaryData->criteria_rv_position_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Asset concentration as % of the total portfolio
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        - IT assets and telecommunications max 70%<br/>
                        - Plant and machinery max 50%<br/>
                        - Furniture and fit outs max 30%<br/>
                        - Any other asset type max 20%
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_asset_portfolio) ? $reviewerSummaryData->criteria_asset_portfolio : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_asset_portfolio_remark) ? $reviewerSummaryData->criteria_asset_portfolio_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Single Borrower Limit
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 15% of Net owned funds (Rs150 Mn)
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_sing_borr_limit) ? $reviewerSummaryData->criteria_sing_borr_limit : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_sing_borr_remark) ? $reviewerSummaryData->criteria_sing_borr_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Borrower Group Limit 
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 25% of Net owned funds (Rs250 Mn)
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_borr_grp_limit) ? $reviewerSummaryData->criteria_borr_grp_limit : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_borr_grp_remark) ? $reviewerSummaryData->criteria_borr_grp_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Exposure on customers below investment grade <br/>
                                    (BBB -CRISIL/CARE/ICRA/India Ratings) and unrated customers 
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 50% of CFPL portfolio
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_invest_grade) ? $reviewerSummaryData->criteria_invest_grade : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_invest_grade_remark) ? $reviewerSummaryData->criteria_invest_grade_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Exposure to a particular industry/sector as a percentage of total portfolio 
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 50% of the total CFPL portfolio
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_particular_portfolio) ? $reviewerSummaryData->criteria_particular_portfolio : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_particular_portfolio_remark) ? $reviewerSummaryData->criteria_particular_portfolio_remark : ''}}
                    </td>
                </tr> 
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h2 style="font-size:16px; margin:0px 0px 0;">Risk Comments</h2></td>
    </tr>
    <tr>
        <td align="left" style="background:#b7b7b7; color:#fff;padding:10px;">
            <h3 style="font-size:14px; margin:0px 0px 0;">Deal Positives</h3></td>
    </tr>
    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                @if(isset($positiveRiskCmntArr) && count($positiveRiskCmntArr)>0)
                    @foreach($positiveRiskCmntArr as $postkey =>$postval)
                    <tr>
                        <td width="50%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                            <strong>{{$postval['cond']}}</strong>
                        </td>
                        <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                            {{$postval['timeline']}}
                        </td>
                    </tr>
                    @endforeach
                @endif               
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#b7b7b7; color:#fff;padding:10px;">
            <h3 style="font-size:14px; margin:0px 0px 0;">Deal Negatives</h3></td>
    </tr>
    <tr>
        <td>
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                @if(isset($negativeRiskCmntArr) && count($negativeRiskCmntArr)>0)
                    @foreach($negativeRiskCmntArr as $postkey =>$postval)
                    <tr>
                        <td width="50%" align="left" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                            <strong>{{$postval['cond']}}</strong>
                        </td>
                        <td align="left" style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                            {{$postval['timeline']}}
                        </td>
                    </tr>
                    @endforeach
                @endif                
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 0;">Recommendation</h3></td>
    </tr>
    <tr>
        <td>
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left">{!!isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : ''!!}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection


