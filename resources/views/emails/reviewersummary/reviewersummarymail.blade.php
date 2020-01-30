@extends('layouts.email')
@section('email_content')

<table width="700" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:14px;margin-top:10px; font-family:Arial; ">
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h2 style="font-size:18px; margin:0px 0px 0;">Cover Note</h2></td>
    </tr>
    <tr>
        <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
            <tr>
                <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left">{!!isset($reviewerSummaryData->cover_note) ? nl2br($reviewerSummaryData->cover_note) : ''!!}</td>
            </tr>
        </table>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:18px; margin:0px 0px 0;">Deal Structure:</h3></td>
    </tr>

    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
        <th width="50%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Criteria</th>
                    <th width="50%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Particulars</th>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Facility Type</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">Lease Loan</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Limit (₹ In Mn)</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->limit_amt) ? '₹ '.$limitOfferData->limit_amt : ''}}</td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Tenor (Months)</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->tenor) ? $limitOfferData->tenor : ''}}</td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Equipment Type</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                    @php 
                    @$equipType = ''     
                    @endphp 
                    @if(isset($limitOfferData->equipment_type_id) && $limitOfferData->equipment_type_id)
                    @php
                        $equipType = Helpers::getEquipmentTypeById($limitOfferData->equipment_type_id)->equipment_name  
                    @endphp
                    @endif
                    {{@$equipType}}</td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Security Deposit</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->security_deposit) ? $limitOfferData->security_deposit : ''}}</td>
                </tr>

                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">Rental Frequency</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($limitOfferData->rental_frequency) ? config('common.rental_frequency.'.$limitOfferData->rental_frequency) : ''}}</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">PTPQ</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                    @if(isset($offerPTPQ) && $offerPTPQ && $offerPTPQ!='')   
                        @foreach ($offerPTPQ as $ok => $ov)
                            {{isset($ov->ptpq_from) ? 'From Period '.$ov->ptpq_from : ''}}
                            {{isset($ov->ptpq_to) ? 'To Period '.$ov->ptpq_to : ''}}
                            {{isset($ov->ptpq_rate) ? 'Rate '.$ov->ptpq_rate : ''}}
                            <br/>
                        @endforeach 
                    @endif
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
                    @if(isset($addSecArr) && count($addSecArr)>0)  
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
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:18px; margin:0px 0px 0;">Pre Disbursement Conditions:</h3></td>
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
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">{{isset($reviewerSummaryData->cond_nach) ? $reviewerSummaryData->cond_nach : ''}}</td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">{{isset($reviewerSummaryData->time_nach) ? $reviewerSummaryData->time_nach : ''}}</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_insp_asset) ? $reviewerSummaryData->cond_insp_asset : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_insp_asset) ? $reviewerSummaryData->time_insp_asset : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_insu_pol_cfpl) ? $reviewerSummaryData->cond_insu_pol_cfpl : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_insu_pol_cfpl) ? $reviewerSummaryData->time_insu_pol_cfpl : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_personal_guarantee) ? $reviewerSummaryData->cond_personal_guarantee : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_personal_guarantee) ? $reviewerSummaryData->cond_insu_pol_cfpl : ''}}
                    </td>
                </tr>          
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:18px; margin:0px 0px 0;">Post Disbursement Conditions:</h3></td>
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
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_pbdit) ? $reviewerSummaryData->cond_pbdit : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_pbdit) ? $reviewerSummaryData->time_pbdit : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_dscr) ? $reviewerSummaryData->cond_dscr : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_dscr) ? $reviewerSummaryData->time_dscr : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_lender_cfpl) ? $reviewerSummaryData->cond_lender_cfpl : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_lender_cfpl) ? $reviewerSummaryData->time_lender_cfpl : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_ebidta) ? $reviewerSummaryData->cond_ebidta : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_ebidta) ? $reviewerSummaryData->time_ebidta : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cond_credit_rating) ? $reviewerSummaryData->cond_credit_rating : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->time_credit_rating) ? $reviewerSummaryData->time_credit_rating : ''}}
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:18px; margin:0px 0px 0;">Approval criteria for IC:</h3></td>
    </tr>
    <tr>
        <td align="left">

            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <th width="25%" style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;">Parameter</th>
                    <th style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Criteria</th>
                    <th style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Deviation</th>
                    <th style="background:#b7b7b7;color:#ffffff;text-align: left;padding: 10px;font-size: 14px;
        border-bottom: #ccc solid 1px;">Remarks</th>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Nominal RV Position
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 5% over the values mentionedin the matrix
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_rv_position) ? $reviewerSummaryData->criteria_rv_position : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_rv_position_remark) ? $reviewerSummaryData->criteria_rv_position_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Asset concentration as % of the total portfolio
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        - IT assets and telecommunications max 70%<br/>
                        - Plant and machinery max 50%<br/>
                        - Furniture and fit outs max 30%<br/>
                        - Any other asset type max 20%
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_asset_portfolio) ? $reviewerSummaryData->criteria_asset_portfolio : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_asset_portfolio_remark) ? $reviewerSummaryData->criteria_asset_portfolio_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Single Borrower Limit
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 15% of Net owned funds (Rs150 Mn)
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_sing_borr_limit) ? $reviewerSummaryData->criteria_sing_borr_limit : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_sing_borr_remark) ? $reviewerSummaryData->criteria_sing_borr_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Borrower Group Limit 
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 25% of Net owned funds (Rs250 Mn)
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_borr_grp_limit) ? $reviewerSummaryData->criteria_borr_grp_limit : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_borr_grp_remark) ? $reviewerSummaryData->criteria_borr_grp_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Exposure on customers below investment grade <br/>
                                    (BBB -CRISIL/CARE/ICRA/India Ratings) and unrated customers 
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 50% of CFPL portfolio
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_invest_grade) ? $reviewerSummaryData->criteria_invest_grade : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_invest_grade_remark) ? $reviewerSummaryData->criteria_invest_grade_remark : ''}}
                    </td>
                </tr> 
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Exposure to a particular industry/sector as a percentage of total portfolio 
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        Max 50% of the total CFPL portfolio
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_particular_portfolio) ? $reviewerSummaryData->criteria_particular_portfolio : ''}}
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->criteria_particular_portfolio_remark) ? $reviewerSummaryData->criteria_particular_portfolio_remark : ''}}
                    </td>
                </tr> 
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h2 style="font-size:18px; margin:0px 0px 10px;">Risk Comments:</h2></td>
    </tr>
    <tr>
        <td align="left" style="background:#b7b7b7; color:#fff;padding:10px;">
            <h3 style="font-size:16px; margin:0px 0px 10px;">Deal Positives:</h3></td>
    </tr>
    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <td width="50%" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_pos_track_rec) ? $reviewerSummaryData->cond_pos_track_rec : ''}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_track_rec) ? $reviewerSummaryData->cmnt_pos_track_rec : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_pos_credit_rating) ? $reviewerSummaryData->cond_pos_credit_rating : ''}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_credit_rating) ? $reviewerSummaryData->cmnt_pos_credit_rating : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_pos_fin_matric) ? $reviewerSummaryData->cond_pos_fin_matric : ''}}</strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_fin_matric) ? $reviewerSummaryData->cmnt_pos_fin_matric : ''}}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_pos_establish_client) ? $reviewerSummaryData->cond_pos_establish_client : ''}} </strong>
                    </td>
                    <td style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_pos_establish_client) ? $reviewerSummaryData->cmnt_pos_establish_client : ''}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#b7b7b7; color:#fff;padding:10px;">
            <h3 style="font-size:18px; margin:0px 0px 0;">Deal Negatives:</h3></td>
    </tr>
    <tr>
        <td>
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <td width="50%" align="left" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong>{{isset($reviewerSummaryData->cond_neg_competition) ? $reviewerSummaryData->cond_neg_competition : ''}}</strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_competition) ? $reviewerSummaryData->cmnt_neg_competition : ''}}
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_neg_forex_risk) ? $reviewerSummaryData->cond_neg_forex_risk : ''}} </strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_forex_risk) ? $reviewerSummaryData->cmnt_neg_forex_risk : ''}}
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;">
                        <strong> {{isset($reviewerSummaryData->cond_neg_pbdit) ? $reviewerSummaryData->cond_neg_pbdit : ''}}</strong>
                    </td>
                    <td align="left" style="padding:8px 10px;font-size: 14px;border-bottom: #ccc solid 1px;">
                        {{isset($reviewerSummaryData->cmnt_neg_pbdit) ? $reviewerSummaryData->cmnt_neg_pbdit : ''}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" style="background:#8a8989; color:#fff;padding:10px;">
            <h3 style="font-size:18px; margin:0px 0px 0;">Recommendation:</h3></td>
    </tr>
    <tr>
        <td align="left">{{isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : ''}}</td>
    </tr>
</table>
@endsection


