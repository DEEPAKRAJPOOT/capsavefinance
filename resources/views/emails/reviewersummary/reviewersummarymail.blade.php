@extends('layouts.email')
@section('email_content')

<table width="700" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:14px;margin-top:10px; font-family:Arial; ">
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h2 style="font-size:16px; margin:0px 0px 0;">Cover Note</h2></td>
    </tr>
    <tr>
        <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
            <tr>
                <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left">{!!isset($reviewerSummaryData->cover_note) ? ($reviewerSummaryData->cover_note) : ''!!}</td>
            </tr>
        </table>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 0;">Deal Structure</h3></td>
    </tr>

    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
            @forelse($leaseOfferData as $key=>$leaseOffer)
                <tr>
        <th width="30%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Criteria</th>
                    <th width="70%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Particulars</th>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Facility Type</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->facility_type_id) ?  $facilityTypeList[$leaseOffer->facility_type_id]  : ''}}</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Equipment Type</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->equipment_type_id) ?  (\Helpers::getEquipmentTypeById($leaseOffer->equipment_type_id)['equipment_name']) : ''}}</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Limit Of The Equipment</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">{!! isset($leaseOffer->prgm_limit_amt) ? ' INR '.number_format($leaseOffer->prgm_limit_amt)  : '0' !!} </td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Tenor (Months)</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->tenor) ? $leaseOffer->tenor : ''}}</td>
                </tr>
                @if($leaseOffer->facility_type_id != 3)
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Security Deposit</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{(($leaseOffer->security_deposit_type == 1)?'INR ':'').$leaseOffer->security_deposit.(($leaseOffer->security_deposit_type == 2)?' %':'')}} of {{config('common.deposit_type')[$leaseOffer->security_deposit_of]}}
                    </td>
                </tr>   
                @endif
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Rental Frequency</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->rental_frequency) ? $arrStaticData['rentalFrequency'][$leaseOffer->rental_frequency] : ''}}   {{isset($leaseOffer->rental_frequency_type) ? 'in '.$arrStaticData['rentalFrequencyType'][$leaseOffer->rental_frequency_type] : ''}}</td>
                </tr>
                 @if($leaseOffer->facility_type_id != 3)
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Pricing Per Thousand</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">@php 
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
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{($leaseOffer->facility_type_id == 3)? 'Rental Discounting' : 'XIRR'}}</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">@if($leaseOffer->facility_type_id == 3)
                         {{$leaseOffer->discounting}}%
                      @else
                         <b>Ruby Sheet</b>: {{$leaseOffer->ruby_sheet_xirr}}%<br/><b>Cash Flow</b>: {{$leaseOffer->cash_flow_xirr}}%
                      @endif</td>

                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Processing Fee</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">{{isset($leaseOffer->processing_fee) ? $leaseOffer->processing_fee."%" : ''}}</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Additional Security</td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
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
                @empty
                    <tr class="">
                        <td>No Offer Found</td>
                    </tr>
            @endforelse
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 0;">Pre Disbursement Conditions</h3></td>
    </tr>
    <tr>
        <td align="left">

            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <th width="50%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Condition</th>
                    <th style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Timeline</th>
                </tr>
                @if(isset($preCondArr) && count($preCondArr)>0)
                    @foreach($preCondArr as $prekey =>$preval)
                    <tr>
                        <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{$preval['cond']}}</td>
                        <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{$preval['timeline']}}</td>
                    </tr>
                    @endforeach
                @else
                     <tr>   
                            <td colspan="2">No Record Found</td>    
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
                    <th width="50%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Condition</th>
                    <th style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Timeline</th>
                </tr>
                @if(isset($postCondArr) && count($postCondArr)>0)
                    @foreach($postCondArr as $postkey =>$postval)
                        <tr>
                            <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                                {{$postval['cond']}}
                            </td>
                            <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                                {{$postval['timeline']}}
                            </td>
                        </tr>
                    @endforeach
                @else
                     <tr>   
                            <td colspan="2">No Record Found</td>    
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
                <tr>
                    <td width="50%" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_pos_track_rec) ? $reviewerSummaryData->cond_pos_track_rec : ''}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_track_rec) ? $reviewerSummaryData->cmnt_pos_track_rec : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_pos_credit_rating) ? $reviewerSummaryData->cond_pos_credit_rating : ''}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_credit_rating) ? $reviewerSummaryData->cmnt_pos_credit_rating : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_pos_fin_matric) ? $reviewerSummaryData->cond_pos_fin_matric : ''}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_fin_matric) ? $reviewerSummaryData->cmnt_pos_fin_matric : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_pos_establish_client) ? $reviewerSummaryData->cond_pos_establish_client : ''}} </strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_establish_client) ? $reviewerSummaryData->cmnt_pos_establish_client : ''}}
                    </td>
                </tr>
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
                <tr>
                    <td width="50%" align="left" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_neg_competition) ? $reviewerSummaryData->cond_neg_competition : ''}}</strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_competition) ? $reviewerSummaryData->cmnt_neg_competition : ''}}
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_neg_forex_risk) ? $reviewerSummaryData->cond_neg_forex_risk : ''}} </strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_forex_risk) ? $reviewerSummaryData->cmnt_neg_forex_risk : ''}}
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:8px 10px;font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_neg_pbdit) ? $reviewerSummaryData->cond_neg_pbdit : ''}}</strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 13px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_pbdit) ? $reviewerSummaryData->cmnt_neg_pbdit : ''}}
                    </td>
                </tr>
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


