<?php 
   $curr_year = date('Y');
   $year_count = 3;
   $start_year = date('Y')-$year_count + 1;
?>
@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container">
      <form method="post" action="{{ route('cam_finance_store') }}">
            @csrf
         <div class="row mt-4">
            <div class="col-lg-12 col-12 mb-4">
               <div class="card">
                  <div class="card-body">
                     <div id="pullMsg"></div>
                     <h2 class="sub-title bg mb-4">Uploaded Finance statement-</h2>
                     <div class="clearfix"></div>
                     @if($financedocs->count() > 0)
                     @foreach($financedocs as $financedoc)
                     <div class="doc" style="text-align: center;">
                        <small>{{ $financedoc->finc_year }}</small>
                        <ul>
                           <li><span class="icon"><i class="fa fa-file-pdf-o"></i></span></li>
                           <li><a href="{{ Storage::url($financedoc->file_path) }}" download target="_blank">Download Finance Statement</a></li>
                           <li><a href="javascript:void(0)"></a></li>
                        </ul>
                     </div>
                     @endforeach
                     <div class="clearfix"></div>
                     <div style="text-align: end;">
                        <a href="javascript:void(0)" class="btn btn-success btn-sm getAnalysis">Get Analysis</a>
                     </div>
                     @endif
                     @if(file_exists(storage_path('app/public/user/'.$appId.'_finance.xlsx')))
                        <div class="clearfix"></div>
                        <div style="text-align: end;">
                           <a class="btn btn-success" href="{{ Storage::url('user/'.$appId.'_finance.xlsx') }}" download>Download analysed Statement</a>
                        </div>
                     @endif 
                     @if(!empty($pending_rec) && $pending_rec['status'] == 'fail')
                        <div class="clearfix"></div>
                        <div style="text-align: end;">
                           <a class="btn btn-success process_stmt" pending="{{ $pending_rec['biz_perfios_id'] }}" href="javascript:void(0)">Process Statement</a>
                        </div>
                     @endif 
                     <div class="clearfix"></div>
                     <br/>
                     <hr>
                     <div id="accordion" role="tablist" aria-multiselectable="true" class="accordion">
                        <div class="card">
                           <div class="card-header" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" role="tab" id="headingOne">
                              <a  class="">
                              Financial Detail Summary
                              </a>
                           </div>
                           <div id="collapseOne" class="collapse colsp show" role="tabpanel" aria-labelledby="headingOne">
                              <div class="card-body pt-3 pl-0 pr-0 pb-0" >
                                 <div class="card">
                                    <table class="table table-bordered overview-table" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td><b>Name of the Borrower</b></td>
                                             <td colspan="4">Chandan</td>
                                          </tr>
                                          <tr>
                                             <td width="25%"><b>Latest Audited Financial Year</b></td>
                                             <td width="15%">2019</td>
                                             <td width="20%"><b>Projections Available for</b> </td>
                                             <td width="20%">
                                                <select class="form-control">
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
                                                        <select class="form-control form-control-select half-width" id="first_year" name="movement_year[]">
                                                            <option value="">Select</option>
                                                            <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                                echo "<option value='$i'>$i</option>";
                                                            } ?>

                                                        </select>
                                                        {!! $errors->first('movement_year.0', '<span class="error">:message</span>') !!}
                                                        <select class="form-control form-control-select half-width" id="first_month" name="movement_month[]">
                                                            <option value="">Select</option>
                                                             <?php for($m = 1; $m <= 12; $m++){
                                                                $month = date('F', strtotime("2017-$m-01"));
                                                                echo "<option value='$month'>$month</option>";
                                                            } ?>
                                                        </select>
                                                        {!! $errors->first('movement_month.0', '<span class="error">:message</span>') !!}
                                                   </th>
                                                   <th width="25%">
                                                        <select class="form-control form-control-select half-width" id="second_year" name="movement_year[]">
                                                            <option value="">Select</option>
                                                            <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                                echo "<option value='$i'>$i</option>";
                                                            } ?>

                                                        </select>
                                                        {!! $errors->first('movement_year.1', '<span class="error">:message</span>') !!}
                                                        <select class="form-control form-control-select half-width" id="second_month" name="movement_month[]">
                                                            <option value="">Select</option>
                                                             <?php for($m = 1; $m <= 12; $m++){
                                                                $month = date('F', strtotime("2017-$m-01"));
                                                                echo "<option value='$month'>$month</option>";
                                                            } ?>
                                                        </select>
                                                        {!! $errors->first('movement_month.1', '<span class="error">:message</span>') !!}
                                                   </th>

                                                   <th width="25%">
                                                        <select class="form-control form-control-select half-width" id="third_year" name="movement_year[]">
                                                            <option value="">Select</option>
                                                            <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                                echo "<option value='$i'>$i</option>";
                                                            } ?>

                                                        </select>
                                                        {!! $errors->first('movement_year.2', '<span class="error">:message</span>') !!}
                                                        <select class="form-control form-control-select half-width" id="third_month" name="movement_month[]">
                                                            <option value="">Select</option>
                                                             <?php for($m = 1; $m <= 12; $m++){
                                                                $month = date('F', strtotime("2017-$m-01"));
                                                                echo "<option value='$month'>$month</option>";
                                                            } ?>
                                                        </select>
                                                        {!! $errors->first('movement_month.2', '<span class="error">:message</span>') !!}
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
                                                   <th width="25%">
                                                      <select class="form-control form-control-select half-width" id="deb_first_year" name="major_deb_year[]">
                                                         <option value="">Select</option>
                                                         <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                            echo "<option value='$i'>$i</option>";
                                                            } ?>
                                                      </select>
                                                      {!! $errors->first('major_deb_year.0', '<span class="error">:message</span>') !!}
                                                      <select class="form-control form-control-select half-width" id="deb_first_month" name="major_deb_month[]">
                                                         <option value="">Select</option>
                                                         <?php for($m = 1; $m <= 12; $m++){
                                                            $month = date('F', strtotime("2017-$m-01"));
                                                            echo "<option value='$month'>$month</option>";
                                                            } ?>
                                                      </select>
                                                      {!! $errors->first('major_deb_month.0', '<span class="error">:message</span>') !!}
                                                   </th>
                                                   <th width="25%">
                                                      <select class="form-control form-control-select half-width" id="deb_second_year" name="major_deb_year[]">
                                                         <option value="">Select</option>
                                                         <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                            echo "<option value='$i'>$i</option>";
                                                            } ?>
                                                      </select>
                                                      {!! $errors->first('major_deb_year.1', '<span class="error">:message</span>') !!}
                                                      <select class="form-control form-control-select half-width" id="deb_second_month" name="major_deb_month[]">
                                                         <option value="">Select</option>
                                                         <?php for($m = 1; $m <= 12; $m++){
                                                            $month = date('F', strtotime("2017-$m-01"));
                                                            echo "<option value='$month'>$month</option>";
                                                            } ?>
                                                      </select>
                                                      {!! $errors->first('major_deb_month.1', '<span class="error">:message</span>') !!}
                                                   </th>
                                                   <th width="25%">
                                                      <select class="form-control form-control-select half-width" id="deb_third_year" name="major_deb_year[]">
                                                         <option value="">Select</option>
                                                         <?php for($i = $start_year; $i <= $curr_year; $i++){
                                                            echo "<option value='$i'>$i</option>";
                                                            } ?>
                                                      </select>
                                                      {!! $errors->first('major_deb_year.2', '<span class="error">:message</span>') !!}
                                                      <select class="form-control form-control-select half-width" id="deb_third_month" name="major_deb_month[]">
                                                         <option value="">Select</option>
                                                         <?php for($m = 1; $m <= 12; $m++){
                                                            $month = date('F', strtotime("2017-$m-01"));
                                                            echo "<option value='$month'>$month</option>";
                                                            } ?>
                                                      </select>
                                                      {!! $errors->first('major_deb_month.2', '<span class="error">:message</span>') !!}
                                                   </th>
                                                </tr>
                                             </thead>
                                             <tbody class="add-me" id="major_debtors">
                                             </tbody>
                                             <thead>
                                                <tr>
                                                   <td>TOTAL</td>
                                                   <td id="first_total">0</td>
                                                   <td id="second_total">0</td>
                                                   <td id="third_total">0</td>
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
                                                <tr>
                                                   <td width="30%">Average Payable Days: </td>
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
                                                      <select class="form-control form-control-select">
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
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header collapsed" role="tab" id="headingTwo" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                              <a class="collapsed" >
                              Profit and Loss
                              </a>
                           </div>
                           <div id="collapseTwo" class="collapse colsp" role="tabpanel" aria-labelledby="headingTwo" style="">
                              <div class="card-body pt-3 pl-0 pr-0 pb-0">
                                 <table class="table table-bordered overview-table " cellspacing="0">
                                    <tbody>
                                       <tr>
                                          <td colspan="4"><b>INCOME</b></td>
                                       </tr>
                                       <tr>
                                          <td>(I) Gross Domestic Sales </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(II) Export Sales</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Gross Sales</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>LESS: Excise duty</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Net Sales</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Increase in Net Sales (%)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>ADD: Trading / Other Operating Income</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">Export Incentives</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">Duty Drawback</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">Others</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Total Operating Income</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>INCREASE IN NET INCOME (%)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4" bgcolor="#e6e4e4"><b class="bold">COST OF SALES</b></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>(I) RAW MATERIALS</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5"> (a) Imported</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5"> (b) Indigenous</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>(II) OTHER SPARES</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(a) Imported</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5"> (b) Indigenous</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td><b>(III) POWER &amp; FUEL</b></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td><b>(IV) DIRECT LABOUR</b></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td><b>(V) OTHER MANUFACTURING EXPENSES</b></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td><b>(VI) DEPRECIATION</b></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td><b>(VII) REPAIRS &amp; MAINTENANCE</b></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td><b>(VIII) COST OF TRADING GOODS</b></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>SUB TOTAL</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>ADD: OPENING STOCK IN PROCESS</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>DEDUCT: CLOSING STOCK IN PROCESS</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>COST OF PRODUCTION:</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>C O P AS % OF GROSS INCOME</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>ADD: OPENING STOCK OF FINISHED GOODS</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>DEDUCT: CLOSING STOCK OF FINISHED GOODS</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>COST OF SALES:</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>COST OF SALES AS % OF GROSS INCOME</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>SELLING, GENERAL &amp; ADM EXPENSES</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Cost of Sales + SGA</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>PROFIT BEFORE INTEREST &amp; TAX (PBIT)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>PBIT AS % OF GROSS SALES</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>Interest payment to Banks</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">Interest - WC</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">Interest - Term Loans</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>Interest payment to FIs</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">Interest - WC</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">Interest - Term Loans</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">Bank Charges</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>INTEREST &amp; OTHER FINANCE CHARGES:</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>INTT. &amp; FIN. CHARGES AS % OF GROSS SALES</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>OPERATING PROFIT BEFORE TAX (OPBT)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>OPBT AS % OF GROSS INCOME</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4" bgcolor="#e6e4e4"><b class="bold">OTHER NON OPERATIVE INCOME</b></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>CASH INFLOW ITEMS</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(I) Interest On Deposits &amp; Dividend Received</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">     (II) Forex Gains</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(III) Non Operating Income from Subsidiaries</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">     (IV) Tax Refund</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">     (V) Misc Income</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">     (VI) Profit on sale of assets &amp; Investments</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Income </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>NON CASH INFLOW ITEMS</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(I) Provisions / Expenses Written Back</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL NON OPERATING INCOME</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4" bgcolor="#e6e4e4"><b class="bold">OTHER NON OPERATING EXP.</b></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>CASH OUTFLOW ITEMS</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(I) Loss on sale of Investments</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(II) Loss on sale of FA</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(III)  Derivative Losses booked</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(IV) Net Loss on Foreign Currency Translation and Transactions, Loss due to fire</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>NON CASH OUTFLOW ITEMS</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(I) Preli.Exp / One Time Expenses Written Off</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(II) Misc Exp. Written Off</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(III) Prov. for doub.debts &amp; Dim.in the val. of Inv.</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(IV) Wealth Tax</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL NON OPERATING EXPENSES</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>NET OF NON OPERATING INCOME/EXPENSES</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>PROFIT BEFORE INTEREST, DEPRECIATION &amp; TAX (PBIDT)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>PROFIT BEFORE TAX / LOSS (PBT)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>TAX PAID</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>PROVISION FOR TAXES - Current Period</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>-  Deffered Taxes</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>PROVISION FOR TAXES - TOTAL</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>NET PROFIT/LOSS (PAT)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>PAT AS % OF GROSS Income</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4" bgcolor="#e6e4e4"><b class="bold">Extraordinary Items adjustments:</b></td>
                                       </tr>
                                       <tr>
                                          <td>Extraordinary Income adjustments (+)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Extraordinary Expenses  adjustments (-)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Total Extraordinary items</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Adjusted PAT (excl Extraordinary  Items)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>EQUITY DIVIDEND PAID</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(I)   AMOUNT</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">   (II)  RATE</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Dividend tax</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Partners' withdrawal</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Dividend -Preference</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>RETAINED PROFIT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header collapsed" role="tab" id="headingThree" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                              <a class="collapsed" >
                              Balance Sheet
                              </a>
                           </div>
                           <div id="collapseThree" class="collapse colsp" role="tabpanel" aria-labelledby="headingThree" style="">
                              <div class="card-body pt-3 pl-0 pr-0 pb-0">
                                 <table class="table table-bordered overview-table" cellspacing="0">
                                    <tbody>
                                       <tr>
                                          <td colspan="4"><b>LIABILITIES</b></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>CURRENT LIABILITIES:</b><br>Short Term borrowings from banks (including bill purchased/discounted)</td>
                                       </tr>
                                       <tr>
                                          <td>(i) from applicant bank (CC / WCDL)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(ii) from other banks</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(of (i) and (ii) in which Bill purchased &amp; disc.)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td align="right">SUB TOTAL</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Sundry Creditors (Trade)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Short term borrowings from Associates &amp; Group Concerns</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Short Term borrowings / Commercial Paper</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Short term borrowings from Others</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Advances/ payments from customers/deposits from dealers.</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Provision for taxation</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Proposed dividend</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Statutory Liabilities( Due within One Year)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Installments of Term loans / Debentures / DPGs etc. (due within 1 year)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Deposits due for repayment (due within 1 year) </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Preference Shares redeemable (within 1 year)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL REPAYMENTS DUE WITHIN 1 YEAR</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Current liabilities &amp; provisions (due within 1 year)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Interest acc but not due</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Provision for NPA</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Provision for leave encashment &amp; gratuity</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Unclaimed dividend</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Liabilities </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Due to  Subsidiary companies/ affiliates</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Tax on Interim Dividend Payable </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td align="right">SUB TOTAL</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL CURRENT LIABILITIES</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>TERM LIABILITIES</b></td>
                                       </tr>
                                       <tr>
                                          <td>WCTL</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Pref. Shares (portion redeemable after 1 Yr)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Term Loans (Excluding installments payable within one year)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Term Loans - From Fis</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Debentures</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Term deposits</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Unsecured loans </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Borrowings from subsidiaries / affiliates (Quasi Equity)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Deposit from Dealers (only if considered as available for long term)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other term liabilities</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Deferred Tax Liability</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Loan &amp; Advances </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL TERM LIABILITIES</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL OUTSIDE LIABILITIES (TOL)</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>NET WORTH</b></td>
                                       </tr>
                                       <tr>
                                          <td>Partners capital / Proprietor's capital</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Share Capital (Paid-up)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Share Application (finalized for allotment)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Total Share Capital</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>RESERVES</b></td>
                                       </tr>
                                       <tr>
                                          <td>     Statutory and Capital Reserves</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>     General Reserve</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>     Revaluation Reserve</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td align="right">Sub Total</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Reserves ( Excluding provisions)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Surplus (+) or deficit (-) in P &amp; L Account</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Share Premium A/c</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Capital Subsidy</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Investment Allowance Utilization Reserve</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL NET WORTH</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL LIABILITIES</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>CONTINGENT LIABILITIES</b></td>
                                       </tr>
                                       <tr>
                                          <td>Arrears of cumulative dividends</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Disputed excise / customs / Income tax / Sales tax Liabilities</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Gratuity Liability not provided for</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Guarantees issued (relating to business)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Guarantees issued (for group companies)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>LCs</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>All other contingent liabilities -(incldg. Bills purchased - Under LC)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4" bgcolor="#e6e4e4"><b class="bold">ASSETS</b></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>CURRENT ASSETS</b></td>
                                       </tr>
                                       <tr>
                                          <td>Cash and Bank Balances</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>INVESTMENTS (Other than Long Term)</b></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">(i) Govt. &amp; other securities</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">    (ii) Fixed deposits with banks</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td class="pl-5">   (iii) Others</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>RECEIVABLES</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>RECEIVABLES other than deferred &amp; exports (Incl. bills purchased &amp; discounted by banks)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Export Receivables (including bill purchased and discounted)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Retention Money / Security Deposit</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>INVENTORY</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Raw Material - Indigenous</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Raw Material - Imported </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Stock in process</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Finished Goods</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Consumable spares - Indigenous</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Consumable spares - Imported</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td align="right">Sub Total: Other Consumable spares</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Other  stocks</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td align="right">Sub Total: Inventory</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Advances to suppliers of raw material</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Advance payment of tax</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other Current Assets:</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Interest Accrued</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Advance receivable in cash or kind</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Sundry Deposit</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Modvat Credit Receivable</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>Other current assets</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL CURRENT ASSETS</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>FIXED ASSETS</b></td>
                                       </tr>
                                       <tr>
                                          <td>(I) Land</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(ii) Building</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(iii) Vehicles</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(IV) Plant &amp; Machinery</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(v) Furniture &amp; Fixtures</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(vi) Other Fixed Assets</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(vii) Capital WIP </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>GROSS BLOCK</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Less: Accumulated Depreciation</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>NET BLOCK</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>OTHER NON CURRENT ASSETS</b></td>
                                       </tr>
                                       <tr>
                                          <td>(I) Investments in Subsidiary companies/ affiliates</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(ii) Other Investments &amp; Investment for acquisition </td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(iii) Due from subsidiaries</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(iv) Deferred receivables (maturity exceeding 1 year)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(v) Margin money kept with banks.</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(vi)Debtors more than 6 months</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(vii) Advance against mortgage of house property</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(viii) Deferred Revenue Expenditure</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(ix) Other Non current assets (surplus for Future expansion, Loans &amp; Advances non current in nature, ICD's, Dues from Directors)</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL OTHER NON CURRENT ASSETS</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4"><b>INTANGIBLE ASSETS (Patents, goodwill, prelim. expenses, bad/doubtful expenses not provided for)</b></td>
                                       </tr>
                                       <tr>
                                          <td>(i) Accumulated Losses, Preliminary expenses, Miscellaneous expenditure not w/off, Other deferred revenue expenses</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td>(ii) Deferred Tax Asset</td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                          <td align="right"></td>
                                       </tr>
                                       <tr>
                                          <td align="right">Sub Total</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>TOTAL ASSETS</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>TANGIBLE NETWORTH</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>Total Liabilities - Total Assets</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4" bgcolor="#e6e4e4"><b class="bold">Build Up of Current Assets</b></td>
                                       </tr>
                                       <tr>
                                          <td>Raw Material - Indigenous  AMOUNT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>MONTH'S CONSUMPTION</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>Raw Material - Imported AMOUNT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>MONTH'S CONSUMPTION</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>Consumable spares indigenous AMOUNT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>MONTH'S CONSUMPTION</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>Consumable spares- Imported AMOUNT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>MONTH'S CONSUMPTION</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>Stock in process - AMOUNT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>MONTH'S COST OF PRODUCTION</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>Finished Goods - AMOUNT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>MONTH'S COST OF SALES</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>RECEIVABLES (DOMESTIC) other than deferred &amp; exports (Incl. bills purchased &amp; discounted by banks)  AMOUNT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>MONTH'S DOMESTIC Income</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td>EXPORT RECV.(Incl. bills purchased &amp; discounted by banks)  AMOUNT</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00"></td>
                                       </tr>
                                       <tr>
                                          <td>MONTH'S EXPORT Income</td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                          <td align="right"><input type="text" class="form-control" value="0.00" disabled=""></td>
                                       </tr>
                                       <tr>
                                          <td colspan="4" bgcolor="#e6e4e4"><b class="bold">BUILD UP OF CURRENT LIABILITY</b></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>   
@endsection
@section('jscript')
<script type="text/javascript">
   appId = '{{$appId}}';
   appurl = '{{URL::route("financeAnalysis") }}';
   process_url = '{{URL::route("process_financial_statement") }}';
   _token = "{{ csrf_token() }}";
</script>

<script type="text/javascript">
   $(document).on('click', '.getAnalysis', function() {
      data = {appId, _token};
      $.ajax({
         url  : appurl,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
         },
         dataType : 'json',
         success:function(result) {
            console.log(result);
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
            if (result['status']) {
               window.open(result['value']['file_url'], '_blank');
            }
         },
         error:function(error) {
            // body...
         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })

   $(document).on('click', '.process_stmt', function() {
      biz_perfios_id = $(this).attr('pending');
      data = {appId, _token, biz_perfios_id};
      $.ajax({
         url  : process_url,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
         },
         dataType : 'json',
         success:function(result) {
            console.log(result);
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
            if (result['status']) {
               window.open(result['value']['file_url'], '_blank');
            }
         },
         error:function(error) {

         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })
</script>
@endsection