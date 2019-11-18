<?php 
   $curr_year = date('Y');
   $year_count = 3;
   $start_year = date('Y')-$year_count+1;
?>
@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-sidebar')

@include('layouts.backend.partials.admin-subnav')

<div class="content-wrapper">
        <ul class="sub-menu-main pl-0 m-0">
            <li>
                <a href="cam.php" class="active">Overview</a>
            </li>
            <li>
                <a href="anchor-view.php">Anchor</a>
            </li>

            <li>
                <a href="promoter.php">Promoter</a>
            </li>
            <li>
                <a href="cibil.php">Credit History &amp; Hygine Check</a>
            </li>
            <li>
                <a href="banking.php">Banking</a>
            </li>
            <li>
                <a href="financial.php">Financial</a>
            </li>
            <li>
                <a href="gst-ledger.php">GST/Ledger Detail</a>
            </li>
            <li>
                <a href="limit-assessment.php">Limit Assessment</a>
            </li>
            <li>
                <a href="limit-management.php">Limit Management</a>
            </li>
        </ul>
    <div class="inner-container">
        <form method="post" action="{{ route('cam_finance_store') }}">
            @csrf
            <div class="card mt-3">
                <div class="card-body pt-3 pb-3">
                    <ul class="float-left mb-0 pl-0">
                        <li><b class="bold">Case ID : 01256</b> </li>
                        <li><b class="bold">Credit Head Status :</b> Reject</li>
                    </ul>
                    <button onclick="downloadCam(49)" class="btn btn-primary float-right btn-sm "> Download</button>
                    <ul class="float-right mr-5 mb-0">
                        <li><b class="bold">Requested Loan Amount :</b> 5Lac</li>
                        <li><b class="bold">Assigned Underwriter :</b> abc</li>
                    </ul>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <table class="table table-bordered overview-table" cellspacing="0">
                        <tbody>
                            <tr>
                                <td><b>Name of the Borrower</b></td>
                                <td colspan="4">Chandan</td>
                            </tr>
                            <tr>
                                <td width="25%"><b>Latest Audited Financial Year</b></td>
                                <td width="15%"><?php echo $curr_year ?></td>
                                <td width="20%"><b>Projections Available for</b> </td>
                                <td width="20%">
                                    <select class="form-control" name="projection_aval_amount">
                                        <option>0</option>
                                        <option>1</option>
                                        <option>2</option>
                                    </select>
                                </td>
                                <td width="20%">(Amount in INR Lacs)</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered overview-table mt-3 " cellspacing="0">
                        <tbody>
                            <tr>
                                <td rowspan="2" valign="middle" bgcolor="#efefef" width="40%">Financial Spread Sheet for the period ended</td>
                                 <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                        echo "<td bgcolor='#efefef' align='right'>31-March-$i</td>";
                                                    } ?>
                            </tr>
                            <tr>

                                 <?php for($i = $start_year; $i <= $curr_year; $i++){ 
                                    $cnt = $curr_year - $i;
                                    ?>
                                <td bgcolor="#efefef" align="right">
                                    <select class="form-control" name="audit[]">
                                        <option>Audited</option>
                                        <option>Unaudited</option>
                                        <option>audit-Unaudited</option>
                                    </select>
                                </td>
                                 <?php  } ?>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="4" bgcolor="#e6e4e4"><b class="bold">SUMMARY OF FINANCIAL CONDITION</b></td>
                            </tr>
                            <tr>
                                <td colspan="4"><b>(A)  PERFORMANCE ANALYSIS</b></td>
                            </tr>
                            <tr>
                                <td>NET SALES (incl. Trading and Other Operating Income)</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_net_sales[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_net_sales[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_net_sales[]" value="0.00"></td>
                            </tr>
                            <tr>
                                <td>     OTHER NON OPERATING INCOME</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_othr_non_income[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_othr_non_income[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_othr_non_income[]" value="0.00"></td>
                            </tr>
                            <tr>
                                <td>     PBDIT (OPERATING PROFIT)</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_pbdit[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_pbdit[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_pbdit[]" value="0.00"></td>
                            </tr>
                            <tr>
                                <td>          DEPRECIATION</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_depreciation[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_depreciation[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_depreciation[]" value="0.00"></td>
                            </tr>
                            <tr>
                                <td> DEPRECIATION / AVEAGRE NET FIXED ASSETS (%)</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_avrg_fixed_assets_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_avrg_fixed_assets_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_avrg_fixed_assets_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>      INTEREST</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>     INTEREST / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst_prcnt[]" value="0.00" disabled=""></td>
                            </tr>


                            <tr>
                                <td>INTEREST COVERAGE RATIO (PBDIT / INTEREST) </td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst_ratio[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst_ratio[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_intrst_ratio[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>NET PROFIT </td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_net_profit[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_net_profit[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_net_profit[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>CASH PROFIT </td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_cash_profit[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_cash_profit[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_cash_profit[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>DSCR </td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_dscr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_dscr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_dscr[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>RAW MATERIAL / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_raw_material_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_raw_material_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_raw_material_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>LABOUR / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_labour_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_labour_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_labour_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>MANUFACTURING EXPENSES / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_mnufctr_expns_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_mnufctr_expns_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="prfomnc_mnufctr_expns_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(B) PROFITABILITY ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>PBDIT / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbdit_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbdit_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbdit_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>PBIT / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbit_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbit_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbit_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>PBT / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbt_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbt_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_pbt_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>NET PROFIT / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_net_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_net_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_net_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>CASH PROFIT / NET SALES (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_cash_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_cash_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_cash_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>RETAINED PROFIT / NET PROFIT (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_retained_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_retained_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_retained_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>RETURN ON NET WORTH (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_net_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_net_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_net_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>RETURN ON ASSETS (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_assets_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_assets_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_assets_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>RETURN ON CAPITAL EMPLOYED--ROCE (%)</td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_cptl_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_cptl_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="profit_return_cptl_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(C) GROWTH ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>NET SALES GROWTH (%)</td>
                                <td align="right"><input type="text" class="form-control" name="growth_net_sales_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="growth_net_sales_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="growth_net_sales_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>NET PROFIT GROWTH (%)</td>
                                <td align="right"><input type="text" class="form-control" name="growth_net_profit_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="growth_net_profit_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="growth_net_profit_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>TANGIBLE NET WORTH GROWTH (%)</td>
                                <td align="right"><input type="text" class="form-control" name="growth_tangible_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="growth_tangible_prcnt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="growth_tangible_prcnt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(D) FINANCIAL POSITION ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>TOTAL ASSETS (TANGIBLE)</td>
                                <td align="right"><input type="text" class="form-control" name="fncl_total_assets[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_total_assets[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_total_assets[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>TOTAL CURRENT ASSETS</td>
                                <td align="right"><input type="text" class="form-control" name="fncl_curr_assets[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_curr_assets[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_curr_assets[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>TOTAL NON CURRENT ASSETS</td>
                                <td align="right"><input type="text" class="form-control" name="fncl_non_curr_assets[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_non_curr_assets[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_non_curr_assets[]" value="0.00"></td>
                            </tr>


                            <tr>
                                <td>TOTAL OUTSIDE LIABILITIES (TOL)</td>
                                <td align="right"><input type="text" class="form-control" name="fncl_tol[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_tol[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_tol[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>TANGIBLE NETWORTH (TNW)</td>
                                <td align="right"><input type="text" class="form-control" name="fncl_tnw[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_tnw[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_tnw[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>INVESTMENT IN ASSCOIATES &amp; SUBSIDIARIES</td>
                                <td align="right"><input type="text" class="form-control" name="fncl_investment[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_investment[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_investment[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>BORROWINGS FROM SUBSIDARIES (QUASI EQUITY)</td>
                                <td align="right"><input type="text" class="form-control" name="fncl_quasi_equity[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_quasi_equity[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_quasi_equity[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>ADJUSTED TANGIBLE NET WORTH (ATNW)</td>
                                <td align="right"><input type="text" class="form-control" name="fncl_atnw[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_atnw[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="fncl_atnw[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(E) LEVERAGE ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>TOL / TNW RATIO</td>
                                <td align="right"><input type="text" class="form-control" name="levrge_tnw[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_tnw[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_tnw[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>TOL / ADJ. TNW (ATNW)</td>
                                <td align="right"><input type="text" class="form-control" name="levrge_atnw[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_atnw[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_atnw[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>LONG TERM DEBT / TNW</td>
                                <td align="right"><input type="text" class="form-control" name="levrge_long_tnw[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_long_tnw[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_long_tnw[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>LONG TERM DEBT / ATNW</td>
                                <td align="right"><input type="text" class="form-control" name="levrge_long_atnw[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_long_atnw[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_long_atnw[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>TERM DEBT(incl. Deb) / CASH PROFIT (Years)</td>
                                <td align="right"><input type="text" class="form-control" name="levrge_cash_profit[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_cash_profit[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_cash_profit[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>NET CASH ACCRUAL / TOTAL DEBT</td>
                                <td align="right"><input type="text" class="form-control" name="levrge_total_debt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_total_debt[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_total_debt[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>DEBT /PBDIT</td>
                                <td align="right"><input type="text" class="form-control" name="levrge_pbdit[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_pbdit[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="levrge_pbdit[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(F) LIQUIDITY POSITION ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>NET WORKING CAPITAL</td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_net_capital[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_net_capital[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_net_capital[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>CURRENT RATIO </td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_curr_ratio[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_curr_ratio[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_curr_ratio[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>QUICK RATIO</td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_quick_ratio[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_quick_ratio[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="liqdty_quick_ratio[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(G) ACTIVITY EFFICIENCY ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>     RECEIVABLE TURNOVER (DOMESTIC) (DAYS)</td>
                                <td align="right"><input type="text" class="form-control" name="activity_domestic_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_domestic_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_domestic_trnvr[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>RECEIVABLE TURNOVER (EXPORT) (DAYS)</td>
                                <td align="right"><input type="text" class="form-control" name="activity_export_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_export_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_export_trnvr[]" value="0.00" disabled=""></td>
                            </tr>


                            <tr>
                                <td>RECEIVABLE TURNOVER DAYS (TOTAL , Inc. DEBTORS &gt; 6 MONTHS)</td>
                                <td align="right"><input type="text" class="form-control" name="activity_total_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_total_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_total_trnvr[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>INVENTORY TURNOVER (DAYS)</td>
                                <td align="right"><input type="text" class="form-control" name="activity_inventory_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_inventory_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_inventory_trnvr[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>CREDITORS TURNOVER (DAYS)</td>
                                <td align="right"><input type="text" class="form-control" name="activity_creditors_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_creditors_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_creditors_trnvr[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td>FIXED ASSETS TURNOVER RATIO (TIMES)</td>
                                <td align="right"><input type="text" class="form-control" name="activity_fixed_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_fixed_trnvr[]" value="0.00" disabled=""></td>
                                <td align="right"><input type="text" class="form-control" name="activity_fixed_trnvr[]" value="0.00" disabled=""></td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(H) FUNDS FLOW ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>LONG TERM SOURCES</td>
                                <td align="right"><input type="text" class="form-control" name="funds_long_source[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="funds_long_source[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="funds_long_source[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>LONG TERM USES</td>
                                <td align="right"><input type="text" class="form-control" name="funds_long_uses[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="funds_long_uses[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="funds_long_uses[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>CONTRIBUTION TO NET WORKING CAPITAL</td>
                                <td align="right"><input type="text" class="form-control" name="funds_net_capital[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="funds_net_capital[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="funds_net_capital[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(I) CASH FLOW ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>NET CASH FROM OPERATIONS</td>
                                <td align="right"><input type="text" class="form-control" name="cash_net[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_net[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_net[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>CASH BEFORE FUNDING</td>
                                <td align="right"><input type="text" class="form-control" name="cash_before_funding[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_before_funding[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_before_funding[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>INVESTMENTS</td>
                                <td align="right"><input type="text" class="form-control" name="cash_investment[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_investment[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_investment[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td colspan="4" bgcolor="#e6e4e4"><b class="bold">CASH BEFORE FUNDING, IF NEGATIVE MET FROM</b></td>
                            </tr>

                            <tr>
                                <td>- WORKING CAP. FRM BANKS &amp; SHT TERM DEBTS</td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_capital[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_capital[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_capital[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>- TERM DEBTS</td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_debts[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_debts[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_debts[]" value="0.00"></td>
                            </tr>

                            <tr>
                                <td>- EQUITY</td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_equity[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_equity[]" value="0.00"></td>
                                <td align="right"><input type="text" class="form-control" name="cash_negative_equity[]" value="0.00"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="data mt-4">
                        <h2 class="sub-title bg">Comment on Financials</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">
                            <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                                <tbody>
                                    <tr>
                                        <td width="30%">
                                            <p><b>Sales and profitability:</b> </p>
                                        </td>
                                        <td><input type="text" name="sales_and_profit" id="sales_and_profit" class="form-control" value=""></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><b>Gearing &amp; TOL/ATNW:</b></p>
                                        </td>
                                        <td><input type="text" name="gearing" id="gearing" class="form-control" value=""></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><b>Liquidity Ratio:</b></p>
                                        </td>
                                        <td><input type="text" name="liquidity_ratio" id="liquidity_ratio" class="form-control" value=""></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><b>Working Capital Cycle:</b></p>
                                        </td>
                                        <td><input type="text" name="capital_cycle" id="capital_cycle" class="form-control" value=""></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><b>Average collection period receivable days:</b></p>
                                        </td>
                                        <td><input type="text" name="average_collection_period" id="average_collection_period" class="form-control" value=""></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="data mt-4">
                        <h2 class="sub-title bg">Movement of debtors:</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">
                            <table class="table overview-table" id="myTable">
                                <thead>
                                    <tr>
                                        <th width="25%"></th>
                                        <th width="25%">
                                            <select class="form-control form-control-select half-width" id="first_year">
                                                <option value="">Select</option>
                                                <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                    echo "<option value='$i'>$i</option>";
                                                } ?>

                                            </select>
                                            <select class="form-control form-control-select half-width" id="first_month">
                                                <option value="">Select</option>
                                                 <?php for($m = 1; $m <= 12; $m++){
                                                    $month = date('F', strtotime("2017-$m-01"));
                                                    echo "<option value='$month'>$month</option>";
                                                } ?>
                                            </select>
                                        </th>
                                        <th width="25%">
                                            <select class="form-control form-control-select half-width" id="second_year">
                                                <option value="">Select</option>
                                                <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                    echo "<option value='$i'>$i</option>";
                                                } ?>

                                            </select>
                                            <select class="form-control form-control-select half-width" id="second_month">
                                                <option value="">Select</option>
                                                 <?php for($m = 1; $m <= 12; $m++){
                                                    $month = date('F', strtotime("2017-$m-01"));
                                                    echo "<option value='$month'>$month</option>";
                                                } ?>
                                            </select>
                                        </th>

                                        <th width="25%">
                                            <select class="form-control form-control-select half-width" id="third_year">
                                                <option value="">Select</option>
                                                <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                    echo "<option value='$i'>$i</option>";
                                                } ?>

                                            </select>
                                            <select class="form-control form-control-select half-width" id="third_month">
                                                <option value="">Select</option>
                                                 <?php for($m = 1; $m <= 12; $m++){
                                                    $month = date('F', strtotime("2017-$m-01"));
                                                    echo "<option value='$month'>$month</option>";
                                                } ?>
                                            </select>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="add-me">
                                    <tr>
                                        <td>Debtors (Rs Lakhs)  </td>
                                        <td><input type="text" name="first_year_name" id="first_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control" value=""></td>
                                        <td><input type="text" name="second_year_name" id="second_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control" value=""></td>
                                        <td><input type="text" name="third_year_name" id="third_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control" value=""></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="data mt-4">
                        <h2 class="sub-title bg">Major debtors:</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">
                            <table class="table overview-table" id="myTable2">
                                <thead>
                                    <tr>
                                        <th width="25%">Debtor</th>
                                        <th width="25%"><select class="form-control form-control-select half-width" id="deb_first_year">
                                           <option value="">Select</option>
                                <?php for($i = $start_year; $i <= $curr_year; $i++){
                                    echo "<option value='$i'>$i</option>";
                                } ?>
                                        </select>
                                        <select class="form-control form-control-select half-width" id="deb_first_month">
                                            <option value="">Select</option>
                                 <?php for($m = 1; $m <= 12; $m++){
                                    $month = date('F', strtotime("2017-$m-01"));
                                    echo "<option value='$month'>$month</option>";
                                } ?>
                                        </select></th>
                                        <th width="25%"><select class="form-control form-control-select half-width" id="deb_second_year">
                                            <option value="">Select</option>
                                <?php for($i = $start_year; $i <= $curr_year; $i++){
                                    echo "<option value='$i'>$i</option>";
                                } ?>
                                        </select>
                                        <select class="form-control form-control-select half-width" id="deb_second_month">
                                           <option value="">Select</option>
                                 <?php for($m = 1; $m <= 12; $m++){
                                    $month = date('F', strtotime("2017-$m-01"));
                                    echo "<option value='$month'>$month</option>";
                                } ?>
                                        </select></th>

                                        <th width="25%"><select class="form-control form-control-select half-width" id="deb_third_year">
                                           <option value="">Select</option>
                                <?php for($i = $start_year; $i <= $curr_year; $i++){
                                    echo "<option value='$i'>$i</option>";
                                } ?>

                                        </select>
                                        <select class="form-control form-control-select half-width" id="deb_third_month">
                                           <option value="">Select</option>
                                 <?php for($m = 1; $m <= 12; $m++){
                                    $month = date('F', strtotime("2017-$m-01"));
                                    echo "<option value='$month'>$month</option>";
                                } ?>
                                        </select></th>
                                    </tr>
                                </thead>
                                <tbody class="add-me" id="major_debtors">
                                </tbody>
                                <thead>
                                    <tr>
                                        <td>TOTAL</td>
                                        <td id="first_total" value="0">0</td>
                                        <td id="second_total" value="0">0</td>
                                        <td id="third_total" value="0">0</td>
                                    </tr>
                                </thead>
                            </table>
                            <button  class="btn btn-primary pull-right btn-sm mt-3"> + Add Row</button>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="data mt-4">
                        <h2 class="sub-title bg">Risk Comments on Financials</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">
                            <textarea class="form-control" id="financial_risk_comments" name="financial_risk_comments" rows="3" value="" spellcheck="false"></textarea>


                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="data mt-4">
                        <h2 class="sub-title bg">Movement of Inventory:</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">
                            <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">

                                <tbody>
                                    <tr><td width="30%">Average Payable Days: </td>
                                        <td><input type="text" name="inventory_payable_days" id="inventory_payable_days" class="form-control" value=""></td>
                                        <td>Projections:
                                        </td>
                                        <td><input type="text" name="inventory_projections" id="inventory_projections" class="form-control" value="" <="" td=""></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="data mt-4">
                        <h2 class="sub-title bg">Inter-Group Transactions:</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">




                            <table class="table overview-table" id="myTable6">
                                <thead>
                                    <tr>
                                        <td>Sister Concern</td>
                                        <td>Nature of Transaction</td>
                                        <td>
                                            <select class="form-control form-control-select" id="deb_first_years">
                                               <option value="">Select</option>
                                <?php for($i = $start_year; $i <= $curr_year; $i++){
                                    echo "<option value='$i'>$i</option>";
                                } ?>
                                            </select>
                                            <small>Amount (Rs Lakh)</small>


                                        </td>
                                    </tr>
                                </thead>
                                <tbody id="inter_group_transaction">

                                </tbody>
                            </table>



                            <button class="btn btn-primary pull-right btn-sm mt-3"> + Add Row</button>

                            <div class="clearfix"></div>
                        </div>
                    </div>    
                    <button class="btn btn-success ml-auto  mt-3"> Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
@section('jscript')

@endsection