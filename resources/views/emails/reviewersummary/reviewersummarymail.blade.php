@extends('layouts.email')
@section('email_content')

<table width="700" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:14px; font-family:Arial; ">
    <tr>
        <td align="left">
            <h2 style="font-size:18px; margin:0px 0px 10px;">Cover Note</h2></td>
    </tr>
    <tr>
        <td align="left">{{isset($reviewerSummaryData->cover_note) ? $reviewerSummaryData->cover_note : 'dddddddddddddddddddddddd'}}</td>
    </tr>
    <tr>
        <td align="left">
            <h3 style="font-size:16px; margin:20px 0px 10px;">Deal Structure:</h3></td>
    </tr>

    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
        <th width="50%" style="background:#8a8989;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Criteria</th>
                    <th width="50%" style="background:#8a8989;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Particulars</th>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Facility Type</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">Lease Loan</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Limit (₹ In Mn)</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->limit_amt) ? '₹ '.$limitOfferData->limit_amt : 'dddddddddddddddddddddddd'}}</td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Tenor (Months)</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->tenor) ? $limitOfferData->tenor : 'dddddddddddddddddddddddd'}}</td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Equipment Type</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->equipment_type_id) ? $limitOfferData->equipment_type_id : 'dddddddddddddddddddddddd'}}</td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Security Deposit</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->security_deposit) ? $limitOfferData->security_deposit : 'dddddddddddddddddddddddd'}}</td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Rental Frequency</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->rental_frequency) ? config('common.rental_frequency.'.$limitOfferData->rental_frequency) : 'dddddddddddddddddddddddd'}}</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">PTPQ</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                    {{isset($limitOfferData->ptpq_from) ? 'From Period '.$limitOfferData->ptpq_from : ''}}
                    {{isset($limitOfferData->ptpq_to) ? 'To Period '.$limitOfferData->ptpq_to : ''}}
                    {{isset($limitOfferData->ptpq_rate) ? 'Rate '.$limitOfferData->ptpq_rate : ''}}
                    </td>
                </tr>
                <tr>
                    <td  valign="top" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">XIRR</td>
                    <td  valign="top" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        Ruby Sheet : {{isset($limitOfferData->ruby_sheet_xirr) ? $limitOfferData->ruby_sheet_xirr.'%' : ''}}
                        <br/>Cash Flow : {{isset($limitOfferData->cash_flow_xirr) ? $limitOfferData->cash_flow_xirr.'%' : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Additional Security</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                    @php 
                    @$addSecArr = []      
                    @endphp                        
                    @if(isset($limitOfferData->addl_security))
                    @php 
                        $addSecArr = explode(',',$limitOfferData->addl_security)
                    @endphp                                     
                    @endif   
                    @if(count($addSecArr)>0)   
                    @foreach ($addSecArr as $k => $v)
                        {{ config('common.addl_security.'.$v).", " }}
                        @if($v==4)
                            {{isset($limitOfferData->comment) ? " Comment- ".$limitOfferData->comment : ''}}
                        @endif
                    @endforeach 
                    @endif         
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td align="left">
            <h3 style="font-size:16px; margin:20px 0px 10px;">Pre/ Post Disbursement Conditions:</h3></td>
    </tr>
    <tr>
        <td align="left">

            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <th width="50%" style="background:#8a8989;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Condition</th>
                    <th style="background:#8a8989;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Timeline</th>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{isset($reviewerSummaryData->cond_nach) ? $reviewerSummaryData->cond_nach : 'dddddddddddddddddddddddd'}}</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($reviewerSummaryData->time_nach) ? $reviewerSummaryData->time_nach : 'dddddddddddddddddddddddd'}}</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_insp_asset) ? $reviewerSummaryData->cond_insp_asset : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_insp_asset) ? $reviewerSummaryData->time_insp_asset : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_insu_pol_cfpl) ? $reviewerSummaryData->cond_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_insu_pol_cfpl) ? $reviewerSummaryData->time_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_personal_guarantee) ? $reviewerSummaryData->cond_personal_guarantee : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_personal_guarantee) ? $reviewerSummaryData->cond_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_pbdit) ? $reviewerSummaryData->cond_pbdit : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_pbdit) ? $reviewerSummaryData->time_pbdit : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_dscr) ? $reviewerSummaryData->cond_dscr : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_dscr) ? $reviewerSummaryData->time_dscr : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_lender_cfpl) ? $reviewerSummaryData->cond_lender_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_lender_cfpl) ? $reviewerSummaryData->time_lender_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_ebidta) ? $reviewerSummaryData->cond_ebidta : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_ebidta) ? $reviewerSummaryData->time_ebidta : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_credit_rating) ? $reviewerSummaryData->cond_credit_rating : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_credit_rating) ? $reviewerSummaryData->time_credit_rating : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td align="left">
            <h2 style="font-size:18px; margin:20px 0px 10px;">Risk Comments:</h2></td>
    </tr>
    <tr>
        <td align="left">
            <h3 style="font-size:16px; margin:0px 0px 10px;">Deal Positives:</h3></td>
    </tr>
    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <td width="50%" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_pos_track_rec) ? $reviewerSummaryData->cond_pos_track_rec : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_track_rec) ? $reviewerSummaryData->cmnt_pos_track_rec : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_pos_credit_rating) ? $reviewerSummaryData->cond_pos_credit_rating : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_credit_rating) ? $reviewerSummaryData->cmnt_pos_credit_rating : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_pos_fin_matric) ? $reviewerSummaryData->cond_pos_fin_matric : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_fin_matric) ? $reviewerSummaryData->cmnt_pos_fin_matric : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_pos_establish_client) ? $reviewerSummaryData->cond_pos_establish_client : 'dddddddddddddddddddddddd'}} </strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_establish_client) ? $reviewerSummaryData->cmnt_pos_establish_client : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="left">
            <h3 style="font-size:16px; margin:20px 0px 10px;">Deal Negatives:</h3></td>
    </tr>
    <tr>
        <td>
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <td width="50%" align="left" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_neg_competition) ? $reviewerSummaryData->cond_neg_competition : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_competition) ? $reviewerSummaryData->cmnt_neg_competition : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_neg_forex_risk) ? $reviewerSummaryData->cond_neg_forex_risk : 'dddddddddddddddddddddddd'}} </strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_forex_risk) ? $reviewerSummaryData->cmnt_neg_forex_risk : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_neg_pbdit) ? $reviewerSummaryData->cond_neg_pbdit : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_pbdit) ? $reviewerSummaryData->cmnt_neg_pbdit : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="left">
            <h3 style="font-size:16px; margin:20px 0px 10px;">Recommendation:</h3></td>
    </tr>
    <tr>
        <td align="left">{{isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : 'dddddddddddddddddddddddd'}}</td>
    </tr>
</table>
@endsection


