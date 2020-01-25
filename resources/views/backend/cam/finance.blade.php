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
                       <!-- <div class="card">Need to check</div> -->
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