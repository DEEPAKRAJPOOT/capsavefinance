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
                <a href="{{ route('cam_overview') }}" class="active">Overview</a>
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
                <a href="{{ route('cam_finance') }}">Financial</a>
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
                                <td width="20%"><b>Projections Available Amount</b> </td>
                                <td width="20%">
                                    <input type="text" class="form-control" name="projection_aval_amount" placeholder="0.00" value="{{old('projection_aval_amount')}}">
                                    {!! $errors->first('projection_aval_amount', '<span class="error">:message</span>') !!}
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

                                 <?php 
                                 $j = 0;
                                 for($i = $start_year; $i <= $curr_year; $i++){ 
                                    $cnt = $curr_year - $i;
                                    ?>
                                <td bgcolor="#efefef">
                                    <select class="form-control" name="audit[]">
                                        <option value="">Select</option>
                                        <option>Audited</option>
                                        <option>Unaudited</option>
                                    </select>
                                     {!! $errors->first('audit.'.$j, '<span class="error">:message</span>') !!}
                                </td>
                                 <?php $j++;  } ?>
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
                                <td>
                                    <input type="text" class="form-control" name="prfomnc_net_sales[]" placeholder="0.00" value="{{old('prfomnc_net_sales.0')}}">
                                    {!! $errors->first('prfomnc_net_sales.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_net_sales[]" placeholder="0.00" value="{{old('prfomnc_net_sales.1')}}">
                                 {!! $errors->first('prfomnc_net_sales.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_net_sales[]" placeholder="0.00" value="{{old('prfomnc_net_sales.2')}}">
                                 {!! $errors->first('prfomnc_net_sales.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>
                            <tr>
                                <td>OTHER NON OPERATING INCOME</td>
                                <td><input type="text" class="form-control" name="prfomnc_othr_non_income[]" placeholder="0.00" value="{{old('prfomnc_othr_non_income.0')}}">
                                    {!! $errors->first('prfomnc_othr_non_income.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_othr_non_income[]" placeholder="0.00" value="{{old('prfomnc_othr_non_income.1')}}">
                                    {!! $errors->first('prfomnc_othr_non_income.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_othr_non_income[]" placeholder="0.00" value="{{old('prfomnc_othr_non_income.2')}}">
                                    {!! $errors->first('prfomnc_othr_non_income.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>
                            <tr>
                                <td>PBDIT (OPERATING PROFIT)</td>
                                <td><input type="text" class="form-control" name="prfomnc_pbdit[]" placeholder="0.00" value="{{old('prfomnc_pbdit.0')}}">
                                {!! $errors->first('prfomnc_pbdit.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_pbdit[]" placeholder="0.00" value="{{old('prfomnc_pbdit.1')}}">
                                {!! $errors->first('prfomnc_pbdit.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_pbdit[]" placeholder="0.00" value="{{old('prfomnc_pbdit.2')}}">
                                {!! $errors->first('prfomnc_pbdit.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>
                            <tr>
                                <td>DEPRECIATION</td>
                                <td><input type="text" class="form-control" name="prfomnc_depreciation[]" placeholder="0.00" value="{{old('prfomnc_depreciation.0')}}">
                                {!! $errors->first('prfomnc_depreciation.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_depreciation[]" placeholder="0.00" value="{{old('prfomnc_depreciation.1')}}">
                                {!! $errors->first('prfomnc_depreciation.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_depreciation[]" placeholder="0.00" value="{{old('prfomnc_depreciation.2')}}">
                                {!! $errors->first('prfomnc_depreciation.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>
                            <tr>
                                <td>DEPRECIATION / AVEAGRE NET FIXED ASSETS (%)</td>
                                <td><input type="text" class="form-control" name="prfomnc_avrg_fixed_assets_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_avrg_fixed_assets_prcnt.0')}}">
                                {!! $errors->first('prfomnc_avrg_fixed_assets_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_avrg_fixed_assets_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_avrg_fixed_assets_prcnt.1')}}">
                                {!! $errors->first('prfomnc_avrg_fixed_assets_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_avrg_fixed_assets_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_avrg_fixed_assets_prcnt.2')}}">
                                {!! $errors->first('prfomnc_avrg_fixed_assets_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>INTEREST</td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst[]" placeholder="0.00" value="{{old('prfomnc_intrst.0')}}">
                                {!! $errors->first('prfomnc_intrst.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst[]" placeholder="0.00" value="{{old('prfomnc_intrst.1')}}">
                                {!! $errors->first('prfomnc_intrst.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst[]" placeholder="0.00" value="{{old('prfomnc_intrst.2')}}">
                                {!! $errors->first('prfomnc_intrst.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>INTEREST / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_intrst_prcnt.0')}}">
                                {!! $errors->first('prfomnc_intrst_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_intrst_prcnt.1')}}">
                                {!! $errors->first('prfomnc_intrst_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_intrst_prcnt.2')}}">
                                {!! $errors->first('prfomnc_intrst_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>


                            <tr>
                                <td>INTEREST COVERAGE RATIO (PBDIT / INTEREST) </td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst_ratio[]" placeholder="0.00" readonly value="{{old('prfomnc_intrst_ratio.0')}}">
                                {!! $errors->first('prfomnc_intrst_ratio.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst_ratio[]" placeholder="0.00" readonly value="{{old('prfomnc_intrst_ratio.1')}}">
                                {!! $errors->first('prfomnc_intrst_ratio.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_intrst_ratio[]" placeholder="0.00" readonly value="{{old('prfomnc_intrst_ratio.2')}}">
                                {!! $errors->first('prfomnc_intrst_ratio.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>NET PROFIT </td>
                                <td><input type="text" class="form-control" name="prfomnc_net_profit[]" placeholder="0.00" value="{{old('prfomnc_net_profit.0')}}">
                                {!! $errors->first('prfomnc_net_profit.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_net_profit[]" placeholder="0.00" value="{{old('prfomnc_net_profit.1')}}">
                                {!! $errors->first('prfomnc_net_profit.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_net_profit[]" placeholder="0.00" value="{{old('prfomnc_net_profit.2')}}">
                                {!! $errors->first('prfomnc_net_profit.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>CASH PROFIT </td>
                                <td><input type="text" class="form-control" name="prfomnc_cash_profit[]" placeholder="0.00" value="{{old('prfomnc_cash_profit.0')}}">
                                {!! $errors->first('prfomnc_cash_profit.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_cash_profit[]" placeholder="0.00" value="{{old('prfomnc_cash_profit.1')}}">
                                {!! $errors->first('prfomnc_cash_profit.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_cash_profit[]" placeholder="0.00" value="{{old('prfomnc_cash_profit.2')}}">
                                {!! $errors->first('prfomnc_cash_profit.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>DSCR </td>
                                <td><input type="text" class="form-control" name="prfomnc_dscr[]" placeholder="0.00" readonly value="{{old('prfomnc_dscr.0')}}">
                                 {!! $errors->first('prfomnc_dscr.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_dscr[]" placeholder="0.00" readonly value="{{old('prfomnc_dscr.1')}}">
                                 {!! $errors->first('prfomnc_dscr.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_dscr[]" placeholder="0.00" readonly value="{{old('prfomnc_dscr.2')}}">
                                 {!! $errors->first('prfomnc_dscr.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>RAW MATERIAL / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="prfomnc_raw_material_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_raw_material_prcnt.0')}}">
                                {!! $errors->first('prfomnc_raw_material_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_raw_material_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_raw_material_prcnt.1')}}">
                                {!! $errors->first('prfomnc_raw_material_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_raw_material_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_raw_material_prcnt.2')}}">
                                {!! $errors->first('prfomnc_raw_material_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>LABOUR / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="prfomnc_labour_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_labour_prcnt.0')}}">
                                {!! $errors->first('prfomnc_labour_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_labour_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_labour_prcnt.1')}}">
                                {!! $errors->first('prfomnc_labour_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_labour_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_labour_prcnt.2')}}">
                                {!! $errors->first('prfomnc_labour_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>MANUFACTURING EXPENSES / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="prfomnc_mnufctr_expns_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_mnufctr_expns_prcnt.0')}}">
                                {!! $errors->first('prfomnc_mnufctr_expns_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_mnufctr_expns_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_mnufctr_expns_prcnt.1')}}">
                                {!! $errors->first('prfomnc_mnufctr_expns_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="prfomnc_mnufctr_expns_prcnt[]" placeholder="0.00" readonly value="{{old('prfomnc_mnufctr_expns_prcnt.2')}}">
                                {!! $errors->first('prfomnc_mnufctr_expns_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(B) PROFITABILITY ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>PBDIT / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="profit_pbdit_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbdit_prcnt.0')}}">
                                {!! $errors->first('profit_pbdit_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_pbdit_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbdit_prcnt.1')}}">
                                {!! $errors->first('profit_pbdit_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_pbdit_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbdit_prcnt.2')}}">
                                {!! $errors->first('profit_pbdit_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>PBIT / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="profit_pbit_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbit_prcnt.0')}}">
                                {!! $errors->first('profit_pbit_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_pbit_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbit_prcnt.1')}}">
                                {!! $errors->first('profit_pbit_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_pbit_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbit_prcnt.2')}}">
                                {!! $errors->first('profit_pbit_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>PBT / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="profit_pbt_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbt_prcnt.0')}}">
                                {!! $errors->first('profit_pbt_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_pbt_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbt_prcnt.1')}}">
                                {!! $errors->first('profit_pbt_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_pbt_prcnt[]" placeholder="0.00" readonly value="{{old('profit_pbt_prcnt.2')}}">
                                {!! $errors->first('profit_pbt_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>NET PROFIT / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="profit_net_prcnt[]" placeholder="0.00" readonly value="{{old('profit_net_prcnt.0')}}">
                                {!! $errors->first('profit_net_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_net_prcnt[]" placeholder="0.00" readonly value="{{old('profit_net_prcnt.1')}}">
                                {!! $errors->first('profit_net_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_net_prcnt[]" placeholder="0.00" readonly value="{{old('profit_net_prcnt.2')}}">
                                {!! $errors->first('profit_net_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>CASH PROFIT / NET SALES (%)</td>
                                <td><input type="text" class="form-control" name="profit_cash_prcnt[]" placeholder="0.00" readonly value="{{old('profit_cash_prcnt.0')}}">
                                {!! $errors->first('profit_cash_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_cash_prcnt[]" placeholder="0.00" readonly value="{{old('profit_cash_prcnt.1')}}">
                                {!! $errors->first('profit_cash_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_cash_prcnt[]" placeholder="0.00" readonly value="{{old('profit_cash_prcnt.2')}}">
                                {!! $errors->first('profit_cash_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>RETAINED PROFIT / NET PROFIT (%)</td>
                                <td><input type="text" class="form-control" name="profit_retained_prcnt[]" placeholder="0.00" readonly value="{{old('profit_retained_prcnt.0')}}">
                                {!! $errors->first('profit_retained_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_retained_prcnt[]" placeholder="0.00" readonly value="{{old('profit_retained_prcnt.1')}}">
                                {!! $errors->first('profit_retained_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_retained_prcnt[]" placeholder="0.00" readonly value="{{old('profit_retained_prcnt.2')}}">
                                {!! $errors->first('profit_retained_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>RETURN ON NET WORTH (%)</td>
                                <td><input type="text" class="form-control" name="profit_return_net_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_net_prcnt.0')}}">
                                {!! $errors->first('profit_return_net_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_return_net_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_net_prcnt.1')}}">
                                {!! $errors->first('profit_return_net_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_return_net_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_net_prcnt.2')}}">
                                {!! $errors->first('profit_return_net_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>RETURN ON ASSETS (%)</td>
                                <td><input type="text" class="form-control" name="profit_return_assets_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_assets_prcnt.0')}}">
                                {!! $errors->first('profit_return_assets_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_return_assets_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_assets_prcnt.1')}}">
                                {!! $errors->first('profit_return_assets_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_return_assets_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_assets_prcnt.2')}}">
                                {!! $errors->first('profit_return_assets_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>RETURN ON CAPITAL EMPLOYED--ROCE (%)</td>
                                <td><input type="text" class="form-control" name="profit_return_cptl_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_cptl_prcnt.0')}}">
                                {!! $errors->first('profit_return_cptl_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="profit_return_cptl_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_cptl_prcnt.1')}}">
                                {!! $errors->first('profit_return_cptl_prcnt.1', '<span class="error">:message</span>') !!}        
                                <td><input type="text" class="form-control" name="profit_return_cptl_prcnt[]" placeholder="0.00" readonly value="{{old('profit_return_cptl_prcnt.2')}}">
                                {!! $errors->first('profit_return_cptl_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(C) GROWTH ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>NET SALES GROWTH (%)</td>
                                <td><input type="text" class="form-control" name="growth_net_sales_prcnt[]" placeholder="0.00" readonly value="{{old('growth_net_sales_prcnt.0')}}">
                                {!! $errors->first('growth_net_sales_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="growth_net_sales_prcnt[]" placeholder="0.00" readonly value="{{old('growth_net_sales_prcnt.1')}}">
                                {!! $errors->first('growth_net_sales_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="growth_net_sales_prcnt[]" placeholder="0.00" readonly value="{{old('growth_net_sales_prcnt.2')}}">
                                {!! $errors->first('growth_net_sales_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>NET PROFIT GROWTH (%)</td>
                                <td><input type="text" class="form-control" name="growth_net_profit_prcnt[]" placeholder="0.00" readonly value="{{old('growth_net_profit_prcnt.0')}}">
                                {!! $errors->first('growth_net_profit_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="growth_net_profit_prcnt[]" placeholder="0.00" readonly value="{{old('growth_net_profit_prcnt.1')}}">
                                {!! $errors->first('growth_net_profit_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="growth_net_profit_prcnt[]" placeholder="0.00" readonly value="{{old('growth_net_profit_prcnt.2')}}">
                                {!! $errors->first('growth_net_profit_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>TANGIBLE NET WORTH GROWTH (%)</td>
                                <td><input type="text" class="form-control" name="growth_tangible_prcnt[]" placeholder="0.00" readonly value="{{old('growth_tangible_prcnt.0')}}">
                                {!! $errors->first('growth_tangible_prcnt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="growth_tangible_prcnt[]" placeholder="0.00" readonly value="{{old('growth_tangible_prcnt.1')}}">
                                {!! $errors->first('growth_tangible_prcnt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="growth_tangible_prcnt[]" placeholder="0.00" readonly value="{{old('growth_tangible_prcnt.2')}}">
                                {!! $errors->first('growth_tangible_prcnt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(D) FINANCIAL POSITION ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>TOTAL ASSETS (TANGIBLE)</td>
                                <td><input type="text" class="form-control" name="fncl_total_assets[]" placeholder="0.00" value="{{old('fncl_total_assets.0')}}">
                                {!! $errors->first('fncl_total_assets.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_total_assets[]" placeholder="0.00" value="{{old('fncl_total_assets.1')}}">
                                {!! $errors->first('fncl_total_assets.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_total_assets[]" placeholder="0.00" value="{{old('fncl_total_assets.2')}}">
                                {!! $errors->first('fncl_total_assets.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>TOTAL CURRENT ASSETS</td>
                                <td><input type="text" class="form-control" name="fncl_curr_assets[]" placeholder="0.00" value="{{old('fncl_curr_assets.0')}}">
                                {!! $errors->first('fncl_curr_assets.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_curr_assets[]" placeholder="0.00" value="{{old('fncl_curr_assets.1')}}">
                                {!! $errors->first('fncl_curr_assets.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_curr_assets[]" placeholder="0.00" value="{{old('fncl_curr_assets.2')}}">
                                {!! $errors->first('fncl_curr_assets.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>TOTAL NON CURRENT ASSETS</td>
                                <td><input type="text" class="form-control" name="fncl_non_curr_assets[]" placeholder="0.00" value="{{old('fncl_non_curr_assets.0')}}">
                                {!! $errors->first('fncl_non_curr_assets.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_non_curr_assets[]" placeholder="0.00" value="{{old('fncl_non_curr_assets.1')}}">
                                {!! $errors->first('fncl_non_curr_assets.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_non_curr_assets[]" placeholder="0.00" value="{{old('fncl_non_curr_assets.2')}}">
                                {!! $errors->first('fncl_non_curr_assets.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>


                            <tr>
                                <td>TOTAL OUTSIDE LIABILITIES (TOL)</td>
                                <td><input type="text" class="form-control" name="fncl_tol[]" placeholder="0.00" value="{{old('fncl_tol.0')}}">
                                {!! $errors->first('fncl_tol.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_tol[]" placeholder="0.00" value="{{old('fncl_tol.1')}}">
                                {!! $errors->first('fncl_tol.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_tol[]" placeholder="0.00" value="{{old('fncl_tol.2')}}">
                                {!! $errors->first('fncl_tol.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>TANGIBLE NETWORTH (TNW)</td>
                                <td><input type="text" class="form-control" name="fncl_tnw[]" placeholder="0.00" value="{{old('fncl_tnw.0')}}">
                                {!! $errors->first('fncl_tnw.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_tnw[]" placeholder="0.00" value="{{old('fncl_tnw.1')}}">
                                {!! $errors->first('fncl_tnw.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_tnw[]" placeholder="0.00" value="{{old('fncl_tnw.2')}}">
                                {!! $errors->first('fncl_tnw.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>INVESTMENT IN ASSCOIATES &amp; SUBSIDIARIES</td>
                                <td><input type="text" class="form-control" name="fncl_investment[]" placeholder="0.00" value="{{old('fncl_investment.0')}}">
                                {!! $errors->first('fncl_investment.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_investment[]" placeholder="0.00" value="{{old('fncl_investment.1')}}">
                                {!! $errors->first('fncl_investment.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_investment[]" placeholder="0.00" value="{{old('fncl_investment.2')}}">
                                {!! $errors->first('fncl_investment.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>BORROWINGS FROM SUBSIDARIES (QUASI EQUITY)</td>
                                <td><input type="text" class="form-control" name="fncl_quasi_equity[]" placeholder="0.00" value="{{old('fncl_quasi_equity.0')}}">
                                {!! $errors->first('fncl_quasi_equity.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_quasi_equity[]" placeholder="0.00" value="{{old('fncl_quasi_equity.1')}}">
                                {!! $errors->first('fncl_quasi_equity.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_quasi_equity[]" placeholder="0.00" value="{{old('fncl_quasi_equity.2')}}">
                                {!! $errors->first('fncl_quasi_equity.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>ADJUSTED TANGIBLE NET WORTH (ATNW)</td>
                                <td><input type="text" class="form-control" name="fncl_atnw[]" placeholder="0.00" value="{{old('fncl_atnw.0')}}">
                                {!! $errors->first('fncl_atnw.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_atnw[]" placeholder="0.00" value="{{old('fncl_atnw.1')}}">
                                {!! $errors->first('fncl_atnw.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="fncl_atnw[]" placeholder="0.00" value="{{old('fncl_atnw.2')}}">
                                {!! $errors->first('fncl_atnw.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(E) LEVERAGE ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>TOL / TNW RATIO</td>
                                <td><input type="text" class="form-control" name="levrge_tnw[]" placeholder="0.00" readonly value="{{old('levrge_tnw.0')}}">
                                {!! $errors->first('levrge_tnw.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_tnw[]" placeholder="0.00" readonly value="{{old('levrge_tnw.1')}}">
                                {!! $errors->first('levrge_tnw.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_tnw[]" placeholder="0.00" readonly value="{{old('levrge_tnw.2')}}">
                                {!! $errors->first('levrge_tnw.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>TOL / ADJ. TNW (ATNW)</td>
                                <td><input type="text" class="form-control" name="levrge_atnw[]" placeholder="0.00" readonly value="{{old('levrge_atnw.0')}}">
                                {!! $errors->first('levrge_atnw.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_atnw[]" placeholder="0.00" readonly value="{{old('levrge_atnw.1')}}">
                                {!! $errors->first('levrge_atnw.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_atnw[]" placeholder="0.00" readonly value="{{old('levrge_atnw.2')}}">
                                {!! $errors->first('levrge_atnw.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>LONG TERM DEBT / TNW</td>
                                <td><input type="text" class="form-control" name="levrge_long_tnw[]" placeholder="0.00" readonly value="{{old('levrge_long_tnw.0')}}">
                                {!! $errors->first('levrge_long_tnw.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_long_tnw[]" placeholder="0.00" readonly value="{{old('levrge_long_tnw.1')}}">
                                {!! $errors->first('levrge_long_tnw.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_long_tnw[]" placeholder="0.00" readonly value="{{old('levrge_long_tnw.2')}}">
                                {!! $errors->first('levrge_long_tnw.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>LONG TERM DEBT / ATNW</td>
                                <td><input type="text" class="form-control" name="levrge_long_atnw[]" placeholder="0.00" readonly value="{{old('levrge_long_atnw.0')}}">
                                {!! $errors->first('levrge_long_atnw.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_long_atnw[]" placeholder="0.00" readonly value="{{old('levrge_long_atnw.1')}}">
                                {!! $errors->first('levrge_long_atnw.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_long_atnw[]" placeholder="0.00" readonly value="{{old('levrge_long_atnw.2')}}">
                                {!! $errors->first('levrge_long_atnw.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>TERM DEBT(incl. Deb) / CASH PROFIT (Years)</td>
                                <td><input type="text" class="form-control" name="levrge_cash_profit[]" placeholder="0.00" readonly value="{{old('levrge_cash_profit.0')}}">
                                {!! $errors->first('levrge_cash_profit.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_cash_profit[]" placeholder="0.00" readonly value="{{old('levrge_cash_profit.1')}}">
                                {!! $errors->first('levrge_cash_profit.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_cash_profit[]" placeholder="0.00" readonly value="{{old('levrge_cash_profit.2')}}">
                                {!! $errors->first('levrge_cash_profit.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>NET CASH ACCRUAL / TOTAL DEBT</td>
                                <td><input type="text" class="form-control" name="levrge_total_debt[]" placeholder="0.00" readonly value="{{old('levrge_total_debt.0')}}">
                                {!! $errors->first('levrge_total_debt.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_total_debt[]" placeholder="0.00" readonly value="{{old('levrge_total_debt.1')}}">
                                {!! $errors->first('levrge_total_debt.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_total_debt[]" placeholder="0.00" readonly value="{{old('levrge_total_debt.2')}}">
                                {!! $errors->first('levrge_total_debt.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>DEBT /PBDIT</td>
                                <td><input type="text" class="form-control" name="levrge_pbdit[]" placeholder="0.00" readonly value="{{old('levrge_pbdit.0')}}">
                                {!! $errors->first('levrge_pbdit.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_pbdit[]" placeholder="0.00" readonly value="{{old('levrge_pbdit.1')}}">
                                {!! $errors->first('levrge_pbdit.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="levrge_pbdit[]" placeholder="0.00" readonly value="{{old('levrge_pbdit.2')}}">
                                {!! $errors->first('levrge_pbdit.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(F) LIQUIDITY POSITION ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>NET WORKING CAPITAL</td>
                                <td><input type="text" class="form-control" name="liqdty_net_capital[]" placeholder="0.00" value="{{old('liqdty_net_capital.0')}}">
                                {!! $errors->first('liqdty_net_capital.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="liqdty_net_capital[]" placeholder="0.00" value="{{old('liqdty_net_capital.1')}}">
                                {!! $errors->first('liqdty_net_capital.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="liqdty_net_capital[]" placeholder="0.00" value="{{old('liqdty_net_capital.2')}}">
                                {!! $errors->first('liqdty_net_capital.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>CURRENT RATIO </td>
                                <td><input type="text" class="form-control" name="liqdty_curr_ratio[]" placeholder="0.00" readonly value="{{old('liqdty_curr_ratio.0')}}">
                                {!! $errors->first('liqdty_curr_ratio.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="liqdty_curr_ratio[]" placeholder="0.00" readonly value="{{old('liqdty_curr_ratio.1')}}">
                                {!! $errors->first('liqdty_curr_ratio.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="liqdty_curr_ratio[]" placeholder="0.00" readonly value="{{old('liqdty_curr_ratio.2')}}">
                                {!! $errors->first('liqdty_curr_ratio.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>QUICK RATIO</td>
                                <td><input type="text" class="form-control" name="liqdty_quick_ratio[]" placeholder="0.00" readonly value="{{old('liqdty_quick_ratio.0')}}">
                                {!! $errors->first('liqdty_quick_ratio.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="liqdty_quick_ratio[]" placeholder="0.00" readonly value="{{old('liqdty_quick_ratio.1')}}">
                                {!! $errors->first('liqdty_quick_ratio.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="liqdty_quick_ratio[]" placeholder="0.00" readonly value="{{old('liqdty_quick_ratio.2')}}">
                                {!! $errors->first('liqdty_quick_ratio.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(G) ACTIVITY EFFICIENCY ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>RECEIVABLE TURNOVER (DOMESTIC) (DAYS)</td>
                                <td><input type="text" class="form-control" name="activity_domestic_trnvr[]" placeholder="0.00" readonly value="{{old('activity_domestic_trnvr.0')}}">
                                {!! $errors->first('activity_domestic_trnvr.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_domestic_trnvr[]" placeholder="0.00" readonly value="{{old('activity_domestic_trnvr.1')}}">
                                {!! $errors->first('activity_domestic_trnvr.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_domestic_trnvr[]" placeholder="0.00" readonly value="{{old('activity_domestic_trnvr.2')}}">
                                {!! $errors->first('activity_domestic_trnvr.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>RECEIVABLE TURNOVER (EXPORT) (DAYS)</td>
                                <td><input type="text" class="form-control" name="activity_export_trnvr[]" placeholder="0.00" readonly value="{{old('activity_export_trnvr.0')}}">
                                {!! $errors->first('activity_export_trnvr.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_export_trnvr[]" placeholder="0.00" readonly value="{{old('activity_export_trnvr.1')}}">
                                {!! $errors->first('activity_export_trnvr.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_export_trnvr[]" placeholder="0.00" readonly value="{{old('activity_export_trnvr.2')}}">
                                {!! $errors->first('activity_export_trnvr.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>


                            <tr>
                                <td>RECEIVABLE TURNOVER DAYS (TOTAL , Inc. DEBTORS &gt; 6 MONTHS)</td>
                                <td><input type="text" class="form-control" name="activity_total_trnvr[]" placeholder="0.00" readonly value="{{old('activity_total_trnvr.0')}}">
                                {!! $errors->first('activity_total_trnvr.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_total_trnvr[]" placeholder="0.00" readonly value="{{old('activity_total_trnvr.1')}}">
                                {!! $errors->first('activity_total_trnvr.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_total_trnvr[]" placeholder="0.00" readonly value="{{old('activity_total_trnvr.2')}}">
                                {!! $errors->first('activity_total_trnvr.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>INVENTORY TURNOVER (DAYS)</td>
                                <td><input type="text" class="form-control" name="activity_inventory_trnvr[]" placeholder="0.00" readonly value="{{old('activity_inventory_trnvr.0')}}">
                                {!! $errors->first('activity_inventory_trnvr.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_inventory_trnvr[]" placeholder="0.00" readonly value="{{old('activity_inventory_trnvr.1')}}">
                                {!! $errors->first('activity_inventory_trnvr.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_inventory_trnvr[]" placeholder="0.00" readonly value="{{old('activity_inventory_trnvr.2')}}">
                                {!! $errors->first('activity_inventory_trnvr.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>CREDITORS TURNOVER (DAYS)</td>
                                <td><input type="text" class="form-control" name="activity_creditors_trnvr[]" placeholder="0.00" readonly value="{{old('activity_creditors_trnvr.0')}}">
                                {!! $errors->first('activity_creditors_trnvr.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_creditors_trnvr[]" placeholder="0.00" readonly value="{{old('activity_creditors_trnvr.1')}}">
                                {!! $errors->first('activity_creditors_trnvr.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_creditors_trnvr[]" placeholder="0.00" readonly value="{{old('activity_creditors_trnvr.2')}}">
                                {!! $errors->first('activity_creditors_trnvr.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>FIXED ASSETS TURNOVER RATIO (TIMES)</td>
                                <td><input type="text" class="form-control" name="activity_fixed_trnvr[]" placeholder="0.00" readonly value="{{old('activity_fixed_trnvr.0')}}">
                                {!! $errors->first('activity_fixed_trnvr.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_fixed_trnvr[]" placeholder="0.00" readonly value="{{old('activity_fixed_trnvr.1')}}">
                                {!! $errors->first('activity_fixed_trnvr.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="activity_fixed_trnvr[]" placeholder="0.00" readonly value="{{old('activity_fixed_trnvr.2')}}">
                                {!! $errors->first('activity_fixed_trnvr.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(H) FUNDS FLOW ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>LONG TERM SOURCES</td>
                                <td><input type="text" class="form-control" name="funds_long_source[]" placeholder="0.00" value="{{old('funds_long_source.0')}}">
                                {!! $errors->first('funds_long_source.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="funds_long_source[]" placeholder="0.00" value="{{old('funds_long_source.1')}}">
                                {!! $errors->first('funds_long_source.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="funds_long_source[]" placeholder="0.00" value="{{old('funds_long_source.2')}}">
                                {!! $errors->first('funds_long_source.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>LONG TERM USES</td>
                                <td><input type="text" class="form-control" name="funds_long_uses[]" placeholder="0.00" value="{{old('funds_long_uses.0')}}">
                                {!! $errors->first('funds_long_uses.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="funds_long_uses[]" placeholder="0.00" value="{{old('funds_long_uses.1')}}">
                                {!! $errors->first('funds_long_uses.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="funds_long_uses[]" placeholder="0.00" value="{{old('funds_long_uses.2')}}">
                                {!! $errors->first('funds_long_uses.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>CONTRIBUTION TO NET WORKING CAPITAL</td>
                                <td><input type="text" class="form-control" name="funds_net_capital[]" placeholder="0.00" value="{{old('funds_net_capital.0')}}">
                                {!! $errors->first('funds_net_capital.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="funds_net_capital[]" placeholder="0.00" value="{{old('funds_net_capital.1')}}">
                                {!! $errors->first('funds_net_capital.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="funds_net_capital[]" placeholder="0.00" value="{{old('funds_net_capital.2')}}">
                                {!! $errors->first('funds_net_capital.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"><b>(I) CASH FLOW ANALYSIS</b></td>
                            </tr>

                            <tr>
                                <td>NET CASH FROM OPERATIONS</td>
                                <td><input type="text" class="form-control" name="cash_net[]" placeholder="0.00" value="{{old('cash_net.0')}}">
                                {!! $errors->first('cash_net.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_net[]" placeholder="0.00" value="{{old('cash_net.1')}}">
                                {!! $errors->first('cash_net.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_net[]" placeholder="0.00" value="{{old('cash_net.2')}}">
                                {!! $errors->first('cash_net.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>CASH BEFORE FUNDING</td>
                                <td><input type="text" class="form-control" name="cash_before_funding[]" placeholder="0.00" value="{{old('cash_before_funding.0')}}">
                                {!! $errors->first('cash_before_funding.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_before_funding[]" placeholder="0.00" value="{{old('cash_before_funding.1')}}">
                                {!! $errors->first('cash_before_funding.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_before_funding[]" placeholder="0.00" value="{{old('cash_before_funding.2')}}">
                                {!! $errors->first('cash_before_funding.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>INVESTMENTS</td>
                                <td><input type="text" class="form-control" name="cash_investment[]" placeholder="0.00" value="{{old('cash_investment.0')}}">
                                {!! $errors->first('cash_investment.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_investment[]" placeholder="0.00" value="{{old('cash_investment.1')}}">
                                {!! $errors->first('cash_investment.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_investment[]" placeholder="0.00" value="{{old('cash_investment.2')}}">
                                {!! $errors->first('cash_investment.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" bgcolor="#e6e4e4"><b class="bold">CASH BEFORE FUNDING, IF NEGATIVE MET FROM</b></td>
                            </tr>

                            <tr>
                                <td>- WORKING CAP. FRM BANKS &amp; SHT TERM DEBTS</td>
                                <td><input type="text" class="form-control" name="cash_negative_capital[]" placeholder="0.00" value="{{old('cash_negative_capital.0')}}">
                                {!! $errors->first('cash_negative_capital.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_negative_capital[]" placeholder="0.00" value="{{old('cash_negative_capital.1')}}">
                                {!! $errors->first('cash_negative_capital.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_negative_capital[]" placeholder="0.00" value="{{old('cash_negative_capital.2')}}">
                                {!! $errors->first('cash_negative_capital.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>- TERM DEBTS</td>
                                <td><input type="text" class="form-control" name="cash_negative_debts[]" placeholder="0.00" value="{{old('cash_negative_debts.0')}}">
                                {!! $errors->first('cash_negative_debts.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_negative_debts[]" placeholder="0.00" value="{{old('cash_negative_debts.1')}}">
                                {!! $errors->first('cash_negative_debts.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_negative_debts[]" placeholder="0.00" value="{{old('cash_negative_debts.2')}}">
                                {!! $errors->first('cash_negative_debts.2', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>

                            <tr>
                                <td>- EQUITY</td>
                                <td><input type="text" class="form-control" name="cash_negative_equity[]" placeholder="0.00" value="{{old('cash_negative_equity.0')}}">
                                {!! $errors->first('cash_negative_equity.0', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_negative_equity[]" placeholder="0.00" value="{{old('cash_negative_equity.1')}}">
                                {!! $errors->first('cash_negative_equity.1', '<span class="error">:message</span>') !!}
                                </td>
                                <td><input type="text" class="form-control" name="cash_negative_equity[]" placeholder="0.00" value="{{old('cash_negative_equity.2')}}">
                                {!! $errors->first('cash_negative_equity.2', '<span class="error">:message</span>') !!}
                                </td>
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
                                        <td><input type="text" name="sales_and_profit" id="sales_and_profit" class="form-control" value="{{old('sales_and_profit')}}">
                                        {!! $errors->first('sales_and_profit', '<span class="error">:message</span>') !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><b>Gearing &amp; TOL/ATNW:</b></p>
                                        </td>
                                        <td><input type="text" name="gearing" id="gearing" class="form-control" value="{{old('gearing')}}">
                                        {!! $errors->first('gearing', '<span class="error">:message</span>') !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><b>Liquidity Ratio:</b></p>
                                        </td>
                                        <td><input type="text" name="liquidity_ratio" id="liquidity_ratio" class="form-control" value="{{old('liquidity_ratio')}}">
                                        {!! $errors->first('liquidity_ratio', '<span class="error">:message</span>') !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><b>Working Capital Cycle:</b></p>
                                        </td>
                                        <td><input type="text" name="capital_cycle" id="capital_cycle" class="form-control" value="{{old('capital_cycle')}}">
                                        {!! $errors->first('capital_cycle', '<span class="error">:message</span>') !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><b>Average collection period receivable days:</b></p>
                                        </td>
                                        <td><input type="text" name="average_collection_period" id="average_collection_period" class="form-control" value="{{old('average_collection_period')}}">
                                        {!! $errors->first('average_collection_period', '<span class="error">:message</span>') !!}
                                        </td>
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
                                        <td><input type="text" name="debtors[]" id="first_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control" value="{{old('debtors.0')}}">
                                        {!! $errors->first('debtors.0', '<span class="error">:message</span>') !!}
                                        </td>
                                        <td><input type="text" name="debtors[]" id="second_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control" value="{{old('debtors.1')}}">
                                        {!! $errors->first('debtors.1', '<span class="error">:message</span>') !!}
                                        </td>
                                        <td><input type="text" name="debtors[]" id="third_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control" value="{{old('debtors.2')}}">
                                        {!! $errors->first('debtors.2', '<span class="error">:message</span>') !!}
                                        </td>
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
                            <textarea class="form-control" name="financial_risk_comments" id="financial_risk_comments" rows="3" value="" spellcheck="false">{{old('financial_risk_comments')}}</textarea>
                            {!! $errors->first('financial_risk_comments', '<span class="error">:message</span>') !!}
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="data mt-4">
                        <h2 class="sub-title bg">Movement of Inventory:</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">
                            <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">

                                <tbody>
                                    <tr><td width="30%">Average Payable Days: </td>
                                        <td><input type="text" name="inventory_payable_days" id="inventory_payable_days" class="form-control" value="{{old('inventory_payable_days')}}">
                                        {!! $errors->first('inventory_payable_days', '<span class="error">:message</span>') !!}
                                        </td>
                                        <td>Projections:
                                        </td>
                                        <td><input type="text" name="inventory_projections" id="inventory_projections" class="form-control" value="{{old('inventory_projections')}}">
                                        {!! $errors->first('inventory_projections', '<span class="error">:message</span>') !!}
                                        </td>
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
                                <tbody id="inter_group_transaction"></tbody>
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