<?php 
   $curr_year = date('Y');
   $year_count = 3;
   $start_year = date('Y')-$year_count + 1;
   $class_enable = 'getAnalysis';
   extract(getColumns());
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
                               echo $xlsx_html;
                            ?>
                         </div>
                     <div class="clearfix"></div>
                          <div class="table-responsive ps ps--theme_default" data-ps-id="c810e56b-c241-b3fb-e641-d7d261adf713">
                             <table id="supplier-listing" class="table table-striped cell-border  no-footer overview-table mb-3" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                   <tr role="row">
                                      <th>Condition</th>
                                      <th class="sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending">Criteria</th>
                                      <th class="sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending"> Yes/No</th>
                                      <th class="sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1" aria-label="Mobile: activate to sort column ascending"> Comments</th>
                                   </tr>
                                </thead>
                                <tbody>
                                   <tr role="row" class="odd">
                                      <td>Adjusted Tangible Net Worth</td>
                                      <td>Positive for last 2 financial years</td>
                                      <td><span class="mr-2"><input type="radio" id="y1" name="y"><label for="y1">Yes</label></span>
                                         <span><input type="radio" id="n1" name="y"><label for="n1">No</label></span>
                                      </td>
                                      <td><textarea class="form-control"></textarea></td>
                                   </tr>
                                   <tr role="row" class="odd">
                                      <td>Cash Profit</td>
                                      <td>Positive for 2 out of last 3 financial years (positive in last year)</td>
                                      <td><span class="mr-2"><input type="radio" id="y2" name="yy"><label for="y2">Yes</label></span>
                                         <span><input type="radio" id="n2" name="yy"><label for="n2">No</label></span>
                                      </td>
                                      <td><textarea class="form-control"></textarea></td>
                                   </tr>
                                   <tr role="row" class="odd">
                                      <td>DSCR</td>
                                      <td>&gt;1.2X</td>
                                      <td><span class="mr-2"><input type="radio" id="y3" name="yyy"><label for="y3">Yes</label></span>
                                         <span><input type="radio" id="n3" name="yyy"><label for="n3">No</label></span>
                                      </td>
                                      <td><textarea class="form-control"></textarea></td>
                                   </tr>
                                   <tr role="row" class="odd">
                                      <td>Debt/EBIDTA</td>
                                      <td>&lt;5X</td>
                                      <td><span class="mr-2"><input type="radio" id="y4" name="yyyy"><label for="y4">Yes</label></span>
                                         <span><input type="radio" id="n4" name="yyyy"><label for="n4">No</label></span>
                                      </td>
                                      <td><textarea class="form-control"></textarea></td>
                                   </tr>
                                </tbody>
                             </table>
                             <div class="ps__scrollbar-x-rail" style="width: 1012px; left: 0px; bottom: 0px;">
                                <div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 872px;"></div>
                             </div>
                             <div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;">
                                <div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                             </div>
                             <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;">
                                <div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                             </div>
                             <div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;">
                                <div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                             </div>
                          </div>
                          <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                       </div>
                    </div>
                    <div id="accordion" role="tablist" aria-multiselectable="true" class="accordion">
                       <div class="card">
                          <div class="card-header collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" role="tab" id="headingOne">
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
                                                     <tr>
                                                        <td height="46">NET SALES GROWTH (%)</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">NET PROFIT GROWTH (%)</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">TANGIBLE NET WORTH GROWTH (%)</td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
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
                                            <td colspan="4"><b>(F) LIQUIDITY POSITION ANALYSIS</b></td>
                                         </tr>
                                         <tr>
                                            <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td height="46">NET WORKING CAPITAL</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">CURRENT RATIO</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">QUICK RATIO</td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                         <tr>
                                            <td colspan="4"><b>(G) ACTIVITY EFFICIENCY ANALYSIS</b></td>
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
                                            <td colspan="4"><b>(H) FUNDS FLOW ANALYSIS</b></td>
                                         </tr>
                                         <tr>
                                            <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td height="46">LONG TERM SOURCES</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">LONG TERM USES</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">CONTRIBUTION TO NET WORKING CAPITAL</td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                         <tr>
                                            <td colspan="4"><b>(I) CASH FLOW ANALYSIS</b></td>
                                         </tr>
                                         <tr>
                                            <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td height="46">NET CASH FROM OPERATIONS</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">CASH BEFORE FUNDING</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">INVESTMENTS</td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                         <tr>
                                            <td colspan="4" bgcolor="#e6e4e4"><b class="bold">CASH BEFORE FUNDING, IF NEGATIVE MET FROM</b></td>
                                         </tr>
                                         <tr>
                                            <td valign="top" style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td height="46">- WORKING CAP. FRM BANKS &amp; SHT TERM DEBTS</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">- TERM DEBTS</td>
                                                     </tr>
                                                     <tr>
                                                        <td height="46">- EQUITY</td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                            <td style="vertical-align:top; padding:0px !important;">
                                               <table class="table-border-none" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                     <tr>
                                                        <td align="right"><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                     </tr>
                                                  </tbody>
                                               </table>
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
                                                  <td><input type="text" name="sales_and_profit" id="sales_and_profit" class="form-control form-control-sm" value=""></td>
                                               </tr>
                                               <tr>
                                                  <td>
                                                     <p><b>Gearing &amp; TOL/ATNW:</b></p>
                                                  </td>
                                                  <td><input type="text" name="gearing" id="gearing" class="form-control form-control-sm" value=""></td>
                                               </tr>
                                               <tr>
                                                  <td>
                                                     <p><b>Liquidity Ratio:</b></p>
                                                  </td>
                                                  <td><input type="text" name="liquidity_ratio" id="liquidity_ratio" class="form-control form-control-sm" value=""></td>
                                               </tr>
                                               <tr>
                                                  <td>
                                                     <p><b>Working Capital Cycle:</b></p>
                                                  </td>
                                                  <td><input type="text" name="capital_cycle" id="capital_cycle" class="form-control form-control-sm" value=""></td>
                                               </tr>
                                               <tr>
                                                  <td>
                                                     <p><b>Average collection period receivable days:</b></p>
                                                  </td>
                                                  <td><input type="text" name="average_collection_period" id="average_collection_period" class="form-control form-control-sm" value=""></td>
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
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="first_year">
                                                        <option value="">Select</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
                                                     </select>
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="first_month">
                                                        <option value="">Select</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                     </select>
                                                  </th>
                                                  <th width="25%">
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="second_year">
                                                        <option value="">Select</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
                                                     </select>
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="second_month">
                                                        <option value="">Select</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                     </select>
                                                  </th>
                                                  <th width="25%">
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="third_year">
                                                        <option value="">Select</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
                                                     </select>
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="third_month">
                                                        <option value="">Select</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                     </select>
                                                  </th>
                                               </tr>
                                            </thead>
                                            <tbody class="add-me">
                                               <tr>
                                                  <td>Debtors (Rs Lakhs)  </td>
                                                  <td><input type="text" name="first_year_name" id="first_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control form-control-sm" value=""></td>
                                                  <td><input type="text" name="second_year_name" id="second_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control form-control-sm" value=""></td>
                                                  <td><input type="text" name="third_year_name" id="third_year_name" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return isNumberKey(event)" class="form-control form-control-sm" value=""></td>
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
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="deb_first_year">
                                                        <option value="">Select</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
                                                     </select>
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="deb_first_month">
                                                        <option value="">Select</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                     </select>
                                                  </th>
                                                  <th width="25%">
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="deb_second_year">
                                                        <option value="">Select</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
                                                     </select>
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="deb_second_month">
                                                        <option value="">Select</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                     </select>
                                                  </th>
                                                  <th width="25%">
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="deb_third_year">
                                                        <option value="">Select</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
                                                     </select>
                                                     <select class="form-control form-control-sm form-control form-control-sm-select half-width" id="deb_third_month">
                                                        <option value="">Select</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                     </select>
                                                  </th>
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
                                         <button class="btn btn-primary pull-right btn-sm mt-3"> + Add Row</button>
                                         <div class="clearfix"></div>
                                      </div>
                                   </div>
                                   <div class="data mt-4">
                                      <h2 class="sub-title bg">Risk Comments on Financials</h2>
                                      <div class="pl-4 pr-4 pb-4 pt-2">
                                         <textarea class="form-control form-control-sm" id="financial_risk_comments" name="financial_risk_comments" rows="3" value="" spellcheck="false"></textarea>
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
                                                  <td><input type="text" name="inventory_payable_days" id="inventory_payable_days" class="form-control form-control-sm" value=""></td>
                                                  <td>Projections:
                                                  </td>
                                                  <td><input type="text" name="inventory_projections" id="inventory_projections" class="form-control form-control-sm" value="" <="" td=""></td>
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
                                                     <select class="form-control form-control-sm form-control form-control-sm-select" id="deb_first_years">
                                                        <option value="">Select</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
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
                             <a class="collapsed">
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
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(I) Gross Domestic Sales</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(II) Export Sales</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">Gross Sales</td>
                                                  </tr>
                                                  <tr>
                                                     <td>LESS: Excise duty</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">Net Sales</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">Increase in Net Sales (%)</td>
                                                  </tr>
                                                  <tr>
                                                     <td>ADD: Trading / Other Operating Income</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Export Incentives</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Duty Drawback</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Others</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">Total Operating Income</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">INCREASE IN NET INCOME (%)</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4" bgcolor="#e6e4e4"><b class="bold">COST OF SALES</b></td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>(I) RAW MATERIALS</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(a) Imported</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(b) Indigenous</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>(II) OTHER SPARES</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(a) Imported</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(b) Indigenous</td>
                                                  </tr>
                                                  <tr>
                                                     <td><b>(III) POWER &amp; FUEL</b></td>
                                                  </tr>
                                                  <tr>
                                                     <td><b>(IV) DIRECT LABOUR</b></td>
                                                  </tr>
                                                  <tr>
                                                     <td><b>(V) OTHER MANUFACTURING EXPENSES</b></td>
                                                  </tr>
                                                  <tr>
                                                     <td><b>(VI) DEPRECIATION</b></td>
                                                  </tr>
                                                  <tr>
                                                     <td><b>(VII) REPAIRS &amp; MAINTENANCE</b></td>
                                                  </tr>
                                                  <tr>
                                                     <td><b>(VIII) COST OF TRADING GOODS</b></td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">SUB TOTAL</td>
                                                  </tr>
                                                  <tr>
                                                     <td>ADD: OPENING STOCK IN PROCESS</td>
                                                  </tr>
                                                  <tr>
                                                     <td>DEDUCT: CLOSING STOCK IN PROCESS</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">COST OF PRODUCTION:</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">C O P AS % OF GROSS INCOME</td>
                                                  </tr>
                                                  <tr>
                                                     <td>ADD: OPENING STOCK OF FINISHED GOODS</td>
                                                  </tr>
                                                  <tr>
                                                     <td>DEDUCT: CLOSING STOCK OF FINISHED GOODS</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">COST OF SALES:</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">COST OF SALES AS % OF GROSS INCOME</td>
                                                  </tr>
                                                  <tr>
                                                     <td>SELLING, GENERAL &amp; ADM EXPENSES</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">Cost of Sales + SGA</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">PROFIT BEFORE INTEREST &amp; TAX (PBIT)</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">PBIT AS % OF GROSS SALES</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>Interest payment to Banks</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>Interest - WC</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Interest - Term Loans</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>Interest payment to FIs</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>Interest - WC</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Interest - Term Loans</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Bank Charges</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">INTEREST &amp; OTHER FINANCE CHARGES:</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">INTT. &amp; FIN. CHARGES AS % OF GROSS SALES</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">OPERATING PROFIT BEFORE TAX (OPBT)</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">OPBT AS % OF GROSS INCOME</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4" bgcolor="#e6e4e4"><b class="bold">OTHER NON OPERATIVE INCOME</b></td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>CASH INFLOW ITEMS</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(I) Interest On Deposits &amp; Dividend Received</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(II) Forex Gains</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(III) Non Operating Income from Subsidiaries</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(IV) Tax Refund</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(V) Misc Income</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(VI) Profit on sale of assets &amp; Investments</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Other Income</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>NON CASH INFLOW ITEMS</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(I) Provisions / Expenses Written Back</td>
                                                  </tr>
                                                  <tr>
                                                     <td>TOTAL NON OPERATING INCOME</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4" bgcolor="#e6e4e4"><b class="bold">OTHER NON OPERATING EXP.</b></td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>CASH OUTFLOW ITEMS</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(I) Loss on sale of Investments</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(II) Loss on sale of FA</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(III) Derivative Losses booked</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(IV) Net Loss on Foreign Currency Translation and Transactions, Loss due to fire</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>NON CASH OUTFLOW ITEMS</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(I) Preli.Exp / One Time Expenses Written Off</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(II) Misc Exp. Written Off</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(III) Prov. for doub.debts &amp; Dim.in the val. of Inv.</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(IV) Wealth Tax</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">TOTAL NON OPERATING EXPENSES</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">NET OF NON OPERATING INCOME/EXPENSES</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">PROFIT BEFORE INTEREST, DEPRECIATION &amp; TAX (PBIDT)</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">PROFIT BEFORE TAX / LOSS (PBT)</td>
                                                  </tr>
                                                  <tr>
                                                     <td>TAX PAID</td>
                                                  </tr>
                                                  <tr>
                                                     <td>PROVISION FOR TAXES - Current Period</td>
                                                  </tr>
                                                  <tr>
                                                     <td>- Deffered Taxes</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">PROVISION FOR TAXES - TOTAL</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">NET PROFIT/LOSS (PAT)</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">PAT AS % OF GROSS Income</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00" disabled=""></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4" bgcolor="#e6e4e4"><b class="bold">Extraordinary Items adjustments:</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>Extraordinary Income adjustments (+)</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Extraordinary Expenses adjustments (-)</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">Total Extraordinary items</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">Adjusted PAT (excl Extraordinary Items)</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>EQUITY DIVIDEND PAID</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(I) AMOUNT</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(II) RATE</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Dividend tax</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Partners' withdrawal</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Dividend -Preference</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">RETAINED PROFIT</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                   </tbody>
                                </table>
                             </div>
                          </div>
                       </div>
                       <div class="card">
                          <div class="card-header collapsed" role="tab" id="headingThree" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                             <a class="collapsed">
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
                                               <tbody>
                                                  <tr>
                                                     <td>(i) from applicant bank (CC / WCDL)</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(ii) from other banks</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(of (i) and (ii) in which Bill purchased &amp; disc.)</td>
                                                  </tr>
                                                  <tr>
                                                     <td align="right" height="46">SUB TOTAL</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Sundry Creditors (Trade)</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Short term borrowings from Associates &amp; Group Concerns</td>
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
                                                     <td>Other Current liabilities &amp; provisions (due within 1 year)</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Interest acc but not due</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Provision for NPA</td>
                                                  </tr>
                                                  <tr>
                                                     <td>Provision for leave encashment &amp; gratuity</td>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>TERM LIABILITIES</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td>Other Loan &amp; Advances</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">TOTAL TERM LIABILITIES</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">TOTAL OUTSIDE LIABILITIES (TOL)</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                                  <tr>
                                                     <td><input type="text" class="form-control form-control-sm" placeholder="0.00"></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>NET WORTH</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>RESERVES</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td>Surplus (+) or deficit (-) in P &amp; L Account</td>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>CONTINGENT LIABILITIES</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td>Guarantees issued (relating to business)</td>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
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
                                               <tbody>
                                                  <tr>
                                                     <td>Cash and Bank Balances</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>&nbsp;</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>INVESTMENTS (Other than Long Term)</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(i) Govt. &amp; other securities</td>
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
                                                     <td>RECEIVABLES other than deferred &amp; exports (Incl. bills purchased &amp; discounted by banks)</td>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>FIXED ASSETS</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td>(IV) Plant &amp; Machinery</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(v) Furniture &amp; Fixtures</td>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>OTHER NON CURRENT ASSETS</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
                                                  <tr>
                                                     <td>(I) Investments in Subsidiary companies/ affiliates</td>
                                                  </tr>
                                                  <tr>
                                                     <td>(ii) Other Investments &amp; Investment for acquisition</td>
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
                                                     <td>(ix) Other Non current assets (surplus for Future expansion, Loans &amp; Advances non current in nature, ICD's, Dues from Directors)</td>
                                                  </tr>
                                                  <tr>
                                                     <td>TOTAL OTHER NON CURRENT ASSETS</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4"><b>INTANGIBLE ASSETS (Patents, goodwill, prelim. expenses, bad/doubtful expenses not provided for)</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                      <tr>
                                         <td colspan="4" bgcolor="#e6e4e4"><b class="bold">Build Up of Current Assets</b></td>
                                      </tr>
                                      <tr>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                                     <td height="59">RECEIVABLES (DOMESTIC) other than deferred &amp; exports (Incl. bills purchased &amp; discounted by banks) AMOUNT</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">MONTH'S DOMESTIC Income</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">EXPORT RECV.(Incl. bills purchased &amp; discounted by banks) AMOUNT</td>
                                                  </tr>
                                                  <tr>
                                                     <td height="46">MONTH'S EXPORT Income</td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
                                            </table>
                                         </td>
                                         <td style="vertical-align:top; padding:0px !important; border-right:none;">
                                            <table class="table-border-none" width="100%">
                                               <tbody>
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
                                               </tbody>
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
@endsection