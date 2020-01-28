<?php 
   $curr_year = date('Y');
   $year_count = 3;
   $start_year = date('Y')-$year_count + 1;
   $class_enable = 'getAnalysis';
   extract(getFinancialDetailSummaryColumns());
   extract(getProfitandLossColumns());
?>
@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container">
        <div class="row mt-4">
           <div class="col-lg-12 col-12 mb-4">
              <div class="card">
                 <div class="card-body ">
                    <div class="row">
                       <div class="col-sm-12">
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
                         @endif
                         <div class="clearfix"></div>
                         <div style="text-align: right;">
                         @if(!empty($active_json_filename) && file_exists(storage_path("app/public/user/docs/$appId/finance/".$active_xlsx_filename)))
                               <a class="btn btn-success btn-sm" href="{{ Storage::url('user/docs/'.$appId.'/finance/'.$active_xlsx_filename) }}" download>Download</a>
                               <a class="btn btn-success btn-sm" href="javascript:void(0)"  data-toggle="modal" data-target="#uploadXLSXdoc" data-url ="{{route('upload_xlsx_document', ['app_id' => request()->get('app_id'),  'file_type' => 'finance']) }}" data-height="150px" data-width="100%">Upload XLSX</a>
                         @endif 
                         @if(request()->get('view_only') && !empty($pending_rec) && $pending_rec['status'] == 'fail')
                         @php $class_enable="disabled"; @endphp
                               <a class="btn btn-success btn-sm process_stmt" pending="{{ $pending_rec['biz_perfios_id'] }}" href="javascript:void(0)">Process</a>
                         @endif 
                         @if(request()->get('view_only') && $financedocs->count() > 0)
                            <a href="javascript:void(0)" class="btn btn-success btn-sm <?php echo $class_enable ?>">Get Analysis</a>
                         @endif
                         </div>
                         <div class="clearfix"></div>
                         <br/>
                         <hr>
                         <div id="paginate">
                            <?php 
                               echo $xlsx_pagination;
                            ?>
                         </div>
                         <div id="gridView">
                            <?php 
                              // echo $xlsx_html;
                            ?>
                         </div>
                          <div class="clearfix"></div>
                          <div id="accordion" role="tablist" aria-multiselectable="true" class="accordion">
                             <div class="card">
                                <div class="card-header" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" role="tab" id="headingOne">
                                   <a class="">
                                   Financial Detail Summary
                                   </a>
                                </div>
                                <div id="collapseOne" class="collapse show colsp" role="tabpanel" aria-labelledby="headingOne">
                                   <div class="card-body pt-3 pl-0 pr-0 pb-0">
                                      <div class="card">
                                         <table class="table table-bordered overview-table" cellspacing="0">
                                            <tbody>
                                               <tr>
                                                  <td><b>Name of the Borrower</b></td>
                                                  <td colspan="4">{{$borrower_name}}</td>
                                               </tr>
                                               <tr>
                                                  <td width="25%"><b>Latest Audited Financial Year</b></td>
                                                  <td width="15%">{{$latest_finance_year}}</td>
                                                  <td width="20%"><b>Projections Available for</b> </td>
                                                  <td width="20%">
                                                     <select class="form-control form-control-sm">
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
                                                  @foreach($audited_years as $aud_year)
                                                  <td bgcolor="#efefef" align="left">31-March-{{$aud_year}}</td>
                                                  @endforeach
                                               </tr>
                                               <tr>
                                                  <td bgcolor="#efefef" align="right">
                                                     <select class="form-control form-control-sm">
                                                        <option>Audited</option>
                                                        <option>Unaudited</option>
                                                     </select>
                                                  </td>
                                                  <td bgcolor="#efefef" align="right">
                                                     <select class="form-control form-control-sm">
                                                        <option>Audited</option>
                                                        <option>Unaudited</option>
                                                     </select>
                                                  </td>
                                                  <td bgcolor="#efefef" align="right">
                                                     <select class="form-control form-control-sm">
                                                        <option>Audited</option>
                                                        <option>Unaudited</option>
                                                     </select>
                                                  </td>
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
                                                  <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($performance_analysis_cols as $cols)
                                                           <tr>
                                                              <td height="46">{{$cols}}</td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @php $yearly_fin_data = getTotalFinanceData($fin_data) @endphp
                                                          @foreach($performance_analysis_cols as $key => $cols)
                                                            <tr>
                                                              <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_fin_data[$key])}}"></td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                               </tr>
                                               <tr>
                                                  <td colspan="4"><b>(B) PROFITABILITY ANALYSIS</b></td>
                                               </tr>
                                               <tr>
                                                  <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($profitability_analysis_cols as $cols)
                                                           <tr>
                                                              <td height="46">{{$cols}}</td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @php $yearly_fin_data = getTotalFinanceData($fin_data) @endphp
                                                          @foreach($profitability_analysis_cols as $key => $cols)
                                                            <tr>
                                                              <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_fin_data[$key])}}"></td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                               </tr>
                                               <tr>
                                                  <td colspan="4"><b>(C) GROWTH ANALYSIS</b></td>
                                               </tr>
                                               <tr>
                                                  <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($growth_analysis_cols as $cols)
                                                           <tr>
                                                              <td height="46">{{$cols}}</td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @php $yearly_growth_data = $growth_data[$year] @endphp
                                                          @foreach($growth_analysis_cols as $key => $cols)
                                                            <tr>
                                                              <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_growth_data[$key])}}" atttr="{{$key}}"></td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                               </tr>
                                               <tr>
                                                  <td colspan="4"><b>(D) FINANCIAL POSITION ANALYSIS</b></td>
                                               </tr>
                                               <tr>
                                                  <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($financial_position_analysis_cols as $cols)
                                                           <tr>
                                                              <td height="46">{{$cols}}</td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @php $yearly_fin_data = getTotalFinanceData($fin_data) @endphp
                                                          @foreach($financial_position_analysis_cols as $key => $cols)
                                                            <tr>
                                                              <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_fin_data[$key])}}"></td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                               </tr>
                                               <tr>
                                                  <td colspan="4"><b>(E) LEVERAGE ANALYSIS</b></td>
                                               </tr>
                                                <tr>
                                                  <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($leverage_analysis_cols as $cols)
                                                           <tr>
                                                              <td height="46">{{$cols}}</td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @php $yearly_fin_data = getTotalFinanceData($fin_data) @endphp
                                                          @foreach($leverage_analysis_cols as $key => $cols)
                                                            <tr>
                                                              <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_fin_data[$key])}}"></td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                               </tr>
                                               <tr>
                                                  <td colspan="4"><b>(F) ACTIVITY EFFICIENCY ANALYSIS</b></td>
                                               </tr>
                                               <tr>
                                                  <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($activity_efficiency_analysis_cols as $cols)
                                                           <tr>
                                                              <td height="46">{{$cols}}</td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @php $yearly_fin_data = getTotalFinanceData($fin_data) @endphp
                                                          @foreach($activity_efficiency_analysis_cols as $key => $cols)
                                                            <tr>
                                                              <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_fin_data[$key])}}"></td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                               </tr>
                                               <tr>
                                                  <td colspan="4"><b>(G) FUNDS FLOW ANALYSIS</b></td>
                                               </tr>
                                               <tr>
                                                  <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($fundsFlowAnalysis_cols as $cols)
                                                           <tr>
                                                              <td height="46">{{$cols}}</td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @php $yearly_fin_data = getTotalFinanceData($fin_data) @endphp
                                                          @foreach($fundsFlowAnalysis_cols as $key => $cols)
                                                            <tr>
                                                              <td height="46" align="right"><input type="text" class="form-control form-control-sm" disabled value="{{sprintf('%.2f', $yearly_fin_data[$key])}}"></td>
                                                           </tr>
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                               </tr>
                                            </tbody>
                                         </table>
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
                                              <td colspan="4" bgcolor="#e6e4e4"><b>INCOME</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    @foreach($income_cols as $income_col)
                                                    <tr>
                                                       <td height="46">{{$income_col}}</td>
                                                    </tr>
                                                    @endforeach
                                                 </table>
                                              </td>
                                              @foreach($finance_data as $year => $fin_data)
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      <tbody>
                                                        @foreach($income_cols as $key => $income_col)
                                                          <tr>
                                                            <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : '' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data) : $key)}}"></td>
                                                         </tr>
                                                         @endforeach
                                                      </tbody>
                                                   </table>
                                                </td>
                                                @endforeach
                                           </tr>

                                           <tr>
                                              <td colspan="4" bgcolor="#e6e4e4"><b>COST OF SALES</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    @foreach($costofsales_cols as $col_key => $costofsales_col)
                                                    @if(is_array($costofsales_col))
                                                    <tr>
                                                       <td height="46" colspan="4"><b>{{implode(' ',preg_split('/(?=[A-Z])/',$col_key))}}</b></td>
                                                    </tr>
                                                    @foreach($costofsales_col as $arr_key => $arr_val)
                                                    <tr>
                                                       <td height="46">{{$arr_key}}</td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                       <td height="46">{{$costofsales_col}}</td>
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                 </table>
                                              </td>
                                              @foreach($finance_data as $year => $fin_data)
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      <tbody>
                                                        @foreach($costofsales_cols as $key => $costofsales_col)
                                                        @if(is_array($costofsales_col))
                                                          <tr>
                                                             <td height="46" style="border-left:none;">&nbsp;</td>
                                                          </tr>
                                                          @foreach($costofsales_col as $arr_key => $arr_val)
                                                          <tr>
                                                            <td height="46" align="right"><input type="text" class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key][$arr_key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key][$arr_key]) : (function_exists($arr_key) ? $arr_key($fin_data) : $arr_key)}}"></td>
                                                         </tr>
                                                          @endforeach
                                                          @else
                                                          <tr>
                                                            <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : '' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data) : $key)}}"></td>
                                                         </tr>
                                                          @endif
                                                         @endforeach
                                                      </tbody>
                                                   </table>
                                                </td>
                                                @endforeach
                                           </tr>
                                           <tr>
                                              <td colspan="4" bgcolor="#e6e4e4"><b class="bold">OTHER NON OPERATIVE INCOME</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    @foreach($othernonoperativeincome_cols as $col_key => $operativeincome_col)
                                                    <tr>
                                                       <td height="46">{{$operativeincome_col}}</td>
                                                    </tr>
                                                    @endforeach
                                                 </table>
                                              </td>
                                              @foreach($finance_data as $year => $fin_data)
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      <tbody>
                                                        @foreach($othernonoperativeincome_cols as $key => $operativeincome_col)
                                                          <tr>
                                                            <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : '' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data) : $key)}}"></td>
                                                         </tr>
    
                                                         @endforeach
                                                      </tbody>
                                                   </table>
                                                </td>
                                                @endforeach
                                           </tr>


                                           <tr>
                                              <td colspan="4" bgcolor="#e6e4e4"><b class="bold">OTHER NON OPERATING EXP.</b></td>
                                           </tr>
                                          <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    @foreach($othernonoperatingexp_cols as $col_key => $operativeExpenses_col)
                                                    <tr>
                                                       <td height="46">{{$operativeExpenses_col}}</td>
                                                    </tr>
                                                    @endforeach
                                                 </table>
                                              </td>
                                              @foreach($finance_data as $year => $fin_data)
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      <tbody>
                                                        @foreach($othernonoperatingexp_cols as $key => $operativeExpenses_col)
                                                          <tr>
                                                            <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : '' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data) : $key)}}"></td>
                                                         </tr>
    
                                                         @endforeach
                                                      </tbody>
                                                   </table>
                                                </td>
                                                @endforeach
                                           </tr>
                                           <tr>
                                              <td colspan="4" bgcolor="#e6e4e4"><b class="bold">Extraordinary Items adjustments:</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    @foreach($extraordinaryitemadjustments_cols as $col_key => $extraadjusted_col)
                                                    <tr>
                                                       <td height="46">{{$extraadjusted_col}}</td>
                                                    </tr>
                                                    @endforeach
                                                 </table>
                                              </td>
                                              @foreach($finance_data as $year => $fin_data)
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      <tbody>
                                                        @foreach($extraordinaryitemadjustments_cols as $key => $extraadjusted_col)
                                                          <tr>
                                                            <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : '' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data) : $key)}}"></td>
                                                         </tr>
    
                                                         @endforeach
                                                      </tbody>
                                                   </table>
                                                </td>
                                                @endforeach
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>EQUITY DIVIDEND PAID</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    @foreach($equityDividendPaid_cols as $col_key => $equityDividend_col)
                                                    <tr>
                                                       <td height="46">{{$equityDividend_col}}</td>
                                                    </tr>
                                                    @endforeach
                                                 </table>
                                              </td>
                                              @foreach($finance_data as $year => $fin_data)
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      <tbody>
                                                        @foreach($equityDividendPaid_cols as $key => $equityDividend_col)
                                                          <tr>
                                                            <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : '' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data) : $key)}}"></td>
                                                         </tr>
    
                                                         @endforeach
                                                      </tbody>
                                                   </table>
                                                </td>
                                                @endforeach
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
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>(i) from applicant bank (CC / WCDL)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(ii) from other banks</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(of (i) and (ii) in which Bill purchased & disc.)</td>
                                                    </tr>
                                                    <tr>
                                                       <td align="right" height="46">SUB TOTAL</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Sundry Creditors (Trade)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Short term borrowings from Associates & Group Concerns</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Short Term borrowings / Commercial Paper</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Short term borrowings from Others</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Advances/ payments from customers/deposits from dealers.</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Provision for taxation</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Proposed dividend</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other Statutory Liabilities( Due within One Year)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Installments of Term loans / Debentures / DPGs etc. (due within 1 year)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Deposits due for repayment (due within 1 year)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Preference Shares redeemable (within 1 year)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>TOTAL REPAYMENTS DUE WITHIN 1 YEAR</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other Current liabilities & provisions (due within 1 year)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Interest acc but not due</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Provision for NPA</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Provision for leave encashment & gratuity</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Unclaimed dividend</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other Liabilities</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Due to Subsidiary companies/ affiliates</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Tax on Interim Dividend Payable</td>
                                                    </tr>
                                                    <tr>
                                                       <td align="right" height="46">SUB TOTAL</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">TOTAL CURRENT LIABILITIES</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>TERM LIABILITIES</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>WCTL</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Pref. Shares (portion redeemable after 1 Yr)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Term Loans (Excluding installments payable within one year)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Term Loans - From Fis</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Debentures</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Term deposits</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Unsecured loans</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Borrowings from subsidiaries / affiliates (Quasi Equity)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Deposit from Dealers (only if considered as available for long term)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other term liabilities</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Deferred Tax Liability</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other Loan & Advances</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">TOTAL TERM LIABILITIES</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">TOTAL OUTSIDE LIABILITIES (TOL)</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>NET WORTH</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>Partners capital / Proprietor's capital</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Share Capital (Paid-up)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Share Application (finalized for allotment)</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Total Share Capital</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td ><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>RESERVES</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>Statutory and Capital Reserves</td>
                                                    </tr>
                                                    <tr>
                                                       <td>General Reserve</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Revaluation Reserve</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Sub Total</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other Reserves ( Excluding provisions)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Surplus (+) or deficit (-) in P & L Account</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Share Premium A/c</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Capital Subsidy</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Investment Allowance Utilization Reserve</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">TOTAL NET WORTH</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">TOTAL LIABILITIES</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>CONTINGENT LIABILITIES</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>Arrears of cumulative dividends</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Disputed excise / customs / Income tax / Sales tax Liabilities</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Gratuity Liability not provided for</td>
                                                    </tr>
                                                    <tr>
                                                       <td >Guarantees issued (relating to business)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Guarantees issued (for group companies)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>LCs</td>
                                                    </tr>
                                                    <tr>
                                                       <td>All other contingent liabilities -(incldg. Bills purchased - Under LC)</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4" bgcolor="#e6e4e4"><b class="bold">ASSETS</b></td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>CURRENT ASSETS</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>Cash and Bank Balances</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>INVESTMENTS (Other than Long Term)</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>(i) Govt. & other securities</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(ii) Fixed deposits with banks</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(iii) Others</td>
                                                    </tr>
                                                    <tr>
                                                       <td>RECEIVABLES</td>
                                                    </tr>
                                                    <tr>
                                                       <td>RECEIVABLES other than deferred & exports (Incl. bills purchased & discounted by banks)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Export Receivables (including bill purchased and discounted)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Retention Money / Security Deposit</td>
                                                    </tr>
                                                    <tr>
                                                       <td>INVENTORY</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Raw Material - Indigenous</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Raw Material - Imported</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Stock in process</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Finished Goods</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other Consumable spares - Indigenous</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other Consumable spares - Imported</td>
                                                    </tr>
                                                    <tr>
                                                       <td align="right" height="46">Sub Total: Other Consumable spares</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other stocks</td>
                                                    </tr>
                                                    <tr>
                                                       <td align="right" height="46">Sub Total: Inventory</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Advances to suppliers of raw material</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Advance payment of tax</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other Current Assets:</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Interest Accrued</td>
                                                    </tr>
                                                    <tr>
                                                       <td align="right">Advance receivable in cash or kind</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Sundry Deposit</td>
                                                    </tr>
                                                    <tr>
                                                       <td align="right">Modvat Credit Receivable</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Other current assets</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">TOTAL CURRENT ASSETS</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>FIXED ASSETS</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>(I) Land</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(ii) Building</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(iii) Vehicles</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(IV) Plant & Machinery</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(v) Furniture & Fixtures</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(vi) Other Fixed Assets</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(vii) Capital WIP</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">GROSS BLOCK</td>
                                                    </tr>
                                                    <tr>
                                                       <td>Less: Accumulated Depreciation</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">NET BLOCK</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>OTHER NON CURRENT ASSETS</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>(I) Investments in Subsidiary companies/ affiliates</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(ii) Other Investments & Investment for acquisition</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(iii) Due from subsidiaries</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(iv) Deferred receivables (maturity exceeding 1 year)</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(v) Margin money kept with banks.</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(vi)Debtors more than 6 months</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(vii) Advance against mortgage of house property</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(viii) Deferred Revenue Expenditure</td>
                                                    </tr>
                                                    <tr>
                                                       <td >(ix) Other Non current assets (surplus for Future expansion, Loans & Advances non current in nature, ICD's, Dues from Directors)</td>
                                                    </tr>
                                                    <tr>
                                                       <td >TOTAL OTHER NON CURRENT ASSETS</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="59">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="59">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="59">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4"><b>INTANGIBLE ASSETS (Patents, goodwill, prelim. expenses, bad/doubtful expenses not provided for)</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td>(i) Accumulated Losses, Preliminary expenses, Miscellaneous expenditure not w/off, Other deferred revenue expenses</td>
                                                    </tr>
                                                    <tr>
                                                       <td>(ii) Deferred Tax Asset</td>
                                                    </tr>
                                                    <tr>
                                                       <td align="right" height="46">Sub Total</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">TOTAL ASSETS</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">TANGIBLE NETWORTH</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Total Liabilities - Total Assets</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td height="59">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td height="59">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td height="59">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                           </tr>
                                           <tr>
                                              <td colspan="4" bgcolor="#e6e4e4"><b class="bold">Build Up of Current Assets</b></td>
                                           </tr>
                                           <tr>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td height="46">Raw Material - Indigenous AMOUNT</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">MONTH'S CONSUMPTION</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Raw Material - Imported AMOUNT</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">MONTH'S CONSUMPTION</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Consumable spares indigenous AMOUNT</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">MONTH'S CONSUMPTION</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Consumable spares- Imported AMOUNT</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">MONTH'S CONSUMPTION</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Stock in process - AMOUNT</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">MONTH'S COST OF PRODUCTION</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">Finished Goods - AMOUNT</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">MONTH'S COST OF SALES</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="59">RECEIVABLES (DOMESTIC) other than deferred & exports (Incl. bills purchased & discounted by banks) AMOUNT</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">MONTH'S DOMESTIC Income</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">EXPORT RECV.(Incl. bills purchased & discounted by banks) AMOUNT</td>
                                                    </tr>
                                                    <tr>
                                                       <td height="46">MONTH'S EXPORT Income</td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td height="59"><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td height="59"><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                 </table>
                                              </td>
                                              <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                 <table class="table-border-none" width="100%">
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td height="59"><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                    </tr>
                                                    <tr>
                                                       <td><input type="text" class="form-control form-control-sm" placeholder="0.00" disabled=""></td>
                                                    </tr>
                                                 </table>
                                              </td>
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
                          <div class="table-responsive ps ps--theme_default">
                            <form method="post" action="{{ route('save_finance_detail') }}">
                              @csrf
                              <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}"> 
                              <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}"> 
                               <input type="hidden" name="fin_detail_id" value="{{isset($finDetailData->fin_detail_id) ? $finDetailData->fin_detail_id : ''}}" />      
                             <table id="supplier-listing" class="table table-striped cell-border  no-footer overview-table mb-3" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                   <tr role="row">
                                       <th>Parameter</th>
                                       <th class="sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1">Criteria</th>
                                       <th class="sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1"> Deviation</th>
                                       <th class="sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1"> Remarks</th>
                                   </tr>
                                </thead>
                                <tbody>
                                   <tr role="row" class="odd">
                                       <td>Adjusted Tangible Net Worth</td>
                                       <td>Positive for last 2 financial years</td>
                                       <td>
                                          <span class="mr-2">
                                          <input type="radio" class="" name="adj_net_worth_check" id="adj_net_worth_check_yes" value="Yes" {{isset($finDetailData->adj_net_worth_check) && $finDetailData->adj_net_worth_check == 'Yes' ? 'checked' : ''}}>
                                          <label for="adj_net_worth_check_yes">Yes</label>
                                          </span>
                                          <span>
                                          <input type="radio" class="" name="adj_net_worth_check" id="adj_net_worth_check_no" value="No" {{!isset($finDetailData->adj_net_worth_check) || $finDetailData->adj_net_worth_check == 'No' ? 'checked' : ''}}>
                                          <label for="adj_net_worth_check_no">No</label>
                                          </span>
                                       </td>
                                       <td><input type="text" class="form-control from-inline" id="adj_net_worth_cmnt" name="adj_net_worth_cmnt" value="{{isset($finDetailData->adj_net_worth_cmnt) ? $finDetailData->adj_net_worth_cmnt : ''}}"></td>                    
                                    </tr>                 
                                    <tr role="row" class="odd">
                                       <td>Cash Profit</td>
                                       <td>Positive for 2 out of last 3 financial years (positive in last year)</td>
                                       <td>
                                          <span class="mr-2">
                                          <input type="radio" class="" name="cash_profit_check" id="cash_profit_check_yes" value="Yes" {{isset($finDetailData->cash_profit_check) && $finDetailData->cash_profit_check == 'Yes' ? 'checked' : ''}}>
                                          <label for="cash_profit_check_yes">Yes</label></span>
                                          <span>
                                          <input type="radio" class="" name="cash_profit_check" id="cash_profit_check_no" value="No" {{!isset($finDetailData->cash_profit_check) || $finDetailData->cash_profit_check == 'No' ? 'checked' : ''}}>
                                          <label for="cash_profit_check_no">No</label></span>
                                       </td>
                                       <td><input type="text" class="form-control from-inline" id="cash_profit_cmnt" name="cash_profit_cmnt" value="{{isset($finDetailData->cash_profit_cmnt) ? $finDetailData->cash_profit_cmnt : ''}}"></td>                    
                                    </tr>
                                    <tr role="row" class="odd">
                                       <td>DSCR</td>
                                       <td>>1.2X</td>
                                       <td>
                                          <span class="mr-2">
                                          <input type="radio" class="" name="dscr_check" id="dscr_check_yes" value="Yes" {{isset($finDetailData->dscr_check) && $finDetailData->dscr_check == 'Yes' ? 'checked' : ''}}>
                                          <label for="dscr_check_yes">Yes</label></span>
                                          <span><input type="radio" class="" name="dscr_check" id="dscr_check_no" value="No" {{!isset($finDetailData->dscr_check) || $finDetailData->dscr_check == 'No' ? 'checked' : ''}}>
                                          <label for="dscr_check_no">No</label></span>
                                       </td>
                                       <td><input type="text" class="form-control from-inline" id="dscr_cmnt" name="dscr_cmnt" value="{{isset($finDetailData->dscr_cmnt) ? $finDetailData->dscr_cmnt : ''}}"></td>                    
                                    </tr>
                                    <tr role="row" class="odd">
                                       <td>Debt/EBIDTA</td>
                                       <td><5X</td>
                                       <td>
                                          <span class="mr-2">
                                          <input type="radio" class="" name="debt_check" id="debt_check_yes" value="Yes" {{isset($finDetailData->debt_check) && $finDetailData->debt_check == 'Yes' ? 'checked' : ''}}>
                                          <label for="debt_check_yes">Yes</label></span>
                                          <span><input type="radio" class="" name="debt_check" id="debt_check_no" value="No" {{!isset($finDetailData->debt_check) || $finDetailData->debt_check == 'No' ? 'checked' : ''}}>
                                          <label for="debt_check_no">No</label></span>
                                       </td>
                                       <td><input type="text" class="form-control from-inline" id="debt_cmnt" name="debt_cmnt" value="{{isset($finDetailData->debt_cmnt) ? $finDetailData->debt_cmnt : ''}}"></td>                    
                                    </tr>
                                </tbody>
                             </table>
                             <div class="data mt-4">
                                <h2 class="sub-title bg">Risk Comments on Financials</h2>
                                <div class="pl-4 pr-4 pb-4 pt-2">
                                   <textarea class="form-control form-control-sm" id="financial_risk_comments" name="financial_risk_comments" rows="3" value="" spellcheck="false">{{isset($finDetailData->financial_risk_comments) ? $finDetailData->financial_risk_comments : ''}}</textarea>
                                   <div class="clearfix"></div>
                                </div>
                             </div>
                             <button type="submit" class="btn btn-success btn-sm float-right mt-2 mb-3"> Save</button>
                           </form>
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
   </div>
</div> 
{!!Helpers::makeIframePopup('uploadXLSXdoc','Upload XLSX Document', 'modal-md')!!}  
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
<script src="https://cdn.ckeditor.com/4.13.1/standard-all/ckeditor.js"></script>
<script>
  CKEDITOR.replace('financial_risk_comments', {
    fullPage: true,
    extraPlugins: 'docprops',
    allowedContent: true,
    height: 320
  });
</script>
@endsection