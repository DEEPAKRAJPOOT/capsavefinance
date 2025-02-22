<?php 
   $curr_year = date('Y');
   $year_count = 3;
   $start_year = date('Y')-$year_count + 1;
   $class_enable = 'getAnalysis';
   extract(getFinancialDetailSummaryColumns());
   extract(getProfitandLossColumns());
   extract(getBalanceSheetLiabilitiesColumns());
   extract(getBalanceSheetAssetsColumns());
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
                         @php
                        $message = (!empty($callBackMessage) ? "<span style='color:red'>$callBackMessage</span>" : '');
                        @endphp
                         <div id="pullMsg">@php echo $message @endphp</div>
                         <h2 class="sub-title bg mb-4">Uploaded Finance statement-</h2>
                         <div class="clearfix"></div>
                         @if($financedocs->count() > 0)
                         @foreach($financedocs as $financedoc)
                         <div class="doc" style="text-align: center;">
                            <small>{{ $financedoc->finc_year }}</small>
                            <ul>
                               <li><span class="icon"><i class="fa fa-file-pdf-o"></i></span></li>
                               <li>
                               @can('download_fin_stmt_doc')
                               <a href="{{ route('download_fin_stmt_doc', ['file_id' => $financedoc->file_id]) }}" download="{{$financedoc->file_name}}" target="_blank">Download Finance Statement</a>
                               @endcan
                               </li>
                               <li><a href="javascript:void(0)"></a></li>
                            </ul>
                         </div>
                         @endforeach
                         @endif
                         <div class="clearfix"></div>
                         <div style="text-align: right;">
                         @if(!empty($active_json_filename) && !empty($active_xlsx_filename) && Storage::exists("public/user/docs/$appId/finance/".$active_xlsx_filename))
                               <a class="btn btn-success btn-sm" href="{{ Storage::url('public/user/docs/'.$appId.'/finance/'.$active_xlsx_filename) }}" download>Download</a>
                         @endif 
                         @if(request()->get('view_only'))
                         @can('upload_xlsx_document_finance')
                               <a class="btn btn-success btn-sm" href="javascript:void(0)"  data-toggle="modal" data-target="#uploadXLSXdoc" data-url ="{{route('upload_xlsx_document_finance', ['app_id' => request()->get('app_id'),  'file_type' => 'finance']) }}" data-height="150px" data-width="100%">Upload XLSX</a>
                         @endcan
                         @endif
                         @if(request()->get('view_only') && empty($message) && !empty($pending_rec) && $pending_rec['status'] == 'fail')
                         @php $class_enable="disabled"; @endphp
                               <a class="btn btn-success btn-sm process_stmt" pending="{{ $pending_rec['biz_perfios_id'] }}" href="javascript:void(0)">Process</a>
                         @endif 
                         @if(request()->get('view_only') && $financedocs->count() > 0)
                           @can('financeAnalysis')
                              <a href="javascript:void(0)" class="btn btn-success btn-sm <?php echo $class_enable ?>">Get Analysis</a>
                           @endcan
                         @endif
                         </div>
                         <div class="clearfix"></div>
                         <br />
                          <form method="post" action="{{ route('save_finance_detail') }}" class="fin-new-height">
                            <div id="accordion" role="tablist" aria-multiselectable="true" class="accordion">
                               <div class="card">
                                  <div class="card-header" data-toggle="collapse" data-parent="#accordion" href="#collapseZero" aria-expanded="true" aria-controls="collapseZero" role="tab" id="headingZero">
                                     <a class="">
                                     View XLSX
                                     </a>
                                  </div>
                                  <div id="collapseZero" class="collapse show colsp" role="tabpanel" aria-labelledby="headingZero">
                                    <div class="card-body pt-3 pl-0 pr-0 pb-0">
                                      <div class="card">
                                        @if(!empty($xlsx_pagination))
                                          <div id="paginate">
                                            <?php 
                                               echo $xlsx_pagination;
                                            ?>
                                         </div>
                                         @endif
                                         <div id="gridView">
                                            <?php 
                                               echo $xlsx_html;
                                            ?>
                                         </div>
                                      </div>
                                    </div>
                                  </div>
                               </div>
                               <div class="card">
                                  <div class="card-header collapsed" data-toggle="collapse" data-parent="#accordion" role="tab" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" id="headingOne">
                                     <a class="">
                                     Financial Detail Summary
                                     </a>
                                  </div>
                                  <div id="collapseOne" class="collapse colsp" role="tabpanel" aria-labelledby="headingOne">
                                     <div class="card-body pt-3 pl-0 pr-0 pb-0">
                                       <table class="table table-bordered overview-table mt-3" cellspacing="0">
                                            <tbody>
                                               <tr>
                                                  <td><b>Name of the Borrower</b></td>
                                                  <td colspan="4"><input type="text" class="form-control form-control-sm" value="{{$borrower_name}}" name="borrower_name"></td>
                                               </tr>
                                               <tr>
                                                  <td width="25%"><b>Latest Audited Financial Year</b></td>
                                                  <td width="14%">{{$latest_finance_year}}</td>
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
                                         <table class="table table-bordered overview-table mt-3" cellspacing="0">
                                            <tbody>
                                                 <tr>
                                                    <td rowspan="2" valign="middle" bgcolor="#efefef" width="39%">Financial Spread Sheet for the period ended</td>
                                                    @foreach($finance_data as $year => $fin_data)
                                                    <td width="20%" bgcolor="#efefef" align="left">31-March-{{$year}} <br /><br />
                                                      <select class="form-control form-control-sm"  name="year[{{$year}}][FinancialYearAudited]">
                                                            <option value = "0" {{ ($fin_data['FinancialYearAudited'] ?? NULL) == '0'? 'Selected' :''}}>Unaudited</option>
                                                            <option value = "1" {{ ($fin_data['FinancialYearAudited'] ?? NULL) == '1'? 'Selected':''}}>Audited</option>
                                                      </select>
                                                    </td>
                                                    @endforeach
                                                 </tr>
                                              </tbody>
                                         </table>
                                         <table class="table table-bordered overview-table mt-3 " cellspacing="0">
                                            <tbody>
                                               <tr>
                                                  <td colspan="4" bgcolor="#e6e4e4"><b>(A)  PERFORMANCE ANALYSIS</b></td>
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
                                                          @php 
                                                          $finance_data_prev = (!empty($finance_data[$year-1]) ? $finance_data[$year-1] : array());
                                                          $yearly_fin_data = getTotalFinanceData($fin_data, $finance_data_prev);
                                                          @endphp
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
                                                  <td colspan="4" bgcolor="#e6e4e4"><b>(B) PROFITABILITY ANALYSIS</b></td>
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
                                                  <td colspan="4" bgcolor="#e6e4e4"><b>(C) GROWTH ANALYSIS</b></td>
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
                                                  <td colspan="4" bgcolor="#e6e4e4"><b>(D) FINANCIAL POSITION ANALYSIS</b></td>
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
                                                  <td colspan="4" bgcolor="#e6e4e4"><b>(E) LEVERAGE ANALYSIS</b></td>
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
                                                  <td colspan="4" bgcolor="#e6e4e4"><b>(F) ACTIVITY EFFICIENCY ANALYSIS</b></td>
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
                                                  <td colspan="4" bgcolor="#e6e4e4"><b>(G) FUNDS FLOW ANALYSIS</b></td>
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
                                                          @php $yearly_growth_data = $growth_data[$year] @endphp
                                                          @foreach($income_cols as $key => $income_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][ProfitAndLoss]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data['ProfitAndLoss'], $yearly_growth_data) : $key)}}"></td>
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
                                                              <td height="46" align="right"><input type="text" <?php echo function_exists($arr_key) ? 'disabled' : 'name="year['.$year.'][ProfitAndLoss]['.$key.']['.$arr_key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key][$arr_key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key][$arr_key]) : (function_exists($arr_key) ? $arr_key($fin_data['ProfitAndLoss']) : $arr_key)}}"></td>
                                                           </tr>
                                                            @endforeach
                                                            @else
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][ProfitAndLoss]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data['ProfitAndLoss']) : $key)}}"></td>
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
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][ProfitAndLoss]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data['ProfitAndLoss']) : $key)}}"></td>
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
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][ProfitAndLoss]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data['ProfitAndLoss']) : $key)}}"></td>
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
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][ProfitAndLoss]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data['ProfitAndLoss']) : $key)}}"></td>
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
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][ProfitAndLoss]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['ProfitAndLoss'][$key]) ? sprintf('%.2f', $fin_data['ProfitAndLoss'][$key]) : (function_exists($key) ? $key($fin_data['ProfitAndLoss']) : $key)}}"></td>
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
                                                <td colspan="4" bgcolor="#e6e4e4"><b>LIABILITIES</b></td>
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>CURRENT LIABILITIES:</b><br>Short Term borrowings from banks (including bill purchased/discounted)</td>
                                             </tr>
                                             <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($currentLiabilities_cols as $col_key => $currentLiabilities_col)
                                                      <tr>
                                                         <td height="46">{{$currentLiabilities_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($currentLiabilities_cols as $key => $currentLiabilities_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Liabilities]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Liabilities'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Liabilities'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Liabilities']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>TERM LIABILITIES</b></td>
                                             </tr>
                                              <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($termLiabilities_cols as $col_key => $termLiabilities_col)
                                                      <tr>
                                                         <td height="46">{{$termLiabilities_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($termLiabilities_cols as $key => $termLiabilities_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Liabilities]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Liabilities'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Liabilities'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Liabilities']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>NET WORTH</b></td>
                                             </tr>
                                              <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($networthLiabilities_cols as $col_key => $networthLiabilities_col)
                                                      <tr>
                                                         <td height="46">{{$networthLiabilities_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($networthLiabilities_cols as $key => $networthLiabilities_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Liabilities]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Liabilities'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Liabilities'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Liabilities']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>RESERVES</b></td>
                                             </tr>
                                             <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($reserveLiabilities_cols as $col_key => $reserveLiabilities_col)
                                                      <tr>
                                                         <td height="46">{{$reserveLiabilities_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($reserveLiabilities_cols as $key => $reserveLiabilities_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Liabilities]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Liabilities'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Liabilities'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Liabilities']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>CONTINGENT LIABILITIES</b></td>
                                             </tr>
                                              <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($contingentLiabilities_cols as $col_key => $contingentLiabilities_col)
                                                      <tr>
                                                         <td height="46">{{$contingentLiabilities_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($contingentLiabilities_cols as $key => $contingentLiabilities_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Liabilities]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Liabilities'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Liabilities'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Liabilities']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
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
                                                      @foreach($assetsCurrent_cols as $col_key => $assetsCurrent_col)
                                                      <tr>
                                                         <td height="46">{{$assetsCurrent_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($assetsCurrent_cols as $key => $assetsCurrent_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Assets]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Assets'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Assets'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Assets']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>INVESTMENTS (Other than Long Term)</b></td>
                                             </tr>
                                             <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($aasetsInvestments_cols as $col_key => $aasetsInvestments_col)
                                                      <tr>
                                                         <td height="46">{{$aasetsInvestments_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($aasetsInvestments_cols as $key => $aasetsInvestments_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Assets]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Assets'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Assets'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Assets']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>FIXED ASSETS</b></td>
                                             </tr>
                                              <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($aasetsFixed_cols as $col_key => $aasetsFixed_col)
                                                      <tr>
                                                         <td height="46">{{$aasetsFixed_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($aasetsFixed_cols as $key => $aasetsFixed_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Assets]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Assets'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Assets'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Assets'],$fin_data['BalanceSheet']['Liabilities']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>OTHER NON CURRENT ASSETS</b></td>
                                             </tr>
                                             <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($otherNonCurrentAssets as $col_key => $otherNonCurrentAsset)
                                                      <tr>
                                                         <td height="46">{{$otherNonCurrentAsset}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($otherNonCurrentAssets as $key => $otherNonCurrentAsset)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Assets]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Assets'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Assets'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Assets']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                                <td colspan="4"><b>INTANGIBLE ASSETS (Patents, goodwill, prelim. expenses, bad/doubtful expenses not provided for)</b></td>
                                             </tr>
                                             <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($inTangibleAssets_cols as $col_key => $inTangibleAssets_col)
                                                      <tr>
                                                         <td height="46">{{$inTangibleAssets_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($inTangibleAssets_cols as $key => $inTangibleAssets_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Assets]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Assets'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Assets'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Assets'],$fin_data['BalanceSheet']['Liabilities']) : $key)}}"></td>
                                                           </tr>
      
                                                           @endforeach
                                                        </tbody>
                                                     </table>
                                                  </td>
                                                  @endforeach
                                             </tr>
                                             <tr>
                                              <td colspan="4" bgcolor="#e6e4e4"><b class="bold">Build Up of Current Assets</b></td>
                                            </tr>
                                             <tr>
                                                <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                   <table class="table-border-none" width="100%">
                                                      @foreach($buildUpofCurrentAssets_cols as $col_key => $buildUpofCurrentAssets_col)
                                                      <tr>
                                                         <td height="46">{{$buildUpofCurrentAssets_col}}</td>
                                                      </tr>
                                                      @endforeach
                                                   </table>
                                                </td>
                                                @foreach($finance_data as $year => $fin_data)
                                                  <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                                     <table class="table-border-none" width="100%">
                                                        <tbody>
                                                          @foreach($buildUpofCurrentAssets_cols as $key => $buildUpofCurrentAssets_col)
                                                            <tr>
                                                              <td height="46" align="right"><input  type="text" <?php echo function_exists($key) ? 'disabled' : 'name="year['.$year.'][BalanceSheet][Assets]['.$key.']"' ?> class="form-control form-control-sm" value="{{isset($fin_data['BalanceSheet']['Assets'][$key]) ? sprintf('%.2f', $fin_data['BalanceSheet']['Assets'][$key]) : (function_exists($key) ? $key($fin_data['BalanceSheet']['Assets']) : $key)}}"></td>
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
                            <div class="table-responsive ps ps--theme_default">
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
                                  <?php $extraData = extraData($finance_data); ?>
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
                                       <td><input type="text" class="form-control from-inline" id="adj_net_worth_cmnt" name="adj_net_worth_cmnt" value="{{!empty($finDetailData->adj_net_worth_cmnt) ? $finDetailData->adj_net_worth_cmnt : $extraData['AdjustedTangibleNetWorth']}}"></td>                    
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
                                       <td><input type="text" class="form-control from-inline" id="cash_profit_cmnt" name="cash_profit_cmnt" value="{{!empty($finDetailData->cash_profit_cmnt) ? $finDetailData->cash_profit_cmnt : $extraData['CashProfit']}}"></td>                    
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
                                       <td><input type="text" class="form-control from-inline" id="dscr_cmnt" name="dscr_cmnt" value="{{!empty($finDetailData->dscr_cmnt) ? $finDetailData->dscr_cmnt : $extraData['DSCR']}}"></td>                    
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
                                       <td><input type="text" class="form-control from-inline" id="debt_cmnt" name="debt_cmnt" value="{{!empty($finDetailData->debt_cmnt) ? $finDetailData->debt_cmnt : $extraData['DebtEBIDTA']}}"></td>                    
                                    </tr>
                                </tbody>
                             </table>
                             <div class="data mt-4">
                                <h2 class="sub-title bg"  style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Risk Comments on Financials</h2>
                                <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                                   <textarea class="form-control form-control-sm" id="financial_risk_comments" name="financial_risk_comments" rows="3" value="" spellcheck="false">{{isset($finDetailData->financial_risk_comments) ? $finDetailData->financial_risk_comments : ''}}</textarea>
                                   <div class="clearfix"></div>
                                <!-- </div> -->
                             </div>
                             @if (request()->get('view_only'))  
                             @can('save_finance_detail')
                             <button type="submit" class="btn btn-success btn-sm float-right mt-2 mb-3"> Save</button>
                             @endcan
                             @endif
                            </div>
                          </form>
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

@section('additional_css')
<style>
    .form-control-new-height{height: calc(1.0em + .75rem + 2px);}
    </style>
    @endsection
@section('jscript')
<script type="text/javascript">
   appId = '{{$appId}}';
   user_id = '{{$user_id}}';
   appurl = '{{URL::route("financeAnalysis") }}';
   process_url = '{{URL::route("process_financial_statement") }}';
   _token = "{{ csrf_token() }}";
   checkFSAStatus = '{{URL::route("check_fsa_status") }}';
</script>
<script type="text/javascript">
    $("tr").each(function(){
      var deleteTd = true;
      target_tr = $(this);
      target_tr.find('td').each(function() {
        var thishtml = $(this).html();
        if(thishtml !== "") {
           deleteTd = false;
        }
      })
      if (deleteTd) {
        target_tr.remove();
      }
    })
 </script>
<script type="text/javascript">
   $(document).on('click', '.getAnalysis', function() {
      data = {appId, _token};
      const getAnalysis = $('.getAnalysis');
      $.ajax({
         url  : appurl,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
           getAnalysis.removeAttr('onclick').addClass('getAnalysis').addClass('disabled').html('<i class="fa fa-spinner" aria-hidden="true"></i> Please wait...');
         },
         dataType : 'json',
         success:function(result) {
            console.log(result);
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
            getAnalysis.removeClass('disabled').html('<i class="fa fa-refresh" aria-hidden="true"></i> Refresh');
            getAnalysis.attr('onclick', 'window.location.reload()').removeClass('getAnalysis');
         },
         error:function(error) {
            getAnalysis.removeAttr('onclick').addClass('getAnalysis').removeClass('disabled').text('Get Analysis');
         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })

   $(document).on('click', '.process_stmt', function() {
      biz_perfios_id = $(this).attr('pending');
      const processStmt = $('.process_stmt');
      data = {appId, _token, biz_perfios_id};
      $.ajax({
         url  : process_url,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
           processStmt.removeAttr('onclick').addClass('process_stmt').addClass('disabled').html('<i class="fa fa-spinner" aria-hidden="true"></i> Please wait...');
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
            processStmt.removeClass('disabled').html('<i class="fa fa-refresh" aria-hidden="true"></i> Refresh');
            processStmt.attr('onclick', 'window.location.reload()').removeClass('process_stmt');
         },
         error:function(error) {
            processStmt.removeAttr('onclick').addClass('process_stmt').removeClass('disabled').text('Process');
         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })


    function getresult(pageNo) {
      getExcel(pageNo);
    }

    function getExcel(page = 1) {
       var fileType = 'finance';
       data = {appId, page, _token, fileType};
       $.ajax({
          url  : '{{URL::route("getExcelSheet") }}',
          type :'POST',
          data : data,
          dataType : 'json',
          success:function(result) {
             $('#gridView').html(result.response.data);
             $('#paginate').html(result.response.paginate);
          },
          error:function(error) {

          },
          complete: function() {

          },
       })
    }
   
   async function checkFsaStatus(buttonType, retries = 30) {
      const data = {appId, _token};
      const processStmt = $('.process_stmt');
      const getAnalysis = $('.getAnalysis');
      try {
         if (buttonType == 'process_button') {
            processStmt.removeAttr('onclick').addClass('process_stmt').addClass('disabled').html('<i class="fa fa-spinner" aria-hidden="true"></i> Please wait...');
         } else {
            getAnalysis.removeAttr('onclick').addClass('getAnalysis').addClass('disabled').html('<i class="fa fa-spinner" aria-hidden="true"></i> Please wait...');
         }
         const response = await $.ajax({
            url: checkFSAStatus,
            type: 'POST',
            data: data,
            dataType: 'json'
         });
         if (response.status === 0) {
            if (buttonType == 'process_button') {
               processStmt.addClass('disabled').html('<i class="fa fa-spinner" aria-hidden="true"></i> Please wait...');
            } else {
               getAnalysis.addClass('disabled').html('<i class="fa fa-spinner" aria-hidden="true"></i> Please wait...');
            }
            if (retries > 0) {
               await new Promise(resolve => setTimeout(resolve, 5000));
               await checkFsaStatus(buttonType, retries - 1);
            } else {
               const errorMsg = "Maximum number of retries reached. Please check the status of your financial statement analysis.";
               const html = '<div class="alert-danger alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+errorMsg+'</div>';
               $("#pullMsg").html(html);
               $(".isloader").hide();
               if (buttonType == 'process_button') {
                  processStmt.removeAttr('onclick').addClass('process_stmt').removeClass('disabled').text('Process');
               } else {
                  getAnalysis.removeAttr('onclick').addClass('getAnalysis').removeClass('disabled').text('Get Analysis');
               }
            }
         } else {
            $(".isloader").hide();
            if (response.status) {
               if (response.response_status == 1){
                  if (buttonType == 'process_button'){
                     processStmt.removeClass('disabled').html('<i class="fa fa-refresh" aria-hidden="true"></i> Refresh');
                     processStmt.attr('onclick', 'window.location.reload()').removeClass('process_stmt');
                  }else {
                     getAnalysis.removeClass('disabled').html('<i class="fa fa-refresh" aria-hidden="true"></i> Refresh');
                     getAnalysis.attr('onclick', 'window.location.reload()').removeClass('getAnalysis');
                  }
                  window.open(response.value.file_url, '_blank');
               }else{
                  if (buttonType == 'process_button'){
                     processStmt.removeAttr('onclick').addClass('process_stmt').removeClass('disabled').text('Process');
                  }else {
                     getAnalysis.removeAttr('onclick').addClass('getAnalysis').removeClass('disabled').text('Get Analysis');
                  }
               }
            }
         }
      } catch (error) {
         const errorMsg = "Error during AJAX call: " + error.statusText + " - " + error.status;
         const html = '<div class="alert-danger alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+errorMsg+'</div>';
         $("#pullMsg").html(html);
         $(".isloader").hide();
         if (buttonType == 'process_button') {
            processStmt.removeAttr('onclick').addClass('process_stmt').removeClass('disabled').text('Process');
         } else {
            getAnalysis.removeAttr('onclick').addClass('getAnalysis').removeClass('disabled').text('Get Analysis');
         }
     }
}
</script>
<script>
      var ckeditorOptions =  {
        filebrowserUploadUrl: "{{route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'file' ])}}",
        filebrowserUploadMethod: 'form',
        imageUploadUrl:"{{ route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'image' ]) }}",
        disallowedContent: 'img{width,height};'
      };
   CKEDITOR.replace('financial_risk_comments', ckeditorOptions);
</script>
@endsection